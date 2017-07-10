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
            );
            
            if($distance_time["distance"] != null)
            {
                $dist = intval(trim(str_replace("km","",$distance_time["distance"])));
                $data["distance"] = $dist;
                
            }
            else
            {
                $data["distance"] = 1000;
            }
            
            $this->create(USER_CHAIN_STORE_TABLE, $data);
        }
    }
    
    public function get_user($id) 
    {
        $user_account = $this->get(USER_ACCOUNT_TABLE, $id);
        
        $user_account->profile = $this->get_specific(USER_PROFILE_TABLE, array("user_account_id" => $user_account->id));
        
        return $user_account;
    }

}
