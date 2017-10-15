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
    
    public function get_product($product_id) 
    {
        echo json_encode($this->cart_model->get_best_store_product(-1, DEFAULT_DISTANCE, MAX_DISTANCE, $this->user, false, null, $product_id));
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
                'qty'     => 1,
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
            'qty'     => 1,
            'price'   => $store_product->price,
            'name'    => 'name_'.$store_product->product_id,
            'options' => array('store_product_id' => $store_product_id)
	);	    
        
        $rowid = $this->cart->insert($data);
		
	if($rowid)
	{
            $result["rowid"] = $rowid;
            $result["success"] = true;
            $result["store_product"] = $store_product;
            $result["product"] = $this->cart_model->get(PRODUCT_TABLE, $product_id);
	}
		
        echo json_encode($result);
    }
    
    public function update()
    {
        $item = json_decode($this->input->post("item"));
        
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
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

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
    
    public function get_cart_contents() 
    {
        echo $this->get_cached_cart_contents();
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
        // get the cart products
        $products = json_decode($this->input->post("products"));
        $search_all = $this->input->post("searchAll") == "true" ? true : false;
        $coords = array("longitude" => $this->input->post("longitude"), "latitude" => $this->input->post("latitude"));
        // get the store_product id if applicable

        foreach($products as $product)
        {
            // get the best store product based on price
            $store_product = $this->cart_model->get_best_store_product($product->id, $distance, $distance, $this->user, $search_all, $coords, $product->store_product_id);
            $cart_item = new stdClass();
            $cart_item->store_product = $store_product;
            $cart_item->store_product->product->in_user_grocery_list = $this->inUserList($product->id);
            $cart_item->product = $this->cart_model->get_product($product->id);
            $cart_item->product->in_user_grocery_list = $this->inUserList($product->id);
            $cart_item->rowid = $product->rowid;
            $cart_item->store_product_id = $product->store_product_id;
            $cart_item->quantity = $product->quantity;
            
            // distance of 0 means it wasn't found within the distance specified
            if($store_product->price == 0)
            {
                array_push($products_not_found_list, $cart_item);
            }
            else
            {
                array_push($optimizedList, $cart_item);
            }
        }
		
        // Order by store
        usort($optimizedList, "sort_by_stores");
        usort($products_not_found_list, "sort_by_stores");
        
        // Merge Lists putting the found items at the top
        $final_list = array_merge($optimizedList, $products_not_found_list);
		
        // returns an array where the items not found are on the bottom of the list
        $res = json_encode($final_list);
        
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
}
