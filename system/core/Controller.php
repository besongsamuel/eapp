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
                'base_url' => base_url(),
                'site_url' => site_url(),
                'controller' => $this->router->fetch_class(),
                'method' => $this->router->fetch_method(),
                'cart' => addslashes(json_encode($this->getCartItems()))
            );

            $this->user = $this->get_user();
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
        
        private function getCartItems()
        {
            $cart = array();

            foreach ($this->cart->contents() as $item) 
            {
                $cart_item = array();

                $product_id = $item['id'];
                $rowid = $item['rowid'];
                $store_product = $this->admin_model->get(STORE_PRODUCT_TABLE, $product_id);
                
                if($store_product === null)
                    continue;
                
                $retailer = $this->admin_model->get(CHAIN_TABLE, $store_product->retailer_id);
                $product = $this->admin_model->get(PRODUCT_TABLE, $store_product->product_id);

                $cart_item['store_product'] = $store_product;
                $cart_item['product'] = $product;
                $cart_item['rowid'] = $rowid;
                $cart_item['retailer'] = $retailer;
                $cart_item['quantity'] = 1;

                array_push($cart, $cart_item);
            }

            return $cart;
        }
        
        private function get_user()
        {
            return $this->account_model->get_user(1);
        }

}
