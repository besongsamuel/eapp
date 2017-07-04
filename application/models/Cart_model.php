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
		
    public function getProducts($product_id)
    {
        $array = array("period_from <=" => date("Y-m-d"), "period_to >=" => date("Y-m-d"), "product_id" => $product_id);
        $this->db->select("*");
        $this->db->from(STORE_PRODUCT_TABLE);
        $this->db->where($array);
        return $this->db->get()->result();
    }
      
    public function getDepartmentStores($retailer_id)
    {
        $array = array("chain_id" => $retailer_id);
        $this->db->select("*");
        $this->db->from(CHAIN_STORE_TABLE);
        $this->db->where($array);
        return $this->db->get()->result();
    }
      
    public function findCloseDepartmentStore($department_stores, $user_address, $distance)
    {
          foreach($department_stores as $store)
          {
              $store_address = $store->postcode;

              $distance_time = $this->geo->distance_time_between($user_address, $store_address);

              if($distance_time["distance"] != null)
              {
                  $dist = intval(trim(str_replace("km","",$distance_time["distance"])));

                  if($dist < $distance)
                  {
                      return $store;
                  }
              }
          }

          return null;
    }
      
    
    
    
    

}
