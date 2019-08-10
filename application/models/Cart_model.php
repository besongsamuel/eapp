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

function cmp_unit_price($a, $b)
{
    if((float)$a->compare_unit_price < (float)$b->compare_unit_price)
    {
        return -1;
    }
    
    if((float)$a->compare_unit_price > (float)$b->compare_unit_price)
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
        $array = array("product_id" => $product_id);
        $this->db->select("*");
        $this->db->from(STORE_PRODUCT_TABLE);
        $this->db->where('period_from <= CURDATE() AND period_to >= CURDATE()', NULL, FALSE);
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
    
    public function get_best_store_product(
            $product_id, 
            $distance, 
            $max_distance, 
            $user, 
            $search_all = false, 
            $coords = null, 
            $store_product_id = -1) 
    {
        $product_found = false;
        
        $store_product = null;
        
        while($distance <= $max_distance && !$product_found)
        {
            $range = "";
            
            $longitude = null;
            $latitude = null;
            
            if($user != null)
            {
                $longitude = $user->profile->longitude;
                $latitude = $user->profile->latitude;
            }
            
            if($user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0)
            {
                $longitude = $coords["longitude"];
                $latitude = $coords["latitude"];
            }
            
            if($latitude != null && $longitude != null)
            {
                $range = "SQRT(POW(69.1 * (latitude - ".$latitude."), 2) + POW(69.1 * (".$longitude." - longitude) * COS(latitude / 57.3), 2))";
            }
            
            $range_select = empty($range) ? "" : ", (".$range.") AS 'range'";
            
            $this->db->select(STORE_PRODUCT_TABLE.".id, unit_price, product_id, ".CHAIN_TABLE.".id as merchant_id, ".CHAIN_STORE_TABLE.".id AS department_store_id".$range_select);
            $this->db->join(CHAIN_TABLE, CHAIN_TABLE.'.id = '.STORE_PRODUCT_TABLE.'.retailer_id');
            $this->db->join(CHAIN_STORE_TABLE, CHAIN_TABLE.'.id = '.CHAIN_STORE_TABLE.'.chain_id');
            
            if($user != null || ($user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0))
            {
                $this->db->where($range." <=".$distance);
            }
            
            if(!$search_all)
            {
                $this->db->join(USER_FAVORITE_STORE_TABLE, USER_FAVORITE_STORE_TABLE.'.retailer_id = '.CHAIN_TABLE.'.id');
                $this->db->where(array("user_account_id" => $user->id));
            }
            
            $this->db->where(array("product_id" => $product_id));
            
            $this->db->where('period_from <= CURDATE() AND period_to >= CURDATE()', NULL, FALSE);
            
            $this->db->order_by("unit_price", "ASC");
			
            $query = $this->db->get_compiled_select(STORE_PRODUCT_TABLE);
            $store_product = $this->db->query($query)->first_row();
            $related_products = array();
            
            if($store_product != null)
            {
                $product_found = true;
                
                // get all the other choices
                $all_store_products = $this->db->query($query)->result();
                $close_store_products = array();
                
                // The store product selected by the user if applicale
                $user_selected_sp = null;
                
                foreach($all_store_products as $val)
                {
                    if($user_selected_sp == null && $val->id == $store_product_id)
                    {
                        $sp = $this->getStoreProduct($val->id);
                        $sp->department_store = $this->get(CHAIN_STORE_TABLE, $val->department_store_id);
                        $sp->department_store->range = $val->range;
                        $sp->merchant_id = $val->merchant_id;
                        $user_selected_sp = $sp;
                        continue;
                    }
                    
                    if(isset($close_store_products[$val->id]) && $close_store_products[$val->id]->merchant_id == $val->merchant_id)
                    {
                        $prev_range = floatval($close_store_products[$val->id]->department_store->range);
                        
                        if($prev_range < floatval($val->range))
                        {
                            continue;
                        }
                    }
                    
                    // Get the full object
                    $sp = $this->getStoreProduct($val->id, false, false, false);
                    
                    // only add if it is comparable
                    if((float)$sp->compare_unit_price > 0)
                    {
                        $sp->department_store = $this->get(CHAIN_STORE_TABLE, $val->department_store_id);
                        $sp->department_store->range = $val->range;
                        $sp->merchant_id = $val->merchant_id;
                        // Get this when it is selected. 
                        $close_store_products[$val->id] = $sp;
                    }
                    
                }
				
		// order by unit price
		usort($close_store_products, "cmp_unit_price");
                
                if($store_product_id != -1)
                {
                    if($user_selected_sp == null)
                    {
                        $user_selected_sp =  $this->getStoreProduct($store_product_id);
                        $user_selected_sp->id = $store_product_id;
                        $user_selected_sp->department_store = new stdClass();
                        $user_selected_sp->department_store->name = "Le magasin n'est pas disponible près de chez vous.";
                        $user_selected_sp->department_store->id = -1;
                        $user_selected_sp->department_store->distance = 0;
                    }
                    
                    array_unshift($close_store_products, $user_selected_sp);
                }
                
                if(sizeof($close_store_products) == 0)
                {
                    $store_product->department_store = $this->get(CHAIN_STORE_TABLE, $store_product->department_store_id);
                    $store_product->department_store->range = $store_product->range;
                    $store_product->merchant_id = $store_product->merchant_id;
                    array_push($close_store_products, $store_product);
                }
                
		// The best store product (cheapest) will be at the top of the list
		$store_product = reset($close_store_products);
		$related_products = $close_store_products;
		// The worst store product (most expensive) will be at the end of the list
		$store_product->worst_product = end($close_store_products);
		//$store_product->worst_product->department_store->distance = $this->compute_driving_distance($store_product->worst_product->department_store, $user, $coords);
                
                if(isset($close_store_products) && count($close_store_products) > 1)
                {
                    $this->statistics->record_product_optimization_stat($store_product);
                }
            }
             
            $distance += DEFAULT_DISTANCE;
        }
        
        $best_Store_product = null;
	
        if($product_found)
        {			
            $best_Store_product = $this->getStoreProduct($store_product->id);
            
            if(sizeof($related_products) > 1)
            {
                $best_Store_product->worst_product = $store_product->worst_product;
            	$best_Store_product->related_products = $related_products;
            }
            
            $best_Store_product->department_store = $store_product->department_store;
        }
        
        // There was no best product wrt the user. Get the cheapest product    
        if($store_product == null)
        {
            $best_Store_product = $this->get_cheapest_store_product($product_id, true, $store_product_id);
            $best_Store_product->worst_product = null;
            $best_Store_product->related_products = array();
        }
        
        return $best_Store_product;
    }
    
    public function get_cheapest_store_product($product_id, $latest = true, $store_product_id = -1) 
    {
        $this->db->where(array("product_id" => $product_id));
        if($store_product_id != -1)
        {
            $this->db->where(STORE_PRODUCT_TABLE.".id = ".$store_product_id);
        }
        if($latest)
        {
            $this->db->where('period_from <= CURDATE() AND period_to >= CURDATE()', NULL, FALSE);
        }
        
        $this->db->order_by("price", "ASC");
        $query = $this->db->get_compiled_select(STORE_PRODUCT_TABLE);
        $store_product = $this->db->query($query)->row();
        $cheapest_store_product = null;
        if($store_product != null)
        {
            $cheapest_store_product = $this->getStoreProduct($store_product->id, true, $latest, true);
            
            if(isset($cheapest_store_product->similar_products) && count($cheapest_store_product->similar_products) > 1)
            {
                // search for the cheapest
                foreach ($cheapest_store_product->similar_products as $similar_sp) 
                {
                    if($similar_sp->compare_unit_price < $cheapest_store_product->compare_unit_price)
                    {
                        $cheapest_store_product = $similar_sp;
                    }
                }
                
                $cheapest_store_product = $this->getStoreProduct($cheapest_store_product->id, true, $latest, true);
                
                // add to product optimization table
                $this->statistics->record_product_optimization_stat($cheapest_store_product);
                
            }
        }
		        
        if($cheapest_store_product == null)
        {
            $cheapest_store_product = $this->create_empty_store_product();
            $cheapest_store_product->product = $this->get_product($product_id);
        }
        $cheapest_store_product->department_store = new stdClass();
        $cheapest_store_product->department_store->name = "Le magasin n'est pas disponible près de chez vous.";
        $cheapest_store_product->department_store->id = -1;
        $cheapest_store_product->department_store->distance = 0;
        
        return $cheapest_store_product;
    }
    
    public function create_empty_store_product()
    {
        $empty_store_product = new stdClass();
        $empty_store_product->price = 0;
        $empty_store_product->retailer = new stdClass();
        $empty_store_product->retailer->image = base_url("/assets/img/stores/no_image_available.png");
        $empty_store_product->retailer->name = "none";       
        return $empty_store_product;
    }
    
    public function get_closest_stores($user, $distance, $products, $search_all = false, $coords = null, $limit = 10)
    {
            $stores = array();
            
            $range = "";
            
            $longitude = null;
            $latitude = null;
            
            if($user != null)
            {
                $longitude = $user->profile->longitude;
                $latitude = $user->profile->latitude;
            }
            
            if($user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0)
            {
                $longitude = $coords["longitude"];
                $latitude = $coords["latitude"];
            }
            
            if($latitude != null && $longitude != null)
            {
                $range = "SQRT(POW(69.1 * (latitude - ".$latitude."), 2) + POW(69.1 * (".$longitude." - longitude) * COS(latitude / 57.3), 2))";
            }
            
            // join chain stores with chain
            $range_select = empty($range) ? "" : ", (".$range.") AS 'range'";
            $this->db->select(CHAIN_STORE_TABLE.'.* '.$range_select);
            $this->db->join(CHAIN_TABLE, CHAIN_TABLE.'.id = '.CHAIN_STORE_TABLE.'.chain_id');
		
            if(!$search_all && $user != null)
            {
                // Join with user favorites and get the closest to set distance
                $this->db->join(USER_FAVORITE_STORE_TABLE, USER_FAVORITE_STORE_TABLE.'.retailer_id = '.CHAIN_TABLE.'.id');
                $this->db->where(array("user_account_id" => $user->id));
                $this->db->where(array($range.' <=' => $distance));
            }
            
            if(($search_all && $user != null) || ($user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0))
            {
                // get closest to user. Search from all stores
                $this->db->where(array($range.' <=' => $distance));
            }
		
            if(!empty($range_select))
            {
                $this->db->order_by("range", "ASC");
            }
	    
            $result = $this->db->get(CHAIN_STORE_TABLE);
			
            foreach($result->result() as $row)
            {
                $department_store = $row;

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
						
						// COMPUTE DRIVING DISTANCE HERE						
                        $stores[$department_store->chain_id]->distance = $this->compute_driving_distance($department_store, $user, $coords);
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
        
    public function get_closest_merchants($user, $coords = null, $distance = 4)
    {
        $stores = array();

        $range = "";

        $longitude = -73.5815;
        $latitude = 45.4921;
        
        if($user != null)
        {
            $longitude = $user->profile->longitude;
            $latitude = $user->profile->latitude;
        }

        if($user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0)
        {
            $longitude = $coords["longitude"];
            $latitude = $coords["latitude"];
        }

        if($latitude != null && $longitude != null)
        {
            $range = "SQRT(POW(69.1 * (latitude - ".$latitude."), 2) + POW(69.1 * (".$longitude." - longitude) * COS(latitude / 57.3), 2))";
        }

        // join chain stores with chain
        $range_select = empty($range) ? "" : ", (".$range.") AS 'range'";
        $this->db->select(CHAIN_STORE_TABLE.'.* '.$range_select);
        $this->db->join(CHAIN_TABLE, CHAIN_TABLE.'.id = '.CHAIN_STORE_TABLE.'.chain_id');
        $this->db->where(array($range.' <=' => $distance));
        $this->db->order_by("range", "ASC");

        $result = $this->db->get(CHAIN_STORE_TABLE);

        foreach($result->result() as $row)
        {
            $department_store = $row;

            if($department_store != null && !isset($stores[$department_store->chain_id]))
            {
                if($this->get_store_product_count($department_store->chain_id) > 0)
                {
                    $stores[$department_store->chain_id] = $this->get(CHAIN_TABLE, $department_store->chain_id);
                    $stores[$department_store->chain_id]->department_store = $department_store;
                    
                    // If there is a company associated to this store, add it
                    if($stores[$department_store->chain_id]->company_id != null)
                    {
                        $stores[$department_store->chain_id]->company = $this->get(COMPANY_TABLE, $stores[$department_store->chain_id]->company_id);
                    }
                    else
                    {
                        $empty_company = new stdClass();
                        $empty_company->description = 'Pas de description';
                        $stores[$department_store->chain_id]->company = $empty_company;
                    }
                }
                
            }
        }
        
        return $stores;
    }
    
    private function get_store_product_count($merchant_id)
    {
        $this->db->where('period_from <= CURDATE() AND period_to >= CURDATE()', NULL, FALSE);
        $this->db->where(array('retailer_id' => $merchant_id));
        $result = $this->db->get(STORE_PRODUCT_TABLE);
        return $result->num_rows();
    }
	
    private function compute_driving_distance($department_store, $user, $coords)
    {
            $driving_distance = 0;

            $distance_time = array();

            if($user != null)
            {
                    $distance_time = $this->geo->GetDrivingDistance($department_store->latitude, $user->profile->latitude, $department_store->longitude, $user->profile->longitude);
            }
            if($user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0)
            {
                    $distance_time = $this->geo->GetDrivingDistance($department_store->latitude, $coords["latitude"], $department_store->longitude, $coords["longitude"]);
            }

            if(isset($distance_time["distance"]) != null)
            {
                    $dist = floatval(trim(str_replace("km","",$distance_time["distance"])));
                    $driving_distance = $dist;
            }

            return $driving_distance;
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
