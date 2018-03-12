<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Company extends CI_Controller 
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('geo');
        $this->load->model("company_model");
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
    
    public function get_store_products() 
    {
        $query = json_decode($this->input->post("query"));
        $query_no_filter = json_decode($this->input->post("query"));
        $query_no_filter->filter = "";
        
        $result = array("data" => array(), "count" => 0);
        
        if($this->user != null && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            
            $result["count"] = $this->company_model->get_store_products_count($this->user->company->chain->id, $query_no_filter);
            
            $result["data"] = $this->company_model->get_store_products($this->user->company->chain->id, $query);
        }
        
        echo json_encode($result);
    }
    
    public function add_store_product() 
    {
        if($this->user != null && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            // Get the store product
            $store_product = json_decode($this->input->post("store_product"), true);
            
            // Upload image
            $this->initialize_upload_library(ASSETS_DIR_PATH.'img/products/', uniqid().".png");
            
            if($this->upload->do_upload('image'))
            {
                $upload_data = $this->upload->data();
                $store_product['image'] = $upload_data['file_name'];
            }
            
            // Get the product_id
            $store_product["product_id"] = $this->company_model->get_product_id($store_product);
            
            // Assign the retailer id
            $store_product["retailer_id"] = $this->user->company->chain->id;
            
            $this->company_model->add_store_product($store_product);
        }
    }
    
    public function add_product_brand() 
    {        
        echo json_encode($this->get_brand($this->input->post("name")));
    }
    
    private function get_brand($name) 
    {
        $brand = $this->company_model->get_where(PRODUCT_BRAND_TABLE, "*", array("name" => $name), false, true);
        
        if(sizeof($brand) > 0)
        {
            return $brand[0];
        }
        else
        {
            $brand = array();
        
            $brand["name"] = $name;
            $brand["is_new"] = true;
            $brand["product_id"] = -1;
            
            $id = $this->company_model->create(PRODUCT_BRAND_TABLE, $brand);
        
            return $this->company_model->get(PRODUCT_BRAND_TABLE, $id);
        }
    }
    
    public function add_unit() 
    {
        echo json_encode($this->get_unit($this->input->post("name")));
    }
    
    private function get_unit($name) 
    {
        $unit = $this->company_model->get_where(UNITS_TABLE, "*", array("name" => $name), false, true);
        
        if($unit && sizeof($unit) > 0)
        {
            return $unit[0];
        }
        else
        {
            $unit = array();
            
            $compare_unit = array();

            $unit["name"] = $name;
            
            $unit["is_new"] = true;

            $compare_unit["name"] = $this->input->post("name");
            $compare_unit["is_new"] = true;

            $compareunit_id = $this->company_model->create(COMPAREUNITS_TABLE, $compare_unit);

            $unit["compareunit_id"] = $compareunit_id;

            $unit_id = $this->company_model->create(UNITS_TABLE, $unit);

            $this->company_model->create(UNIT_CONVERSION, array("unit_id" => $unit_id, "compareunit_id" => $compareunit_id, "equivalent" => 1));
            
            return $this->company_model->get(UNITS_TABLE, $unit_id);
        }
    }
    
    
    public function batch_delete_store_products() 
    {
        $store_products = json_decode($this->input->post("store_products"));
        
        foreach ($store_products as $value) 
        {
            $this->company_model->delete(STORE_PRODUCT_TABLE, array("id" => $value->id));
        }
        
    }
    
    public function uploadExcelProducts($fileName) 
    {
        if($this->user && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            $this->company_model->delete(STORE_PRODUCT_TABLE, array("retailer_id" => $this->user->company->chain->id));
            
            $res = $this->company_model->get_where(COMPANY_SUBSCRIPTIONS_TABLE, "*", array("subscription" => $this->user->subscription));
            
            $company_subscription = $res[0];
            
            $items_added = 0;
            
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        
            $spreadsheet = $reader->load($fileName);
            $reader->setLoadSheetsOnly(["products"]);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            foreach ($sheetData as $key => $value) 
            {
                if($items_added > $company_subscription->product_count)
                {
                    return;
                }
                
                if($key == 1)
                {
                    continue;
                }

                if($value["A"] == null 
                        || $value["F"] == null 
                        || empty($value["F"]) 
                        || $value["H"] == null
                        || $value["I"] == null
                        || $value["J"] == null)
                {
                    continue;
                }

                $store_product = array();

                $store_product["store_name"] = $value["A"];

                if($value["B"] && !empty($value["B"]))
                {
                    $store_product["brand_id"] = $this->get_brand($value["B"])->id;
                }
                $store_product["country"] = $value["C"];
                $store_product["state"] = $value["D"] == null ? '' : $value["D"];
                $store_product["format"] = $value["E"] == null ? '' : $value["E"];

                if($value["F"] && !empty($value["F"]))
                {
                    $store_product["unit_id"] = $this->get_unit($value["F"])->id;
                }
                else
                {
                    $store_product["unit_id"] = -1;
                }

                $store_product["size"] = $value["G"] == null ? '' : $value["G"];
                $store_product["price"] = $value["H"];

                $store_product["period_from"] = $value["I"];
                $store_product["period_to"] = $value["J"];

                $store_product["image"] = $value["K"] == null ? '' : $value["K"];
                $store_product["retailer_id"] = $this->user->company->chain->id;
                $store_product["product_id"] = $this->company_model->get_product_id($store_product);

                $this->company_model->create(STORE_PRODUCT_TABLE, $store_product);
                
                $items_added++;

            }
        }

    }
        
    public function upload_products()
    {
        $this->load->library('upload');
        
        $this->load->helper('text');
        
        if($this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            $fileName = uniqid();
        
            // Initialize the Upload Library
            $config['upload_path'] = ASSETS_DIR_PATH."files/";
            $config['file_name'] = $fileName.'.xlsx';
            $config['overwrite'] = true;
            $config['allowed_types'] = '*';
            $this->upload->initialize($config);
            // Upload the store logo
            $upload_success = $this->upload->do_upload("products");

            if($upload_success)
            {
                // Create csv_array from file uploaded
                $file_path = ASSETS_DIR_PATH."files/".$fileName.".xlsx";

                $this->uploadExcelProducts($file_path);

                unlink($file_path);
            }
            else
            {
                echo json_encode($this->upload->display_errors());
            }
        }
        
        
    }

    
}
