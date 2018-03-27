<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Eapp extends CI_Controller 
{

    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('eapp_model');
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
            
            $retailers[$key]->image = base_url('/assets/img/stores/').$retailers[$key]->image;
        }
        
        echo json_encode($retailers);
    }
    
    public function get_brands() 
    {
        $brands = $this->admin_model->get_all(PRODUCT_BRAND_TABLE);
        
        foreach ($brands as $key => $value) 
        {
            if($value != null && strpos($value->image, 'http') === FALSE)
            {
                $path = ASSETS_DIR_PATH."img/stores/".$value->image;
            
                if(!file_exists($path))
                {
                    $brands[$key]->image = "no_image_available.png";
                }

                $brands[$key]->image = base_url('/assets/img/stores/').$brands[$key]->image;
            }
        }
        
        echo json_encode($brands);
    }
    
    public function get_security_questions() 
    {
        $security_questions = $this->admin_model->get_all(SECURITY_QUESTIONS);
        
        echo json_encode($security_questions);
    }
    
    public function get_close_retailers() 
    {
        $distance = $this->input->post("distance");
        
        $coords = array
        (
            'longitude' => $this->input->post("longitude"),
            'latitude' => $this->input->post("latitude")
         );
        
        $retailers = $this->cart_model->get_closest_merchants($this->user, $coords, $distance);
        
        foreach ($retailers as $key => $value) 
        {
            $path = ASSETS_DIR_PATH."img/stores/".$value->image;
            
            if(!file_exists($path))
            {
                $retailers[$key]->image = "no_image_available.png";
            }
           $retailers[$key]->image = base_url('/assets/img/stores/').$retailers[$key]->image;
        }
        
        echo json_encode($retailers);
    }
    
    public function add_product_to_list() 
    {
        if($this->user != null)
        {
            // Get the product id to add
            $product_id = $this->input->post("product_id");
            // get the current product list
            $product_list = $this->get_grocery_list($this->input->post("list_id"));
            
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
                "id" => $this->input->post("list_id"),
                "grocery_list" => json_encode($product_list)
            );

            $this->account_model->create(USER_GROCERY_LIST_TABLE, $data);
            
            $user = $this->account_model->get_user($this->user->id);
            $return = array();
            $return["grocery_lists"] = $user->grocery_lists;
            $return["success"] = true;
            
            echo json_encode($return);
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
    
    private function get_grocery_list($listID) 
    {
        // get the current product list
        $product_list = $this->account_model->get_specific(USER_GROCERY_LIST_TABLE, array("id" => $listID));

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
            $product_list = $this->get_grocery_list($this->input->post("list_id"));
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
                    "id" => $this->input->post("list_id"),
                    "grocery_list" => json_encode($newProductList)
                );

                $this->account_model->create(USER_GROCERY_LIST_TABLE, $data);
                
                $user = $this->account_model->get_user($this->user->id);
                $return = array();
                $return["grocery_lists"] = $user->grocery_lists;
                $return["success"] = true;

                echo json_encode($return);

            }
        }
        
        echo false;
    }
    
    public function get_cart_contents() 
    {
        echo $this->get_cached_cart_contents();
    }
    
    public function change_distance() 
    {
        if($this->user != null)
        {
            $data = array
            (
                'id' => $this->user->profile->id,
                $this->input->post("distance_to_change") => $this->input->post("value")
            );
            
            // Update the user
            $this->shop_model->create(USER_PROFILE_TABLE, $data);
            $this->set_user();
            
            // Return the user
            echo json_encode($this->user);
        }
    }
    
    public function get_categories() 
    {
        $categories = $this->admin_model->get_all(CATEGORY_TABLE);
        
        foreach ($categories as $key => $value) 
        {
            $path = ASSETS_DIR_PATH."img/categories/".$value->image;
            
            if(!file_exists($path) || empty($value->image))
            {
                $categories[$key]->image = "no_image_available.png";
            }
            
            $categories[$key]->image = base_url('/assets/img/categories/').$categories[$key]->image;
        } 
        
        echo json_encode($categories);
        
    }
    
    public function get_subcategories() 
    {
        $subcategories = $this->admin_model->get_all(SUB_CATEGORY_TABLE);
        
        echo json_encode($subcategories);
        
    }
	
    public function get_admin_subcategories() 
    {
        $result = array();

        $query = json_decode($this->input->post("query"));

        $allQuery = new stdClass();
        $allQuery->page = 1;
        $allQuery->filter = $query->filter;
        $allQuery->limit = 1000;
        
        $subcategories = $this->admin_model->query_subcategories($allQuery);

        $result['count'] = sizeof($subcategories);

        $result['sub_categories'] = $this->admin_model->query_subcategories($query);

        $result['categories'] = $this->admin_model->get_all(CATEGORY_TABLE);
        
        echo json_encode($result);
        
    }
    
    public function get_unit_compareunits() 
    {
        $data = array
        (
            'unit_id' => $this->input->post("id")
        );
        
        echo json_encode($this->admin_model->get_unit_compareunits($data));
    }
    
    public function get_compareunit_units() 
    {
        $data = array
        (
            UNIT_CONVERSION.'.compareunit_id' => $this->input->post("id")
        );
        
        echo json_encode($this->admin_model->get_compareunit_units($data));
    }
    
    public function get_compareunits() 
    {
        $units = $this->admin_model->get_all(COMPAREUNITS_TABLE);
        
        echo json_encode($units);
    }
    
    public function get_units() 
    {
        $units = $this->admin_model->get_all(UNITS_TABLE);
        
        echo json_encode($units);
    }
    
    public function get_unit_compareunit() 
    {
        $unit_compareunit = $this->admin_model->get_all(UNIT_CONVERSION);
        
        echo json_encode($unit_compareunit);
    }
    
    public function get_product_unit_compareunit() 
    {
        $unit_compareunit = $this->admin_model->get_all(PRODUCT_UNIT_CONVERSION);
        
        echo json_encode($unit_compareunit);
    }
    
    public function get_products() 
    {
        $query = json_decode($this->input->post("query"));
        
        $data = new stdClass();
        
        $data->count = $this->admin_model->get_products_count($query->filter);
        
        $data->products = $this->admin_model->get_products($query);
        
        $data->subcategories = $this->admin_model->get_all(SUB_CATEGORY_TABLE);
        
        $data->units = $this->admin_model->get_all(COMPAREUNITS_TABLE);
        
        echo json_encode($data);
    }
    
    public function delete_product($id) 
    {
        $this->admin_model->delete(PRODUCT_TABLE, $id);
    }
    
    public function delete_sub_category($id) 
    {
        $this->admin_model->delete(SUB_CATEGORY_TABLE, $id);
    }
    
    public function get_otiprix_product($id) 
    {
        $product = $this->admin_model->get(PRODUCT_TABLE, $id);
        
        $path = ASSETS_DIR_PATH."img/products/".$product->image;
            
        if(!file_exists($path))
        {
            $product->image = "no_image_available.png";
        }
            
        $product->image = base_url('/assets/img/products/').$product->image;
        
        
        echo json_encode($product);
    }
    
    public function get_products_count() 
    {
        echo json_encode($this->admin_model->get_products_count());
    }


    public function get_store_product() 
    {
        $id = $this->input->post("id");
        
        echo json_encode($this->admin_model->getStoreProduct($id, false, false));
    }
    
    public function get_user_optimizations() 
    {
        $user_optimization = $this->account_model->get_user_optimizations($this->user);
        
        echo json_encode($user_optimization);
    }
    
    public function subscribe() 
    {
        $this->load->helper('email');
        
        $result = array();
        
        $email = $this->input->post("email");
        
        // Check if the email exists in our database
        $subscription = $this->account_model->get_specific(NEWSLETTER_SUBSCRIPTIONS, array("email" => $email));
        
        if(isset($subscription) && $subscription->type == 1)
        {
            $result["subscribed"] = false;
            $result["message"] = "Vous êtes déjà abonné à Otiprix.";
            $result["title"] = "Déjà abonné";
        }
        else
        {
            
            if (valid_email($email))
            {
                $data = array("email" => $email, "type" => 1, "unsubscribe_token" => $this->GUID());
            
                if(isset($subscription))
                {
                    $data["id"] = $subscription->id;
                }

                $this->account_model->create(NEWSLETTER_SUBSCRIPTIONS, $data);

                $result["message"] = "Vous avez été abonné avec succès à Otiprix.";
                $result["title"] = "Succès";
                $result["subscribed"] = true;
            }
            else
            {
                $result["subscribed"] = false;
                $result["title"] = "Email invalide";
                $result["message"] = "L'e-mail entré n'est pas valide..";
            }
            
        }
        
        echo json_encode($result);
    }
    
    public function unsubscribe()
    {
        $result = array("success" => true);
        
        $token = $this->input->post("token");
        
        $subscription = $this->admin_model->get_specific(NEWSLETTER_SUBSCRIPTIONS, array("unsubscribe_token" => $token));
        
        if(isset($subscription) && $subscription->type == 1)
        {            
            // Unsubscribe the user
            $this->admin_model->create(NEWSLETTER_SUBSCRIPTIONS, array("id" => $subscription->id, "type" => 0));
        }
        else
        {
            $result["success"] = false;
        }
        
        echo json_encode($result);
    }
    
    public function get_email_from_unsubscribe_token()
    {
        $result = array("success" => true, "email" => "");
        
        $token = $this->input->post("token");
        
        $subscription = $this->admin_model->get_specific(NEWSLETTER_SUBSCRIPTIONS, array("unsubscribe_token" => $token));
        
        if(isset($subscription) && $subscription->type == 1)
        {            
            $result["email"] = $subscription->email;
        }
        else
        {
            $result["success"] = false;
        }
        
        echo json_encode($result);
    }
    
    public function get_latest_products() 
    {
        echo json_encode($this->home_model->get_store_products_limit(25, 0)["products"]);
    }
    
    public function get_user_products_with_store_products() 
    {
        $filter = $this->input->post("filter");
        
        $user_stores = array();
        
        if($this->user != null)
        {
            $this->eapp_model->get_user_favorite_stores($this->user->id);
            
            echo json_encode($this->eapp_model->get_products_with_store_products($filter, $user_stores));
        }
        
        
    }
    
    public function hit_table() 
    {
        
    }
   
}
