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
    
    public function getStoreProduct($id, $includeRelatedProducts = true) 
    {
        // Get the store product object
        $store_product = $this->get(STORE_PRODUCT_TABLE, $id);

        if($store_product != null)
        {
            // Get associated product
            $store_product->product = $this->get(PRODUCT_TABLE, $store_product->product_id);
            // Get product store
            $store_product->retailer = $this->get(CHAIN_TABLE, $store_product->retailer_id);
            // Get product brand
            $store_product->brand = $this->get(BRANDS_TABLE, $store_product->brand_id);
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
                    $store_product->related_products = $this->getRelatedProducts($store_product);
                }
                
            }
        }
        
        return $store_product;
    }
    
    /**
     * This method gets the other store products related to this store product
     * @param type $storeProduct
     */
    private function getRelatedProducts($store_product_id) 
    {
		// Get the store product
		$storeProduct = $this->get(STORE_PRODUCT_TABLE, $store_product_id);
        $array = array("product_id" => $storeProduct->product_id, STORE_PRODUCT_TABLE.".id !=" => $storeProduct->id);
        $get = sprintf("%s.*, %s.name, %s.image, %s.name as retailer_name", STORE_PRODUCT_TABLE, PRODUCT_TABLE, PRODUCT_TABLE, CHAIN_TABLE);
        $join = sprintf("%s.product_id = %s.id", STORE_PRODUCT_TABLE, PRODUCT_TABLE);
        $join2 = sprintf("%s.retailer_id = %s.id", STORE_PRODUCT_TABLE, CHAIN_TABLE);
        $this->db->select($get);
        $this->db->from(STORE_PRODUCT_TABLE);
        $this->db->join(PRODUCT_TABLE, $join);
        $this->db->join(CHAIN_TABLE, $join2);
        $this->db->where($array);
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
    
    public function get_store_products_limit($limit, $offset)
    {
	 $result = array();
	    
	// Get the distinct product id's present 
	$this->db->limit($limit, $offset);
	$product_ids = $this->get_distinc(STORE_PRODUCT_TABLE, "product_id", null);
	
	$this->db->reset_query();
	
	// Get cheapest store product for each product
	foreach($product_ids as $product_id)
	{
		$this->db->limit(1);
		$this->db->select("id");
        	$this->db->from(STORE_PRODUCT_TABLE);
        	$this->db->where("product_id", $product_id);
		$this->db->order_by("price", "ASC");
		$store_prodoct_id = $this->db->get()->id;
		$this->db->reset_query();
        	$store_product = getStoreProduct($store_prodoct_id, false);
		$result[$store_product->id] = $store_product;
	}
                
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

	$this->db->get($table_name)->result();
    }

}
