<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Account extends CI_Controller 
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('geo');

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
    
    public function get_stats() 
    {
        
        $order = $this->input->post("order");
        $period = $this->input->post("period");
        $limit = $this->input->post("limit");
        
        
        if(!$this->user)
        {
            return;
        }
        
        $stats = new stdClass();
           
        $stats->get_percentage_bio_added_to_cart = $this->statistics->get_percentage_bio($period, 1);
        
        $stats->get_percentage_bio_viewed = $this->statistics->get_percentage_bio($period, 0);
       
        $stats->top_product_categories = $this->statistics->get_top_product_categories($order, $period, $limit);
        
        $stats->top_bio_products = $this->statistics->get_top_products($period, $order, -1,  1, -1, $limit);
        
        $stats->top_cart_bio_products = $this->statistics->get_top_products($period, $order, -1,  1, 1, $limit);
        
        $stats->most_visited_store = 
                $this->statistics->most_visited_store($order, $period, 1);
        
        if($this->user->subscription > COMPANY_SUBSCRIPTION)
        {
            $stats->get_top_recurring_products = $this->statistics->get_top_recurring_products($period, $order, 0, $limit);
            
            $stats->top_product_retailers = $this->statistics->get_top_product_retailers($order, $period, $limit);
            
            // Get top viewed products
            $stats->top_viewed_products = $this->statistics->get_top_products($period, $order, -1,  -1, 0, $limit);
            
            // Get top cart products
            $stats->top_cart_products = $this->statistics->get_top_products($period, $order, -1,  -1, 1, $limit);
            
            //Get top viewed product states
            $stats->top_viewed_product_states = $this->statistics->get_top_states($order, $period, 0, $limit);
            
            $stats->get_store_visitors_info = $this->statistics->get_store_visitors_info();
            
        }
        
        if($this->user->subscription == COMPANY_SUBSCRIPTION + 2)
        {
            // Get top viewed products
            $stats->top_searched_products = $this->statistics->get_top_products($period, $order, -1,  -1, 3, $limit);
            
            // Get top viewed products
            $stats->top_listed_products = $this->statistics->get_top_products($period, $order, -1,  -1, 2, $limit);
            
            // Get top products added to cart by state
            $stats->top_cart_product_states = $this->statistics->get_top_states($order, $period, 1, $limit);
            
            $stats->top_listed_products = $this->statistics->get_top_products($period, $order, -1,  -1, 2, $limit);
            
            $stats->top_product_brands = $this->statistics->get_top_product_brands($order, $period, $limit);
            
            $stats->get_top_visited_chains = $this->statistics->get_top_visited_chains($order, $period, $limit);
            
            $stats->get_product_visitors_info = $this->statistics->get_product_visitors_info(1);
            
            $stats->get_store_userlist_info = $this->statistics->get_store_userlist_info();
            
            $stats->top_optimized_chains = $this->statistics->get_top_optimized_chains();
        }
                
        echo json_encode($stats);
        
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
    public function update_user_store_table()
    {
        $this->account_model->update_user_store_table($this->user); 
    }
        
    public function index($index = 3) 
    {
        $this->data['tabIndex'] = $index; 
        
        if($this->user)
        {
            if($this->user->subscription < COMPANY_SUBSCRIPTION)
            {
                $favoriteStores = $this->account_model->get_user_favorite_stores($this->user->id);
            
                if(sizeof($favoriteStores) > 0)
                {
                    $this->data['tabIndex'] = 0;
                }
            }
        }
        else
        {
            $this->data['redirectToLogin'] = json_encode(true);
        }
        
        if($this->user != null && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            
            $this->data['tabIndex'] = $index;
            
            $this->data['script'] = $this->load->view('account/scripts/index_company', $this->data, TRUE);
            $this->data['body'] = $this->load->view('account/index_company', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
        else
        {
            $this->data['script'] = $this->load->view('account/scripts/index', $this->data, TRUE);
            $this->data['body'] = $this->load->view('account/index', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
    }
    
    public function login() 
    {
        $this->data['script'] = $this->load->view('account/scripts/login', $this->data, TRUE);
        $this->data['body'] = $this->load->view('account/login', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function register($type = '') 
    {
        if(!isset($type) || empty($type))
        {
            $this->data['script'] = $this->load->view('account/scripts/select_account_type', $this->data, TRUE);
            $this->data['body'] = $this->load->view('account/select_account_type', $this->data, TRUE);
        }
        else if($type == "personal")
        {
            $this->data['script'] = $this->load->view('account/scripts/personal', $this->data, TRUE);
            $this->data['body'] = $this->load->view('account/personal', $this->data, TRUE);
        }
        else if($type == "company")
        {
            $this->data['script'] = $this->load->view('account/scripts/company', $this->data, TRUE);
            $this->data['body'] = $this->load->view('account/company', $this->data, TRUE);
        }
        
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function account_created() 
    {
        $this->data['script'] = $this->load->view('account/scripts/account_created', $this->data, TRUE);
        $this->data['body'] = $this->load->view('account/account_created', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function select_subscription() 
    {
        if($this->user && $this->user->company)
        {
            $this->data['script'] = $this->load->view('account/scripts/select_subscription', $this->data, TRUE);
            $this->data['body'] = $this->load->view('account/select_subscription', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
        else
        {
            header('Location: '.  site_url('/account'));
        }
    }
    
    public function password_forgotten() 
    {
        $this->data['script'] = $this->load->view('account/scripts/password_forgotten', $this->data, TRUE);
        $this->data['body'] = $this->load->view('account/password_forgotten', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function send_activation_email() 
    {
        if($this->user && $this->user->is_active == 0)
        {
            $subject = "Activez votre compte";
        
            $email_path = ASSETS_DIR_PATH."templates/mail/activate_email.html";

            // get the contents of the file. 
            $mail = file_get_contents($email_path);

            // do the proper replacements of the tags
            $mail = str_replace("[TITLE]", "Activez votre compte", $mail);
            $mail = str_replace("[LASTNAME]", $this->user->profile->firstname, $mail);
            $mail = str_replace("[FIRSTNAME]", $this->user->profile->lastname, $mail);
            $mail = str_replace("[ACTIVATE_URL]", $this->get_activation_url(), $mail);
            $mail = str_replace("[OTIPRIX_ADDRESS]", "550 Avenue Saint-Dominique, Saint-Hyacinthe, J2S 5M6", $mail);

            // images
            $mail = str_replace("[LOGO_IMAGE]", base_url("/assets/img/logo.png"), $mail);
            
            // image icons
            $mail = str_replace("[IMAGE_FACEBOOK]", base_url("/assets/img/icons/if_facebook_circle_gray_107140.png"), $mail);
            $mail = str_replace("[IMAGE_YOUTUBE]", base_url("/assets/img/icons/if_youtube_circle_gray_107133.png"), $mail);
            $mail = str_replace("[IMAGE_TWITTER]", base_url("/assets/img/icons/if_twitter_circle_gray_107135.png"), $mail);
            $mail = str_replace("[UNSUBSCRIBE_URL]", $this->get_unsubscribe_url(), $mail);

            mail($this->user->email, $subject, $mail, $this->get_otiprix_header());
        }
    }
    
    public function activate_account() 
    {
        // Get the token 
        $token = $this->input->get("token");
        
        if($token)
        {
            // Check if the token exists in the database
            $user_token = $this->account_model->get_specific(TOKENS_TABLE, array("token" => $token));
            
            // Token Exists
            if($user_token)
            {
                // Delete token
                $this->account_model->delete(TOKENS_TABLE, array("token" =>$token));
                
                // activate user
                $this->account_model->create(USER_ACCOUNT_TABLE, array("id" => $user_token->user_account_id, "is_active" => 1));
                
                // If the user is not logged, log the user
                if(!$this->user)
                {
                    // Get the user data
                    $user = $this->account_model->get(USER_ACCOUNT_TABLE, $user_token->user_account_id);
                    $this->login_user(array("email" => $user->email, "password" => $user->password));
                }
                // Refresh user
                $this->set_user();
                $this->data['user'] = addslashes(json_encode($this->user));
                // Go to home page
                $this->data['script'] = $this->load->view('home/scripts/index', '', TRUE);
                $this->data['latestProducts'] = $this->home_model->get_store_products_limit(25, 0)["products"];
                $this->data['body'] = $this->load->view('home/index', $this->data, TRUE);
                $this->data['activated'] = 1;
                $this->parser->parse('eapp_template', $this->data);
            }
            else
            {
                // Goto the home page
                header('Location: '.  site_url('/home'));
            }
        }
        else
        {
            // Goto the home page
            header('Location: '.  site_url('/home'));
        }
    }
    
    public function send_password_reset() 
    {
        
        $result = array
        (
            "success" => false,
            "message" => ""
        );
        
        $email = $this->input->post("email");
        
        $account = $this->account_model->get_specific(USER_ACCOUNT_TABLE, array("email" => $email));
        
        if($account)
        {
            $result["success"] = true;
            $result["message"] = "Email envoyé avec success.";
            
            // Create a password reset token
            $reset_token = $this->GUID();
            // Assign reset token to the user account
            $this->account_model->create(USER_ACCOUNT_TABLE, array("id" => $account->id, "reset_token" => $reset_token));
            // send the reset email
            $this->send_password_reset_email($email, $reset_token);
        }
        else
        {
            $result["message"] = "L'email que vous avez entré n'est pas associé avec un compte dans notre base de données.";
        }
        
        echo json_encode($result);
    }
    
    private function send_password_reset_email($email, $reset_token)
    {
        $user_account = $this->account_model->get_specific(USER_ACCOUNT_TABLE, array("email" => $email));
        
        if($user_account)
        {
            $reset_url = html_entity_decode(site_url('/account/reset_password?reset_token=').$reset_token);
            
            $subject = "Réinitialisation de mot de passe";
        
            $email_path = ASSETS_DIR_PATH."templates/mail/reset_password.html";

            // get the contents of the file. 
            $mail = file_get_contents($email_path);

            // do the proper replacements of the tags
            $mail = str_replace("[TITLE]", "Réinitialisation de mot de passe", $mail);
            $mail = str_replace("[EMAIL]", $email, $mail);
            $mail = str_replace("[PASSWORD_RESET_URL]", $reset_url, $mail);
            $mail = str_replace("[OTIPRIX_ADDRESS]", "550 Avenue Saint-Dominique, Saint-Hyacinthe, J2S 5M6", $mail);

            // images
            $mail = str_replace("[LOGO_IMAGE]", base_url("/assets/img/logo.png"), $mail);

            // image icons
            $mail = str_replace("[IMAGE_FACEBOOK]", base_url("/assets/img/icons/if_facebook_circle_gray_107140.png"), $mail);
            $mail = str_replace("[IMAGE_YOUTUBE]", base_url("/assets/img/icons/if_youtube_circle_gray_107133.png"), $mail);
            $mail = str_replace("[IMAGE_TWITTER]", base_url("/assets/img/icons/if_twitter_circle_gray_107135.png"), $mail);
            $mail = str_replace("[UNSUBSCRIBE_URL]", $this->get_unsubscribe_url(), $mail);

            mail($email, $subject, $mail, $this->get_otiprix_header());
            
        }
    }
    
    public function reset_password() 
    {
        // Get account with reset token
        $account = $this->account_model->get_specific(USER_ACCOUNT_TABLE, array("reset_token" => $this->input->get('reset_token')));
        
        if($account && !empty($account->reset_token))
        {
            // Load the reset view
            $this->data['script'] = $this->load->view('account/scripts/reset_password', $this->data, TRUE);
            $this->data['body'] = $this->load->view('account/reset_password', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
        else
        {
            // Load page that token is no longer available
            // Load the reset view
            $this->data['script'] = $this->load->view('errors/scripts/message', $this->data, TRUE);
            $this->data['body'] = $this->load->view('errors/message', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
        
    }
    
    public function invalid() 
    {
        $this->data['script'] = $this->load->view('errors/scripts/message', $this->data, TRUE);
        $this->data['body'] = $this->load->view('errors/message', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);    
    }
    
    public function unsubscribe() 
    {
        $this->data['script'] = $this->load->view('account/scripts/unsubscribe', $this->data, TRUE);
        $this->data['body'] = $this->load->view('account/unsubscribe', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);    
    }
    
    public function select_department_stores() 
    {
        if($this->user != null && $this->user->subscription >= COMPANY_SUBSCRIPTION && $this->user->is_new)
        {
            $this->data['script'] = $this->load->view('account/scripts/select_department_stores', $this->data, TRUE);
            $this->data['body'] = $this->load->view('account/select_department_stores', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
        else
        {
            header('Location: '.  site_url('/account'));
        }
        
    }
    
    public function page_under_construction() 
    {
        $this->load->view('/account/coming-soon', $this->data);
    }
	
    public function my_grocery_list() 
    {
        
        if($this->user != null)
        {
            $this->data['script'] = $this->load->view('account/scripts/my_list', $this->data, TRUE);
            $this->data['body'] = $this->load->view('account/my_list', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
        else
        {
            $this->rememberme->recordOrigPage();
            header('Location: '.  site_url('/account/login'));
        }
        
    }
    
    public function select_store() 
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
        $this->data['script'] = $this->load->view('account/scripts/select_store', $this->data, TRUE);
        $this->data['body'] = $this->load->view('account/select_store', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function save_favorite_stores() 
    {
        $result = array("success" => false);
        
        if($this->user != null)
        {
            
            $selected_retailers = json_decode($this->input->post("selected_retailers"));

            $this->account_model->clear_user_favorite_stores($this->user->id);

            $this->account_model->clear_user_favorite_stores($this->user->id);

            $result["success"] = true;

            foreach ($selected_retailers as $retailer_id) 
            {
                $data = array("user_account_id" => $this->user->id, "retailer_id" => $retailer_id);

                $this->account_model->create(USER_FAVORITE_STORE_TABLE, $data);
            }
        }
        
        echo json_encode($result);
    }
    
    public function get_favorite_stores()
    {
        if($this->user != null)
        {
            echo json_encode($this->account_model->get_favorite_stores($this->user->id));
        }
        else
        {
            echo json_encode(array());
        }
    }

    public function save_user_list()
    {
        $data = array("success" => false);
        
        if($this->user != null)
        {
            $data = array
            (
                "user_account_id" => $this->user->id,
                "id" => $this->input->post("id"),
                "grocery_list" => $this->input->post("my_list")
            );
            
            if($this->input->post("name") != null)
            {
                $data["name"] = $this->input->post("name");
            }
                        
            $this->account_model->create(USER_GROCERY_LIST_TABLE, $data);
            
            // Get new user
            $user = $this->account_model->get_user($this->user->id);
            
            $data["success"] = true; 
            $data["grocery_lists"] = $user->grocery_lists;
        }
        
        echo json_encode($data);
    }

    public function perform_login()
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $condition = array(
                'email'=>$this->input->post('email'),
                'password' => md5($this->input->post('password'))
            );
            
            $data = $this->login_user($condition);
        }
        
        echo json_encode($data);
        
        exit;
    }
    
    public function send_verification()
    {
        $phone_number = $this->input->post("number");
        
        $this->send_verification_code($phone_number);
    }
    
    public function validate_code() 
    {
        $result = array("success" => false);
        
        $code = trim($this->input->post("code"));
        
        $user = $this->account_model->get_specific(USER_ACCOUNT_TABLE, array("code" => $code, "id" => $this->user->id));
        
        if($user != null && strlen($code) == 4)
        {
            $this->account_model->create(USER_ACCOUNT_TABLE, array("code" => "", "id" => $this->user->id, "phone_verified" => 1));
            
            $result["success"] = true;
            
            $result["message"] = "Votre numéro de téléphone a été vérifié";
        }
        else
        {
            $result["message"] = "Le code saisi est incorrect";
            $result["code"] = $code;
        }
        
        echo json_encode($result);
    }

    /*
     * User registration
     */
    public function registration()
    {
        $data = array();
		
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->form_validation->set_rules('email', 'Email', 'callback_email_check');

            $user_account = $this->input->post('account');
            $user_profile = $this->input->post('profile');
            $user_account['password'] = md5($user_account['password']);	
            $user_account['subscription'] = 0;
            $user_account['username'] = $user_account['email'];
            $user_account['account_number'] = mt_rand(1000000, 9999999);
			
            if($this->form_validation->run() == true)
	    	{
                $insert = $this->account_model->create(USER_ACCOUNT_TABLE, $user_account);
		    
                if($insert)
                {
                    // create user profile
                    $user_profile['user_account_id'] = $insert;
                    
                    // get longitude and latitude
                    $coordinates = $this->geo->get_coordinates($user_profile["city"], $user_profile["address"], $user_profile["state"], $user_profile["country"]);
                    if($coordinates)
                    {
                        $user_profile["longitude"] = $coordinates["long"];
                        $user_profile["latitude"] = $coordinates["lat"];
                    }
                    
                    $insert = $this->account_model->create(USER_PROFILE_TABLE, $user_profile);
                    $this->session->set_userdata('success_msg', 'Your registration was successfully. Please login to your account.');
                    
                    $this->login_user(array(
                        'email'=>$user_account['email'],
                        'password' => $user_account['password'])
                    );
                    
                    $this->subscribe_logged_user();
                    
                    $this->send_registration_email();
                    
                    // MailChimp API credentials
                    //$this->add_user_to_mailchimp($this->config->item('users_list_id'), $this->config->item('mailchimp_api_key'), $this->get_activation_url());
                    
                    $data['user'] = $this->user;
                    
                    $data["success"] = true;
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
        
        $email_path = ASSETS_DIR_PATH."templates/mail/welcome_user.html";
        
        // get the contents of the file. 
        $mail = file_get_contents($email_path);
        
        // do the proper replacements of the tags
        $mail = str_replace("[TITLE]", "Bienvenue à Otiprix", $mail);
        $mail = str_replace("[LASTNAME]", $this->user->profile->firstname, $mail);
        $mail = str_replace("[FIRSTNAME]", $this->user->profile->lastname, $mail);
        $mail = str_replace("[EMAIL]", $this->user->email, $mail);
        $mail = str_replace("[ACTIVATE_URL]", $this->get_activation_url(), $mail);
        $mail = str_replace("[LOGIN_URL]", site_url("account/login"), $mail);
        $mail = str_replace("[OTIPRIX_URL]", site_url(), $mail);
        $mail = str_replace("[OTIPRIX_ADDRESS]", "550 Avenue Saint-Dominique, Saint-Hyacinthe, J2S 5M6", $mail);
        
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
            
    public function save_profile() 
    {
        $result = array("success" => false);
        if($this->user != null && $_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $user_profile = $this->input->post('profile');
            $user_profile['id'] = $this->user->profile->id;
            
            $coordinates = $this->geo->get_coordinates($user_profile["city"], $user_profile["address"], $user_profile["state"], $user_profile["country"]);
            if($coordinates)
            {
                $user_profile["longitude"] = $coordinates["long"];
                $user_profile["latitude"] = $coordinates["lat"];
            }
            
            $insert = $this->account_model->create(USER_PROFILE_TABLE, $user_profile);
            
            if($insert)
            {
                $this->set_user();
                $result['success'] = true;
                $result['user'] = $this->user;
            }
        }
        
        echo json_encode($result);
        
    }
    
    public function modify_password() 
    {
        $result = array
        (
            'success' => false,
        );
        
        $condition = array
        (
            'reset_token' => $this->input->post('reset_token')
        );
            
        $account = $this->account_model->get_specific(USER_ACCOUNT_TABLE, $condition);
        
        if($account)
        {
            $data = array
            (
                'id' => $account->id,
                'reset_token' => ''
            );
            
            // Change the password
            $user_account = array();
            $user_account['password'] = md5($this->input->post('password'));
            $user_account['id'] = $account->id;
            $insert = $this->account_model->create(USER_ACCOUNT_TABLE, $user_account);

            $result["message"] = "Une erreur de serveur est survenue. Veuillez réessayer plus tard.";

            if($insert)
            {
                $result["success"] = true;
                $result["message"] = "Votre mot de passe a été changé.";
                // Remove token
                $this->account_model->create(USER_ACCOUNT_TABLE, $data);
            }
        }
        else
        {
            $result["message"] = "Le code de réinitialisation fourni n'est plus disponible.";
        }
        
        echo json_encode($result);
    }
    
    public function change_password() 
    {
        $result = array("success" => false);
        
        if($this->user != null && $_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $condition = array(
                'email'=>   $this->user->email,
                'password' => md5($this->input->post('old_password'))
            );
            
            $checkLogin = $this->account_model->get_specific(USER_ACCOUNT_TABLE, $condition);
            
            if($checkLogin)
            {
                $user_account = array();
                $user_account['password'] = md5($this->input->post('password'));
                $user_account['id'] = $this->user->id;
                $insert = $this->account_model->create(USER_ACCOUNT_TABLE, $user_account);
                
                $result["message"] = "Une erreur de serveur est survenue. Veuillez réessayer plus tard.";
                
                if($insert)
                {
                    $result["success"] = true;
                    $result["message"] = "Votre mot de passe a été changé.";
                }
            }
            else
            {
                $result["success"] = false;
                $result["message"] = "Le mot de passe saisi est incorrect.";
            }
        }
        
        echo json_encode($result);
        
    }
    
    public function change_security_qa() 
    {
        $result = array("success" => false);
        
        if($this->user != null && $_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $user_account = array();
            $user_account['security_question_answer'] = $this->input->post('security_question_answer');
            $user_account['security_question_id'] = $this->input->post('security_question_id');
            $user_account['id'] = $this->user->id;
            $insert = $this->account_model->create(USER_ACCOUNT_TABLE, $user_account);

            $result["message"] = "Une erreur de serveur est survenue. Veuillez réessayer plus tard.";

            if($insert)
            {
                $result["success"] = true;
                $result["message"] = "Votre question de sécurité et votre réponse ont été modifiées..";
            }
        }
        
        echo json_encode($result);
    }
    
    /*
     * User logout
     */
    public function logout()
    {
        $this->session->unset_userdata('isUserLoggedIn');
        $this->session->unset_userdata('userId');
        $this->session->sess_destroy();
    }
    
    public function delete_grocery_list() 
    {
        if($this->user != null)
        {
            $data = array();
        
            $id = $this->input->post('id');

            $this->account_model->delete(USER_GROCERY_LIST_TABLE, array('id' => $id));

            // Get new user
            $user = $this->account_model->get_user($this->user->id);

            $data["success"] = true; 
            $data["grocery_lists"] = $user->grocery_lists;


            echo json_encode($data);
        }
        
    }
    
    public function create_new_list() 
    {
        $result = array();
        
        if($this->user != null)
        {
            $name = $this->input->post('name');
            
            if(!empty($name))
            {
                $equivalent_list = $this->account_model->get_specific(USER_GROCERY_LIST_TABLE, array("name" => $name));
                
                if($equivalent_list)
                {
                    $result['message'] = "Une liste d'épicerie avec le même nom existe déjà.";
                    $result['success'] = false;
                }
                else
                {
                    $data = array("name" => $name, "user_account_id" => $this->user->id);
                    
                    $index = $this->account_model->create(USER_GROCERY_LIST_TABLE, $data);
                    
                    $list = $this->account_model->get(USER_GROCERY_LIST_TABLE, $index);
                    
                    if($list)
                    {
                        $result['message'] = "Votre liste d'épicerie a été crée avec succès.";
                        $result['success'] = true;
                        $list_object = new stdClass();
                        $list_object->id = $list->id;
                        $list_object->name = $list->name;
                        $list_object->products = array();
                        $list_object->stores = array();
                        $result['data'] = $list_object;
                    }
                    else
                    {
                        $result['message'] = "Une erreur inattendue du serveur s'est produite. Veuillez réessayer plus tard.";
                        $result['success'] = false;
                    }
                }
                
            }
            else
            {
                $result['message'] = "Le nom de la liste est vide";
                $result['success'] = false;
            }
            
            echo json_encode($result);
        }
    }
    
    public function add_department_store() 
    {
        if($this->user != null && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            $department_store = json_decode($this->input->post('department_store'), true);
            $department_store["chain_id"] = $this->user->company->chain->id;
            
            if(!isset($department_store["longitude"]))
            {
                $coordinates = $this->geo->get_coordinates($department_store["city"], $department_store["address"], $department_store["state"], $department_store["country"]);
                if($coordinates)
                {
                    $department_store["longitude"] = $coordinates["long"];
                    $department_store["latitude"] = $coordinates["lat"];
                }
            }
            
            $id = $this->account_model->create(CHAIN_STORE_TABLE, $department_store);
            
            if($id)
            {
                // Change company to not new
                $this->account_model->create(COMPANY_TABLE, array("id" => $this->user->company->id, "is_new" => 0));
                
                echo json_encode(array("id" => $id, "success" => true));
            }
        }
    }
    
    public function remove_department_store()
    {
        if($this->user != null && $this->user->subscription >= COMPANY_SUBSCRIPTION)
        {
            $id = $this->input->post('id');
            
            $this->account_model->delete(CHAIN_STORE_TABLE, array("id" => $id));
            
            echo json_encode(true);
        }
    }
    
    public function toggle_new()
    {
        if($this->user != null && $this->user->is_new)
        {
            $this->account_model->create(USER_ACCOUNT_TABLE, array('id' => $this->user->id, 'is_new' => false));
            
            json_encode(true);
        }
        
    }
    
}
