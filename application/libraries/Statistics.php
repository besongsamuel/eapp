<?php

defined('BASEPATH') OR exit('No direct script access allowed');


function cmp_count_desc($a, $b)
{
    if($a['count'] < $b['count'])
    {
        return 1;
    }
    else if($a['count'] > $b['count'])
    {
        return -1;
    }
    else
    {
        return 0;
    }
}

function cmp_count_asc($a, $b)
{
    if($a['count'] > $b['count'])
    {
        return 1;
    }
    else if($a['count'] < $b['count'])
    {
        return -1;
    }
    else
    {
        return 0;
    }
}

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
    
    private $product_statistics;
    
    private $product_optimization_statistics;
    
    private $retailer_visits;
    
    private $retailer_statistics;

    // We'll use a constructor, as you can't directly call a function
    // from a property definition.
    public function __construct($params)
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();

        $this->user = $params['user'];
        
        $this->CI->load->model('eapp_model');
        
        $this->CI->load->library("session");
        
        $dates_sql = "MONTH(date_created) AS month, YEAR(date_created) AS year, YEAR(CURRENT_DATE()) AS current_year, MONTH(CURRENT_DATE()) AS current_month";
        
        $this->product_statistics = $query = $this->CI->db->query("SELECT *, ".$dates_sql." FROM ".PRODUCT_STATS)->result_array();
        
        $this->product_optimization_statistics = $query = $this->CI->db->query("SELECT *, ".$dates_sql." FROM ".PRODUCT_OPTIMIZATION_STATS)->result_array();;
        
        $this->retailer_visits = $query = $this->CI->db->query("SELECT *, ".$dates_sql." FROM ".CHAIN_VISITS)->result_array();;
        
        $this->retailer_statistics = $query = $this->CI->db->query("SELECT *, ".$dates_sql." FROM ".CHAIN_STATS)->result_array();;
            
    }
    
    private function get_products_from_query($stats) 
    {
        $result = array();
        
        foreach ($stats as $row) 
        {
            $product = $this->CI->eapp_model->get_product($row['product_id']);
            
            $product->count = $row['count'];
            
            if(isset($row['retailer_id']))
            {
                $product->retailer = $this->CI->eapp_model->get(CHAIN_TABLE, $row['retailer_id']);
            }
            
            array_push($result, $product);
        }
        
        return $result;
    }
    
    private function get_retailers_from_query($stats) 
    {
        $result = array();
        
        foreach ($stats as $row) 
        {
            if(isset($row['retailer_id']))
            {
                $retailer = $this->CI->eapp_model->get(CHAIN_TABLE, $row['retailer_id']);
                
                if($retailer)
                {
                    $retailer->count = $row['count'];
                
                    array_push($result, $retailer);
                }
                
            }
        }
        
        return $result;
    }
    
    private function get_brands_from_query($stats) 
    {
        $result = array();
        
        foreach ($stats as $row) 
        {
            $brand = $this->CI->eapp_model->get(PRODUCT_BRAND_TABLE, $row['brand_id']);
            $brand->count = $row['count'];
            
            array_push($result, $brand);
        }
        
        return $result;
    }
    
    private function get_categories_from_query($stats) 
    {
        $result = array();
        
        foreach ($stats as $row) 
        {
            $category = $this->CI->eapp_model->get(CATEGORY_TABLE, $row['product_category_id']);
            $category->count = $row['count'];
            
            array_push($result, $category);
        }
        
        return $result;
    }
    
    private function filter_by_date($stats, $period) 
    {
        $result = array();
        
        foreach ($stats as $stat) 
        {
            if($period == 0)
            {
                if($stat['month'] == $stat['current_month'] && $stat['year'] == $stat['current_year'])
                {
                    array_push($result, $stat);
                }
            }
            
            if($period == 1)
            {
                if($stat['year'] == $stat['current_year'])
                {
                    array_push($result, $stat);
                }
            }
        }
        
        return $result;
    }
    
    private function filter_by($stats, $value, $column_name) 
    {
        if($value == -1)
        {
            return $stats;
        }
        
        $result = array();
        
        foreach ($stats as $stat) 
        {
            if($stat[$column_name] == $value)
            {
                array_push($result, $stat);
            }
        }
        
        return $result;
    }
    
    private function get_composite_column($columns) 
    {
        $result = "";
        
        $first = true;
        
        foreach ($columns as $value) 
        {
            if($first)
            {
                $result.= $value;
            
                $first = false;
            }
            else
            {
                $result.= "_".$value;
            }
            
        }
    }
    
    private function group_by($stats, $columns) 
    {
        $result = array();
        
        foreach ($stats as $stat) 
        {
            $column_name = $this->get_composite_column($columns);
                        
            if(isset($result[$column_name]))
            {
                $result[$column_name]["count"] += 1;
            }
            else
            {
                $result[$column_name] = $stat;
                $result[$column_name]["count"] = 1;
            }
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
        
        $result = array();
        
        $result = $this->filter_by_date($this->product_statistics, $period);
        
        $result = $this->filter_by($result, $in_flyer, "in_flyer");
        
        $result = $this->filter_by($result, $bio, "bio");
        
        $result = $this->filter_by($result, $action, "type");
        
        $result = $this->group_by($result, array("product_id"));
        
        usort($result, "cmp_count_".$order);
        
        if($limit != -1)
        {
            $result = array_slice($result, 0, $limit);
        }
        
        return $this->get_products_from_query($result);
    }
    
    public function get_top_recurring_products(
            $period = 0, 
            $order = 'desc', 
            $action = 0,
            $limit = 5) 
    {
        
        $result = array();
        
        $result = $this->filter_by_date($this->retailer_statistics, $period);
        
        $result = $this->filter_by($result, $action, "type");
        
        $result = $this->group_by($result, array("product_id"));
        
        usort($result, "cmp_count_".$order);
                
        if($limit != -1)
        {
            $result = array_slice($result, 0, $limit);
        }
        
        return $this->get_products_from_query($result);
    }
    
    private function get_total_products(
            $period = 0, 
            $in_flyer = -1, 
            $bio = -1,
            $action = 0) 
    {
        $result = array();
        
        $result = $this->filter_by_date($this->product_statistics, $period);
        
        $result = $this->filter_by($result, $in_flyer, "in_flyer");
        
        $result = $this->filter_by($result, $bio, "bio");
        
        $result = $this->filter_by($result, $action, "type");
        
        return count($result);

    }
    
    public function get_percentage_bio($period = 0, $action = 0) 
    {
        $non_bio_count = $this->get_total_products($period, -1, 0, $action);
        
        $bio_count = $this->get_total_products($period, -1, 1, $action);
        
        return round(((float)$bio_count / (float)($bio_count + $non_bio_count)) * 100, 2);
    }
     
    /**
     * This method gets the products that 
     * @param type $order
     * @return type
     */
    public function get_top_product_retailers($order = 'desc', $period = 0, $limit = 5) 
    {
        
        $result = array();
        
        $result = $this->filter_by_date($this->retailer_statistics, $period);
        
        $result = $this->group_by($result, array("product_id", "retailer_id"));
        
        usort($result, "cmp_count_".$order);
                
        if($limit != -1)
        {
            $result = array_slice($result, 0, $limit);
        }
        
        return $this->get_products_from_query($result);
        
    }
    
    public function get_top_countries($order = 'desc', $period = 0, $action = 0, $limit = 5) 
    {
        $result = array();
        
        $result = $this->filter_by_date($this->product_statistics, $period);
        
        $result = $this->filter_by($result, $action, "type");
        
        $result = $this->group_by($result, array("product_id", "country"));
        
        usort($result, "cmp_count_".$order);
                
        if($limit != -1)
        {
            $result = array_slice($result, 0, $limit);
        }
        
        foreach ($result as $key => $value) 
        {
            if($key == "country")
            {
                $result["name"] = $value;
            }
        }
        
        return $result;
    }
    
    public function get_top_states($order = 'desc', $period = 0, $action = 0, $limit = 5) 
    {
        $result = array();
        
        $result = $this->filter_by_date($this->product_statistics, $period);
        
        $result = $this->filter_by($result, $action, "type");
        
        $result = $this->group_by($result, array("product_id", "state"));
        
        usort($result, "cmp_count_".$order);
                
        if($limit != -1)
        {
            $result = array_slice($result, 0, $limit);
        }
        
        foreach ($result as $key => $value) 
        {
            if($key == "state")
            {
                $result["name"] = $value;
            }
        }
        
        return $result;
    }
    
    public function get_top_product_brands($order = 'desc', $period = 0, $limit = 5) 
    {
        $result = array();
        
        $result = $this->filter_by_date($this->product_statistics, $period);
        
        $result = $this->group_by($result, array("brand_id"));
        
        usort($result, "cmp_count_".$order);
        
        if($limit != -1)
        {
            $result = array_slice($result, 0, $limit);
        }
        
        $final_result = array();
        
        foreach ($result as $value) 
        {
            if($value["brand_id"] != 0 && $value["brand_id"] != -1 && isset($value["brand_id"]))
            {
                array_push($value, $final_result);
            }
        }
        
        return $this->get_brands_from_query($final_result);
        
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
        
        return $this->get_categories_from_query($query->result_array());
    }
    
    public function get_top_visited_chains($order = 'desc', $period = 0, $limit = 5) 
    {
        
        $result = array();
        
        $result = $this->filter_by_date($this->retailer_visits, $period);
        
        $result = $this->group_by($result, array("retailer_id"));
        
        usort($result, "cmp_count_".$order);
                
        if($limit != -1)
        {
            $result = array_slice($result, 0, $limit);
        }
        
        return $this->get_retailers_from_query($result);
        
    }
    
    public function get_top_optimized_chains($order = 'desc', $period = 0, $limit = 5) 
    {
        $result = array();
        
        $result = $this->filter_by_date($this->product_optimization_statistics, $period);
        
        $result = $this->group_by($result, array("retailer_id"));
        
        usort($result, "cmp_count_".$order);
                
        if($limit != -1)
        {
            $result = array_slice($result, 0, $limit);
        }
        
        return $this->get_retailers_from_query($result);
    }
    
    public function get_store_visitors_info() 
    {
        $result = new stdClass();
        
        // Get all visits
        $total_visits =  count($this->retailer_visits);
                
        $my_store_visits = $this->CI->db->query("SELECT * FROM ".CHAIN_VISITS." WHERE retailer_id = ".$this->user->company->chain->id)->result();
        
        if(count($my_store_visits) > 0)
        {
            $result->visits = round((float)(count($my_store_visits) / $total_visits) * 100, 2);
        
            $distance_total = 0;

            foreach ($my_store_visits as $value) 
            {
                $distance_total += $value->distance;
            }

            $result->avg_distance = round((float)($distance_total / count($my_store_visits)), 2);
            
            return $result;
            
        }
    }
    
    public function get_store_userlist_info() 
    {
        $result = new stdClass();
        
        // Get all visits
        $total_users_with_favorite_stores =  count($this->CI->db->query("SELECT DISTINCT user_account_id FROM ".USER_FAVORITE_STORE_TABLE)->result());
        
        $users_with_my_store_as_favorite = $this->CI->db->query("SELECT DISTINCT user_account_id FROM ".USER_FAVORITE_STORE_TABLE." WHERE retailer_id = ".$this->user->company->chain->id)->result();
        
        if(count($users_with_my_store_as_favorite) > 0)
        {
            $result->users = round((float)(count($users_with_my_store_as_favorite) / $total_users_with_favorite_stores) * 100, 2);
            
            return $result;
        }
        
        
    }
    
    public function get_product_visitors_info($action) 
    {
        $result = new stdClass();
        
        // Get all visits
        $all_product_stats =  count($this->CI->db->query("SELECT count(*) FROM ".PRODUCT_STATS." WHERE type = ".$action)->result());
        
        $store_product_stats = $this->CI->db->query("SELECT * FROM ".PRODUCT_STATS." WHERE retailer_id = ".$this->user->company->chain->id." AND type = ".$action)->result();
        
        if(count($store_product_stats) > 0)
        {
            $result->visits = round((float)(count($store_product_stats) / $all_product_stats) * 100, 2);
        
            $distance_total = 0;

            foreach ($store_product_stats as $value) 
            {
                $distance_total += $value->distance;
            }

            $result->avg_distance = round((float)($distance_total / count($store_product_stats)), 2);
            
            return $result;
            
        }
        
        
    }
    
    public function most_visited_store($order = 'desc', $period = 0, $limit = 5) 
    {
        $mvs = new stdClass();
        
        $retailers =  $this->get_top_visited_chains($order, $period, $limit);
        
        if(sizeof($retailers) > 0)
        {
            $most_visited_retailer = $retailers[0];
            
            $month_year = "MONTH(date_created) as month, YEAR(date_created) as year";
                        
            $query = $this->CI->db->query("SELECT count(id) as count, ".$month_year." FROM ".CHAIN_STATS." WHERE retailer_id = ".$most_visited_retailer->id." GROUP BY year, month");
            
            $sum = 0;
            
            $result = $query->result();
            
            foreach ($result as $value) 
            {
                $sum += $value->count;
            }
            
            if(count($result) == 0)
            {
                return;
            }
            
            $mvs->avg =  $sum / count($result);
            
            $mvs->retailer = $most_visited_retailer;
            
            return $mvs;

        }
    }
        
    public function record_product_optimization_stat($store_product) 
    {
        $today = date("Y-m-d");
        
        $user_id = -1;
        
        if($this->CI->session->userdata('userId'))
        {
            $user_id = $this->CI->session->userdata('userId');
        }
        
        $search_data = array(
                        'retailer_id' => $store_product->retailer_id,
                        'product_id' => $store_product->product_id,
                        'period_from' => $store_product->period_from);
        
        if($user_id != -1)
        {
            $search_data['user_account_id'] = $user_id;
        }
        else
        {
            $search_data['session_id'] = session_id();
        }
        
        $session_statistics = $this->CI->eapp_model->get_specific(PRODUCT_OPTIMIZATION_STATS, $search_data);
        
        if(!$session_statistics)
        {
            $search_data['json_store_product'] = json_encode($store_product);
            $search_data['date_created'] = $today;
            $search_data['user_account_id'] = $user_id;
            $search_data['session_id'] = session_id();
            
            $this->CI->eapp_model->create(PRODUCT_OPTIMIZATION_STATS, $search_data);
        }
    }
}
