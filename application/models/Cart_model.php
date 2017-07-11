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
    
	public function get_closest_stores($user, $distance)
	{
		$stores = array();
		
		// Limit to the first 5 closest
		$this->db->limit(5);
		$this->db->where(array("user_id" => $user->id, "distance <=" => $distance));
		$this->db->order_by("distance", "ASC");
		$result = $this->db->get(USER_CHAIN_STORE_TABLE);
	
		foreach($result->result() as $row)
		{
                    $department_store = $this->get(CHAIN_STORE_TABLE, $row->chain_store_id);

                    if($department_store != null)
                    {
                        $stores[$department_store->chain_id] = $department_store;
                    }
		}
		
		return $stores;
	}
}
