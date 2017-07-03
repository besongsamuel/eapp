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
      
    public function getStoreProduct($id) 
    {
        // Get the store product object
        $store_product = $this->get(STORE_PRODUCT_TABLE, $id);

        if($store_product != null)
        {
            // Get associated product
            $store_product->product = $this->get(PRODUCT_TABLE, $store_product->product_id);
            // Get product store
            $store_product->retailer = $this->get(CHAIN_TABLE, $store_product->retailer_id);
            // Get subcategory
            if($store_product->product != null)
            {
                $store_product->subcategory = $this->get(SUB_CATEGORY_TABLE, $store_product->product->subcategory_id);
                // Get category
                if($store_product->subcategory != null)
                {
                    $store_product->category = $this->get(CATEGORY_TABLE, $store_product->subcategory->product_category_id);
                }
                // Get associated store products
                $array = array("product_id" => $store_product->product->id, STORE_PRODUCT_TABLE.".id !=" => $store_product->id);
                $get = sprintf("%s.*, %s.name, %s.image, %s.name as retailer_name", STORE_PRODUCT_TABLE, PRODUCT_TABLE, PRODUCT_TABLE, CHAIN_TABLE);
		$join = sprintf("%s.product_id = %s.id", STORE_PRODUCT_TABLE, PRODUCT_TABLE);
                $join2 = sprintf("%s.retailer_id = %s.id", STORE_PRODUCT_TABLE, CHAIN_TABLE);
                $this->db->select($get);
                $this->db->from(STORE_PRODUCT_TABLE);
                $this->db->join(PRODUCT_TABLE, $join);
                $this->db->join(CHAIN_TABLE, $join2);
                $this->db->where($array);
                $store_product->related_products = $this->db->get()->result();
            }
        }
        
        return $store_product;
    }
    

}
