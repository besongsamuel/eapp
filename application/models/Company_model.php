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
class Company_model extends CI_Model 
{
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
    }
    

    public function get_store_products($retailer_id, $query)
    {
        $result = array();
        
        $this->db->join(PRODUCT_TABLE, $this->store_product_product_join, "left outer");
        $this->db->limit($query->limit, $query->limit * ($query->page - 1));
        
        if(isset($query->filter) && !empty($query->filter))
        {
            $this->add_name_filter($query->filter);
        }
        
        $store_products = $this->get_where(STORE_PRODUCT_TABLE, STORE_PRODUCT_TABLE.".id", array("retailer_id" => $retailer_id));
        
        foreach ($store_products as $value) 
        {
            $result[$value->id] = $this->getStoreProduct($value->id, false, false, false);
            $result[$value->id]->store_product = $this->get(STORE_PRODUCT_TABLE, $value->id);
        }
        
        return $result;
    }
    
    public function get_store_products_count($retailer_id, $query)
    {
        
        $this->db->limit($query->limit, $query->limit * ($query->page - 1));
        
        return sizeof($this->get_where(STORE_PRODUCT_TABLE, "id", array("retailer_id" => $retailer_id), true));
    }
    
    public function add_store_product($store_product) 
    {
        $this->company_model->create(STORE_PRODUCT_TABLE, $store_product);
    }
    
    public function get_product_id($store_product) 
    {
        // Search for product
        $this->db->select(PRODUCT_TABLE.'.*');
        $this->add_product_name_filter($store_product["store_name"]);
        $products = $this->db->get(PRODUCT_TABLE)->result_array();
        
        // Get Match
        if(sizeof($products) > 0)
        {
            return $this->get_product_id_match($products, $store_product["store_name"]);
        }
        else
        {
            // No Product match, create new product. 
            $product = array();
            $product["name"] = $store_product["store_name"];
            $product["is_new"] = true;
            $product["subcategory_id"] = -1;
            $product["image"] = $store_product["image"];
            // Attempt to get compare_unit_id
            $unit_compare_unit = $this->get_where(UNIT_CONVERSION, "compareunit_id", array("unit_id" => $store_product["unit_id"]));
            if(isset($unit_compare_unit) && sizeof($unit_compare_unit) > 0)
            {
                $product["unit_id"] = $unit_compare_unit[0]->compareunit_id;
            }
            
            return $this->create(PRODUCT_TABLE, $product);
            
        }
    }
    
    private function get_product_id_match($products, $filter) 
    {
        $query = trim(mb_strtolower($filter, 'UTF-8'));
                
        foreach ($products as $product) 
        {
            $product_name = trim(mb_strtolower($product["name"], 'UTF-8'));
            
            if($product_name == $query)
            {
                return $product["id"];
            }
            
            $tags = explode(",", $product["tags"]);
            
            foreach ($tags as $tag_name) 
            {
                if(!empty($tag_name))
                {
                    if($product_name == $tag_name)
                    {
                        return $product["id"];
                    }
                }
            }
        }
        
        foreach ($products as $product) 
        {
            $product_name = trim(mb_strtolower($product["name"], 'UTF-8'));
            
            if(strpos($product_name, $query) === 0)
            {
                return $product["id"];
            }
            
            foreach ($tags as $tag_name) 
            {
                if(!empty($tag_name))
                {
                    if(strpos($tag_name, $query) === 0)
                    {
                        return $product["id"];
                    }
                }
            }
        }
        
        foreach ($products as $product) 
        {
            $product_name = trim(mb_strtolower(product["name"], 'UTF-8'));
            
            if(strpos($product_name, $query) > 0)
            {
                return $product["id"];
            }
            
            foreach ($tags as $tag_name) 
            {
                if(!empty($tag_name))
                {
                    if(strpos($tag_name, $query) > 0)
                    {
                        return $product["id"];
                    }
                }
            }
        }
        
        
    }
	
	
}
