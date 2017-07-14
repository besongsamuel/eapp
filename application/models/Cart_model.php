<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of Admin_model
 *
 * @author besong
 */

function cmp_stores_by_num_items($a, $b)
{
    if($b->store_items_cost < $a->store_items_cost)
    {
        return -1;
    }
    
    if($b->store_items_cost > $a->store_items_cost)
    {
        return 1;
    }
    
    return 0;
}

class Cart_model extends CI_Model 
{
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->load->library("geo");
    }
    
    /**
     * Gets a list of store products related to a given product
     * @param type $product_id
     * @return type
     */
    public function getProducts($product_id)
    {
        $array = array("period_from <=" => date("Y-m-d"), "period_to >=" => date("Y-m-d"), "product_id" => $product_id);
        $this->db->select("*");
        $this->db->from(STORE_PRODUCT_TABLE);
        $this->db->where($array);
        return $this->db->get()->result();
    }
     
    /**
     * Gets the department stores for the chain that are in the 
     * user's current city
     * @param type $retailer_id The id of the chain or retailer
     * @param type $user The user's object
     * @return type returns an object
     */
    public function getDepartmentStores($retailer_id, $user)
    {
        $array = array("chain_id" => $retailer_id);
        $this->db->select("*");
        $this->db->from(CHAIN_STORE_TABLE);
        $this->db->where($array);
        $this->db->like("city", $user->profile->city);
        return $this->db->get()->result();
    }
    
    /**
     * For a given department stores in the user's city, this method
     * grabs those stores that are closes to him
     * @param type $department_stores
     * @param type $user
     * @param type $distance
     * @return a department store object
     */
    public function findCloseDepartmentStore($department_stores, $user, $distance)
    {
        $closest = null;
        
        foreach($department_stores as $store)
        {
            // For the current user, get the row containing the distance from the 
            // user's address
            $user_chain_store = $this->account_model->get_specific(USER_CHAIN_STORE_TABLE, array("chain_store_id" => $store->id,  "user_id" => $user->id));

            if($user_chain_store != null 
            && $user_chain_store->distance < $distance 
            && ($closest == null || $closest->distance > $user_chain_store->distance))
            {
                $store->distance = $user_chain_store->distance;
                $closest = $store;
            }
        }

        return $closest;
    }
    
	public function get_closest_stores($user, $distance, $products, $limit = 5)
	{
            $stores = array();

            $this->db->where(array("user_id" => $user->id, "distance <=" => $distance));
            $this->db->order_by("distance", "ASC");
            $result = $this->db->get(USER_CHAIN_STORE_TABLE);

            foreach($result->result() as $row)
            {
                $department_store = $this->get(CHAIN_STORE_TABLE, $row->chain_store_id);

                if($department_store != null)
                {
                    $department_store->chain = $this->get(CHAIN_TABLE, $department_store->chain_id);

                    //check number of products the store has
                    $store_items_cost = $this->store_has_product($department_store->chain, $products);

                    //check if the chain store has at least one of the products
                    if($store_items_cost > 0)
                    {
                        $stores[$department_store->chain_id] = new stdClass();
                        $stores[$department_store->chain_id]->store = $department_store;
                        $stores[$department_store->chain_id]->store_items_cost = $store_items_cost;
                        $stores[$department_store->chain_id]->distance = $row->distance;
                    }

                }
            }
            
            // order stores by those that have the most products
            usort($stores, "cmp_stores_by_num_items");
            
            // get the top 5
            if(sizeof($stores) > $limit)
            {
                $stores = array_slice($stores, 0, 5);
            }

            return $stores;
	}
        
        public function get_user_closest_retailer_store($user, $distance, $retailer_id)
	{
            $this->db->select(CHAIN_STORE_TABLE.".*, distance");
            $this->db->join(CHAIN_STORE_TABLE, CHAIN_STORE_TABLE.".id = ".USER_CHAIN_STORE_TABLE.".chain_store_id");
            $this->db->join(CHAIN_TABLE, CHAIN_TABLE.".id = ".CHAIN_STORE_TABLE.".chain_id");
            $this->db->where(array("user_id" => $user->id, "distance <=" => $distance, CHAIN_TABLE.".id" => $retailer_id));
            $this->db->order_by("distance", "ASC");
            $stores = $this->db->get(USER_CHAIN_STORE_TABLE);
            
            if($stores != null && $stores->num_rows() > 0)
            {
                return $stores->row();
            }
            else
            {
                return null;
            }
            
	}
        
        private function store_has_product($store, $products)
        {
            $store_items_cost = 0;
            
            foreach ($products as $product) 
            {
                $store_product = $this->getStoreProduct($product->id, false, false);
                              
                $product_found = $this->cart_model->get_specific(STORE_PRODUCT_TABLE, array("product_id" => $store_product->product->id, "retailer_id" => $store->id));
                
                if($product_found != null)
                {
                    $store_items_cost += $product_found->price;
                }
            }
            
            return $store_items_cost;
        }
}
