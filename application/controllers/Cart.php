<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller {

    
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
        $this->data['body'] = $this->load->view('cart/index', '', TRUE);
        $this->data['distance_from_home'] = 'Distance from home';
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
        
        
}
