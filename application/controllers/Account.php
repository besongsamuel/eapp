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
    
    public function submit_favorite_stores() 
    {
        $result = array("success" => false);
        
        $email = $this->input->post("email");
	$selected_retailers = json_decode($this->input->post("selected_retailers"));
        
        // Get user account associated with email
        $user_account = $this->account_model->get_specific(USER_ACCOUNT_TABLE, array("email" => $email));
        
        $this->account_model->clear_user_favorite_stores($user_account->id);
        
        if($user_account != null)
        {
            $this->account_model->clear_user_favorite_stores($user_account->id);
            
            $result["success"] = true;
            
            foreach ($selected_retailers as $retailer_id) 
            {
                $data = array("user_account_id" => $user_account->id, "retailer_id" => $retailer_id);
                
                $this->account_model->create(USER_FAVORITE_STORE_TABLE, $data);
            }
        }
        
        echo json_encode($result);
    }
    
    public function save_user_list()
    {
        $data = array("success" => false);
        
        if($this->user != null)
        {
            $data = array
            (
                "user_account_id" => $this->user->id,
                "grocery_list" => $this->input->post("my_list")
            );
            
            $this->account_model->delete(USER_GROCERY_LIST_TABLE, array("user_account_id" => $this->user->id));
            
            $this->account_model->create(USER_GROCERY_LIST_TABLE, $data);
            
            $data["success"] = true; 
        }
        
        echo json_encode($data);
    }

    /*
     * User login
     */
    public function perform_login()
    {
        $data = array("success" => false);
        
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $condition = array(
                'email'=>$this->input->post('email'),
                'password' => md5($this->input->post('password'))
            );
            $checkLogin = $this->account_model->get_specific(USER_ACCOUNT_TABLE, $condition);
            
            if($checkLogin){
                $this->session->set_userdata('isUserLoggedIn',TRUE);
                $this->session->set_userdata('userId',$checkLogin->id);
                $data["success"] = true;
                $this->set_user();
                $data["user"] = json_encode($this->user);
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
            }else{
                $data['message'] = 'E-mail ou mot de passe incorrect, réessayez.';
            }
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

            $this->form_validation->set_rules('account[email]', 'Email', 'callback_email_check');

            $user_account = $this->input->post('account');
            $user_profile = $this->input->post('profile');
	    	$user_account['password'] = md5($user_account['password']);	
            $user_account['subscription'] = 0;
            $user_account['username'] = $user_account['email'];
			
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
    
    
}
