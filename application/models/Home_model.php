
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
		
	public function GetLatestProducts($store_id = -1)
	{
		
		$get = sprintf("%s.*, %s.name, %s.image", STORE_PRODUCTS_TABLE, PRODUCTS_TABLE, PRODUCTS_TABLE);
		$join = sprintf("%s.product_id = %s.id", STORE_PRODUCTS_TABLE, PRODUCTS_TABLE);
		$array = array('period_from <=' => date("Y-m-d"), 'date >=' => date("Y-m-d"));
		if($store_id > -1)
		{
			$array['store_id = '] = $store_id;
		}
		$this->db->select($get);
		$this->db->from(STORE_PRODUCTS_TABLE);
		$this->db->join(PRODUCTS_TABLE, $join);
		$this->db->where($array);
		return $this->db->get();
	}
}
