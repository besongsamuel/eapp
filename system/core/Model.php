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

    public $latest_products_condition;
    public $store_product_product_join;
    public $store_product_subcategory_join;
    private $filter_settings;
    private $units;
    private $product_unit_conversions;
    public  $user;


    /**
     * Class constructor
     *
     * @return	void
     */
    public function __construct()
    {
        log_message('info', 'Model Class Initialized');
        $this->filter_settings = $this->get_all("otiprix_filter_settings");
        $this->latest_products_condition = 'period_from <= CURDATE() AND period_to >= CURDATE()';
        $this->store_product_product_join = sprintf("%s.product_id = %s.id", STORE_PRODUCT_TABLE, PRODUCT_TABLE);
        $this->store_product_subcategory_join = sprintf("%s.subcategory_id = %s.id", PRODUCT_TABLE, SUB_CATEGORY_TABLE);
        
        // Get all unit conversion table
        $this->units = $this->get_all(UNIT_CONVERSION);
        
        $this->product_unit_conversions = $this->get_all(PRODUCT_UNIT_CONVERSION);
        
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
        
    public function get($table_name, $id, $columns = "*")
    {
        $this->db->select($columns);
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
    
    public function get_specific($table_name, $data,  $columns = "*")
    {
        $this->db->select($columns);
        $this->db->where($data);
        $query = $this->db->get($table_name);
        if($query != null)
        {
            return $query->row();
        }
        else
        {
            return null;
        }
    }
    
    private function compare_unit_price($store_product)
    {      
        
        if($this->units == null)
        {
            $this->units = $this->get_all(UNIT_CONVERSION);
        }
        
        if($this->product_unit_conversions == null)
        {
            $this->product_unit_conversions = $this->get_all(PRODUCT_UNIT_CONVERSION);
        }
        
        $result = array("compare_unit_price" => 0, "equivalent" => 0);
                
        $format = 1;
                
        // get format
        $pieces = explode("x", $store_product->format);
        
        foreach ($pieces as $value) 
        {
            $format *= (int)$value;
        }
        
        if($format == 0)
        {
            $format = 1;
        }
        
        $sp_compare_unit = $this->get_specific(COMPAREUNITS_TABLE, array("id" => $store_product->compareunit_id));
        
        $sp_unit = $this->get_specific(UNITS_TABLE, array("id" => $store_product->unit_id));
        
        foreach ($this->units as $unit) 
        {
            if($store_product->unit_id == $unit->unit_id 
                    && $store_product->compareunit_id == $unit->compareunit_id)
            {
                // Convert format quantity
                $compare_format = $format * $unit->equivalent;
                
                $result["equivalent"] = (float)$unit->equivalent;
                $result["compare_unit_price"] = (float)$store_product->price / (float)$compare_format;
                
                return $result;
            }
        }
        
        foreach ($this->product_unit_conversions as $product_unit_conversion) 
        {
            if($store_product->product_id == $product_unit_conversion->product_id)
            {
                if($store_product->unit_id == $product_unit_conversion->unit_id 
                        && $store_product->compareunit_id == $product_unit_conversion->compareunit_id)
                {
                    // Convert format quantity
                    $compare_format = $format * $product_unit_conversion->equivalent;

                    // Get the unit price
                    $result["equivalent"] = $product_unit_conversion->equivalent;
                    $result["compare_unit_price"] = (float)$store_product->price / (float)$compare_format;
                
                    return $result;              

                }
            }
        }
        
        if(isset($sp_unit) && isset($sp_compare_unit))
        {
            if($sp_unit->name == $sp_compare_unit->name)
            {
                $result["equivalent"] = 1;
                $result["compare_unit_price"] = (float)$store_product->price / (float)$format;

                return $result;
            }
        }
        
        return $result;
    }
    
    private function get_price_from_compare_unit_price($store_product, $most_expensive_store_product) 
    {
        // If there is no unit price set
        // and the compare units of the product and its expensive counterpart
        // are different, we do nothing
        if($most_expensive_store_product->compare_unit_price == 0 
                || $most_expensive_store_product->compareunit_id !=  $store_product->compareunit_id)
        {
            return 0;
        }
        
        
        if($this->units == null)
        {
            $this->units = $this->get_all(UNIT_CONVERSION);
        }
        
        if($this->product_unit_conversions == null)
        {
            $this->product_unit_conversions = $this->get_all(PRODUCT_UNIT_CONVERSION);
        }
        
        $format = 1;
                
        // get format
        $pieces = explode("x", $store_product->format);
        
        foreach ($pieces as $value) 
        {
            $format *= (int)$value;
        }
        
        if($format == 0)
        {
            $format = 1;
        }
        
        foreach ($this->units as $unit) 
        {
            if($store_product->unit_id == $unit->unit_id 
                    && $store_product->compareunit_id == $unit->compareunit_id)
            {
                
                $price =  ($most_expensive_store_product->compare_unit_price  * $format) * $unit->equivalent;
                
                return number_format((float)$price, 2, '.', '');
            }
        }
        
        return 0;
    }

    public function getStoreProduct($id, $includeRelatedProducts = true, $latestProduct = true, $minified = false) 
    {
        // Get the store product object
	if($latestProduct)
	{
            $this->db->where('period_from <= CURDATE() AND period_to >= CURDATE()', NULL, FALSE);
	}
	
        $store_product_columns = "*";
        $chain_columns = "*";
        $units_columns = "*";
        $brand_columns = "*";
        if($minified)
        {
            $store_product_columns = "id, product_id, retailer_id, brand_id, unit_id, compareunit_id, country, state, organic, format, size, quantity, price, unit_price, period_from, period_to, image";
            $chain_columns = "id, name, image";
            $units_columns = "id, name";
            $brand_columns = "id, name, image";
        }
        
        $store_product = $this->get(STORE_PRODUCT_TABLE, $id, $store_product_columns);

        if($store_product != null)
        {
            // Get associated product
            $store_product->product = $this->get_product($store_product->product_id, $minified);
            
            // If the name of the store product is not set, use that of the product
            if(!isset($store_product->name) || empty($store_product->name))
            {
                $store_product->name = $store_product->product->name;
            }
            
            // Get product store
            $store_product->retailer = $this->get_retailer($store_product->retailer_id, $chain_columns);
            // Get product unit
            $store_product->unit = $this->get(UNITS_TABLE, $store_product->unit_id, $units_columns);
            $compare = $this->compare_unit_price($store_product);
            $store_product->compare_unit_price = $compare['compare_unit_price'];
            $store_product->equivalent = $compare['equivalent'];
            // Get subcategory
            if($store_product->product != null && $includeRelatedProducts)
            {
                $store_product->similar_products = $this->get_related_products($store_product);
                
                $most_expensive = $store_product;
                
                foreach ($store_product->similar_products as $value) 
                {
                    if((float)$value->compare_unit_price > $most_expensive->compare_unit_price)
                    {
                        $most_expensive = $value;
                        
                    }
                }
                
                if($store_product->id != $most_expensive->id)
                {
                     $store_product->regular_price = $this->get_price_from_compare_unit_price($store_product, $most_expensive);
                }
                else
                {
                     $store_product->regular_price = 0;
                }
                
            }
            
            // Get the brand from the database
            $store_product->brand = $this->get(PRODUCT_BRAND_TABLE, $store_product->brand_id, $brand_columns);
            
            // If the brand contains a web image and the product has no image
            if($store_product->brand != null 
                    && strpos($store_product->brand->image, 'http') !== FALSE
                    && (empty($store_product->product->image) || !isset($store_product->product->image)))
            {
                $store_product->product->image = $store_product->brand->image;
            }
            // If the brand contains a local image and the product has no image
            else if($store_product->brand != null 
                    && file_exists(ASSETS_DIR_PATH."/assets/img/products/".$store_product->brand->image)
                    && (empty($store_product->product->image) || !isset($store_product->product->image)))
            {
                $store_product->product->image = base_url("/assets/img/products/").$store_product->brand->image;
            }
                        
            // If the store product has a web image, use it instead
            if(strpos($store_product->image, 'http') !== FALSE)
            {
                $store_product->product->image = $store_product->image;
            }
            
            // If the store product has a local image, use it
            else if(file_exists(ASSETS_DIR_PATH.'img/products/'.$store_product->image) 
                    && !empty($store_product->image) 
                    && isset($store_product->image))
            {
                $store_product->product->image = base_url("/assets/img/products/").$store_product->image;
            }
            
            // It has it's own store name. Override the product name with this
            if(!empty($store_product->store_name))
            {
                $store_product->product->name = $store_product->store_name;
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
    
    public function get_all_where_in($table_name, $column_name, $values, $assoc = false) 
    {
        
        if(empty($values))
        {
            return array();
        }
        
        $this->db->where_in($column_name, $values);
        
        $query =  $this->db->get($table_name);
        
        $result_set = $assoc ? $query->result_array() : $query->result();
        
        if($assoc)
        {
            foreach ($result_set as $key => $value) 
            {
                 // Handle special case for country/state
                if($key == "state" && empty($result_set[$key]))
                {
                    if(isset($result_set["country"]))
                    {
                        $result_set["state"] = $result_set["country"];
                    }
                }

            }
        }
        
        return $result_set;
    }
	
    public function get_products($query)
    {
        $this->add_product_name_filter($query->filter);
        
        $this->db->limit($query->limit, $query->limit * ($query->page - 1));
        
        if($query->is_new)
        {
            $this->db->where("is_new", 1);
        }
        
        if($query->no_tags)
        {
            $this->db->where("tags", "");
        }
        
        $products = $this->get_all(PRODUCT_TABLE);
        
        foreach ($products as $key => $value) 
        {
            $store_image_path = ASSETS_DIR_PATH."img/products/".$value->image;
            
            if(!file_exists($store_image_path) || empty($value->image))
            {
                $products[$key]->image = "no_image_available.png";
            }
            
            if($products[$key]->tags == "")
            {
                $products[$key]->tags_array = array(); 
            }
            else
            {
                $products[$key]->tags_array = array_unique(explode(",", $products[$key]->tags)); 
            }
            
            
            
            $products[$key]->image = base_url('/assets/img/products/').$products[$key]->image;
        }

        return $products;
    }
    
    public function get_products_count($filter)
    {
        $this->add_product_name_filter($filter);
        
        $query = $this->db->query('SELECT * FROM '.PRODUCT_TABLE);

        return $query->num_rows();
    }
    
    public function get_chains()
    {
        
        $chains = $this->get_all(CHAIN_TABLE);
        
        foreach ($chains as $key => $value) 
        {
            $store_image_path = ASSETS_DIR_PATH."img/stores/".$value->image;
            
            if(!file_exists($store_image_path) || empty($value->image))
            {
                $chains[$key]->image = "no_image_available.png";
            }
            
            $chains[$key]->image = base_url('/assets/img/stores/').$chains[$key]->image;
        }
        
        return $chains;
    }
        
    /**
     * This method gets the other store products related to this store product
     * @param type $storeProduct
     */
    private function get_related_products($storeProduct, $latestProduct = true) 
    {
        $related_products = array();
        
        $array = array("product_id" => $storeProduct->product_id, STORE_PRODUCT_TABLE.".id !=" => $storeProduct->id);
        $get = sprintf("%s.id", STORE_PRODUCT_TABLE);
        $this->db->select($get);
        $this->db->from(STORE_PRODUCT_TABLE);
        $this->db->where($array);
        // Get the store product object
        if($latestProduct)
        {
            $this->db->where('period_from <= CURDATE() AND period_to >= CURDATE()', NULL, FALSE);
        }
        $ids = $this->db->get()->result();
        
        foreach ($ids as $value) 
        {
            $store_product = $this->getStoreProduct($value->id, false);
            
            // Check that this related store product is comparable. 
            // i.e. The unit of the store product can be converted to
            // the compare unit
            if((float)$store_product->compare_unit_price > 0)
            {
                array_push($related_products, $store_product);
            }
            
        }
        
        return $related_products;
        
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
    
    public function get_product($product_id, $get_store_products = true, $minified = false) 
    {
        $product_columns = "*";
        $subcategory_columns = "*";
        $category_columns = "*";
        
        if($minified)
        {
            $product_columns = "id, name, subcategory_id, image";
            $subcategory_columns = "id, name, product_category_id";
            $category_columns = "id, name";
        }
        
        $value = $this->get(PRODUCT_TABLE, $product_id, $product_columns);
        
        if(!isset($value))
        {
            $value = new stdClass();
            
            $value->image = "no_image_available.png";
            $value->subcategory_id = -1;
            $value->unit_id = -1;
            $value->name = "-";
            
        }
                        
        $store_image_path = ASSETS_DIR_PATH."img/products/".$value->image;
        
        if(strpos($value->image, 'http') === FALSE)
        {
            // File doesn't exist or image value is empty, set the empty image value
            if(!file_exists($store_image_path) || empty($value->image))
            {
                $value->image = "no_image_available.png";
            }
            
            $value->image = base_url("/assets/img/products/").$value->image;
        }
        
        $value->subcategory = $this->get(SUB_CATEGORY_TABLE, $value->subcategory_id, $subcategory_columns);
        
        // Get category
        if($value->subcategory != null)
        {
            $value->category = $this->get(CATEGORY_TABLE, $value->subcategory->product_category_id, $category_columns);
        }
        
        // Get the product's compare product unit
        $value->unit = $this->get(UNITS_TABLE, $value->unit_id, "*");
        
        if($get_store_products)
        {
            $value->store_products = $this->get_flyer_products($product_id, $minified);
        }	 

        return $value;
    }
    
    public function get_retailer($retailer_id, $chain_columns) 
    {
        $retailer = $this->get(CHAIN_TABLE, $retailer_id, $chain_columns);
            
        if($retailer == null)
        {
            return $retailer;
        }
        
        $store_image_path = base_url('/assets/img/stores/').$retailer->image;

        // Retailer Image does not contain http, set its path
        if(strpos($retailer->image, 'http') === FALSE)
        {
            if(!file_exists($store_image_path) || empty($retailer->image))
            {
                $retailer->image = base_url('/assets/img/stores/no_image_available.png');
            }

            $retailer->image = $store_image_path;
        }
        
        return $retailer;
        
    }
	
    private function get_flyer_products($product_id, $minified)
    {
        $store_product_columns = "*";
        
        if($minified)
        {
            $store_product_columns = "id, product_id, retailer_id, brand_id, unit_id, country, state, organic, format, size, quantity, price";
        }
        $this->db->where('period_from <= CURDATE() AND period_to >= CURDATE()', NULL, FALSE);
        $this->db->select($store_product_columns.", ".PRODUCT_BRAND_TABLE.".name as brandName, ".PRODUCT_BRAND_TABLE.".id as brand_id");
        $this->db->where(array(STORE_PRODUCT_TABLE.".product_id" => $product_id, "in_flyer" => 1));
        $this->db->join(PRODUCT_BRAND_TABLE, PRODUCT_BRAND_TABLE.".id = ".STORE_PRODUCT_TABLE.".brand_id", "left outer");
        $result = $this->db->get(STORE_PRODUCT_TABLE);

        return $result->result();
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
    
    public function update($table_name, $data, $condition)
    {
        $data['date_modified'] = date("Y-m-d H:i:s");
        $this->db->update($table_name, $data, $condition);
    }
    
    public function get_store_products_limit(
            $limit, 
            $offset, 
            $latest_products = true, 
            $filter = null, 
            $order = null, 
            $store_id = null, 
            $category_id = null, 
            $settingsFilter = null, 
            $viewAll = true, 
            $my_location = null, 
            $distance = 100,
            $popular_products = false)
    {
        
        if((int)$category_id == -1)
        {
            $popular_products = true;
        }
        
        if($distance == 0)
        {
            $distance = 100;
        }
        
        $result = array();
        
        $close_stores = $this->get_close_stores($my_location, $distance);
        
        // Get the distinct product id's present 
        $this->db->limit($limit, $offset);
        
        // Perform sorting here if required
        $this->apply_order_by($order);
		
        // Get the store product object
        if($latest_products)
        {
            $result = $this->get_latest_products($filter, $store_id, $category_id, $settingsFilter, $viewAll, $close_stores);
        }
        else
        {
            // since we are not getting the latest products, return all the products
            $result = $this->get_all_products($filter, $store_id, $category_id, $settingsFilter);
        }
        return $result;
    }
    
    private function get_latest_products(
            $filter = null, 
            $store_id = null, 
            $category_id = null, 
            $settingsFilter = null, 
            $viewAll = false, 
            $close_stores = null)
    {
        $result = array();
        
        $products = array();
        
        // Get products that satisfy conditions
        $product_ids = $this->get_distinct_latest_products($filter, $store_id, $category_id, $settingsFilter, $viewAll, $close_stores);
		
        $non_limited_product_ids = $this->get_distinct_latest_products($filter, $store_id, $category_id, $settingsFilter, $viewAll, $close_stores);
        $result["count"] = sizeof($non_limited_product_ids);
        
        // Get cheapest store product for each product
        // close to the user    
        foreach($product_ids as $product_id)
        {
            if($viewAll)
            {
                $store_product = $this->getStoreProduct($product_id->id, false, true);
                $store_product->quantity = 1;
                $products[$store_product->id] = $store_product;
                $this->db->reset_query();
            }
            else
            {
                $res = $this->get_best_latest_store_product($product_id->product_id, $filter, $store_id, $category_id, $settingsFilter);
                
                if($res)
                {                    
                    $store_product = $this->getStoreProduct($res->id, true, true);
                    $store_product->quantity = 1;
                    $products[$store_product->id] = $store_product;
                    $this->db->reset_query();
                }
            }
        }
        
        $products = $this->order_product_results($products, $filter);
        
        $result["products"] = $products;
        
        return $result;
    }
    
    private function order_product_results($products_array, $filter)
    {
        $perfect_match = array();
        
        $matches = array();
        
        $close_matches = array();
        
        $related = array();
        
        if(empty($filter))
        {
            return $products_array;
        }
        
        $new_filter = trim(mb_strtolower($filter, 'UTF-8'));
        
        foreach ($products_array as $store_product) 
        {
            if(!empty($store_product->store_name))
            {
                $store_name = trim(mb_strtolower($store_product->store_name, 'UTF-8'));
                
                if(strpos($store_name, $new_filter) === 0)
                {
                    if(strlen($store_name) == strlen($new_filter))
                    {
                        array_push($perfect_match, $store_product);
                    }
                    else
                    {
                        array_push($matches, $store_product);
                    }
                    
                    continue;
                }
                else if(strpos($store_name, $new_filter) > 0)
                {
                    array_push($close_matches, $store_product);
                    continue;
                }
                
            }
            
            if(!empty($store_product->product->name))
            {
                $product_name = trim(mb_strtolower($store_product->product->name, 'UTF-8'));
                
                if(strpos($product_name, $new_filter) === 0)
                {
                    if(strlen($product_name) == strlen($new_filter))
                    {
                        array_push($perfect_match, $store_product);
                    }
                    else
                    {
                        array_push($matches, $store_product);
                    }
                    
                    continue;
                }
                else if(strpos($product_name, $new_filter) > 0)
                {
                    array_push($close_matches, $store_product);
                    continue;
                }
            }
            
            $value_match = false;

            $other_names = explode(",", $store_product->product->tags);

            foreach ($other_names as $other_name) 
            {
                if(!empty($other_name))
                {
                    $tag_name = trim(mb_strtolower($other_name, 'UTF-8'));
                    
                    if(strpos($tag_name, $new_filter) === 0)
                    {
                        $value_match = true;
                        
                        if(strlen($tag_name) == strlen($new_filter))
                        {
                            array_push($perfect_match, $store_product);
                        }
                        else
                        {
                            array_push($matches, $store_product);
                        }
                        
                        break;
                    }
                    else if(strpos($tag_name, $new_filter) > 0)
                    {
                        $value_match = true;
                        array_push($close_matches, $store_product);
                        break;
                    }
                    
                }
            }

            if($value_match)
            {
                continue;
            }
            
            array_push($related, $store_product);
        }
        
        
        return array_merge($perfect_match, $matches, $close_matches, $related);
    }
        
    private function apply_order_by($order)
    {
        if($order)
        {
            if(strpos($order, "-") !== false) // sort in descending order
            {
                $order = str_replace("-", "", $order);
                
                if($order == 'date_modified')
                {
                    $order = STORE_PRODUCT_TABLE.".".$order;
                }
                
                if($order == 'name')
                {
                    $order = PRODUCT_TABLE.".".$order;
                }
                
                $this->db->order_by($order, "DESC");
            }
            else
            {
                if($order == 'date_modified')
                {
                    $order = STORE_PRODUCT_TABLE.".".$order;
                }
                
                if($order == 'name')
                {
                    $order = PRODUCT_TABLE.".".$order;
                }
                
                $this->db->order_by($order, "ASC");
            }
        }
    }
	
    protected function add_name_filter($filter) 
    {
        if($filter != null && !empty($filter))
        {
            $this->db->group_start();
            
            $this->db->like(PRODUCT_TABLE.".name", $filter, 'after');
            $this->db->or_like(STORE_PRODUCT_TABLE.".store_name", $filter, 'after');
            
            $this->db->or_like(PRODUCT_TABLE.".name", ' '.$filter.' ');
            $this->db->or_like(STORE_PRODUCT_TABLE.".store_name", ' '.$filter.' ');
            
            $this->db->or_like(PRODUCT_TABLE.".name", ' '.$filter);
            $this->db->or_like(STORE_PRODUCT_TABLE.".store_name", ' '.$filter);
            
            $this->db->group_end();
     
        }
    }
    
    public function add_product_name_filter($filter) 
    {
        if($filter != null)
        {
            $this->db->group_start();
            
            $this->db->like(PRODUCT_TABLE.".name", $filter, 'after');
            
            $this->db->or_like(PRODUCT_TABLE.".name", ' '.$filter.' ');
            
            $this->db->or_like(PRODUCT_TABLE.".name", ' '.$filter);
            
            $this->db->or_like(PRODUCT_TABLE.".tags", $filter);
            
            $this->db->group_end();
            
        }
    }
    
    private function add_specific_store_filter($store_id) 
    {
        if($store_id != null)
        {
            $this->db->where(array("retailer_id" => $store_id));
        }
    }
        
    private function add_specific_category_filter($category_id) 
    {
        if($category_id != null)
        {
            if((int)$category_id > -1)
            {
                $this->db->where(array(SUB_CATEGORY_TABLE.".product_category_id" => $category_id));
            }
            
            if((int)$category_id == -1)
            {
                $this->db->where(array("is_popular" => 1));
            }
        }
    }
    
    private function add_settings_filter($settingsFilter) 
    {
        if($settingsFilter != null)
        {
            
            foreach ($this->filter_settings as $setting) 
            {
                if(isset($settingsFilter[$setting->name]) && !empty($settingsFilter[$setting->name]))
                {                    
                    $this->db->where_in($setting->column_table.'.'.$setting->column_name, explode(",", $settingsFilter[$setting->name]));
                }
            }
        }
    }
    
    public function get_distinct_latest_store_product_property($property_name, $filter, $store_id, $category_id, $settingsFilter = null, $assoc = false, $close_stores = null) 
    {
        $this->db->join(PRODUCT_TABLE, $this->store_product_product_join);
        $this->db->join(SUB_CATEGORY_TABLE, $this->store_product_subcategory_join, "left outer");
        
        $this->add_distance_condition($close_stores);
        
        $this->add_name_filter($filter);
        
        $this->add_specific_store_filter($store_id);
        
        $this->add_specific_category_filter($category_id);
        
        $this->add_settings_filter($settingsFilter);
        
        $product_ids = $this->get_distinct(STORE_PRODUCT_TABLE, $property_name, $this->latest_products_condition, $assoc);
       
        return $product_ids;
    }
    
    public function get_store_product_property($property_name, $product_ids, $results_filter, $close_stores, $assoc = false) 
    {
        $this->db->join(PRODUCT_TABLE, $this->store_product_product_join);
        $this->db->join(SUB_CATEGORY_TABLE, $this->store_product_subcategory_join, "left outer");
        
        $this->add_distance_condition($close_stores);
        
        $this->add_settings_filter($results_filter);
        
        if(sizeof($product_ids) > 0)
        {
            $this->db->where_in(STORE_PRODUCT_TABLE.".product_id", $product_ids);
        }
        
        $property_array = $this->get_distinct(STORE_PRODUCT_TABLE, $property_name, $this->latest_products_condition, $assoc);
       
        return $property_array;
    }
    
    public function get_close_stores($my_location, $distance)
    {
        if($my_location != null && $my_location["latitude"] != null && $my_location["longitude"] != null)
        {
            $result = array();
            
            $this->db->distinct();
            $this->db->select(CHAIN_STORE_TABLE.".chain_id");
            $range = "SQRT(POW(69.1 * (latitude - ".$my_location["latitude"]."), 2) + POW(69.1 * (".$my_location["longitude"]." - longitude) * COS(latitude / 57.3), 2))";
            $this->db->where($range." <=".$distance);
            
            foreach ($this->db->get(CHAIN_STORE_TABLE)->result() as $value) 
            {
                array_push($result, $value->chain_id);
            } 
            
            if(sizeof($result) == 0)
            {
                array_push($result, -1);
            }
            
            return $result;
        }
        else
        {
            return null;
        }
    }
    
    private function add_distance_condition($close_stores) 
    {
        if($close_stores)
        {
            $this->db->where_in(STORE_PRODUCT_TABLE.'.retailer_id', $close_stores);
        }
    }
    
    public function get_distinct_latest_products($filter, $store_id, $category_id, $settingsFilter = null, $viewAll = true, $close_stores = null)
    {
        $product_id_column = "product_id";
        
        if($viewAll)
        {
            $product_id_column = STORE_PRODUCT_TABLE.".id";
        }
        
        return $this->get_distinct_latest_store_product_property($product_id_column, $filter, $store_id, $category_id, $settingsFilter, false, $close_stores);
    }
	
    private function get_best_latest_store_product($product_id, $filter, $store_id, $category_id, $settingsFilter = null)
    {
        $this->db->join(PRODUCT_TABLE, $this->store_product_product_join);
        $this->db->join(SUB_CATEGORY_TABLE, $this->store_product_subcategory_join, "left outer");
        $this->add_name_filter($filter);
        $this->db->order_by("price", "ASC");
        $this->db->select(STORE_PRODUCT_TABLE.".*, price, product_id, ".PRODUCT_TABLE.".name");
        $this->db->where("product_id", $product_id);
        $this->db->where($this->latest_products_condition, NULL, FALSE);
        
        $this->add_specific_store_filter($store_id);
        
        $this->add_specific_category_filter($category_id);
        
        $this->add_settings_filter($settingsFilter);
        
        $result_array = $this->db->get(STORE_PRODUCT_TABLE)->result();
        
        $best_store_product = null;
        
        foreach ($result_array as $store_product) 
        {
            $compare = $this->compare_unit_price($store_product);
            
            if($best_store_product == null)
            {
                $best_store_product = $store_product;
                $best_store_product->compare_unit_price = $compare['compare_unit_price'];
            }
            else
            {
                if($compare['compare_unit_price'] < $best_store_product->compare_unit_price)
                {
                    $best_store_product = $store_product;
                    $best_store_product->compare_unit_price = $compare['compare_unit_price'];
                }
            }
        }
        
        if(isset($result_array) && count($result_array) > 1 && $best_store_product != null)
        {
            // add to product optimization table
            $this->statistics->record_product_optimization_stat($best_store_product);
        }
        
        return $best_store_product;
    }
    
    private function get_all_products($filter = null, $store_id = null, $category_id = null, $settingsFilter = null)
    {
        $result = array();
        $products = array();
        // Executed with the limit clause
        $product_ids = $this->get_distinct_products($filter, $store_id, $category_id, $settingsFilter);

        // This is executed without the limit clause
        $non_limited_product_ids = $this->get_distinct_products($filter, $store_id, $category_id, $settingsFilter);
        $result["count"] = sizeof($non_limited_product_ids);
        
        // Get all products
        foreach($product_ids as $product_id)
        {
            $res = $this->get_best_store_product($product_id->id, $filter, $store_id, $category_id);
            
            if($res)
            {
                $store_product = $this->getStoreProduct($product_id->id, false, false);
                $store_product->quantity = 1;
                $products[$store_product->id] = $store_product;
                $this->db->reset_query();
            }
        }
        
        $result["products"] = $products;
        
        return $result;
    }
	
    private function get_distinct_products($filter, $store_id, $category_id, $settingsFilter)
    {
        $this->db->join(PRODUCT_TABLE, $this->store_product_product_join);
        $this->db->join(SUB_CATEGORY_TABLE, $this->store_product_subcategory_join, "left outer");	
        
        $this->add_name_filter($filter);
        
        $this->add_specific_store_filter($store_id);
        
        $this->add_specific_category_filter($category_id);
        
        $this->add_settings_filter($settingsFilter);
        
        return $this->get_distinct(STORE_PRODUCT_TABLE, STORE_PRODUCT_TABLE.".id", null);
    }
	
    private function get_best_store_product($product_id, $filter, $store_id, $category_id, $settingsFilter = null)
    {
        $this->db->join(PRODUCT_TABLE, $this->store_product_product_join);
        $this->db->join(SUB_CATEGORY_TABLE, $this->store_product_subcategory_join, "left outer");	
        
        $this->add_name_filter($filter);
        
        $this->add_specific_store_filter($store_id);
        
        $this->add_specific_category_filter($category_id);
        
        $this->add_settings_filter($settingsFilter);
        
        $this->db->order_by("price", "ASC");
        $this->db->select(STORE_PRODUCT_TABLE.".*, price, product_id, ".PRODUCT_TABLE.".name");
        $this->db->where(STORE_PRODUCT_TABLE.".id", $product_id);
        
        // search for the cheapest
        
        $result_array = $this->db->get(STORE_PRODUCT_TABLE)->result();
        
        $best_store_product = null;
        
        foreach ($result_array as $store_product) 
        {
            $compare = $this->compare_unit_price($store_product);
            
            if($best_store_product == null)
            {
                $best_store_product = $store_product;
                $best_store_product->compare_unit_price = $compare['compare_unit_price'];
            }
            else
            {
                if($compare['compare_unit_price'] < $best_store_product->compare_unit_price)
                {
                    $best_store_product = $store_product;
                    $best_store_product->compare_unit_price = $compare['compare_unit_price'];
                }
            }
        }
        
        if(isset($result_array) && count($result_array) > 1 && $best_store_product != null)
        {
            // add to product optimization table
            $this->statistics->record_product_optimization_stat($best_store_product);
        }

        return $best_store_product;
    }

    public function get_distinct($table_name, $columns, $where, $assoc = false)
    {
    	$this->db->distinct();

        $this->db->select($columns);

        if($where != null)
        {
            $this->db->where($where, NULL, FALSE);
        }
        
        $query = $this->db->get_compiled_select($table_name);
                
        if($assoc)
        {
            return $this->db->query($query)->result_array();
        }
        else
        {
            return $this->db->query($query)->result();
        }
        
    }
    
    public function get_where($table_name, $columns, $where, $as_array = false, $escape = false)
    {
        $this->db->select($columns);

        if($where != null)
        {
            $this->db->where($where, NULL, $escape);
        }
        
        $select_sql = $this->db->get_compiled_select($table_name);
        
        if($as_array)
        {
            return $this->db->query($select_sql)->result_array();
        }
        else
        {
            return $this->db->query($select_sql)->result();
        }
    }
    
    
    
    /*
    * Method to get for a given user the different stores
    */
    public function get_favorite_stores($user_id)
    {
        $this->db->select(CHAIN_TABLE.".*");
        $this->db->join(USER_FAVORITE_STORE_TABLE, USER_FAVORITE_STORE_TABLE.'.retailer_id = '.CHAIN_TABLE.'.id', 'right outer');
        $this->db->where(array("user_account_id" => $user_id));
        $result = $this->db->get(CHAIN_TABLE)->result();
        
        foreach ($result as $key => $value) 
        {
            
            if(isset($value->id))
            {
                if(strpos($result[$key]->image, 'http://') == FALSE)
                {
                     $result[$key]->image = base_url('/assets/img/stores/').$result[$key]->image;
                }
            }
            else
            {
                $newItem = new stdClass();
                $newItem->id = -1;
                $result[$key] = $newItem;
            }
        }
        
        while (sizeof($result) < 4)
        {
            $newItem = new stdClass();
            $newItem->id = -1;
            array_push($result, $newItem);
        }
        
        return $result;
    }
    
    public function delete($table_name, $data)
    {
        $this->db->where($data, NULL, FALSE);
        $this->db->delete($table_name);
    }
	
    /*
    * This is called when an item is clicked on the front end
    */
    public function hit($table_name, $id)
    {
        $this->db->set('hits', 'hits + 1', FALSE);
        $this->db->where("id", $id);
        $this->db->update($table_name);
    }
    
    public function get_mostviewed_categories() 
    {
        $this->db->order_by("hits", "DESC");
        $this->db->limit(5);
        $query = $this->db->get_compiled_select(CATEGORY_TABLE);
        return $this->db->query($query)->result();
    }
    
    public function get_user_favorite_stores($user_id)
    {
        $this->db->where(array("user_account_id" => $user_id));
        $this->db->select("retailer_id");
        $query = $this->db->get(USER_FAVORITE_STORE_TABLE);
        $result = array();
        foreach ($query->result() as $value) 
        {
            $result[$value->retailer_id] = $value->retailer_id;
            
        }
        
        return $result;
    }

}
