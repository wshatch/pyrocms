<?php namespace Module\Search;

/**
 * Search Events
 *
 * @author  PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Search
 */
class Events
{   
    public function __construct()
    {
        // Load the search index model
        ci()->load->model('search/search_index_m');

		// Post a blog to twitter and whatnot
        \Library\Events::register('post_published', array($this, 'index_post'));
        \Library\Events::register('post_updated', array($this, 'index_post'));
        \Library\Events::register('post_deleted', array($this, 'drop_post'));

		// Post a blog to twitter and whatnot
        \Library\Events::register('page_created', array($this, 'index_page'));
        \Library\Events::register('page_updated', array($this, 'index_page'));
        \Library\Events::register('page_deleted', array($this, 'drop_page'));
    }
    
    public function index_post($id)
    {
    	ci()->load->model('blog/blog_m');

    	$post = ci()->blog_m->get($id);

    	// Only index live articles
    	if ($post->status === 'live')
    	{
    		ci()->search_index_m->index(
    			'blog', 
    			'blog:post', 
    			'blog:posts', 
    			$id,
    			'blog/'.date('Y/m/', $post->created_on).$post->slug,
    			$post->title,
    			$post->intro, 
    			array(
    				'cp_edit_uri' 	=> 'admin/blog/edit/'.$id,
    				'cp_delete_uri' => 'admin/blog/delete/'.$id,
    				'keywords' 		=> $post->keywords,
    			)
    		);
    	}
    	// Remove draft articles
    	else
    	{
    		ci()->search_index_m->drop_index('blog', 'blog:post', $id);
    	}
	}

    public function drop_post($ids)
    {
    	foreach ($ids as $id)
    	{
			ci()->search_index_m->drop_index('blog', 'blog:post', $id);
		}
	}
    
    public function index_page($id)
    {
    	ci()->load->model('pages/page_m');

    	// Get the page (with the chunks)
    	$page = ci()->page_m->get($id);

    	// Only index live articles
    	if ($page->status === 'live')
    	{
    		ci()->search_index_m->index(
    			'pages', 
    			'pages:page', 
    			'pages:pages', 
    			$id,
    			$page->uri,
    			$page->title,
    			$page->meta_description ? $page->meta_description : null, 
    			array(
    				'cp_edit_uri' 	=> 'admin/pages/edit/'.$id,
    				'cp_delete_uri' => 'admin/pages/delete/'.$id,
    				'keywords' 		=> $page->meta_keywords,
    			)
    		);
    	}
    	// Remove draft articles
    	else
    	{
    		ci()->search_index_m->drop_index('pages', 'pages:page', $id);
    	}
	}

    public function drop_page($ids)
    {
    	foreach ($ids as $id)
    	{
			ci()->search_index_m->drop_index('pages', 'pages:page', $id);
		}
	}
}

/* End of file events.php */