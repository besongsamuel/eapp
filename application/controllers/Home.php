<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

     public function __construct()
    {
        parent::__construct();
        $this->load->library('cart');
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
    public function index()
    {
        $this->data['script'] = $this->load->view('home/scripts/index', '', TRUE);
        
        $top_categories = $this->home_model->get_mostviewed_categories();
        
        $my_location = $this->get_my_location();
        
        $category_products = array();
        
        $popular_products = $this->shop_model->get_store_products_limit(
                8, 
                0, 
                true, 
                null, 
                "price", 
                null, 
                -1, // Popular category 
                null, 
                false,
                $my_location, // Current user location
                100 // Search distance in KM 
                );
        
        if(sizeof($popular_products["products"]) > 0)
        {
            $category = new stdClass();
            
            $category->id = -1;
            
            $category->name = "Produits Populaire";
            
            $popular_products["category"] = $category;
            
            array_push($category_products, $popular_products);
        }
        
        foreach ($top_categories as $category) 
        {
            $products = $this->shop_model->get_store_products_limit(
                8, 
                0, 
                true, 
                null, 
                "price", 
                null, 
                $category->id, 
                null, 
                false,
                $my_location,
                100);
            
            $products["category"] = $category;
            
            array_push($category_products, $products);
        }
        
        $this->data["categoryProducts"] = $category_products;
        
        $this->data['body'] = $this->load->view('home/index', $this->data, TRUE);
        $this->rememberme->recordOrigPage();
        $this->parser->parse('eapp_template', $this->data);
    }

    public function change_location()
    {
        $this->data['body'] = $this->load->view('home/change-location', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function testing()
    {
        $this->load->view('home/testing');
    }
    
    public function about()
    {
        $this->data['script'] = $this->load->view('home/scripts/about-us', $this->data, TRUE);
        $this->data['body'] = $this->load->view('home/about-us', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
	
    public function contact()
    {
        $this->data['body'] = $this->load->view('home/contact-us', $this->data, TRUE);
        $this->data['script'] = $this->load->view('home/scripts/contact-us', $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function contactus()
    {
        $name = $this->input->post("name");
        $email = $this->input->post("email");
        $subject = 'From '.$name.' ('.$email.') : '.$this->input->post("subject");
        $comment = $this->input->post("comment");
        $to_email = "infos@otiprix.com";
        
        $data = array
        (
            "name" => $name,
            "email" => trim($email)            
        );
        
        $contact = $this->home_model->get_specific(CONTACTS_TABLE, array("email" => trim($email)));
        
        if(!$contact)
        {
            $this->home_model->create(CONTACTS_TABLE, $data);
        }
                
        set_error_handler(function(){ });
        echo json_encode( array("result" => mail($to_email,$subject,$comment,$this->get_otiprix_header())));            
        restore_error_handler();
    }
    
    public function goback() 
    {
        $redirect_url = $this->rememberme->getOrigPage();
                
        if(!$redirect_url)
        {
            $redirect_url = "home";
        }
        
        redirect($redirect_url);
    }
}
