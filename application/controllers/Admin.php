<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('upload');
        $this->load->helper('text');
        
        if($this->user != null && $this->user->subscription === 0)
        {
            header('Location: '.  site_url('/home'));
        }
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
    
    public function create_product_brand() 
    {
        $this->load->helper('file');
        
        $data = array
        (
            "name" => $this->input->post("name"),
            "product_id" => $this->input->post("product_id")
        );
        
        $this->initialize_upload_library(ASSETS_DIR_PATH.'img/products/', uniqid().".png");
        
        $response = array();
        if($this->upload->do_upload('image'))
        {
            $upload_data = $this->upload->data();
            $data['image'] = $upload_data['file_name'];
            $response['message'] = "Image ".$upload_data['file_name']." was uploaded successfully. ";
        }
        else
        {
            $data['image'] = "no_image_available.png";
            $response['message'] = 'Brand created sucessfully';
        }
        
        $response['success'] = true;
        
        
        $id = $this->admin_model->create(PRODUCT_BRAND_TABLE, $data);
        
        $response['newBrand'] = $this->admin_model->get(PRODUCT_BRAND_TABLE, $id);
        
        echo json_encode($response);
        
    }
    
    public function create_product()
    {
        $this->load->helper('file');
        
        $data = array
        (
            "name" => $this->input->post("name"),
            "subcategory_id" => $this->input->post("subcategory_id"),
            "unit_id" => $this->input->post("unit_id")
        );
        
        $this->initialize_upload_library(ASSETS_DIR_PATH.'img/products/', uniqid().".png");
        
        $response = array();
        
        
        if($this->upload->do_upload('image'))
        {
            $upload_data = $this->upload->data();
            $data['image'] = $upload_data['file_name'];
            $response['message'] = "Image ".$upload_data['file_name']." was uploaded successfully. ";
        }
        else
        {
            $data['image'] = "no_image_available.png";
            $response['message'] = 'Product created sucessfully';
        }
        
        $response['success'] = true;
        
        $id = $this->admin_model->create(PRODUCT_TABLE, $data);
        
        $response['newProduct'] = $this->admin_model->get(PRODUCT_TABLE, $id);
        
        echo json_encode($response);
        
    }
    
    public function edit_otiprix_product()
    {
        $this->load->helper('file');
        
        $product = json_decode($this->input->post("product"));
        
        $data = array
        (
            "unit_id" => $product->unit_id,
            "subcategory_id" => $product->subcategory_id,
            "tags" => $product->tags,
        );
        
        // User is editing a product
        if(isset($product->id))
        {
            $data["id"] = $product->id;
        }
        
        $this->initialize_upload_library(ASSETS_DIR_PATH.'img/products/', uniqid().".png");
        
        $response = array();
        
        if($this->upload->do_upload('image'))
        {
            $upload_data = $this->upload->data();
            $data['image'] = $upload_data['file_name'];
        }
        
        $response['success'] = true;
        
        $this->admin_model->create(PRODUCT_TABLE, $data);
        
        echo json_encode($response);
        
    }
    
    public function upload_product_image()
    {

        $this->load->helper('file');
        
        $this->initialize_upload_library(ASSETS_DIR_PATH.'img/products/', uniqid().".png");
        
        $response = array();
                
        if($this->upload->do_upload('product_image'))
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
    
    public function create_store_product($id = null)
    {
        
        if($this->user->subscription == 0)
        {
            header('Location: '.  site_url('/home'));
        }
        
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
            // Define default store product
            $this->data['store_product'] = array
            (
                'id' => -1,
                'organic' => 0,
                'in_flyer' => 0,
                'retailer_id' => 1,
                'country' => 'Canada',
                'state' => 'Quebec',
                'format' => '1x1',
                'quantity' => '1',
                'period_from' => date("Y-m-d"),
                'period_to' => date("Y-m-d")
            );
		
            $this->data['id'] = $id;
            
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
        $this->rememberme->recordOrigPage();
        $this->data['body'] = $this->load->view('admin/store_products', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function view_products() 
    {    
        if($this->user->subscription != 2)
        {
            header('Location: '.  site_url('/home'));
        }
        
        $this->rememberme->recordOrigPage();
        $this->data['body'] = $this->load->view('admin/view_products', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function edit_product() 
    {   
        if($this->user->subscription != 2)
        {
            header('Location: '.  site_url('/home'));
        }
        
        $this->rememberme->recordOrigPage();
        $this->data['body'] = $this->load->view('admin/edit_product', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function create_otiprix_product() 
    {  
        if($this->user->subscription != 2)
        {
            header('Location: '.  site_url('/home'));
        }
        
        $this->rememberme->recordOrigPage();
        $this->data['body'] = $this->load->view('admin/create_otiprix_product', $this->data, TRUE);
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
        $this->upload_to_database("chains", CHAIN_TABLE);
    }
    
    public function upload_stores() 
    {
        $this->upload_to_database("stores", CHAIN_STORE_TABLE);
    }
    
    public function upload_units() 
    {
        $this->upload_to_database("units", UNITS_TABLE);
    }
    
    public function upload_unit_compareunit() 
    {
        $this->upload_to_database("unit_compareunit", UNIT_CONVERSION);
    }
    
    public function upload_product_unit_compareunit() 
    {
        $this->upload_to_database("product_unit_compareunit", PRODUCT_UNIT_CONVERSION);
    }
    
    public function upload_products() 
    {
        $this->upload_to_database("products", PRODUCT_TABLE);
    }
    
    public function upload_categories() 
    {
        $this->upload_to_database("categories", CATEGORY_TABLE);
    }
    
    public function upload_subcategories() 
    {
        $this->upload_to_database("subcategories", SUB_CATEGORY_TABLE);
    }
    
    /**
     * This method uploads a CSV to the database
     * @param type $fileName The name used in the view and the name that shall be stored
     * in the files directory
     * @param type $databaseTableName The name of the database where to store the data
     */
    private function upload_csv_database($fileName, $databaseTableName, $data)
    {
        $result = array();
        
        $result["errors"] = array();
        
        $result["bad_data"] = array();
        
        if($this->user->subscription != 2)
        {
            return;
        }
        
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
            
            // Map the csv file date to an array
            $csv_array = array_map('str_getcsv', file($file_path));
            
            // Get the header from the csv_array. 
            $header = array_shift($csv_array);
            
            if(sizeof($header) > 20)
            {
                // Something went wrong. 
                array_push($result["errors"], "The file supplied seems to be invalid. ");
                return $result;
            }

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
                if($this->admin_model->create($databaseTableName, $utf8data) === FALSE)
                {
                    array_push($result["bad_data"], "An error while occured adding data: ".explode(",", $utf8data).' \n');
                }
            }
        }
        else
        {
            // add upload errors to the list of errors. 
            array_push($result["errors"], $this->upload->display_errors());
        }
        
        return $result;
    }
    
    private function upload_to_database($file_name, $database_table_name)
    {
        if($this->user->subscription != 2)
        {
            return;
        }
        
        if ($this->input->method(TRUE) === 'POST') 
        {
            $response = $this->upload_csv_database($file_name, $database_table_name, null);
            
            if(sizeof($response["errors"]) > 0)
            {
                $response['success'] = false;
                $response['message'] = "An error occured while trying to upload data. ";
            }
            else
            {
                $response['success'] = true;
                $response['message'] =  ucwords($file_name)." were uploaded successfully. ";
            }
            
            if(sizeof($response["bad_data"]) > 0)
            {
                $response['message'] .= "Some data was not uploaded. ";
            }
            
            echo json_encode($response);
        }
    }
    
    public function uploads() 
    {
        if($this->user->subscription < 2)
        {
            header('Location: '.  site_url('/admin/create_store_product'));
        }
        
        $this->rememberme->recordOrigPage();
        $this->data['body'] = $this->load->view('admin/uploads', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    function combine_array(&$row, $key, $header) 
    {
        $row = array_combine($header, $row);
    }
	
    public function hit()
    {
        $this->admin_model->hit($this->input->post("table_name"), $this->input->post("id"));
    }
}
