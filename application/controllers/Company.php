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
        
        $result = array("data" => array(), "count" => 0);
        
        if($this->user != null && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            
            $result["count"] = $this->company_model->get_store_products_count($this->user->company->chain->id, $query);
            
            $result["data"] = $this->company_model->get_store_products($this->user->company->chain->id, $query);
        }
        
        echo json_encode($result);
    }
    
    public function add_store_product() 
    {
        if($this->user != null && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            $products_count = $this->company_model->get_company_products_count($this->user->company->chain->id);
            
            $res = $this->company_model->get_where(COMPANY_SUBSCRIPTIONS_TABLE, "*", array("subscription" => $this->user->subscription));
            
            $company_subscription = $res[0];
            
            if($products_count < $company_subscription->product_count)
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
                
                echo json_encode(array("success" => true, "max_items" => $company_subscription->product_count));
                return;
            }
            
            echo json_encode(array("success" => false, "max_items" => $company_subscription->product_count));
            
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

            $compare_unit["name"] = $name;
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
    
    public function uploadExcelProducts($fileName, $replace) 
    {
        if($this->user && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            if($replace)
            {
                $this->company_model->delete(STORE_PRODUCT_TABLE, array("retailer_id" => $this->user->company->chain->id));
            }
            
            $res = $this->company_model->get_where(COMPANY_SUBSCRIPTIONS_TABLE, "*", array("subscription" => $this->user->subscription));
            
            $company_subscription = $res[0];
            
            $items_added = $this->company_model->get_company_products_count($this->user->company->chain->id);
            
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        
            $spreadsheet = $reader->load($fileName);
            $reader->setLoadSheetsOnly(["products"]);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            foreach ($sheetData as $key => $value) 
            {
                if($items_added > $company_subscription->product_count)
                {
                    echo json_encode(array("success" => false, "max_items" => $company_subscription->product_count));
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
                
                if($value["C"] != null)
                {
                    $store_product["country"] = $value["C"];
                }
                if($value["D"] != null)
                {
                    $store_product["state"] = $value["D"];
                }
                if($value["E"] != null)
                {
                    $store_product["format"] = $value["E"];
                }
                
                if($value["F"] && !empty($value["F"]))
                {
                    $store_product["unit_id"] = $this->get_unit($value["F"])->id;
                }
                else
                {
                    $store_product["unit_id"] = -1;
                }

                if($value["G"] != null)
                {
                    $store_product["size"] = $value["G"];
                }
                
                $store_product["price"] = $value["H"];

                $store_product["period_from"] = $value["I"];
                $store_product["period_to"] = $value["J"];

                if($value["K"] != null)
                {
                    $store_product["image"] = $value["K"];
                }
                else
                {
                    $store_product["image"] = "";
                }
                
                $store_product["retailer_id"] = $this->user->company->chain->id;
                $store_product["product_id"] = $this->company_model->get_product_id($store_product);

                $this->company_model->create(STORE_PRODUCT_TABLE, $store_product);
                
                $items_added++;

            }
            
            echo json_encode(array("success" => true, "max_items" => $company_subscription->product_count));
        }

    }
        
    public function upload_products()
    {
        $this->load->library('upload');
        
        $this->load->helper('text');
        
        $replace = json_decode($this->input->post("replace"));
        
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

                $this->uploadExcelProducts($file_path, $replace);

                unlink($file_path);
            }
            else
            {
                echo json_encode($this->upload->display_errors());
            }
        }
        
        
    }
    
    public function register() 
    {
        $this->load->library('form_validation');
        $this->load->library('geo');
        
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->load->helper('file');
                        
            $user_account = json_decode($this->input->post('account'), true);
            $company_profile = json_decode($this->input->post('profile'), true);
            $company = json_decode($this->input->post('company'), true);
            
            $this->form_validation->set_rules('email', 'Email', 'callback_email_check');
            
            $user_account['password'] = md5($user_account['password']);	
            // Subscription of 10 is a company
            $user_account['subscription'] = 10;
            // User account and user email have the same value
            $user_account['username'] = $user_account['email'];
            // Set the account number
            $user_account['account_number'] = mt_rand(1000000, 9999999);
			
            if($this->form_validation->run() == true)
            {
                // Create the account
                $user_account_id = $this->account_model->create(USER_ACCOUNT_TABLE, $user_account);
		    
                if($user_account_id)
                {
                    // create user profile
                    $company_profile['user_account_id'] = $user_account_id;
                    
                    $company['user_account_id'] = $user_account_id;
                    
                    if(!isset($company_profile["longitude"]) || !isset($company_profile["latitude"]))
                    {
                        // get longitude and latitude
                        $coordinates = $this->geo->get_coordinates($company_profile["city"], $company_profile["address"], $company_profile["state"], $company_profile["country"]);

                        if($coordinates)
                        {
                            $company_profile["longitude"] = $coordinates["long"];
                            $company_profile["latitude"] = $coordinates["lat"];
                        }
                    }
                    
                    // Create Company Profile
                    $this->account_model->create(USER_PROFILE_TABLE, $company_profile);
                    
                    $this->initialize_upload_library(ASSETS_DIR_PATH.'img/stores/', uniqid().".png");
        
                    $chain = array();
                    
                    $chain['name'] = $company['name'];
                    
                    if($this->upload->do_upload('image'))
                    {
                        $upload_data = $this->upload->data();
                        $chain['image'] = $upload_data['file_name'];
                    }
                    else
                    {
                        $chain['image'] = "no_image_available.png";
                    }
                    
                    // Create company
                    $company_id = $this->account_model->create(COMPANY_TABLE, $company);
                    
                    $chain['company_id'] = $company_id;
                    
                    // Create Chain
                    $this->account_model->create(CHAIN_TABLE, $chain);
                                        
                    $data["success"] = true;
                    
                    $this->login_user(array(
                        'email'=>$user_account['email'],
                        'password' => $user_account['password'])
                    );
                    
                    $this->send_registration_message();
                    
                    $data['user'] = $this->user;
                }
                else
                {
                    $data["success"] = false;
                    $data["message"] = "Des problèmes sont survenus, veuillez réessayer plus tard.";
                }
            }
            else
            {
                $data["success"] = false;
                $data["message"] = "Le courrier électronique fourni est déjà pris.";
            }
        }
        
        echo json_encode($data);
    }
    
    private function send_registration_message() 
    {
        
        $mail_subject = 'Bienvenue a Otiprix';

        $login_link = site_url("/account/login");
        
        $message =  
                '<table width="100%" style="padding: 2%; margin-left: 2%; margin-right: 2%;">
                    <tbody>
                        <tr><td width="100%" height="18;"><p style="font-size: 12px; color : #1abc9c; text-align: center;">L\’ÉPICERIE AU PETIT PRIX</p></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td><h1>Bienvenue,</h1></td></tr>
                        <tr><td><p><b>et merci, <span style="color: #1abc9c;">'.$this->user->profile->lastname.' '.$this->user->profile->firstname.'</span></b></p></td></tr>
                        <tr><td width="100%" height="8"></td></tr>
                        <tr><td><p>C\'est officiel. Vous êtes un client OtiPrix. Merci pour la confiance accordée à OtiPrix.com. Cet email confirme la création de votre compte associé à votre adresse courriel: <span style="color: #1abc9c;">'.$this->user->email.'</span></p></td></tr>
                        <tr><td><p>N’attendez pas, <a href="'.$login_link.'">connectez-vous</a>, personnalisez votre compte et commencez à explorer. Vous trouverez des conseils pour faciliter votre mise en route, des offres spéciales et bien plus. Commencez ensuite à afficher vos produits sur OtiPrix.com afin de présenter vos meilleurs rabais aux consommateurs. Pour toute assistance, appelez le numéro indiqué ci-dessous et nous vous mettrons sur la bonne voie.</p></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td><p>Pour toute assistance ou commentaires, écrivez-nous à l’adresse ci-dessous et nous vous mettrons sur la bonne voie.</p></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td><p>Merci de ne pas répondre à ce message : nous ne traitons pas les mails envoyés à cette adresse.</p></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                    </tbody>
                </table>';
        
        $message .= $this->get_otiprix_mail_footer($this->user->email);
        
        mail($this->user->email, $mail_subject, $message, $this->get_otiprix_header());
    }
    
    public function edit_company() 
    {
        if($this->user && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            $company = json_decode($this->input->post('company'), true);
            $company['id'] = $this->user->company->id;
            
            $this->company_model->create(COMPANY_TABLE, $company);
        }
    }
    
    public function change_logo() 
    {
        if($this->user && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            // Upload the image
            $this->load->helper('file');
            
            $this->initialize_upload_library(ASSETS_DIR_PATH.'img/stores/', uniqid().".png");
        
            $chain = array();

            $chain['id'] = $this->user->company->chain->id;

            if($this->upload->do_upload('image'))
            {
                $upload_data = $this->upload->data();
                $chain['image'] = $upload_data['file_name'];
            }
            else
            {
                $chain['image'] = $this->input->post('image_name');
            }
            
            $this->company_model->create(CHAIN_TABLE, $chain);
        }
    }

    
}
