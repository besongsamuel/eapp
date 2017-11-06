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
class Account_model extends CI_Model 
{
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->load->library("geo");
    }
    
    public function clear_user_favorite_stores($user_id)
    {
        $this->db->where(array("user_account_id" => $user_id));
        $this->db->delete(USER_FAVORITE_STORE_TABLE);
    }
    
    public function get_user_favorite_stores($user_id)
    {
        $this->db->where(array("user_account_id" => $user_id));
        $this->db->select("retailer_id");
        $query = $this->db->get(USER_FAVORITE_STORE_TABLE);
        $result = array();
        foreach ($query->result() as $value) 
        {
            $result[$value->retailer_id] = $value->retailer_id;
        }
        
        return $result;
    }


    public function update_user_store_table($user) 
    {
        // Get user favorite stores in city
        $this->db->like("city", trim($user->profile->city));
        $join = sprintf("%s.chain_id = %s.retailer_id", CHAIN_STORE_TABLE, USER_FAVORITE_STORE_TABLE);
        $this->db->join(USER_FAVORITE_STORE_TABLE, $join);
        $this->db->where(array(USER_FAVORITE_STORE_TABLE.".user_account_id" => $user->id));
        $department_stores = $this->get_all(CHAIN_STORE_TABLE);
        
        $this->db->where(array("user_id" => $user->profile->id));
        $this->db->delete(USER_CHAIN_STORE_TABLE);
        
        foreach ($department_stores as $department_store) 
        {
            $distance_time = $this->geo->distance_time_between($user->profile, $department_store);
            
            $data = array
            (
                "user_id" => $user->id,
                "chain_store_id" => $department_store->id,
				"distance" => 1000
            );
            
            if($distance_time["distance"] != null)
            {
                $dist = intval(trim(str_replace("km","",$distance_time["distance"])));
                $data["distance"] = $dist;
                
            }
            
            $this->create(USER_CHAIN_STORE_TABLE, $data);
        }
    }
    
    public function get_user($account_id) 
    {
        $user_account = $this->get(USER_ACCOUNT_TABLE, $account_id);
        
        $user_account->profile = $this->get_specific(USER_PROFILE_TABLE, array("user_account_id" => $user_account->id));
        
        $user_account->optimizations = $this->get_user_optimizations($user_account);
        
        $product_list = $this->get_specific(USER_GROCERY_LIST_TABLE, array("user_account_id" => $user_account->id));
		        
        $favorite_stores = $this->get_favorite_stores($account_id);
        
        if($product_list == null)
        {
            $user_account->grocery_list = array();
        }
        else
        {
            $user_account->grocery_list = array();
            
            $product_list = json_decode($product_list->grocery_list);
            
            if($product_list == NULL)
            {
                $product_list = array();
            }
            
            foreach ($product_list as $item) 
            {
                $product = $this->get_product($item->id);
		$product->store_products = array();    
		
		            // This is a list of user favorite stores where this product is available
                $product->store = array();

                // For each favorite store, get the store_product and price of the product
                foreach($favorite_stores as $favorite_store)
                {
                    // product should be currently available 
                    $this->db->where('period_from <= CURDATE() AND period_to >= CURDATE()', NULL, FALSE);
                    $store_product = $this->get_specific(STORE_PRODUCT_TABLE, array("retailer_id" => $favorite_store->id, "product_id" => $product->id));

                    if($store_product != null)
                    {
			array_push($product->store_products, $store_product);
                        $product->store[$favorite_store->id] = $favorite_store;
                        $product->store[$favorite_store->id]->store_product = $store_product;
                    }
                }
				
                $product->quantity = $item->quantity;
                array_push($user_account->grocery_list, $product);
            }
        }
        
        return $user_account;
    }
	
	/*
     * get rows from the users table
     */
    function getRows($params = array()){
        $this->db->select('*');
        $this->db->from(USER_ACCOUNT_TABLE);
        
        //fetch data by conditions
        if(array_key_exists("conditions",$params)){
            foreach ($params['conditions'] as $key => $value) {
                $this->db->where($key,$value);
            }
        }
        
        if(array_key_exists("id",$params)){
            $this->db->where('id',$params['id']);
            $query = $this->db->get();
            $result = $query->row_array();
        }else{
            //set start and limit
            if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit'],$params['start']);
            }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit']);
            }
            $query = $this->db->get();
            if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
                $result = $query->num_rows();
            }elseif(array_key_exists("returnType",$params) && $params['returnType'] == 'single'){
                $result = ($query->num_rows() > 0)?$query->row_array():FALSE;
            }else{
                $result = ($query->num_rows() > 0)?$query->result_array():FALSE;
            }
        }

        //return fetched data
        return $result;
    }
    
    /*
     * Insert user information
     */
    public function insert($data = array()) {
        //add created and modified data if not included
        if(!array_key_exists("created", $data)){
            $data['created'] = date("Y-m-d H:i:s");
        }
        if(!array_key_exists("modified", $data)){
            $data['modified'] = date("Y-m-d H:i:s");
        }
        
        //insert user data to users table
        $insert = $this->db->insert($this->userTbl, $data);
        
        //return the status
        if($insert){
            return $this->db->insert_id();;
        }else{
            return false;
        }
    }
    
    public function get_user_optimizations($user) 
    {
        if($user == null)
        {
            return null;
        }
        
        // get current week optimizations
        $optimizations = new stdClass();
        
        $optimizations->currentWeek = $this->db->query("SELECT *, YEARWEEK(date_created) as weekCreated, YEARWEEK(NOW()) as currentWeek FROM ".USER_OPTIMIZATION_TABLE." HAVING weekCreated = currentWeek AND user_account_id = ".$user->id)->result();
        $optimizations->currentYear = $this->db->query("SELECT *, YEAR(date_created) as yearCreated, YEAR(NOW()) as currentYear FROM ".USER_OPTIMIZATION_TABLE." HAVING yearCreated = currentYear AND user_account_id = ".$user->id)->result();
        $optimizations->currentMonth = $this->db->query("SELECT *, YEAR(date_created) as yearCreated, YEAR(NOW()) as currentYear, MONTH(date_created) as monthCreated, MONTH(NOW()) as currentMonth FROM ".USER_OPTIMIZATION_TABLE." HAVING yearCreated = currentYear AND monthCreated = currentMonth AND user_account_id = ".$user->id)->result();
        
        $this->db->where("user_account_id", $user->id);
        $optimizations->overall = $this->db->get(USER_OPTIMIZATION_TABLE)->result();
        
        return $optimizations;
        
    }

}
