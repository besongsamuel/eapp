<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Eapp extends CI_Controller 
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function site_url() 
    {
        echo json_encode(site_url());
    }
    
    public function base_url() 
    {
        echo json_encode(base_url());
    }
    
    public function get_retailers() 
    {
        $retailers = $this->admin_model->get_all(CHAIN_TABLE);
        
        foreach ($retailers as $key => $value) 
        {
            $path = ASSETS_DIR_PATH."img/stores/".$value->image;
            
            if(!file_exists($path))
            {
                $retailers[$key]->image = "no_image_available.png";
            }
        }
        
        echo addslashes(json_encode($retailers));
    }
    
    public function add_product_to_list() 
    {
        if($this->user != null)
        {
            // Get the product id to add
            $product_id = $this->input->post("product_id");
            // get the current product list
            $product_list = $this->get_grocery_list();
            
            // check if product is already in list
            if($this->list_contains_product($product_list, $product_id) == FALSE)
            {
                $item = new stdClass();
                $item->id = $product_id;
                $item->quantity = 1;
                // add product to list.
                array_push($product_list, $item);
            }
            // Save list
            $data = array
            (
                "user_account_id" => $this->user->id,
                "grocery_list" => json_encode($product_list)
            );

            $this->account_model->delete(USER_GROCERY_LIST_TABLE, array("user_account_id" => $this->user->id));
            $this->account_model->create(USER_GROCERY_LIST_TABLE, $data);

            echo true;
        }
         
        echo false;
    }
    
    private function list_contains_product($product_list, $product_id) 
    {
        foreach ($product_list as $item) 
        {
            if($item->id == $product_id)
            {
                return TRUE;
            }
        }
        
        return FALSE;
    }
    
    private function get_grocery_list() 
    {
        // get the current product list
        $product_list = $this->account_model->get_specific(USER_GROCERY_LIST_TABLE, array("user_account_id" => $this->user->id));

        if(isset($product_list->grocery_list))
        {
            $product_list = json_decode($product_list->grocery_list);
        }
        else
        {
            $product_list = array();
        }
        
        if(!isset($product_list))
        {
            $product_list = array();
        }
        
        return $product_list;
    }
    
    public function remove_product_from_list() 
    {
        if($this->user != null)
        {
            // Get the product id to add
            $product_id = $this->input->post("product_id");
            // get the current product list
            $product_list = $this->get_grocery_list();
            // check if product is already in list
            if($this->list_contains_product($product_list, $product_id) == TRUE)
            {
                $newProductList = array();

                foreach ($product_list as $item) 
                {
                    if($item->id != $product_id)
                    {
                        array_push($newProductList, $item);
                    }
                }

                // Save list
                $data = array
                (
                    "user_account_id" => $this->user->id,
                    "grocery_list" => json_encode($newProductList)
                );

                $this->account_model->delete(USER_GROCERY_LIST_TABLE, array("user_account_id" => $this->user->id));
                $this->account_model->create(USER_GROCERY_LIST_TABLE, $data);

                echo true;
            }
        }
        
        echo false;
    }
    
    public function get_cart_contents() 
    {
        echo $this->get_cached_cart_contents();
    }
   
}
