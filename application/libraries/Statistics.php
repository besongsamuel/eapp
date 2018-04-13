<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Statistics
 *
 * @author besongsamuel
 */
class Statistics 
{
    //put your code here
    
    protected $CI;
    
    private $user;

    // We'll use a constructor, as you can't directly call a function
    // from a property definition.
    public function __construct($params)
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();

        $this->user = $params['user'];
        
        $this->CI->load->model('eapp_model');
            
    }
    
    private function get_products_from_query($query) 
    {
        $result = array();
        
        foreach ($query->result() as $row) 
        {
            $product = $this->CI->eapp_model->get_product($row->product_id);
            
            $product->count = $row->count;
            
            if(isset($row->retailer_id))
            {
                $product->retailer = $this->CI->eapp_model->get(CHAIN_TABLE, $row->retailer_id);
            }
            
            array_push($result, $product);
        }
        
        return $result;
    }
    
    private function get_brands_from_query($query) 
    {
        $result = array();
        
        foreach ($query->result() as $row) 
        {
            $brand = $this->CI->eapp_model->get(PRODUCT_BRAND_TABLE, $row->brand_id);
            $brand->count = $row->count;
            
            array_push($result, $brand);
        }
        
        return $result;
    }
    
    private function get_categories_from_query($query) 
    {
        $result = array();
        
        foreach ($query->result() as $row) 
        {
            $category = $this->CI->eapp_model->get(CATEGORY_TABLE, $row->product_category_id);
            $category->count = $row->count;
            
            array_push($result, $category);
        }
        
        return $result;
    }
   
    /**
     * Gets top products
     * @param type $period 0 for in the current month, 1 for in the current year
     * @param type $order the sort order
     * @param type $in_flyer 1 if we want products in flyer, 0 if we don't want products in flyers, -1 for all
     * @param type $action 1 for products added to cart, 0 for products viewed, 2 for products added to user's list, 3 appeared in search
     * @return type
     */
    public function get_top_products(
            $period = 0, 
            $order = 'desc', 
            $in_flyer = -1, 
            $bio = -1,
            $action = 0,
            $limit = 5) 
    {
        
        if($period == 0)
        {
            // Get products for current month
            $period_sql = " AND MONTH(date_created) = MONTH(CURRENT_DATE()) AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        else
        {
            // Get products for current year
            $period_sql = " AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        
        if($in_flyer == -1)
        {
            $in_flyer_sql = "";
        }
        else
        {
            $in_flyer_sql = "AND in_flyer = ".$in_flyer;
        }
        
         if($bio == -1)
        {
            $bio_sql = "";
        }
        else
        {
            $bio_sql = "AND bio = ".$bio;
        }
        
        $action_sql = "WHERE type = ".$action;
        
        if($action == -1)
        {
            $action_sql = "WHERE (type = 0 OR type = 1) ";
        }
        
        $limit_sql = " LIMIT ".$limit;
        
        if($limit == -1)
        {
            $limit_sql = "";
        }
        
        $query = $this->CI->db->query("SELECT COUNT(id) as count, product_id FROM "
                .PRODUCT_STATS." ".$action_sql." ".$in_flyer_sql." ".$period_sql." ".$bio_sql." GROUP BY product_id ORDER BY count ".$order.$limit_sql);
        
        
        return $this->get_products_from_query($query);
    }
    
    private function get_total_products(
            $period = 0, 
            $in_flyer = -1, 
            $bio = -1,
            $action = 0) 
    {
         if($period == 0)
        {
            // Get products for current month
            $period_sql = " AND MONTH(date_created) = MONTH(CURRENT_DATE()) AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        else
        {
            // Get products for current year
            $period_sql = " AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        
        if($in_flyer == -1)
        {
            $in_flyer_sql = "";
        }
        else
        {
            $in_flyer_sql = " AND in_flyer = ".$in_flyer;
        }
        
        if($bio == -1)
        {
            $bio_sql = "";
        }
        else
        {
            $bio_sql = "AND bio = ".$bio;
        }
        
        $action_sql = " WHERE type = ".$action;
        
        if($action == -1)
        {
            $action_sql = " WHERE (type = 0 OR type = 1) ";
        }
        
        $query = $this->CI->db->query("SELECT COUNT(id) as count FROM "
                .PRODUCT_STATS." ".$action_sql." ".$in_flyer_sql." ".$period_sql." ".$bio_sql);
        
        return $query->row()->count;
    }
    
    public function get_percentage_bio($period = 0, $action = 0) 
    {
        $non_bio_count = $this->get_total_products($period, -1, 0, $action);
        
        $bio_count = $this->get_total_products($period, -1, 1, $action);
        
        return ((float)$bio_count / (float)($bio_count + $non_bio_count)) * 100;
    }
     
    /**
     * This method gets the products that 
     * @param type $order
     * @return type
     */
    public function get_top_product_retailers($order = 'desc', $period = 0, $limit = 5) 
    {
        
        if($period == 0)
        {
            // Get products for current month
            $period_sql = " WHERE MONTH(date_created) = MONTH(CURRENT_DATE()) AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        else
        {
            // Get products for current year
            $period_sql = " WHERE YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        
        $query = $this->CI->db->query("SELECT count(id) as count, retailer_id, product_id FROM ".PRODUCT_STATS." ".$period_sql." GROUP BY retailer_id, product_id order by count ".$order." LIMIT ".$limit);
        
        return $this->get_products_from_query($query);
    }
    
    public function get_top_countries($order = 'desc', $period = 0, $action = 0, $limit = 5) 
    {
        $action_sql = "WHERE type = ".$action;
        
        if($action == -1)
        {
            $action_sql = "WHERE (type = 0 OR type = 1) ";
        }
        
        if($period == 0)
        {
            // Get products for current month
            $period_sql = " AND MONTH(date_created) = MONTH(CURRENT_DATE()) AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        else
        {
            // Get products for current year
            $period_sql = " AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        
        $query = $this->CI->db->query("SELECT count(id) as count, country, product_id FROM ".PRODUCT_STATS." ".$action_sql." ".$period_sql." GROUP BY country, product_id order by count ".$order." LIMIT ".$limit);
        
        return $query->result();
    }
    
    public function get_top_states($order = 'desc', $period = 0, $action = 0, $limit = 5) 
    {
        $action_sql = "WHERE type = ".$action;
        
        if($action == -1)
        {
            $action_sql = "WHERE (type = 0 OR type = 1) ";
        }
        
        if($period == 0)
        {
            // Get products for current month
            $period_sql = " AND MONTH(date_created) = MONTH(CURRENT_DATE()) AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        else
        {
            // Get products for current year
            $period_sql = " AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        
        $query = $this->CI->db->query("SELECT count(id) as count, state FROM ".PRODUCT_STATS." ".$action_sql." ".$period_sql." AND state != '' GROUP BY state order by count ".$order." LIMIT ".$limit);
        
        return $query->result();
    }
    
    public function get_top_product_brands($order = 'desc', $period = 0, $limit = 5) 
    {
        if($period == 0)
        {
            // Get products for current month
            $period_sql = " AND MONTH(date_created) = MONTH(CURRENT_DATE()) AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        else
        {
            // Get products for current year
            $period_sql = " AND YEAR(date_created) = YEAR(CURRENT_DATE())";
        }
        
        $query = $this->CI->db->query("SELECT count(id) as count, brand_id FROM ".PRODUCT_STATS." WHERE (brand_id <> -1 AND brand_id <> 0 AND brand_id IS NOT NULL) ".$period_sql." GROUP BY brand_id order by count ".$order." LIMIT ".$limit);
        
        return $this->get_brands_from_query($query);
        
    }
    
    public function get_top_product_categories($order = 'desc', $period = 0, $limit = 5) 
    {
        if($period == 0)
        {
            // Get products for current month
            $period_sql = " AND MONTH(".PRODUCT_STATS.".date_created) = MONTH(CURRENT_DATE()) AND YEAR(".PRODUCT_STATS.".date_created) = YEAR(CURRENT_DATE())";
        }
        else
        {
            // Get products for current year
            $period_sql = " AND YEAR(".PRODUCT_STATS.".date_created) = YEAR(CURRENT_DATE())";
        }
        
        $products_join_clause = " INNER JOIN ".PRODUCT_TABLE." ON (".PRODUCT_STATS.".product_id = ".PRODUCT_TABLE.".id) ";
        $subcategories_join_clause = " LEFT OUTER JOIN ".SUB_CATEGORY_TABLE." ON (".SUB_CATEGORY_TABLE.".id = ".PRODUCT_TABLE.".subcategory_id) ";
        
        $query_string = "SELECT count(".PRODUCT_STATS.".id) as count, ".SUB_CATEGORY_TABLE.".product_category_id FROM ".PRODUCT_STATS." ".$products_join_clause." ".$subcategories_join_clause." ".$period_sql." GROUP BY product_category_id order by count ".$order." LIMIT ".$limit;
        
        $query = $this->CI->db->query($query_string);
        
        return $this->get_categories_from_query($query);
    }
    
    
}
