<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Account extends CI_Controller {

     public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('geo');
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
    
    public function index() 
    {
        $this->data['tabIndex'] = 3; 
        
        if($this->user)
        {
            $favoriteStores = $this->account_model->get_user_favorite_stores($this->user->id);
            
            if(sizeof($favoriteStores) > 0)
            {
                $this->data['tabIndex'] = 0;
            }
        }
        else
        {
            $this->data['redirectToLogin'] = json_encode(true);
        }
        
        $this->data['body'] = $this->load->view('account/index', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function login() 
    {
        $this->data['body'] = $this->load->view('account/login', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function register() 
    {
        $this->data['body'] = $this->load->view('account/register', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function account_created() 
    {
        $this->data['body'] = $this->load->view('account/account_created', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function password_forgotten() 
    {
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
            $this->data['body'] = $this->load->view('account/reset_password', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
        else
        {
            // Load page that token is no longer available
            // Load the reset view
            $this->data['body'] = $this->load->view('errors/message', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
        }
        
    }
    
    public function invalid() 
    {
        $this->data['body'] = $this->load->view('errors/message', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);    
    }
    
    public function unsubscribe() 
    {
        $this->data['body'] = $this->load->view('account/unsubscribe', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);    
    }
    
    public function page_under_construction() 
    {
        $this->load->view('/account/coming-soon', $this->data);
    }
	
    public function my_grocery_list() 
    {
        
        if($this->user != null)
        {
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

    /*
     * User login
     */
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
    
    private function login_user($loginData) 
    {
        $data = array("success" => false);
        
        $checkLogin = $this->account_model->get_specific(USER_ACCOUNT_TABLE, $loginData);
            
        if($checkLogin)
        {
            $this->session->set_userdata('isUserLoggedIn',TRUE);
            $this->session->set_userdata('userId',$checkLogin->id);
            $data["success"] = true;
            $this->set_user();
            $data["user"] = $this->user;
            $data["redirect"] = $this->rememberme->getOrigPage();

            if(!$data["redirect"])
            {
                $data["redirect"] = "home";
            }

            $rememberme = $this->input->post("rememberme");

            if($rememberme)
            {
                $this->rememberme->setCookie($this->input->post('email'));
            }    
        }
        else
        {
            $data['message'] = 'E-mail ou mot de passe incorrect, réessayez.';
        }
        
        return $data;
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
            $this->form_validation->set_rules('account[email]', 'Email', 'callback_email_check');

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
    
    private function send_company_registration_message() 
    {
        $mail_subject = 'Bienvenue';

        $login_link = site_url("/account/login");
        
        $message = '<div>';
    
        $message += '<div style="padding: 30px; margin-left: 20px; margin-right: 20px;">';
        $message += '<p style="font-size: 12px; color : #1abc9c; text-align: center;">L’ÉPICERIE AU PETIT PRIX</p>';
    
        $message += '<h1>Bienvenue</h1>';
        $message += '<p><b>et merci, <span style="color: #1abc9c;">'.$this->user->profile->firstname.'</span></b></p>';
        $message += '<br>';
        $message += '<br>';
        $message += '<p>C\'est officiel. Vous êtes un client OtiPrix. Merci pour la confiance accordée à OtiPrix.com. Cet email confirme la création de votre compte associé à votre adresse courriel: <span style="color: #1abc9c;">'.$this->user->email.'</span></p>';
        $message += '<p>N’attendez pas, <a href="'.$login_link.'">connectez-vous</a>, ersonnalisez votre compte et commencez à explorer. Vous trouverez des conseils pour faciliter votre mise en route, des offres spéciales et bien plus. Commencez ensuite à afficher vos produits sur OtiPrix.com afin de présenter vos meilleurs rabais aux consommateurs. Pour toute assistance, appelez le numéro indiqué ci-dessous et nous vous mettrons sur la bonne voie.</p>';
        $message += '<br>';
        $message += '<p>Merci de ne pas répondre à ce message : nous ne traitons pas les mails envoyés à cette adresse.</p>';
        $message += '<br>';
        $message += '</div>';
    
        $message += '<div style="color : #1abc9c; text-align: center; border: 1px solid #1abc9c; padding: 30px; margin-left: 20px; margin-right: 20px;">';
        $message += '<p><b>Votre numéro de client : <span style="color: #1abc9c;">'.$this->user->account_number.'</span></b></p>';
        $message += '<p><b>8h30 à 20h du lundi au vendredi : <a href>Infos@otiprix.com</a></b></p>';
        $message += '<a><input type="button" value="Commencez a reduire votre facture"/></a>';
        $message .= '</div>';
        $message += $this->get_otiprix_mail_footer($this->user->email);
        
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
    
    /*
     * Existing email check during validation
     */
    public function email_check($str){
        $condition = array('email'=>$str);
        $checkEmail = $this->account_model->get_specific(USER_ACCOUNT_TABLE, $condition);
        if($checkEmail != null){
            $this->form_validation->set_message('email_check', 'The given email already exists.');
            return FALSE;
        } 
        return TRUE;
    }
    
    private function get_otiprix_mail_footer($email)
    {
        // Check if the user is subscribed to our service
        $subscription = $this->account_model->get_specific(NEWSLETTER_SUBSCRIPTIONS, array("email" => $email));
        
        $unsubscribe_link = site_url("/account/unsubscribe?token=");
        
        if(isset($subscription))
        {
            $unsubscribe_link .= $subscription->unsubscribe_token;
        }
        
        $otiprix_address = OtIPRIX_ADDRESS;
        
        $output = '
                
            <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f1f1f1">
                <tbody>
                    <tr>
                        <td width="100%" align="center">
                            <table width="650" align="center" border="0" cellspacing="0" cellpadding="0">
                                <tbody>
                                    <tr><td width="100%" height="32"></td></tr>
                                    <tr><td align="center"><span style="color:#75787D;font-size:12px;line-height:18px;font-family:\'Roboto\', Helvetica, Arial, sans-serif;" class="yiv7776326831fallback-text yiv7776326831appleLinksGrey">© 2017 Otiprix Technology. All Rights Reserved.<br>'.$otiprix_address.'</span></td></tr>
                                    <tr><td align="center"><span style="color:#75787D;font-size:12px;line-height:18px;font-family:\'Roboto\', Helvetica, Arial, sans-serif;" class="yiv7776326831fallback-text yiv7776326831appleLinksGrey">8h30 à 20h du lundi au vendredi : infos@otiprix.com</span></td></tr>
                                    <tr><td height="8" width="100%"></td></tr>
                                    <tr><td align="center">
                                        <span style="color:#75787D;font-size:12px;line-height:18px;">
                                            <a rel="nofollow" target="_blank" href onclick="window.open("www.otiprix.com/assets/files/privacy_policy.pdf", "_blank", "fullscreen=yes"); return false;" style="color:#75787D;text-decoration:underline;font-family:\'Roboto\', Helvetica, Arial, sans-serif;" class="yiv7776326831fallback-text">Privacy Policy</a> | 
                                            <a rel="nofollow" target="_blank" href onclick="window.open("www.otiprix.com/assets/files/terms_and_conditions.pdf", "_blank", "fullscreen=yes"); return false;" style="color:#75787D;text-decoration:underline;font-family:\'Roboto\', Helvetica, Arial, sans-serif;" class="yiv7776326831fallback-text">Terms and Conditions</a> |';
        
        if(isset($subscription) && $subscription->type == 1)
        {
            $output =   '<a rel="nofollow" target="_blank" href="'.$unsubscribe_link.'" style="color:#75787D;text-decoration:underline;font-family:\'Roboto\', Helvetica, Arial, sans-serif;" class="yiv7776326831fallback-text">Unsubscribe</a>'; 
        }
        
                
        $output .=  '</span></td></tr>
                                    <tr><td height="16" width="100%"></td></tr>
                                    <tr><td align="center" width="100%">
                                        <table width="auto" align="center" border="0" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <a rel="nofollow" target="_blank" href="https://www.facebook.com/otiprix.otiprix.1"><img src="https://loebig.files.wordpress.com/2013/10/facebook2.png" width="24" alt="Facebook" border="0"></a>&nbsp;
                                                        <a rel="nofollow" target="_blank" href="https://www.youtube.com/channel/UCbwxS8s1WKYgGCRzd9vIl5A"><img width="24" src="https://loebig.files.wordpress.com/2013/10/youtube2.png" alt="Instagram" border="0"></a>&nbsp;
                                                        <a rel="nofollow" target="_blank" href="https://twitter.com/otiprix"><img width="24" src="https://loebig.files.wordpress.com/2013/10/twitter2.png" alt="Twitter" border="0"></a>&nbsp;
                                                        <a rel="nofollow" target="_blank" href="https://plus.google.com/u/0/117638375580963001925"><img width="24" src="https://loebig.files.wordpress.com/2013/10/google2.png" alt="Google+" border="0"></a>&nbsp;
                                                    </td>
                                                    <td width="16"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    </tr>
                                    <tr><td width="100%" height="32"></td></tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>';
        
        return $output;
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
    
}
