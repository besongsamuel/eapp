<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

function cmp_store_product_asc_name($a, $b)
{
    return strcmp($a->product->name, $b->product->name);
}

function cmp_store_product_desc_name($a, $b)
{
    return strcmp($b->product->name, $a->product->name);
}

function cmp_store_product_asc_date($a, $b)
{
    if(strtotime($a->period_from) < strtotime($b->period_from))
    {
        return -1;
    }
    else if(strtotime($a->period_from) > strtotime($b->period_from))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}

function cmp_store_product_desc_date($a, $b)
{
    if(strtotime($a->period_from) < strtotime($b->period_from))
    {
        return 1;
    }
    else if(strtotime($a->period_from) > strtotime($b->period_from))
    {
        return -1;
    }
    else
    {
        return 0;
    }
}




/**
 * Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {

    /**
     * Class constructor
     *
     * @return	void
     */
    public function __construct()
    {
            log_message('info', 'Model Class Initialized');
    }

    // --------------------------------------------------------------------

    /**
     * __get magic
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param	string	$key
     */
    public function __get($key)
    {
            // Debugging note:
            //	If you're here because you're getting an error message
            //	saying 'Undefined Property: system/core/Model.php', it's
            //	most likely a typo in your model code.
            return get_instance()->$key;
    }
        
    public function eapp_get($table_name, $id)
    {
        $this->db->select("*");
        $this->db->from($table_name);
        $this->db->where('id', $id);
        $query = $this->db->get();
        if($query != null)
        {
            return $query->row();
        }
        else
        {
            return null;
        }
    }
        
    public function get($table_name, $id)
    {
        $this->db->select("*");
        $this->db->from($table_name);
        $this->db->where('id', $id);
        $query = $this->db->get();
        if($query != null)
        {
            return $query->row();
        }
        else
        {
            return null;
        }
    }
    
    public function get_specific($table_name, $data)
    {
        $this->db->select("*");
        $this->db->from($table_name);
        $this->db->where($data);
        $query = $this->db->get();
        if($query != null)
        {
            return $query->row();
        }
        else
        {
            return null;
        }
    }
    
    public function getStoreProduct($id, $includeRelatedProducts = true, $latestProduct = true) 
    {
        // Get the store product object
	if($latestProduct)
	{
		$array = array('period_from <=' => date("Y-m-d"), 'period_to >=' => date("Y-m-d"));
		$this->db->where($array);
	}
	
        $store_product = $this->get(STORE_PRODUCT_TABLE, $id);

        if($store_product != null)
        {
            // Get associated product
            $store_product->product = $this->get(PRODUCT_TABLE, $store_product->product_id);
            
            $product_image_path = ASSETS_DIR_PATH."img/products/".$store_product->product->image;
            if(!file_exists($product_image_path) || empty($store_product->product->image))
            {
                $store_product->product->image = "no_image_available.png";
            }
            
            // Get product store
            $store_product->retailer = $this->get(CHAIN_TABLE, $store_product->retailer_id);
            $store_image_path = ASSETS_DIR_PATH."img/stores/".$store_product->retailer->image;
            if(!file_exists($store_image_path) || empty($store_product->retailer->image))
            {
                $store_product->retailer->image = "no_image_available.png";
            }
            // Get product unit
            $store_product->unit = $this->get(UNITS_TABLE, $store_product->unit_id);
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
                if($includeRelatedProducts)
                {
                    $store_product->related_products = $this->get_related_products($store_product);
                }
                
            }
        }
        
        return $store_product;
    }
    
    public function get_all($table_name)
    {
        $result = array();
        
        $query =  $this->db->get($table_name);
        
        foreach ($query->result() as $value) 
        {
            $result[$value->id] = $value;
        }
        
        return $result;
    }
        
    /**
     * This method gets the other store products related to this store product
     * @param type $storeProduct
     */
    private function get_related_products($storeProduct, $latestProduct = true) 
    {
        $array = array("product_id" => $storeProduct->product_id, STORE_PRODUCT_TABLE.".id !=" => $storeProduct->id);
        $get = sprintf("%s.*, %s.name, %s.image, %s.name as retailer_name", STORE_PRODUCT_TABLE, PRODUCT_TABLE, PRODUCT_TABLE, CHAIN_TABLE);
        $join = sprintf("%s.product_id = %s.id", STORE_PRODUCT_TABLE, PRODUCT_TABLE);
        $join2 = sprintf("%s.retailer_id = %s.id", STORE_PRODUCT_TABLE, CHAIN_TABLE);
        $this->db->select($get);
        $this->db->from(STORE_PRODUCT_TABLE);
        $this->db->join(PRODUCT_TABLE, $join);
        $this->db->join(CHAIN_TABLE, $join2);
        $this->db->where($array);
        // Get the store product object
	if($latestProduct)
	{
            $where = array('period_from <=' => date("Y-m-d"), 'period_to >=' => date("Y-m-d"));
            $this->db->where($where);
	}
        return $this->db->get()->result();
    }
    
    public function get_all_limit($table_name, $limit, $offset)
    {
        $result = array();
        
        $this->db->limit($limit, $offset);
        
        $query =  $this->db->get($table_name);
        
        foreach ($query->result() as $value) 
        {
            $result[$value->id] = $value;
        }
        
        return $result;
    }
    
    /**
     * Create an EAPP object
     * @param type $table_name
     * @param type $data
     */
    public function create($table_name, $data, $is_new = false)
    {
        if(isset($data['id']) && !$is_new)
        {
            $query = $this->db->get_where($table_name, array('id' => $data['id']));
            $count = $query->num_rows(); 
            if($count === 0)
            {
                $data['date_created'] = date("Y-m-d H:i:s");
                $this->db->insert($table_name, $data);
                return $this->db->insert_id();
            }
            else
            {
                $this->db->where('id', $data['id']);
                $this->db->update($table_name, $data);
                return $data['id'];
            }
        }
        else
        {
            $this->db->insert($table_name, $data);
            return $this->db->insert_id();
        }
        
        
    }
    
    public function get_store_products_limit($limit, $offset, $latest_products = true, $filter = null, $order = null, $store_id = null)
    {
	$result = array();
	// Get the distinct product id's present 
	$this->db->limit($limit, $offset);
        // Get the store product object
        
	if($latest_products)
	{
            $result = $this->get_latest_products($user, $filter, $store_id);
	}
        else
        {
            // since we are not getting the latest products, return all the products
            $result = $this->get_all_products($user, $filter, $store_id);
        }
        
        // Perform sorting here if required
        if($order)
        {
            if(strpos($order, "-") !== false) // sort in descending order
            {
                $order = str_replace("-", "", $order);
                usort($result["products"], "cmp_store_product_desc_".$order);
                $result["sorting_method"] = "cmp_store_product_desc_".$order;
            }
            else
            {
                usort($result["products"], "cmp_store_product_asc_".$order);
                $result["sorting_method"] = "cmp_store_product_asc_".$order;
            }
        }
        
        return $result;
    }
    
    private function get_latest_products($filter = null, $store_id = null)
    {
        $result = array();
        $products = array();
        $latest_products_condition = array('period_from <=' => date("Y-m-d"), 'period_to >=' => date("Y-m-d"));
	$store_product_product_join = sprintf("%s.product_id = %s.id", STORE_PRODUCT_TABLE, PRODUCT_TABLE);
        
        if($filter != null)
        {
            $this->db->join(PRODUCT_TABLE, $store_product_product_join);
            $this->db->like("name", $filter);
        }
	    
	if($store_id != null)
	{
	    $this->db->where(array("retailer_id" => $store_id));
	}
	
	// Get products that satisfy conditions
        $product_ids = $this->get_distinct(STORE_PRODUCT_TABLE, "product_id", $latest_products_condition);
        
        // Determine the complete list number
        if($filter != null)
        {
            $join = sprintf("%s.product_id = %s.id", STORE_PRODUCT_TABLE, PRODUCT_TABLE);
            $this->db->join(PRODUCT_TABLE, $join);
            $this->db->like("name", $filter);
        }
	if($store_id != null)
	{
	    $this->db->where(array("retailer_id" => $store_id));
	}
        $non_limited_product_ids = $this->get_distinct(STORE_PRODUCT_TABLE, "product_id", $latest_products_condition);
        $result["count"] = sizeof($non_limited_product_ids);
        
        // Get cheapest store product for each product
	// close to the user    
	foreach($product_ids as $product_id)
	{
            // Add filter for search puroses
            $this->db->limit(1);
            if($filter != null)
            {
                $this->db->like("name", $filter);
            }
            $this->db->order_by("price", "ASC");
            $this->db->select(STORE_PRODUCT_TABLE.".id, price, product_id, name");
            $this->db->join(PRODUCT_TABLE, $store_product_product_join);
            $this->db->where("product_id", $product_id->product_id);
            $this->db->where($latest_products_condition);
            if($store_id != null)
            {
                $this->db->where(array("retailer_id" => $store_id));
            }
            
            $res = $this->db->get(STORE_PRODUCT_TABLE)->row();
            
            if($res)
            {
                $store_prodoct_id = $res->id;
                $store_product = $this->getStoreProduct($store_prodoct_id, false, true);
                if($store_product != null)
                {
                    $products[$store_product->id] = $store_product;
                }
                
                $this->db->reset_query();
            }
	}
        
        $result["products"] = $products;
        
        return $result;
    }
    
    private function get_all_products($filter = null, $store_id = null)
    {
        $result = array();
        $products = array();
        
        if($filter != null)
        {
            $join = sprintf("%s.product_id = %s.id", STORE_PRODUCT_TABLE, PRODUCT_TABLE);
            $this->db->join(PRODUCT_TABLE, $join);
            $this->db->like("name", $filter);
        }
	if($store_id != null)
	{
	    $this->db->where(array("retailer_id" => $store_id));
	}
        $product_ids = $this->get_distinct(STORE_PRODUCT_TABLE, STORE_PRODUCT_TABLE.".id", null);
        
        if($filter != null)
        {
            $join = sprintf("%s.product_id = %s.id", STORE_PRODUCT_TABLE, PRODUCT_TABLE);
            $this->db->join(PRODUCT_TABLE, $join);
            $this->db->like("name", $filter);
        }
	if($store_id != null)
	{
	    $this->db->where(array("retailer_id" => $store_id));
	}
        $non_limited_product_ids = $this->get_distinct(STORE_PRODUCT_TABLE, STORE_PRODUCT_TABLE.".id", null);
        $result["count"] = sizeof($non_limited_product_ids);
        
        // Get all products
        foreach($product_ids as $product_id)
	{
            // Add filter for search puroses
            $this->db->limit(1);
            if($filter != null)
            {
                $this->db->like("name", $filter);
            }
            if($store_id != null)
            {
                $this->db->where(array("retailer_id" => $store_id));
            }
            
            $this->db->order_by("price", "ASC");
            $this->db->select(STORE_PRODUCT_TABLE.".id, price, product_id, name");
            $join = sprintf("%s.product_id = %s.id", STORE_PRODUCT_TABLE, PRODUCT_TABLE);
            $this->db->join(PRODUCT_TABLE, $join);
            $this->db->where(STORE_PRODUCT_TABLE.".id", $product_id->id);
            $res = $this->db->get(STORE_PRODUCT_TABLE)->row();
            
            if($res)
            {
                $store_product = $this->getStoreProduct($product_id->id, false, false);
                $products[$store_product->id] = $store_product;
                $this->db->reset_query();
            }
	}
        
        $result["products"] = $products;
        
        return $result;
    }

    public function get_distinct($table_name, $columns, $where)
    {
        
        
    	$this->db->distinct();

	$this->db->select($columns);
	    
	if($where !== null)
	{
	    $this->db->where($where);
	}
                
	return $this->db->get($table_name)->result();
    }

}
