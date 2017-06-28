<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

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
            
            $this->data['stores'] = $this->admin_model->get_all(CHAIN_TABLE);
	    $this->data['latestProducts'] = $this->home_model->GetLatestProducts(-1);
            $this->data['body'] = $this->load->view('home/index', $this->data, TRUE);
            $this->parser->parse('eapp_template', $this->data);
	}
}
