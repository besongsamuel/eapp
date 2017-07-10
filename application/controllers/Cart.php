<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {

    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('cart');
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
    public function index()
    {
        $this->data['body'] = $this->load->view('cart/index', '', TRUE);
        $this->data['distance_from_home'] = 'Distance from home';
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function product($id)
    {
        $storeProduct = $this->cart_model->getStoreProduct($id);
        $data["relatedProducts"] = $storeProduct->related_products;
        $data["retailer"] = $storeProduct->retailer;
        $data['store_product'] = addslashes(json_encode($storeProduct));
        $data['products'] = addslashes(json_encode($this->admin_model->get_all(PRODUCT_TABLE)));
        $this->data['body'] = $this->load->view('cart/product', $data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    /**
     * Inserts a well formated item to the cart
     * and returns the rowid of the item inserted
     */
    public function insert()
    {
        $product_id = $this->input->post("product_id");
	    
        $store_product = $this->admin_model->get(STORE_PRODUCT_TABLE, $product_id);
	$product = $this->admin_model->get(PRODUCT_TABLE, $store_product->product_id);
        $retailer = $this->admin_model->get(CHAIN_TABLE, $store_product->retailer_id);
	
	$data = array(
		'id'      => $store_product->id,
		'qty'     => $store_product->quantity,
		'price'   => $store_product->price,
		'name'    => $product->name
	);	    
        
        $rowid = $this->cart->insert($data);
		
	$result = array
	(
		"success" => false,
		"rowid" => $rowid
	);

	if($rowid)
	{
		$result["success"] = true;
		$result["store_product"] = $store_product;
		$result["product"] = $product;
                $result["retailer"] = $retailer;
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
    public function getOptimizedList()
    {
	$optimizedList = array();    
    	$distance = $this->input->post("distance");
	$store_products = json_decode($this->input->post("store_products"));
	    
	foreach($store_products as $s_product)
	{
            // Get the store product
            $store_product = $this->admin_model->get(STORE_PRODUCT_TABLE, $s_product->id);
            // Get all store products created with the same product
            $available_store_products = $this->cart_model->getProducts($store_product->product_id);
            // Get chepeast withing required distance
            $optimized_store_product = $this->optimizeProductList($available_store_products, $distance);
            $full_store_product = $this->cart_model->getStoreProduct($optimized_store_product->id, false, false);
            $full_store_product->departmentStore = isset($optimized_store_product->departmentStore) ? $optimized_store_product->departmentStore : null;
            $cart_item['store_product'] = $full_store_product;
            $cart_item['rowid'] = $s_product->rowid;
            $cart_item['quantity'] = $s_product->quantity;
            array_push($optimizedList, $cart_item);
	}
	
	echo json_encode($optimizedList);
    }
	
    /**
     * Given a list of products, returns the best match
     * with regards to the distance
     * @param type $available_store_products
     * @param type $distance
     * @return type
     */
    private function optimizeProductList($available_store_products, $distance)
    {
	 $best_match = null;
	 
        foreach($available_store_products as $store_product)
        {
            if($best_match === null)
            {
                $best_match = $store_product;
            }
            else
            {
                if($store_product->price < $best_match->price)
                {
                    $closestDepartmentStore = $this->get_closest_department_store($best_match->retailer_id, $distance);

                    if($closestDepartmentStore != null)
                    {
                        $best_match = $store_product;
                        $best_match->departmentStore = $closestDepartmentStore;
                    }
                }
            }
        }
         
        if($best_match != null)
        {
            if(!isset($best_match->departmentStore))
            {
                $best_match->departmentStore = $this->get_closest_department_store($best_match->retailer_id, $distance);
            }
        }
         
         return $best_match;
    }
    
    private function get_closest_department_store($retailer_id, $distance) 
    {
        // Get the department stores related to this product
        $department_stores = $this->cart_model->getDepartmentStores($retailer_id);

        $closestDepartmentStore = $this->cart_model->findCloseDepartmentStore($department_stores, $this->user, $distance);
        
        return $closestDepartmentStore;
        
    }
}
