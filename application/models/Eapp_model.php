
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of Admin_model
 *
 * @author beson
 */
class Eapp_model extends CI_Model 
{
    public function __construct()
    {
	parent::__construct();
	// Your own constructor code
    } 
    
    public function get_products_with_store_products($query_string, $user_stores) 
    {
        // Limit results to first 100
        $this->db->limit(100);
        $this->db->select(PRODUCT_TABLE.'.*');
        // Join with the store products table
        $this->db->join(STORE_PRODUCT_TABLE, $this->store_product_product_join);
        $this->add_name_filter($query_string);
        // Get the list of products
        $products = $this->db->get(PRODUCT_TABLE)->result();
        
        foreach ($products as $key => $product) 
        {
            $products[$key]->store_products = new stdClass();
            
            $products[$key]->store_products->in_my_store = array();
            
            $products[$key]->store_products->others = array();
            
            $store_products = $this->get_product_store_products($products[$key]->id);
                        
            if(sizeof($user_stores) > 0)
            {
                foreach ($user_stores as $store) 
                {
                    foreach ($store_products as $store_product) 
                    {
                        if($store->retailer_id == $store_product->retailer_id)
                        {
                            array_push($products[$key]->store_products->in_my_store, $store_product);
                        }
                        else
                        {
                            array_push($products[$key]->store_products->others, $store_product);
                        }
                    }
                }
            }
            else
            {
                $products[$key]->store_products->others = array_merge($products[$key]->store_products->others, $store_products);
            }
        }
        
        return $products;
    }
    
    private function get_product_store_products($id) 
    {
        $this->db->where($this->latest_products_condition, NULL, FALSE);
        
        return $this->get_where(STORE_PRODUCT_TABLE, "*", array("product_id" => $id), true);
        
    }
}
