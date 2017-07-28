<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of Admin_model
 *
 * @author beson
 */
class Admin_model extends CI_Model 
{
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
    }
    
    public function update($table_name, $id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($table_name, $data);
    }
	
    public function delete($table_name, $id = null)
    {
        if($id != null)
        {
            $this->db->where('id', $id);
        }
        
        $this->db->delete($table_name);
    }
    
    public function exists($table_name, $column_name, $check_value)
    {
        $this->db->select("id");
        $this->db->from($table_name);
        $this->db->like($column_name, $check_value);
        $query = $this->db->get();
        if($query != null)
        {
            return $query->row() != null;
        }
        else
        {
            return false;
        }
    }
    
    public function get_like($table_name, $column_name, $check_value)
    {
        $this->db->select("id");
        $this->db->from($table_name);
        $this->db->like($column_name, $check_value);
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
    
    public function searchProducts($name)
    {
        $result = array();
        $this->db->like('name', $name);
        $query =  $this->db->get(PRODUCT_TABLE);
	    
        foreach ($query->result() as $value) 
        {
            $store_image_path = ASSETS_DIR_PATH."img/products/".$value->image;
            if(!file_exists($store_image_path) || empty($value->image))
            {
                $value->image = "no_image_available.png";
            }
            
            $value->subcategory = $this->get(SUB_CATEGORY_TABLE, $value->subcategory_id);

            // Get category
            if($value->subcategory != null)
            {
                    $value->category = $this->get(CATEGORY_TABLE, $value->subcategory->product_category_id);
            }
			
            $result[$value->id] = $value;
        }
        
        return $result;
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
}
