<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function sort_by_stores($itemA, $itemB)
{
	$al = strtolower($itemA->store_product->retailer->name);
	$bl = strtolower($itemB->store_product->retailer->name);
	if ($al == $bl) 
	{
		return 0;
	}
	return ($al > $bl) ? +1 : -1;
}

class Cart extends CI_Controller {

    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('cart');
        $this->load->library('geo');
    }
    
    public function get_latest_products() 
    {
        echo json_encode($this->home_model->get_store_products_limit(25, 0)["products"]);
    }
    
    public function get_product($store_product_id) 
    {
        echo json_encode($this->cart_model->getStoreProduct($store_product_id, true, false));
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
        $this->data['script'] = $this->load->view('cart/scripts/index', '', TRUE);
        $this->data['body'] = $this->load->view('cart/index', '', TRUE);
        $this->data['distance_from_home'] = 'Distance from home';
        $this->rememberme->recordOrigPage();
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function product($id, $store_product_id = -1)
    {
        // get best product
        $store_product = $this->cart_model->get_best_store_product($id, DEFAULT_DISTANCE, MAX_DISTANCE, $this->user, false, null, $store_product_id);
        $data['store_product'] = addslashes(json_encode($store_product));
        $this->data['body'] = $this->load->view('cart/product', $data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function insert_batch() 
    {
        $items = json_decode($this->input->post("items"));
        
        foreach ($items as $item) 
        {
            $store_product = $this->cart_model->get_cheapest_store_product($item->product_id);
            
            if($store_product == null)
            {
                continue;
            }

            $data = array
            (
                'id'      => $item->product_id,
                'qty'     => $item->quantity,
                'price'   => $store_product->price,
                'name'    => 'name_'.$item->product_id
            );	    

            $this->cart->insert($data);
        }
    }
    
    /**
     * Inserts a well formated item to the cart
     * and returns the rowid of the item inserted
     */
    public function insert()
    {
        $product_id = $this->input->post("product_id");
        
        $store_product_id = $this->input->post("store_product_id");
        
        $quantity = $this->input->post("quantity");
        
        if(!isset($quantity))
        {
            $quantity = 1;
        }
        
        $result = array
	(
            "success" => false,
	);
        
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
        
        if($store_product == null)
        {
            echo json_encode($result);
            return;
        }
	
	$data = array
        (
            'id'      => $store_product->product_id,
            'qty'     => $quantity,
            'price'   => $store_product->price,
            'name'    => 'name_'.$store_product->product_id,
            'options' => array('store_product_id' => $store_product_id, "quantity" => $quantity)
	);	    
        
        $rowid = $this->cart->insert($data);
		
	if($rowid)
	{
            $result["rowid"] = $rowid;
            $result["success"] = true;
            $result["store_product"] = $store_product;
            $result["product"] = $this->cart_model->get(PRODUCT_TABLE, $product_id);
            $result["quantity"] = $quantity;
	}
		
        echo json_encode($result);
    }
    
    public function update()
    {
        $item = json_decode($this->input->post("item"), true);
        
        $return = $this->cart->update($item);
        
        echo json_encode($return);
    }
    
    public function remove() 
    {
        
        $rowid = $this->input->post("rowid");
        
        $result = array
	(
            "success" => false,
	);
        
        if($this->cart->remove($rowid))
        {
            $result["success"] = true;
        }
        
        echo json_encode($result);
    }
    
    public function mail_user_cart() 
    {
        $html_content = $this->input->post("content");
        
        set_error_handler(function(){ });
                    
        $subject = "Merci d'utiliser OtiPrix";
        $headers = "From: no-reply@otiprix.com \r\n";
        $headers .= "Reply-To: no-reply@otiprix.com \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        echo json_encode(mail($this->user->email,$subject,$html_content,$headers));            
                    
        restore_error_handler();
    }
    
    /**
     * Descroy the cart
     */
    public function destroy()
    {
        $this->cart->destroy();
    }
        
    /**
     * Get cart contents
     */
    public function get_contents() 
    {
        echo json_encode($this->cart->contents(TRUE));
    }
    
    /**
     * Method that gets an optimized list from a list of cart items
     * within a given distance
     */
    public function update_cart_list()
    {
        // list of products found within the distance
        $optimizedList = array();   
        // list of products that were not found within the distance
        $products_not_found_list = array();
        // get the distance
        $distance = $this->input->post("distance");
        // Get the cart items
        $cart_items = json_decode($this->get_cached_cart_contents());
        
        $search_all = $this->input->post("searchAll") == "true" ? true : false;
        $view_optimized_list = $this->input->post("viewOptimizedList") == "true" ? true : false;
        
        $coords = array("longitude" => $this->input->post("longitude"), "latitude" => $this->input->post("latitude"));
        
        $resultsFilter = json_decode($this->input->post('resultsFilter'), true);
        
        $settings = $this->get_settings($resultsFilter, $this->get_my_location(), $distance);
        
        foreach($this->get_filtered_cart_items($cart_items, $resultsFilter, $this->get_my_location(), $distance) as $cart_item)
        {
            // get the best store product based on price
            $store_product = $this->cart_model->get_best_store_product(
                    $cart_item->product->id, 
                    $distance, 
                    $distance, 
                    $this->user, 
                    $search_all, 
                    $coords, 
                    $view_optimized_list ? -1 : 
                    $cart_item->store_product_id);
            $item = new stdClass();
            $item->store_product = $store_product;
            $item->product = $this->cart_model->get_product($cart_item->product->id);
            $item->rowid = $cart_item->rowid;
            $item->store_product_id = $cart_item->store_product_id;
            $item->quantity = $cart_item->quantity;
            
            // distance of 0 means it wasn't found within the distance specified
            if($store_product->price == 0)
            {
                array_push($products_not_found_list, $item);
            }
            else
            {
                array_push($optimizedList, $item);
            }
        }
		
        // Order by store
        usort($optimizedList, "sort_by_stores");
        usort($products_not_found_list, "sort_by_stores");
        
        // Merge Lists putting the found items at the top
        $final_list = array_merge($optimizedList, $products_not_found_list);
        
        $result = array();
        
        $result["items"] = $final_list;
        $result["cartFilterSettings"] = $settings;
		
        // returns an array where the items not found are on the bottom of the list
        $res = json_encode($result);
        
        if(!$res)
        {
        	echo json_last_error();
        }
        else
        {
        	echo $res;
        }
    }
	
    public function send_sms()
    {
        $this->send_twilio_sms($this->input->post("sms"));
    }
    
    public function save_user_optimisation() 
    {
        if($this->user != null)
        {
            $optimization_data = json_decode($this->input->post("optimization_data"));
            
            $data = array();
            
            $data["mode"] = $optimization_data->mode;
            $data["price_optimization"] = $optimization_data->price_optimization;
            $data["items"] = json_encode($optimization_data->items);
            $data["user_account_id"] = $this->user->id;
            
            $this->account_model->create(USER_OPTIMIZATION_TABLE, $data);
        }
    }
    
    private function get_filtered_cart_items($cart_items, $resultsFilter, $my_location, $distance) 
    {        
        $filtered_cart_items = array();
        
        $product_list = array();
        
        foreach ($cart_items as $cart_item) 
        {
            
            array_push($product_list, $cart_item->product->id);
            
        }
        
        $unique_ids =  $this->shop_model->get_store_product_property(PRODUCT_TABLE.'.id', $product_list, $resultsFilter, $this->shop_model->get_close_stores($my_location, $distance), false);
        
        foreach ($cart_items as $cart_item) 
        {
            
            foreach ($unique_ids as $id) 
            {
                if($cart_item->product->id == $id->id)
                {
                    array_push($filtered_cart_items, $cart_item);
                }
            }
        }
        
        return $filtered_cart_items;
    }
    
    private function get_settings($resultsFilter, $my_location, $distance) 
    {
        // Get the cart list
        $cart_items = json_decode($this->get_cached_cart_contents());
        
        $product_list = array();
        
        foreach ($cart_items as $value) 
        {
            array_push($product_list, $value->product->id);
        }
                
        // Get the settings object
        $settings = $this->shop_model->get_all("otiprix_filter_settings");
        
        $result = array();
        
        foreach ($settings as $setting) 
        {
          
            $settings_ids = $this->get_settings_item($setting->column_table.'.'.$setting->column_name, $product_list, null, $my_location, $distance);
            
            // create an array with the ids
            $index_array = array();
            
            foreach ($settings_ids as $unique_index) 
            {
                array_push($index_array, $unique_index[$setting->column_name]);
            }
            
            $result[$setting->name] = new stdClass();
            
            $result[$setting->name]->values = $this->create_settings_object($setting, $index_array, $resultsFilter);
            
            $result[$setting->name]->setting = $setting;
        }
        
        return $result;
        
    }
       
    private function get_settings_item($property, $product_ids, $resulstFilter, $my_location = null, $distance = 100) 
    {
        // Get the cart Items
        $close_stores = $this->cart_model->get_close_stores($my_location, $distance);
        
        return $this->cart_model->get_store_product_property($property, $product_ids, $resulstFilter, $close_stores, true);
    }
    
}
