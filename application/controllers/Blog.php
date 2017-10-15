<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data['css'] = $this->load->view('blog/css', $this->data, TRUE);
        $this->data['scripts'] = $this->load->view('blog/scripts', $this->data, TRUE);
	$this->data['recent_posts'] = $this->load->view('blog/recent_posts_widget', $this->data, TRUE);
		
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
    public function press_release()
    {
        $this->rememberme->recordOrigPage();
        $this->data["post_type"] = 0;
        $this->data['body'] = $this->parser->parse("blog/press-release", $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    } 
    
    public function stats()
    {
        $this->rememberme->recordOrigPage();
        $this->data["post_type"] = 1;
        $this->data['body'] = $this->parser->parse("blog/press-release", $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function videos()
    {
        $this->rememberme->recordOrigPage();
        $this->data["post_type"] = 2;
        $this->data['body'] = $this->parser->parse("blog/press-release", $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    }
    
    public function get_posts()
    {
        $type = $this->input->post("type");
        
        $offset = $this->input->post("offset");
        
        $result = array();
        
        $result["recentPosts"] = $this->blog_model->get_recent_posts($type, 3, $offset);
        
        $not_in = array();
                
        foreach ($result["recentPosts"] as $post) 
        {
            array_push($not_in, $post->id);
        }
            
        $result["otherPosts"] = array();
        
        if(sizeof($not_in) > 0)
        {
            $result["otherPosts"] = $this->blog_model->get_other_posts($type, 3, $not_in);
        }
        
        
        $result["blogPostCount"] = $this->blog_model->blog_post_count();
        
        echo json_encode($result);
    }
    
    public function search_posts() {
        
        $result = array();
        $result["otherPosts"] = $this->blog_model->search_posts($this->input->post("type"), $this->input->post("filter"));
        echo json_encode($result);
    }


    public function detail($post_id)
    {
        $this->rememberme->recordOrigPage();
        $this->data["post"] = addslashes(json_encode($this->blog_model->get(BLOG_POSTS, $post_id)));
        $this->data['body'] = $this->parser->parse("blog/stat-detail", $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
    } 
		
    public function like()
    {
        $id = $this->input->post("post_id");
        $this->blog_model->like($id, $this->user);
        echo  json_encode(addslashes($this->blog_model->get_post_data($id, BLOG_POSTS_LIKES)));	
    }

    public function dislike()
    {
        $id = $this->input->post("post_id");
        $this->blog_model->dislike($id, $this->user);
        return json_encode(addslashes($this->blog_model->get_post_data($id, BLOG_POSTS_LIKES)));	
    }
    
    public function read($id) 
    {
        
        $post = $this->blog_model->get_post($id);
        // get article
        $this->data['post'] = addslashes(json_encode($post));
        // get other posts
        $this->data["otherPosts"] = addslashes(json_encode($this->blog_model->get_other_posts($post->type, 3, array($id))));
        $this->rememberme->recordOrigPage();
        $this->data['body'] = $this->parser->parse("blog/read", $this->data, TRUE);
        $this->parser->parse('eapp_template', $this->data);
        
        // display it
    }
    
    public function view($id) 
    {
        $this->read($id);
    }
}
