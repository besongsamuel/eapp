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
        
        $this->db->join(PRODUCT_TABLE, $this->store_product_product_join, "left outer");
        
        if(isset($query->filter) && !empty($query->filter))
        {
            $this->add_name_filter($query->filter);
        }
        
        return sizeof($this->get_where(STORE_PRODUCT_TABLE, STORE_PRODUCT_TABLE.".id", array("retailer_id" => $retailer_id)));
        
    }
    
    public function get_company_products_count($retailer_id)
    {        
        return sizeof($this->get_where(STORE_PRODUCT_TABLE, "id", array("retailer_id" => $retailer_id), true));
    }
    
    public function add_store_product($store_product) 
    {
        $this->company_model->create(STORE_PRODUCT_TABLE, $store_product);
    }
    
    public function get_product_id($store_product, $products) 
    {
        // Search for product
        $match = $this->get_product_id_match($products, $store_product["store_name"]);
        
        if($match !== false)
        {
            return $match;
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
    
    private function get_product_id_match($products, $store_product_name) 
    {
        $store_product_name = trim(mb_strtolower($store_product_name, 'UTF-8'));
        
        $possible_matches = array();
                
        foreach ($products as $product) 
        {
            $product_name = trim(mb_strtolower($product->name, 'UTF-8'));
            
            if($product_name == $store_product_name)
            {
                return $product->id;
            }
            else
            {
                if(strpos($store_product_name, $product_name) !== false)
                {
                    $match = new stdClass();
                    $match->id =  $product->id;
                    $match->name = $product_name;
                    $match->match_offset =  abs(strlen($store_product_name) - strlen($product_name));
                    array_push($possible_matches, $match);
                }
            }
            
            // Get the product tags
            $tags = explode(",", $product->tags);
            
            foreach ($tags as $tag_name) 
            {
                if(isset($tag_name) && trim($tag_name) != '' && $tag_name != null)
                {
                    if($store_product_name == $tag_name)
                    {
                        return $product->id;
                    }
                    else
                    {
                        if (strpos($store_product_name, $tag_name)  !== false) 
                        {
                            $match = new stdClass();
                            $match->id = $product->id;
                            $match->name = $tag_name;
                            $match->match_offset = abs(strlen($store_product_name) - strlen($tag_name));
                            array_push($possible_matches, $match);
                        }
                    }
                }
            }
        }
        
        if (sizeof($possible_matches) == 0) {
            return false;
        }

        $match_product = null;

        foreach ($possible_matches as $match) {

            if ($match_product == null) {
                $match_product = $match;
            } else {
                if ($match_product->match_offset > $match->match_offset) {
                    $match_product = $match;
                }
            }
        }

        return $match_product->id;
    }
	
	
}
