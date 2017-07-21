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
    
    public function update_user_store_table($user) 
    {
        // Get user stores
        $this->db->like("city", trim($user->profile->city));
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

}
