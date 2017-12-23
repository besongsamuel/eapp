<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require __DIR__ . '/../../vendor/twilio/sdk/Twilio/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

/**
 * Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller {

    /**
     * Reference to the CI singleton
     *
     * @var	object
     */
    private static $instance;

    public $data;

    public $user;

    // Your Account SID and Auth Token from twilio.com/console
    private $sid = '';
    private $token = '';

    private $sms_host = '';
    private $sms_username = 'admin';
    private $sms_password = 'Password01$';
    private $sms_port = 9710;

    /**
     * Class constructor
     *
     * @return	void
     */
    public function __construct()
    {
        self::$instance =& $this;

        // Assign all the class objects that were instantiated by the
        // bootstrap file (CodeIgniter.php) to local class variables
        // so that CI can run as one big super object.
        foreach (is_loaded() as $var => $class)
        {
            $this->$var =& load_class($class);
        }
        $this->load =& load_class('Loader', 'core');
        $this->load->library('parser', 'router');
        $this->load->helper('url');
        $this->load->library('cart');
        $this->load->initialize();
        log_message('info', 'Controller Class Initialized');
        $this->set_user();
        // Set template data
        $this->data = array(
            'title' => 'épicerie a petit prix',
            'header_my_account' => 'My Account',
            'header_my_list' => 'My Shopping List',
            'header_my_cart' => 'My Cart',
            'header_login' => 'Login',
            'menu_home' => 'Home',
            'menu_shoppage' => 'Shop Page',
            'menu_cart' => 'Cart',
            'menu_searchproduct' => 'Find Product',
            'menu_categories' => 'Categories',
            'menu_flyers' => 'Flyers',
            'menu_contact' => 'Contact',
                            'scripts' => '',
                            'css' => '',
            'base_url' => base_url(),
            'site_url' => site_url(),
            'controller' => $this->router->fetch_class(),
            'method' => $this->router->fetch_method(),
            'user' => addslashes(json_encode($this->user)),
            'mostviewed_categories' => addslashes(json_encode($this->home_model->get_mostviewed_categories()))
        ); 

        if(($this->user == null) && ($this->router->fetch_method() != 'page_under_construction' && $this->router->fetch_method() != 'perform_login'))
        {
            //header('Location: '.  site_url('/account/page_under_construction'));
        }
        $this->sid = $this->config->item("sid");

        $this->token = $this->config->item("token");

        $this->sms_host = gethostbyname('otiprix.sytes.net');
    }
        
        

    // --------------------------------------------------------------------

    /**
     * Get the CI singleton
     *
     * @static
     * @return	object
     */
    public static function &get_instance()
    {
        return self::$instance;
    }

    public function get_cached_cart_contents()
    {
        $cart = array();

        foreach ($this->cart->contents() as $item) 
        {
            $cart_item = array();

            $product_id = $item['id'];
            $rowid = $item['rowid'];
            // Get best match close to user

            $store_product_id = -1;

            if($this->cart->has_options($rowid))
            {
                $options = $this->cart->product_options($rowid);
                $store_product_id = $options['store_product_id'];
            }

            if($store_product_id == -1)
            {
                // Get best match close to user
                $store_product = $this->cart_model->get_cheapest_store_product($product_id);
            }
            else
            {
                $store_product = $this->cart_model->getStoreProduct($store_product_id, false, true, true);
                $store_product->department_store = new stdClass();
                $store_product->department_store->name = "Le magasin n'est pas disponible près de chez vous.";
                $store_product->department_store->id = -1;
                $store_product->department_store->distance = 0;
            }

            if($store_product === null)
            {
                continue; 
            }

            $cart_item['store_product'] = $store_product;
            $cart_item['store_product_id'] = $store_product_id;
            $cart_item['product'] = isset($store_product->product) ? $store_product->product : $this->cart_model->get_product($product_id);
            $cart_item['rowid'] = $rowid;
            $cart_item['quantity'] = $item['qty'];

            array_push($cart, $cart_item);
        }

        if(sizeof($cart) > 0)
        {
            return json_encode($cart);
        }
        else
        {
            return json_encode(array());
        }
    }

    public function set_user()
    {
        $this->user = null;

        // user is still null, check if remember me was checked
        $cookie_user = $this->rememberme->verifyCookie();

        if ($cookie_user) 
        {
            $condition = array('email'=>$this->input->post($cookie_user));
            // find user account of cookie_user stored in application database
            $checkLogin = $this->account_model->get_specific(USER_ACCOUNT_TABLE, $condition);
            // set session if necessary
            if (!$this->session->userdata('userId')) 
            {
                $this->session->set_userdata('userId', $checkLogin->id);
            }
            $this->user = $this->account_model->get_user($this->session->userdata('userId'));
         }
         else if ($this->session->userdata('isUserLoggedIn')) 
         {
             $this->user = $this->account_model->get_user($this->session->userdata('userId'));
         }

    }
        
    public function inUserList($product_id) 
    {
        if(isset($this->user))
        {
            foreach ($this->user->grocery_list as $product) 
            {
                if($product_id == $product->id)
                {
                    return true;
                }
            }
        }

        return false;
    }

    public function send_verification_code($phone_number)
    {

        $code = $this->generatePIN(4);

        // insert code in user account
        $data = array("phone" => $phone_number, "code" => $code, "id" => $this->user->id, "phone_verified" => 0);

        $this->account_model->create(USER_ACCOUNT_TABLE, $data);

        $this->SendMessage($phone_number, "Votre code de vérification est : ".$code);

        echo json_encode(true);
    }

    private function generatePIN($digits = 4){
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while($i < $digits){
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    public function send_twilio_sms($sms)
    {
        $this->SendMessage($this->user->phone, $sms);

        echo json_encode(true);
    }

    private function SendMessage($number, $message)
    {
        $client = new Client($this->sid, $this->token);	

        $client->messages->create(		
            // the number you'd like to send the message to		
            str_replace("+", "", $number),		
            array(		
                // A Twilio phone number you purchased at twilio.com/console		
                'from' => '+14388008069',		
                // the body of the text message you'd like to send		
                'body' => $message		
            )		
        );	
    }

    private function send_message_opsolete($number, $message)
    {
        /* Create a TCP/IP socket. */
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            return "socket_create() failed: reason: " . socket_strerror(socket_last_error());
        }

        /* Make a connection to the Diafaan SMS Server host */
        $result = socket_connect($socket, $this->sms_host, $this->sms_port);
        if ($result === false) {
            return "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket));
        }

        /* Create the HTTP API query string */
        $query = '/http/send-message/';
        $query .= '?username='.urlencode($this->sms_username);
        $query .= '&password='.urlencode($this->sms_password);
        $query .= '&to='.urlencode($number);
        $query .= '&message='.urlencode($message);

        /* Send the HTTP GET request */
        $in = "GET ".$query." HTTP/1.1\r\n";
        $in .= "Host: www.myhost.com\r\n";
        $in .= "Connection: Close\r\n\r\n";
        $out = '';
        socket_write($socket, $in, strlen($in));

        /* Get the HTTP response */
        $out = '';
        while ($buffer = socket_read($socket, 2048)) {
            $out = $out.$buffer;
        }
        socket_close($socket);

        /* Extract the last line of the HTTP response to filter out the HTTP header and get the send result*/
        $lines = explode("\n", $out);  
        return end($lines);  
    }

    public function GUID()
    {
        return sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
       

}
