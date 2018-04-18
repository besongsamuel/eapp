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
        $this->data['script'] = $this->load->view('shop/scripts/index', $this->data, TRUE);
        $this->rememberme->recordOrigPage();
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function select_flyer_store()
    {
        $this->data['script'] = $this->load->view('shop/scripts/select_flyer_store', $this->data, TRUE);
        $this->data['body'] = $this->load->view('shop/select_flyer_store', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function categories()
    {
        $this->data['script'] = $this->load->view('shop/scripts/select_category', $this->data, TRUE);
        $this->data['body'] = $this->load->view('shop/select_category', $this->data, TRUE);
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
    
    public function get_store_products()
    {
        $limit = $this->input->post('limit');
        
        $page = $this->input->post('page') - 1;
	
	$offset = $limit * $page;
        
        $filter = $this->input->post('filter');
        
        $order = $this->input->post('order');
	    
	$store_id = $this->input->post('store_id');
	$category_id = $this->input->post('category_id');
        $distance = (int)$this->input->post('distance');
        
        $resultsFilter = json_decode($this->input->post('resultsFilter'), true);
        
        $viewConfig =json_decode($this->input->post('viewConfig'));
           
        $get_latest_products = true;
        
        $viewAll = true;
        
        if(isset($viewConfig) && isset($viewConfig->viewAll))
        {
            $viewAll = $viewConfig->viewAll;
        }
        
        $my_location = $this->get_my_location();
                        
        $products = $this->shop_model->get_store_products_limit(
                $limit, 
                $offset, 
                $get_latest_products, 
                $filter, 
                $order, 
                $store_id, 
                $category_id, 
                $resultsFilter, 
                $viewAll,
                $my_location,
                $distance);
        
        $products["settings"] = $this->get_settings($category_id, $store_id, $resultsFilter, $my_location, $distance);
        
        if($filter && !empty($filter))
        {
            foreach ($products["products"] as $sp) 
            {
                $this->record_product_stat($sp->id, STAT_TYPE_SEARCH);
            }
        }
        
        
        echo json_encode($products);
    }
    
    private function get_settings($category_id, $store_id, $resultsFilter, $my_location, $distance) 
    {
        
        // Get the settings object
        $settings = $this->shop_model->get_all("otiprix_filter_settings");
        
        $result = array();
        
        foreach ($settings as $setting) 
        {
          
            $settings_ids = $this->get_settings_item($setting->column_table.'.'.$setting->column_name, $category_id, $store_id, $my_location, $distance);
            
            // create an array with the ids
            $index_array = array();
            
            foreach ($settings_ids as $unique_index) 
            {
                array_push($index_array, $unique_index[$setting->column_name]);
            }
            
            $result[$setting->name] = new stdClass();
            
            $result[$setting->name]->values = $this->create_settings_object($setting, $index_array, $resultsFilter);
            
            $result[$setting->name]->setting = $setting;
        }
        
        return $result;
        
    }
    
    private function get_settings_item($property, $category_id, $store_id, $my_location = null, $distance = 100) 
    {
        return $this->shop_model->get_distinct_latest_store_product_property($property, null, $store_id, $category_id, null, true, $this->shop_model->get_close_stores($my_location, $distance));
    }
    
    
}
