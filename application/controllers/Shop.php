<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends CI_Controller {

     public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $this->data['categories'] = addslashes(json_encode($this->admin_model->get_all(CATEGORY_TABLE)));
        $this->data['stores'] = addslashes(json_encode($this->admin_model->get_all(CHAIN_TABLE))) ;
        $this->data['products'] = addslashes(json_encode($this->admin_model->get_all(STORE_PRODUCT_TABLE)));
        $this->data['body'] = $this->load->view('shop/index', $this->data, TRUE);
        $this->rememberme->recordOrigPage();
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function select_flyer_store()
    {
        $this->data['body'] = $this->load->view('shop/select_flyer_store', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function get_retailers() 
    {
        $coords = array("longitude" => $this->input->post("longitude"), "latitude" => $this->input->post("latitude"));
        
        $distance = $this->input->post("distance");
        
        if($this->user != null)
        {
            $data = array
            (
                'id' => $this->user->profile->id,
                'optimization_distance' => $distance
            );

            $this->shop_model->create(USER_PROFILE_TABLE, $data);
            
            $this->set_user();
        }
        
        $retailers = $this->cart_model->get_closest_merchants($this->user, $coords, $distance);
        
        foreach ($retailers as $key => $value) 
        {
            $path = ASSETS_DIR_PATH."img/stores/".$value->image;
            
            if(!file_exists($path))
            {
                $retailers[$key]->image = "no_image_available.png";
            }
            $retailers[$key]->image = base_url('/assets/img/stores/').$retailers[$key]->image;
        }
        
        $result = array();
        $result["retailers"] = $retailers;
        echo json_encode($result);
    }
	
    public function categories()
    {
        $this->data['body'] = $this->load->view('shop/select_category', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function get_store_products()
    {
        $limit = $this->input->post('limit');
        
        $page = $this->input->post('page') - 1;
	
	$offset = $limit * $page;
        
        $filter = $this->input->post('filter');
        
        $order = $this->input->post('order');
	    
	$store_id = $this->input->post('store_id');
	$category_id = $this->input->post('category_id');
        
        $resultsFilter = json_decode($this->input->post('resultsFilter'));
        
	$get_latest_products = true;
                
        $products = $this->shop_model->get_store_products_limit($limit, $offset, $get_latest_products, $filter, $order, $store_id, $category_id, $resultsFilter);
        
        $products["settings"] = $this->get_settings($filter, $category_id, $store_id, $resultsFilter);
        
        echo json_encode($products);
    }
    
    private function get_settings($filter, $category_id, $store_id, $resultsFilter) 
    {
        if($resultsFilter == null)
        {
            $resultsFilter = new stdClass();
            $resultsFilter->stores = "";
            $resultsFilter->brands = "";
            $resultsFilter->categories = "";
            $resultsFilter->origins = "";
        }
        
        if(!isset($resultsFilter->brands))
        {
            $resultsFilter->brands = "";
        }
        
        $result = array();
        
        $store_ids = $this->get_settings_item("retailer_id", $filter, $category_id, $store_id);
        
        $id_stores = array();
        
        foreach ($store_ids as $value) 
        {
            array_push($id_stores, $value->retailer_id);
        }
        
        $result["stores"] = $this->create_settings_object(CHAIN_TABLE, $id_stores, "STORE", explode(",", $resultsFilter->stores) );
        
        
        $brand_ids = $this->get_settings_item("brand_id", $filter, $category_id, $store_id);
        
        $id_brands = array();
        
        foreach ($brand_ids as $value) 
        {
            array_push($id_brands, $value->brand_id);
        }
        
        $result["brands"] = $this->create_settings_object(PRODUCT_BRAND_TABLE, $id_brands, "BRAND", explode(",", $resultsFilter->brands));
        
        $category_ids = $this->get_settings_item("product_category_id", $filter, $category_id, $store_id);
        
        $id_category = array();
        
        foreach ($category_ids as $value) 
        {
            array_push($id_category, $value->product_category_id);
        }
        
        $result["categories"] = $this->create_settings_object(CATEGORY_TABLE, $id_category, "CATEGORY", explode(",", $resultsFilter->categories));
        
        $origins = $this->get_settings_item("country", $filter, $category_id, $store_id);
        
        $origins_array = array();
        
        
        
        foreach ($origins as $value) 
        {
            $origin_object = new stdClass();
            
            $origin_object->selected = false;
            
            if($resultsFilter->origins)
            {
                foreach (explode(",", $resultsFilter->origins) as $settingValue) 
                {
                    if($settingValue == $value->country)
                    {
                        $origin_object->selected = true;
                        break;
                    }
                }
            }
            
            
            if($value->country == "")
            {
                $value->country = "Autre";
            }
            
            if($value->country == "undefined")
            {
                $value->country = "Pas connu";
            }
            
            
            $origin_object->type = "ORIGIN";
            $origin_object->name = $value->country;
            
            
            $origin_object->id = sizeof($origins_array) + 1;
            array_push($origins_array, $origin_object);
        }
        
        $result["origins"] = $origins_array;
        
        return $result;
        
    }
    
    private function create_settings_object($table_name, $values_array, $type, $currentSettings) 
    {
        
        $result = $this->shop_model->get_all_where_in($table_name, "id", $values_array);
        
        foreach ($result as $key => $value) 
        {
            $result[$key]->selected = false;
            
            foreach ($currentSettings as $settingValue) 
            {
                if($value->id == $settingValue)
                {
                    $result[$key]->selected = true;
                    break;
                }
            }
            
            $result[$key]->type = $type;
        }
        
        return $result;
    }
    
    private function get_settings_item($property, $filter, $category_id, $store_id) 
    {
        return $this->shop_model->get_distinct_latest_store_product_property($property, $filter, $store_id, $category_id, null);
    }
    
    
}
