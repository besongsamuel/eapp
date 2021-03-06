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
    private $ACCOUNT_COLUMNS;
    
    private $PROFILE_COLUMNS;
    
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $this->load->library("geo");
        
        $this->ACCOUNT_COLUMNS = "id, email, subscription, is_new, is_active, code, phone, phone_verified, account_number, payment_token";
        
        $this->PROFILE_COLUMNS = "id, firstname, lastname, country, city, state, address, profile_value, postcode, longitude, latitude";
        
    }
    
    public function get_company_accounts($query) {
        
        
        $q = $this->db->query('SELECT * FROM '.COMPANY_TABLE);

        $count = $q->num_rows();
        
        $this->db->select("id, name, email, phone");
        
        $this->db->limit($query->limit, $query->limit * ($query->page - 1));
        
        return array("count" => $count, "accounts" => $this->get_all(COMPANY_TABLE));

    }
    
    public function get_user_accounts($query) {
        
        
        $q = $this->db->query('SELECT * FROM '.USER_ACCOUNT_TABLE);

        $count = $q->num_rows();
        
        $this->db->select("id, username, email, is_active");
        
        $this->db->limit($query->limit, $query->limit * ($query->page - 1));
        
        return array("count" => $count, "accounts" => $this->get_all(USER_ACCOUNT_TABLE));

    }
    
    public function clear_user_favorite_stores($user_id)
    {
        $this->db->where(array("user_account_id" => $user_id));
        $this->db->delete(USER_FAVORITE_STORE_TABLE);
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
    
    /**
     * Get User account grocery list information
     * @param type $account_id
     */
    public function get_user_grocery_lists($account_id) 
    {
        $result = array
        (
            "grocery_lists" => array(),
        );
        
        $product_lists = $this->get_where(USER_GROCERY_LIST_TABLE, '*', array("user_account_id" => $account_id));
		        
        $favorite_stores = $this->get_favorite_stores($account_id);
        
        if($product_lists != null)
        {
            foreach ($product_lists as $product_list) 
            {                                
                // Create a new object that represents the list
                $list_object = new stdClass();
                $list_object->id = $product_list->id;
                $list_object->name = $product_list->name;
                $list_object->products = array();
                $list_object->stores = array();
                
                $product_list = json_decode($product_list->grocery_list);
            
                if($product_list == NULL)
                {
                    $product_list = array();
                }

                $product_array = array();

                foreach ($product_list as $item) 
                {
                    $product = $this->get_product($item->id);

                    array_push($product_array, $item->id);

                    // Get all store products for the given product
                    $this->db->where($this->latest_products_condition, NULL, FALSE);
                    $product->store_products = $store_product = $this->get_where(STORE_PRODUCT_TABLE, "*", array("product_id" => $product->id));  

                    // This is a list of user favorite stores where this product is available
                    $product->user_stores = array();

                    $product->quantity = $item->quantity;

                    array_push($list_object->products, $product);

                }
                
                //For each favorite store, get the store_product and price of the product
                foreach($favorite_stores as $favorite_store)
                {
                    // product should be currently available 
                    
                    if(sizeof($product_array) > 0)
                    {
                        $this->db->where_in("product_id", $product_array);
                    }
                    else
                    {
                        $favorite_store->store_products = array();
                        array_push($list_object->stores, $favorite_store);
                        continue;
                    }
                    
                    $this->db->where($this->latest_products_condition, NULL, FALSE);
                    $this->db->where(array("retailer_id" => $favorite_store->id));
                    $favorite_store_store_products = array();

                    foreach ($this->db->get(STORE_PRODUCT_TABLE)->result() as $value) 
                    {
                        if(!isset($favorite_store_store_products[$value->product_id]))
                        {
                            $favorite_store_store_products[$value->product_id] = array();
                        }

                        array_push($favorite_store_store_products[$value->product_id], $value);
                    }

                    $favorite_store->store_products = $favorite_store_store_products;
                    array_push($list_object->stores, $favorite_store);
                }
                
                array_push($result["grocery_lists"], $list_object);
            }
        }
        
        return $result;
    }
    
    public function get_user($account_id) 
    {
        $user_account = $this->get(USER_ACCOUNT_TABLE, $account_id, $this->ACCOUNT_COLUMNS);
        
        if($user_account == null)
        {
            return null;
        }
        
        // It' a company
        if($user_account->subscription >= COMPANY_SUBSCRIPTION)
        {            
            // Get company
            $user_account->company = $this->get_specific(COMPANY_TABLE, array("user_account_id" => $account_id));
            
            $user_account->company->subscription = $this->get_specific(COMPANY_SUBSCRIPTIONS_TABLE, array("subscription" => $user_account->subscription));
            
            // Get the chain of the company
            $user_account->company->chain = $this->get_specific(CHAIN_TABLE, array("company_id" => $user_account->company->id));
            
            if($user_account->company->chain != null)
            {
                //  Get chain stores
                $user_account->company->chain->department_stores = $this->get_where(CHAIN_STORE_TABLE, "*", array("chain_id" => $user_account->company->chain->id), true);
            }
            
        }
        
        $user_account->profile = $this->get_specific(USER_PROFILE_TABLE, array("user_account_id" => $account_id), $this->PROFILE_COLUMNS);
        
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
            return $this->db->insert_id();
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
        
        $optimizations->currentWeek = $this->db->query("SELECT *, YEARWEEK(date_created) as weekCreated, YEARWEEK(NOW()) as currentWeek, DAY(date_created) as day FROM ".USER_OPTIMIZATION_TABLE." HAVING weekCreated = currentWeek AND user_account_id = ".$user->id." order by day asc")->result();
        $optimizations->currentYear = $this->db->query("SELECT *, YEAR(date_created) as yearCreated, YEAR(NOW()) as currentYear, MONTH(date_created) as month FROM ".USER_OPTIMIZATION_TABLE." HAVING yearCreated = currentYear AND user_account_id = ".$user->id." order by month asc")->result();
        $optimizations->currentMonth = $this->db->query("SELECT *, YEAR(date_created) as yearCreated, YEAR(NOW()) as currentYear, MONTH(date_created) as monthCreated, MONTH(NOW()) as currentMonth, WEEK(date_created) as week FROM ".USER_OPTIMIZATION_TABLE." HAVING yearCreated = currentYear AND monthCreated = currentMonth AND user_account_id = ".$user->id." order by week asc")->result();
        
        
        $optimizations->checkDay = $this->db->query("SELECT ROUND(AVG(price_optimization), 2) AS total,DATE_FORMAT(date_created, '%d-%m-%Y') AS date FROM ".USER_OPTIMIZATION_TABLE." WHERE price_optimization > 1 AND user_account_id = ".$user->id." AND YEARWEEK(date_created, 1) = YEARWEEK(NOW(), 1) GROUP BY DATE(date_created) ORDER BY DATE(date_created)")->result();
		
        $optimizations->checkWeek = $this->db->query("SELECT ROUND(AVG(price_optimization), 2) AS total, DATE_FORMAT(str_to_date(concat(yearweek(date_created), ' monday'), '%X%V %W'), '%d-%m-%Y')  AS date FROM ".USER_OPTIMIZATION_TABLE." WHERE price_optimization > 1 AND user_account_id = ".$user->id." AND MONTH(date_created) = MONTH(NOW()) GROUP BY Yearweek(date_created, 1) ORDER BY Yearweek(date_created, 1)")->result();
		
        $optimizations->checkMonth = $this->db->query("SELECT ROUND(AVG(price_optimization), 2) AS total,DATE_FORMAT(date_created, '%m-%Y') AS date FROM ".USER_OPTIMIZATION_TABLE." WHERE price_optimization > 1 AND user_account_id = ".$user->id." AND YEAR(date_created) = YEAR(NOW()) GROUP BY date ORDER BY MONTH(date_created)")->result();
        
        $this->db->where("user_account_id", $user->id);
        $this->db->order_by("date_created", "asc");
        $optimizations->overall = $this->db->get(USER_OPTIMIZATION_TABLE)->result();
        
        return $optimizations;
        
    }

}
