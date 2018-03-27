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
require __DIR__ . '/../../vendor/autoload.php';


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
            'scripts' => $this->load->view('scripts', $this->data, TRUE),
            'css' => $this->load->view('css', $this->data, TRUE),
            'base_url' => base_url(),
            'site_url' => site_url(),
            'redirectToLogin' => json_encode(false),
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
            if (!$this->session->userdata('userId') && $checkLogin) 
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
            $number,		
            array(		
                // A Twilio phone number you purchased at twilio.com/console		
                'from' => '+15799990395',		
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
    
    protected function get_otiprix_header($to_email = "infos@otiprix.com") 
    {
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // More headers
        $headers .= 'From: <'.$to_email.'>' . "\r\n";
        
        return $headers;
    }
    
    protected function get_my_location() 
    {
        $coords = array("longitude" => $this->input->post("longitude"), "latitude" => $this->input->post("latitude"));
        
        $longitude = null;
        $latitude = null;

        if($this->user != null)
        {
            $longitude = $this->user->profile->longitude;
            $latitude = $this->user->profile->latitude;
        }

        if($this->user == null && $coords != null && $coords["latitude"] != 0 && $coords["longitude"] != 0)
        {
            $longitude = $coords["longitude"];
            $latitude = $coords["latitude"];
        }
        
        return array("longitude" => $longitude, "latitude" => $latitude);
    }
    
    protected function create_settings_object($settings_object, $index_array, $currentSettings) 
    {
        if(isset($settings_object->table_name) && !empty($settings_object->table_name))
        {
            // Get the objects
            $result = $this->shop_model->get_all_where_in($settings_object->table_name, "id", $index_array, true);

            foreach ($result as
                    $key => $value) 
            {
                $result[$key]["selected"] = false;

                if(isset($currentSettings) && isset($currentSettings[$settings_object->name]))
                {
                    foreach (explode(",", $currentSettings[$settings_object->name]) as $settingValue) 
                    {
                        if($value["id"] == $settingValue)
                        {
                            $result[$key]["selected"] = true;
                            break;
                        }
                    }
                }

                $result[$key]["type"] = $settings_object->name;
            }
        }
        else
        {

            $result = array();
            
            foreach ($index_array as $value) 
            {
                $settings_value = array();

                $settings_value["selected"] = false;

                if(isset($currentSettings) && !empty($currentSettings[$settings_object->name]))
                {
                    foreach (explode(",", $currentSettings[$settings_object->name]) as $settingValue)  
                    {
                        if($settingValue == $value)
                        {
                            $settings_value["selected"] = true;
                            break;
                        }
                    }
                }

                if($value == "")
                {
                    $settings_value["name"] = "Autre";
                }
                else
                {
                    $settings_value["name"] = $value;
                }

                $settings_value["type"] = $settings_object->name;
                $settings_value["id"] = $value;

                array_push($result, $settings_value);
            }
        }
        
        return $result;
    }
    
    protected function get_otiprix_mail_footer($email)
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
    
    protected function login_user($loginData) 
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
       

}
