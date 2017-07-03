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
    
    /**
     * Create an EAPP object
     * @param type $table_name
     * @param type $data
     */
    public function create($table_name, $data)
    {
        if(isset($data['id']))
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
    
    public function update($table_name, $id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($table_name, $data);
    }
	
    public function delete($table_name, $id)
    {
        $this->db->where('id', $id);
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
	
    public function searchProducts($name)
    {
        $result = array();
        $this->db->like('name', $name);
        $query =  $this->db->get(PRODUCTS_TABLE);
	    
        foreach ($query->result() as $value) 
        {
            $result[$value->id] = $value;
        }
        
        return $result;
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
