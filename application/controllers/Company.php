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
    
    /**
     * Whenever the account is modified, it is no longer new
     * @param type $val
     */
    private function toggle_is_new($val) 
    {
        if($this->user->company)
        {
            $this->company_model->create(COMPANY_TABLE, array("id" => $this->user->company->id, "is_new" => $val));
        }
    }
    
    public function add_store_product() 
    {
        if($this->user != null && $this->user->subscription >= COMPANY_SUBSCRIPTION && $this->user->company->is_valid)
        {
            $products_count = $this->company_model->get_company_products_count($this->user->company->chain->id);
            
            $res = $this->company_model->get_where(COMPANY_SUBSCRIPTIONS_TABLE, "*", array("subscription" => $this->user->subscription));
            
            if($this->user->company->is_new)
            {
                $this->toggle_is_new(0);
            }
            
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
        if($this->user && $this->user->subscription >= COMPANY_SUBSCRIPTION && $this->user->company->is_valid)
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
                
                if($key < 4)
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
            if($this->user->company->is_new)
            {
                $this->toggle_is_new(0);
            }
            
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
                  
            $this->form_validation->set_rules('account[email]', 'Email', 'email_check');
            $user_account = $this->input->post('account');
            $company_profile = json_decode($this->input->post('profile'), true);
            $company = json_decode($this->input->post('company'), true);
            
            if(sizeof($this->company_model->get_where(COMPANY_TABLE, "*", array("neq" => $company["neq"]))) > 0)
            {
                $data["success"] = false;
                $data["message"] = "Le NEQ fourni est déjà pris..";
                echo json_encode($data);
                return;
            }
            
            
            
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
                    
                    $this->subscribe_logged_user();
                    
                    $this->send_registration_email();
                    
                    $message = "Un nouveau compte d'entreprise a été créé pour l'entreprise: ".$company['name']." avec NEQ ".$company['neq'].". Veuillez vous rendre sur le panneau d'administration et valider le NEQ.";
                    
                    //notify otiprix team
                    $this->send_generic_email("infos@otiprix.com", "administrateur", "Nouveau compte d'entreprise créé", $message);
                    
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
    
    public function send_registration_email() 
    {
        $subject = "Bienvenue à Otiprix";
        
        $email_path = ASSETS_DIR_PATH."templates/mail/welcome_company.html";
        
        // get the contents of the file. 
        $mail = file_get_contents($email_path);
        
        // do the proper replacements of the tags
        $mail = str_replace("[TITLE]", "Bienvenue à Otiprix", $mail);
        $mail = str_replace("[COMPANY_NAME]", $this->user->company->name, $mail);
        $mail = str_replace("[EMAIL]", $this->user->email, $mail);
        $mail = str_replace("[ACTIVATE_URL]", $this->get_activation_url(), $mail);
        $mail = str_replace("[LOGIN_URL]", site_url("account/login"), $mail);
        $mail = str_replace("[OTIPRIX_URL]", site_url(), $mail);
        $mail = str_replace("[OTIPRIX_ADDRESS]", "76 Rue Jean-Perrin, Gatineau, QC, J8V 2R2", $mail);
        
        // images
        $mail = str_replace("[LOGO_IMAGE]", base_url("/assets/img/logo.png"), $mail);
        $mail = str_replace("[PROMO_1]", base_url("/assets/img/step-2.jpg"), $mail);
        $mail = str_replace("[PROMO_2]", base_url("/assets/img/list-calculator.jpg"), $mail);
        // image icons
        $mail = str_replace("[IMAGE_FACEBOOK]", base_url("/assets/img/icons/if_facebook_circle_gray_107140.png"), $mail);
        $mail = str_replace("[IMAGE_YOUTUBE]", base_url("/assets/img/icons/if_youtube_circle_gray_107133.png"), $mail);
        $mail = str_replace("[IMAGE_TWITTER]", base_url("/assets/img/icons/if_twitter_circle_gray_107135.png"), $mail);
        $mail = str_replace("[UNSUBSCRIBE_URL]", $this->get_unsubscribe_url(), $mail);
                
        mail($this->user->email, $subject, $mail, $this->get_otiprix_header());
    }
        
    public function edit_company() 
    {
        if($this->user && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            $company = json_decode($this->input->post('company'), true);
            
            if($this->user->company->is_valid == 1)
            {
                unset($company['neq']);
            }
            
            $company['id'] = $this->user->company->id;
            
            $company['is_new'] = 0;
            
            $this->company_model->create(COMPANY_TABLE, $company);
        }
    }
    
    public function change_logo() 
    {
        if($this->user && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            // Get and delete the current image file
            if(file_exists(ASSETS_DIR_PATH.'img/stores/'.$this->user->company->chain->image_name))
            {
                unlink(ASSETS_DIR_PATH.'img/stores/'.$this->user->company->chain->image_name);
            }
            
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
    
    public function paypal_payment() 
    {
        // Database variables
        $host = "localhost"; //database location
        $user = ""; //database username
        $pass = ""; //database password
        $db_name = ""; //database name

        // PayPal settings
        $paypal_email = 'infos@otiprix.com';
        $return_url = site_url("account/payment-successful");
        $cancel_url = site_url("account/payment-cancelled");
        $notify_url = site_url("account/payments");

        $subscription_id = $this->input->post("id");
        
        // Get the subscription
        $subscription = $this->company_model->get_specific(COMPANY_SUBSCRIPTIONS_TABLE, array("id" => $subscription_id));
        
        $item_name = $subscription->name;
        $item_amount = $subscription->price;

        // Include Functions
        include("functions.php");

        // Check if paypal request or response
        if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"]))
        {
            $querystring = '';

            // Firstly Append paypal account to querystring
            $querystring .= "?business=".urlencode($paypal_email)."&";

            // Append amount& currency (£) to quersytring so it cannot be edited in html

            //The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
            $querystring .= "item_name=".urlencode($item_name)."&";
            $querystring .= "amount=".urlencode($item_amount)."&";
            $querystring .= "cmd=_xclick&";
            $querystring .= "no_note=1&";
            $querystring .= "lc=CA&";
            $querystring .= "currency_code=CAD&";
            $querystring .= "bn=PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest&";
            $querystring .= "first_name=".$this->user->profile->firstname."&";
            $querystring .= "last_name=".$this->user->profile->lastname."&";
            $querystring .= "payer_email=".$this->user->email."&";
            $querystring .= "item_number=".$subscription_id."&";
            
            //loop for posted values and append to querystring
            foreach($_POST as $key => $value) {
                $value = urlencode(stripslashes($value));
                $querystring .= "$key=$value&";
            }

            // Append paypal return addresses
            $querystring .= "return=".urlencode(stripslashes($return_url))."&";
            $querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
            $querystring .= "notify_url=".urlencode($notify_url);

            // Append querystring with custom field
            //$querystring .= "&custom=".USERID;

            // Redirect to paypal IPN
            header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
            exit();
        } else {
           // Response from PayPal
        }
        
    }
    
    public function change_store_product_image() 
    {
        if($this->user && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            
            $id = $this->input->post("id");
            
            $product = $this->company_model->get(STORE_PRODUCT_TABLE, $id);
            
            // Get and delete the current image file
            if(file_exists(ASSETS_DIR_PATH.'img/products/'.$product->image))
            {
                unlink(ASSETS_DIR_PATH.'img/products/'.$product->image);
            }
            
            // Upload the image
            $this->load->helper('file');
            
            $this->initialize_upload_library(ASSETS_DIR_PATH.'img/products/', uniqid().".png");
        
            $row = array();

            $row['id'] = $product->id;

            if($this->upload->do_upload('image'))
            {
                $upload_data = $this->upload->data();
                $row['image'] = $upload_data['file_name'];
            }
            else
            {
                $row['image'] = "";
            }
            
            $this->company_model->create(STORE_PRODUCT_TABLE, $row);
        }
    }
    
    public function select_subscription() 
    {
        $subscription = (int)$this->input->post("subscription");
        
        if($this->user && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            if($this->user->company->is_new)
            {
                $this->toggle_is_new(0);
            }
            
            $result = array("success" => true);
            
            switch ($subscription) 
            {
                case 0:
                    // This is free, switch the user to the free account
                    // Update the user' subscription. 
                    
                    if($this->user->subscription > COMPANY_SUBSCRIPTION)
                    {
                        // Remove recurring payments
                    }
                    
                    $this->company_model->create(USER_ACCOUNT_TABLE, array("subscription" => 10, "id" => $this->user->id));
                    
                    break;
                case 1:
                    break;
                case 2:
                    break;

                default:
                    break;
            }
            
            echo json_encode($result);
        }
    }
    
    public function submit_payment() 
    {
        $nonce = json_decode($this->input->post('nonce'));
        
        $subscription = $this->input->post('subscription');
        
        $selected_subscription = $this->company_model->get_specific(COMPANY_SUBSCRIPTIONS_TABLE, array("subscription" => $subscription));
        
        if($selected_subscription && $selected_subscription->price > 0)
        {
            $result = $this->gateway->transaction()->sale([
                'amount' => $selected_subscription->price,
                'paymentMethodNonce' => $nonce,
                'options' => [ 'submitForSettlement' => True ]
            ]);
            
            // Update the user' current subscription
            if($result->success)
            {
                $this->company_model->create(USER_ACCOUNT_TABLE, array("id" => $this->user->id, "subscription" => $selected_subscription->subscription));
                
                echo json_encode($result->transaction);
            }
        }
        
        
    }
    
    public function get_client_token() 
    {
        if($this->user)
        {
            $token = $this->user->payment_token;
            
            if(!isset($token))
            {
                // Create a new token for the user
                $clientToken = $this->gateway->clientToken()->generate();
                // Save the new token to the user
                $this->account_model->create(USER_ACCOUNT_TABLE, array("id" => $this->user->id, "payment_token" => $clientToken));
                
                $token = $clientToken;
            }
            
            echo $token;
        }
    }

    
}
