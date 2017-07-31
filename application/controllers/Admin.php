<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('upload');
        $this->load->helper('text');
    }
    
    private function initialize_upload_library($uploadDirectory, $fileName)
    {
        $config['upload_path'] = $uploadDirectory;
        $config['file_name'] = $fileName;
        $config['overwrite'] = true;
        $config['allowed_types'] = 'gif|jpg|png|jpeg|csv|tiff|jfif';
        $config['max_size']     = '10000';
        
        $this->upload->initialize($config);
    }
    
    public function index() 
    {
        
    }
    
    /*
     * Sets longitude and latitude for all stores
     */
    public function set_chain_store_coordinates() 
    {
        // get all chain stores
        $chain_stores = $this->admin_model->get_all(CHAIN_STORE_TABLE);
        
        foreach ($chain_stores as $store) 
        {
            if($store->longitude == 0 && $store->latitude == 0)
            {
                $data = array("longitude" => 0, "latitude" => 0, "id" => $store->id);
                $coordinates = $this->geo->get_coordinates($store->city, $store->address, $store->state, $store->country);
                if($coordinates)
                {
                    $data["longitude"] = $coordinates["long"];
                    $data["latitude"] = $coordinates["lat"];
                }

                $this->admin_model->create(CHAIN_STORE_TABLE, $data);
            }
            
        }
    }
	
    public function searchProducts()
    {
    	$product_name = $this->input->post("name");
	echo json_encode($this->admin_model->searchProducts($product_name));
    }
    
    public function upload_product_image()
    {
        $this->load->helper('file');
        
        $this->initialize_upload_library(ASSETS_DIR_PATH.'img/products/', uniqid().".png");
        
        $response = array();
        
        if($this->upload->do_upload('image'))
        {
            $upload_data = $this->upload->data();
            $product_data = array();
            $product_data['image'] = $upload_data['file_name'];
            $product_data['id'] = $this->input->post("product_id");
            $this->admin_model->create(PRODUCT_TABLE, $product_data);
            
            $response['success'] = true;
            $response['message'] = "Image ".$upload_data['file_name']." was uploaded successfully. ";
        }
        else
        {
            $response['success'] = false;
            $response['message'] = $this->upload->display_errors();
        }
        
        echo json_encode($response);
    }
    public function create_new_brand()
    {
        $response = array();
        $data = array();
        $data['name'] = $this->input->post('name');
        $response['id'] = $this->admin_model->create(BRANDS_TABLE, $data);
        
        $response['success'] = true;
        $response['message'] = "Brand ".$data['name']." was created successfully. ";
        
        echo json_encode($response);
        
    }
	
    public function create_product()
    {
        $response = array();
        $data = array();
        $data['name'] = $this->input->post('name');
        $response['id'] = $this->admin_model->create(PRODUCT_TABLE, $data, true);
        
        $response['success'] = true;
        $response['message'] = "Product ".$data['name']." was created successfully. ";
        
        echo json_encode($response);
        
    }
	
    public function create_store_product($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            $store_product = $this->input->post('product');
            
            $this->admin_model->create(STORE_PRODUCT_TABLE, $store_product);
            
            $response = array();
            $response['success'] = true;
            $response['id'] = $store_product['id'];
            $response['message'] = "Product was created successfully. ";
            echo json_encode($response);
        }
        else
        {
            $this->data['products'] = addslashes(json_encode($this->admin_model->get_all(PRODUCT_TABLE)));
            $this->data['retailers'] = addslashes(json_encode($this->admin_model->get_all(CHAIN_TABLE)));
            $this->data['compareunits'] = addslashes(json_encode($this->admin_model->get_all(COMPAREUNITS_TABLE)));
            $this->data['units'] = addslashes(json_encode($this->admin_model->get_all(UNITS_TABLE)));
            $this->data['brands'] = addslashes(json_encode($this->admin_model->get_all(BRANDS_TABLE)));
		
            // Define default store product
            $this->data['store_product'] = array
            (
                    'id' => -1,
                    'organic' => 0,
                    'in_flyer' => 0,
		    'retailer_id' => 1,
                    'format' => '1x1',
                    'quantity' => '1',
                    'period_from' => date("Y-m-d"),
                    'period_to' => date("Y-m-d")
            );
		
            $this->data['id'] = $id;
            
            if(isset($id) && $id > -1)
            {
                $this->data['store_product'] = json_encode($this->admin_model->get(STORE_PRODUCT_TABLE, $id));
            }
            else
            {
                $this->data['store_product'] = json_encode($this->data['store_product']);
            }
            
            $this->rememberme->recordOrigPage();
            $this->data['body'] = $this->load->view('admin/create_store_product', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
    }
	
    public function delete_store_product()
    {
            $id = $this->input->post('id');
            $this->admin_model->delete(STORE_PRODUCT_TABLE, $id);
    }
    
    public function store_products() 
    {
        $this->data['store_products'] = addslashes(json_encode($this->admin_model->get_all(STORE_PRODUCT_TABLE)));
        $this->data['products'] = addslashes(json_encode($this->admin_model->get_all(PRODUCT_TABLE)));
        $this->data['retailers'] = addslashes(json_encode($this->admin_model->get_all(CHAIN_TABLE)));
        $this->data['units'] = addslashes(json_encode($this->admin_model->get_all(UNITS_TABLE)));
        
        $this->rememberme->recordOrigPage();
        $this->data['body'] = $this->load->view('admin/store_products', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
    }
    
    public function get_store_products()
    {
        $limit = $this->input->post('limit');
        
        $page = $this->input->post('page') - 1;
        
        $filter = $this->input->post('filter');
        
        $order = $this->input->post('order');
                
        $products = $this->shop_model->get_store_products_limit($limit, $limit * $page, false, $filter, $order);
        
        echo json_encode($products);
    }
    public function upload_chains() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('chains', CHAIN_TABLE, null);
            
            $response = array();
            $response['success'] = true;
            $response['message'] = "Retailer were uploaded successfully. ";
            echo json_encode($response);
        }
    }
    
    public function upload_stores() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('stores', CHAIN_STORE_TABLE, null);
            
            $response = array();
            $response['success'] = true;
            $response['message'] = "Retailer stores were uploaded successfully. ";
            echo json_encode($response);
        }
    }
    
    public function upload_units() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('units', UNITS_TABLE, null);
            
            $response = array();
            $response['success'] = true;
            $response['message'] = "Units were uploaded successfully. ";
            echo json_encode($response);
        }
    }
    
    /**
     * This method uploads products to a given store
     */
    public function upload_products() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('products', PRODUCT_TABLE, null);
            
            $response = array();
            $response['success'] = true;
            $response['message'] = "Products were uploaded successfully. ";
            echo json_encode($response);
        }
    }
    
    public function upload_categories() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('categories', CATEGORY_TABLE, null);
            
            $response = array();
            $response['success'] = true;
            $response['message'] = "Categories were uploaded successfully. ";
            echo json_encode($response);
        }
    }
    
    public function upload_subcategories() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('subcategories', SUB_CATEGORY_TABLE, null);
            
            $response = array();
            $response['success'] = true;
            $response['message'] = "Sub Categories were uploaded successfully. ";
            echo json_encode($response);
        }
    }
    
    /**
     * This method uploads a CSV to the database
     * @param type $fileName The name used in the view and the name that shall be stored
     * in the files directory
     * @param type $databaseTableName The name of the database where to store the data
     */
    private function upload_csv_database($fileName, $databaseTableName, $data)
    {
        // Initialize the Upload Library
            $config['upload_path'] = ASSETS_DIR_PATH."files/";
            $config['file_name'] = $fileName.'-'.date("Y-m-d").'.csv';
            $config['overwrite'] = true;
            $config['allowed_types'] = '*';
            $this->upload->initialize($config);
            // Upload the store logo
            $upload_success = $this->upload->do_upload($fileName);
            
            if($upload_success)
            {
                // Create csv_array from file uploaded
                $file_path = ASSETS_DIR_PATH."files/".$fileName."-".date("Y-m-d").".csv";
                $csv_array = array_map('str_getcsv', file($file_path));
                $header = array_shift($csv_array);
                
                // Get Column Names
                $columns = array();
                foreach ($header as $key => $value) 
                {
                    $columns[$key] = utf8_encode($value);
                }
                array_walk($csv_array, array($this, 'combine_array'), $columns);
                
                foreach ($csv_array as  $value) 
                {
                    // If a data value is available, append it contents to the value
                    if($data != null)
                    {
                        $query_data = array();
                        array_push($value, $query_data);
                    }
                    
                    // UTF-8 enconde the data 
                    $utf8data = array();
                    foreach ($value as $key => $entry) 
                    {
                        $utf8data[$key] = utf8_encode($entry);
                    }
                    
                    //create data
                    $this->admin_model->create($databaseTableName, $utf8data);
                }
            }
    }
    public function uploads() 
    {
        $this->rememberme->recordOrigPage();
        $this->data['body'] = $this->load->view('admin/uploads', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    function combine_array(&$row, $key, $header) 
    {
        $row = array_combine($header, $row);
    }
}
