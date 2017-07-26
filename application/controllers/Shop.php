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
        $this->data['stores'] = addslashes(json_encode($this->admin_model->get_all(CHAIN_TABLE))) ;
        $this->data['products'] = addslashes(json_encode($this->admin_model->get_all(STORE_PRODUCT_TABLE)));
        $this->data['body'] = $this->load->view('shop/index', $this->data, TRUE);
        $this->rememberme->recordOrigPage();
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function select_flyer_store()
    {
        $retailers = $this->admin_model->get_all(CHAIN_TABLE);
        
        foreach ($retailers as $key => $value) 
        {
            $path = ASSETS_DIR_PATH."img/stores/".$value->image;
            
            if(!file_exists($path))
            {
                $retailers[$key]->image = "no_image_available.png";
            }
        }
        $this->data['retailers'] = addslashes(json_encode($retailers));
        $this->data['body'] = $this->load->view('shop/select_flyer_store', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
	
    public function select_category()
    {
        $categories = $this->admin_model->get_all(CATEGORY_TABLE);
        
        foreach ($categories as $key => $value) 
        {
            $path = ASSETS_DIR_PATH."img/stores/".$value->image;
            
            if(!file_exists($path) || empty($value->image))
            {
                $categories[$key]->image = "no_image_available.png";
            }
        }
        $this->data['categories'] = addslashes(json_encode($categories));
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
	
	$get_latest_products = true;
                
        $products = $this->shop_model->get_store_products_limit($limit, $offset, $get_latest_products, $filter, $order, $store_id);
        
        echo json_encode($products);
    }
    
    
}
