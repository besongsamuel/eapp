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
    
    public function upload_product_image()
    {
        $this->load->helper('file');
        
        $this->initialize_upload_library(ASSETS_DIR_PATH.'img/products/', uniqid().".png");
        
        if($this->upload->do_upload('image'))
        {
            $upload_data = $this->upload->data();

            $product_data = array();
            $product_data['image'] = $upload_data['file_name'];
            $product_data['id'] = $this->input->post("product_id");
            $this->admin_model->create(PRODUCT_TABLE, $product_data);
        }
    }

    public function create_new_brand()
    {
        $data = array();
        $data['name'] = $this->input->post('name');
        echo $this->admin_model->create(BRANDS_TABLE, $data);
        
    }

    public function create_store_product()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {

            $store_product = $this->input->post('product');
            $store_product['country'] = $this->input->post('country');
            $store_product['state'] = $this->input->post('state');
            
            
            $this->admin_model->create(STORE_PRODUCT_TABLE, $store_product);
        }
        else
        {
            $this->data['products'] = addslashes(json_encode($this->admin_model->get_all(PRODUCT_TABLE)));
            $this->data['retailers'] = addslashes(json_encode($this->admin_model->get_all(CHAIN_TABLE)));
            $this->data['compareunits'] = addslashes(json_encode($this->admin_model->get_all(COMPAREUNITS_TABLE)));
            $this->data['units'] = addslashes(json_encode($this->admin_model->get_all(UNITS_TABLE)));
            $this->data['brands'] = addslashes(json_encode($this->admin_model->get_all(BRANDS_TABLE)));
            
            $this->data['body'] = $this->load->view('admin/create_store_product', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
    }
    
    public function upload_chains() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('chains', CHAIN_TABLE, null);
        }
    }
    
    public function upload_stores() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('stores', CHAIN_STORE_TABLE, null);
        }
    }
    
    public function upload_units() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('units', UNITS_TABLE, null);
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
        }
    }
    
    public function upload_categories() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('categories', CATEGORY_TABLE, null);
        }
    }
    
    public function upload_subcategories() 
    {
        if ($this->input->method(TRUE) === 'POST') 
        {
            $this->upload_csv_database('subcategories', SUB_CATEGORY_TABLE, null);
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
            $config['allowed_types'] = 'csv|xlsx';
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
        $this->data['body'] = $this->load->view('admin/uploads', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    function combine_array(&$row, $key, $header) 
    {
        $row = array_combine($header, $row);
    }
}