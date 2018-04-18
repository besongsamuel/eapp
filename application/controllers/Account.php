<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Account extends CI_Controller 
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('geo');
        $this->load->library('statistics', array('user' => $this->user));

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
        
        if($this->user->subscription > COMPANY_SUBSCRIPTION)
        {
            $stats->top_product_retailers = $this->statistics->get_top_product_retailers($order, $period, $limit);
            
            // Get top viewed products
            $stats->top_viewed_products = $this->statistics->get_top_products($period, $order, -1,  -1, 0, $limit);
            
            // Get top cart products
            $stats->top_cart_products = $this->statistics->get_top_products($period, $order, -1,  -1, 1, $limit);
            
            //Get top viewed product states
            $stats->top_viewed_product_states = $this->statistics->get_top_states($order, $period, 0, $limit);
            
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
    
    public function password_forgotten() 
    {
        $this->data['script'] = $this->load->view('account/scripts/password_forgotten', $this->data, TRUE);
        $this->data['body'] = $this->load->view('account/password_forgotten', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
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
            $this->send_password_reset_email($reset_token, $email);
        }
        else
        {
            $result["message"] = "L'email que vous avez entré n'est pas associé avec un compte dans notre base de données.";
        }
        
        echo json_encode($result);
    }
    
    public function send_password_reset_email($reset_token, $email) 
    {
        $mail_subject = 'Réinitialisation de mot de passe';
        
        $reset_url = html_entity_decode(site_url('/account/reset_password?reset_token=').$reset_token);
        
        $user_account = $this->account_model->get_specific(USER_ACCOUNT_TABLE, array("email" => $email));
        
        if($user_account)
        {
            $user_profile = $this->account_model->get_specific(USER_PROFILE_TABLE, array("user_account_id" => $user_account->id));
            
            if(!isset($user_profile))
            {
                return FALSE;
            }
            
            $message = 
                '<table width="100%" style="padding: 5px; margin-left: 5px; margin-right: 5px;">
                    <tbody>
                        <tr><td width="100%" height="18;"><p style="font-size: 12px; color : #1abc9c; text-align: center;">L’ÉPICERIE AU PETIT PRIX</p></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td><p><b>Bonjour, <span style="color: #1abc9c;">'.$user_profile->lastname.', '.$user_profile->firstname.'</span></b></p></td></tr>
                        <tr><td width="100%" height="8"></td></tr>
                        <tr><td><p>Vous avez récemment demandé à réinitialiser votre mot de passe pour votre compte <a href="http://www.otiprix.com">OtiPrix.</a> Utilisez le bouton ci-dessous pour le réinitialiser. Cette réinitialisation de mot de passe n\'est valide que pour les 24h suivantes.</p></td></tr>
                        <tr><td width="100%" height="8"></td></tr>
                        <tr>
                            <td width="100%" style="text-align: center;">
                                <a href="'.$reset_url.'">
                                    <input type="button" value="Réinitialisez votre mot de passe" style="height: 44px; width : 300px; color: #fff; background-color: #1abc9c; font-size: 14px;" />
                                </a>
                            </td>
                        </tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td><p>Si vous n\'avez pas demandé de réinitialisation de mot de passe, ignorez cet e-mail ou contactez l\'assistance si vous avez des questions.</p></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td><p>Merci,</p></td></tr>
                        <tr><td><p>Equipe Otiprix</p></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                    </tbody>
                </table>';
        
            $message .= $this->get_otiprix_mail_footer($email);

            mail($email, $mail_subject, $message, $this->get_otiprix_header());
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
                    $data["success"] = true;
                    
                    $this->login_user(array(
                        'email'=>$user_account['email'],
                        'password' => $user_account['password'])
                    );
                    
                    $this->add_user_to_mailchimp('09a06e4d7e');
                    
                    //$this->send_registration_message();
                    
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
                        <tr><td><p>Merci pour la confiance accordée à <a href="http://www.otiprix.com">OtiPrix.</a> Cet email confirme la création de votre compte associé à votre adresse courriel : <span style="color: #1abc9c;">'.$this->user->email.'.</span></p></td></tr>
                        <tr><td><p>N’attendez pas, <a href="'.$login_link.'">connectez-vous</a>, personnalisez votre compte et commencez à explorer. Vous trouverez en un seul clic les vrais rabais dans les grandes surfaces et dans tous les petits magasins proches de vous. Faites votre liste d’épicerie et réduisez votre facture d’épicerie en un temps record grâce à <a href="http://www.otiprix.com">OtiPrix.</a> N’attendez plus, commencez dès aujourd\'hui ici.</p></td></tr>
                        <tr><td><p>Pour toute assistance ou commentaires, écrivez-nous à l’adresse ci-dessous et nous vous mettrons sur la bonne voie.</p></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                        <tr><td><p>Merci de ne pas répondre à ce message : nous ne traitons pas les mails envoyés à cette adresse.</p></td></tr>
                        <tr><td width="100%" height="32"></td></tr>
                    </tbody>
                </table>';
        
        $message .= $this->get_otiprix_mail_footer($this->user->email);
        
        mail($this->user->email, $mail_subject, $message, $this->get_otiprix_header());
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
