<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of Admin_model
 *
 * @author besong
 */
class Blog_model extends CI_Model 
{
    public function __construct()
    {
	    parent::__construct();
	    // Your own constructor code
    }
    
	/**
	* Get all recent posts of type statistics
	*/
    public function get_recent_stat_posts()
    {
        return $this->get_recent_posts(POST_TYPE_STAT);
    }
	
	public function like_post($post_id, $user)
	{
		if($user != null && isset($post_id))
		{
			$data = array("post_id" => $post_id, "user_account_id" => $user->id);
			$index = $this->db->insert(BLOG_POSTS_LIKES, $data);
			return $index;
		}
		
		return FALSE;
	}
	
	public function dislike_post($post_id)
	{
		if(isset($post_id))
		{
			$data = array("post_id" => $post_id);
			$this->db->delete(BLOG_POSTS_LIKES, $data);
			
			return $post_id;
		}
		
		return FALSE;
	}
	
	public function comment_post($post_id, $user, $comment)
	{
		if($user != null && isset($post_id) && isset($comment))
		{
			$data = array("post_id" => $post_id, "user_account_id" => $user->id, "comment" => $comment);
			$index = $this->db->insert(BLOG_POSTS_COMMENTS, $data);
			return $index;
		}
		
		return FALSE;
	}
        
        public function get_post($post_id) 
        {
            $this->db->where(array("id" => $post_id));
            $post = $this->db->get(BLOG_POSTS)->row();
            
            if($post != null)
            {
                // get post comments
                $post->comments = $this->get_post_data($post->id, BLOG_POSTS_COMMENTS);
                // get post likes
                $post->likes = $this->get_post_data($post->id, BLOG_POSTS_LIKES);
            }
            
            return $post;
        }
	
	/**
	* Get all recent posts 
	*/
	public function get_recent_posts($type = -1, $limit = null, $offset = null)
	{
            $result_array = array();
            
            if($limit != null)
            {
                if($offset == null)
                {
                    $offset = 0;
                }
                
                $this->db->limit($limit, $offset);
            }
            
            $this->db->order_by('date_modified', 'DESC');
            
            if($type != -1)
            {
                $this->db->where(array("type" => $type));
                $result = $this->db->get(BLOG_POSTS);
            }
            else
            {
                $result = $this->db->get(BLOG_POSTS);
            }

            foreach($result->result() as $post)
            {
                // get post comments
                $post->comments = $this->get_post_data($post->id, BLOG_POSTS_COMMENTS);

                // get post likes
                $post->likes = $this->get_post_data($post->id, BLOG_POSTS_LIKES);

                $result_array[$post->id] = $post;
            }

            return $result_array;
	}
        
        public function get_other_posts($type = -1, $limit = 3, $not_in = null) 
        {
            $result_array = array();
            
            if($limit != null)
            {
                $this->db->limit($limit);
            }
            
            $this->db->order_by('date_modified', 'DESC');
            
            $this->db->where_not_in('id', $not_in);
            
            if($type != -1)
            {
                $this->db->where(array("type" => $type));
                $result = $this->db->get(BLOG_POSTS);
            }
            else
            {
                $result = $this->db->get(BLOG_POSTS);
            }
            
            foreach($result->result() as $post)
            {
                // get post comments
                $post->comments = $this->get_post_data($post->id, BLOG_POSTS_COMMENTS);

                // get post likes
                $post->likes = $this->get_post_data($post->id, BLOG_POSTS_LIKES);;

                $result_array[$post->id] = $post;
            }

            return $result_array;
            
        }
        
        public function search_posts($type, $filter, $limit = 3) 
        {
            $result_array = array();
            $this->db->limit($limit);
            $this->db->where(array("type" => $type));
            $this->db->like('title', $filter);
            $result = $this->db->get(BLOG_POSTS);
            
            foreach($result->result() as $post)
            {
                // get post comments
                $post->comments = $this->get_post_data($post->id, BLOG_POSTS_COMMENTS);

                // get post likes
                $post->likes = $this->get_post_data($post->id, BLOG_POSTS_LIKES);

                $result_array[$post->id] = $post;
            }

            return $result_array;
        }
        
        public function blog_post_count($type = -1) 
        {
            if($type != -1)
            {
                $this->db->where(array("type" => $type));
                $result = $this->db->get(BLOG_POSTS);
            }
            else
            {
                $result = $this->db->get(BLOG_POSTS);
            }
            
            return $result->num_rows();
        }
	
	public function get_post_data($post_id, $table_name)
	{
            $result_array = array();
            $this->db->where(array("post_id" => $post_id));
            $res = $this->db->get($table_name);

            foreach($res->result() as $data)
            {
                $result_array[$data->id] = $data;
            }

            return $result_array;
	}
}
