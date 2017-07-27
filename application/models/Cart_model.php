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
    if($b->store_items_cost < $a->num_items)
    {
        return -1;
    }
    
    if($b->store_items_cost > $a->num_items)
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
    
    public function get_best_store_product($product_id, $distance, $max_distance, $user, $search_all = false, $coords = null) 
    {
        $product_found = false;
        
        $store_product = null;
        
        while($distance <= $max_distance && !$product_found)
        {
            $range = "";
            
            if($user != null)
            {
                $range = '(3958*3.1415926*sqrt((latitude-'.$user->profile->latitude.')*(latitude-'.$user->profile->latitude.') + cos(latitude/57.29578)*cos('.$user->profile->latitude.'/57.29578)*(longitude-'.$user->profile->longitude.')*(longitude-'.$user->profile->longitude.'))/180)';
            }
            
            if($user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0)
            {
                $range = '(3958*3.1415926*sqrt((latitude-'.$coords["latitude"].')*(latitude-'.$coords["latitude"].') + cos(latitude/57.29578)*cos('.$coords["latitude"].'/57.29578)*(longitude-'.$coords["longitude"].')*(longitude-'.$coords["longitude"].'))/180)';
            }
            
            $range_select = empty($range) ? "" : ", (".$range.") AS 'range'";
            
            $this->db->select(STORE_PRODUCT_TABLE.".id, ".CHAIN_STORE_TABLE.".id AS department_store_id".$range_select);
            $this->db->join(CHAIN_TABLE, CHAIN_TABLE.'.id = '.STORE_PRODUCT_TABLE.'.retailer_id');
            $this->db->join(CHAIN_STORE_TABLE, CHAIN_TABLE.'.id = '.CHAIN_STORE_TABLE.'.chain_id');

            if(!$search_all && $user != null)
            {
                $this->db->join(USER_FAVORITE_STORE_TABLE, USER_FAVORITE_STORE_TABLE.'.retailer_id = '.CHAIN_TABLE.'.id');
                $this->db->where(array("user_account_id" => $user->id));
                $this->db->where(array('(3958*3.1415926*sqrt((latitude-'.$user->profile->latitude.')*(latitude-'.$user->profile->latitude.') + cos(latitude/57.29578)*cos('.$user->profile->latitude.'/57.29578)*(longitude-'.$user->profile->longitude.')*(longitude-'.$user->profile->longitude.'))/180) <=' => $distance));
            }
            
            if($search_all && $user != null)
            {
                $this->db->where(array('(3958*3.1415926*sqrt((latitude-'.$user->profile->latitude.')*(latitude-'.$user->profile->latitude.') + cos(latitude/57.29578)*cos('.$user->profile->latitude.'/57.29578)*(longitude-'.$user->profile->longitude.')*(longitude-'.$user->profile->longitude.'))/180) <=' => $distance));
            }
            
            // Check if coordinates are set
            if($user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0)
            {
                $this->db->where(array('(3958*3.1415926*sqrt((latitude-'.$coords["latitude"].')*(latitude-'.$coords["latitude"].') + cos(latitude/57.29578)*cos('.$coords["latitude"].'/57.29578)*(longitude-'.$coords["longitude"].')*(longitude-'.$coords["longitude"].'))/180) <=' => $distance));
            }

            $this->db->where(array("product_id" => $product_id));

            $this->db->order_by("price", "ASC");
            if(!empty($range_select))
            {
                $this->db->order_by("range", "ASC");
            }
            $query = $this->db->get_compiled_select(STORE_PRODUCT_TABLE);
            $store_product = $this->db->query($query)->row();
            $product_found = $store_product != null;
            $distance += DEFAULT_DISTANCE;
        }
        
        $best_Store_product = null;
	
        if($store_product != null)
        {
            $best_Store_product = $this->getStoreProduct($store_product->id, false, false);
            $best_Store_product->department_store = $this->get(CHAIN_STORE_TABLE, $store_product->department_store_id);
            
            $best_Store_product->department_store->distance = 0;
            if(isset($store_product->distance))
            {
                $best_Store_product->department_store->distance = $store_product->distance;
            }
            
        }
	
	// There was no best product wrt the user. Get the cheapest product    
	if($store_product == null)
	{
	    $this->db->order_by("price", "ASC");
	    $store_product = $this->get_specific(STORE_PRODUCT_TABLE, array("product_id" => $product_id));
	    if($store_product != null)
	    {
	    	$best_Store_product = $this->getStoreProduct($store_product->id, false, false);
		$best_Store_product->department_store = new stdClass();
		$best_Store_product->department_store->name = "RAS";
	        $best_Store_product->department_store->distance = 0;
	    }
	}
        
        return $best_Store_product;
    }
    
    public function get_closest_stores($user, $distance, $products, $searchAll = false, $coords = null, $limit = 5)
	{
            $stores = array();
            
            $range = "";
            
            if($user != null)
            {
                $range = '(3958*3.1415926*sqrt((latitude-'.$user->profile->latitude.')*(latitude-'.$user->profile->latitude.') + cos(latitude/57.29578)*cos('.$user->profile->latitude.'/57.29578)*(longitude-'.$user->profile->longitude.')*(longitude-'.$user->profile->longitude.'))/180)';
            }
            
            if($user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0)
            {
                $range = '(3958*3.1415926*sqrt((latitude-'.$coords["latitude"].')*(latitude-'.$coords["latitude"].') + cos(latitude/57.29578)*cos('.$coords["latitude"].'/57.29578)*(longitude-'.$coords["longitude"].')*(longitude-'.$coords["longitude"].'))/180)';
            }
            
            $range_select = empty($range) ? "" : ", (".$range.") AS 'range'";
            
            $this->db->join(CHAIN_TABLE, CHAIN_TABLE.'.id = '.CHAIN_STORE_TABLE.'.chain_id');
            $result = $this->db->get(CHAIN_STORE_TABLE);
            
            $this->db->where(array("user_id" => $user->id, "distance <=" => $distance));

            foreach($result->result() as $row)
            {
                $department_store = $this->get(CHAIN_STORE_TABLE, $row->chain_store_id);

                if($department_store != null)
                {
                    $department_store->chain = $this->get(CHAIN_TABLE, $department_store->chain_id);

                    //check number of products the store has
                    $result = $this->store_has_product($department_store->chain, $products);

                    //check if the chain store has at least one of the products
                    if($result['num_items'] > 0)
                    {
                        $stores[$department_store->chain_id] = new stdClass();
                        $stores[$department_store->chain_id]->store = $department_store;
                        $stores[$department_store->chain_id]->store_items_cost = $result['store_total_cost'];
                        $stores[$department_store->chain_id]->num_items = $result['num_items'];
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
        $num_items = 0;

        foreach ($products as $product) 
        {
            $product_found = $this->cart_model->get_specific(STORE_PRODUCT_TABLE, array("product_id" => $product->id, "retailer_id" => $store->id));

            if($product_found != null)
            {
                $store_items_cost += $product_found->price;
                $num_items++;
            }
        }

        $result = array();
        $result['num_items'] = $num_items;
        $result['store_total_cost'] = $store_items_cost;
        return $result;
    }
    
    // get all the zipcodes within the specified radius - default 20
    function get_stores_within($lat, $lon, $radius)
    {
        $this->db->where(array('(3958*3.1415926*sqrt((latitude-'.$lat.')*(latitude-'.$lat.') + cos(latitude/57.29578)*cos('.$lat.'/57.29578)*(longitude-'.$lon.')*(longitude-'.$lon.'))/180) <=' => $radius));
        return $this->db->get(CHAIN_STORE_TABLE)->result();
    }
}
