<?php
use Illuminate\Support\Facades\DB;
use App\Models\User;

if (!function_exists('thumb')) {
	function thumb($file) 	{

		$pos = strrpos($file, DIRECTORY_SEPARATOR);

		if($pos !== false)
		{
			$file = substr_replace($file, DIRECTORY_SEPARATOR.'thumb_', $pos, 1);
		}
			
		return asset("uploads".DIRECTORY_SEPARATOR.$file);
	}
}

if (!function_exists('thumb_square')) {
	function thumb_square($file) 	{

		$pos = strrpos($file, DIRECTORY_SEPARATOR);

		if($pos !== false)
		{
			$file = substr_replace($file, DIRECTORY_SEPARATOR.'thumb_square_', $pos, 1);
		}
			
		return asset("uploads".DIRECTORY_SEPARATOR.$file);
	}
}


if (!function_exists('image')) {
	function image($file) 	{				
		return asset("uploads".DIRECTORY_SEPARATOR.$file);
	}
}


// format date
if (!function_exists('date_locale')) {
	function date_locale($date, $format = null) {
	
		$locale = DB::table('sys_lang')
            ->where('locale', App::getLocale())      
			->first();  
		$date_format = $locale->date_format;	

		if(! $format || $format == 'date') {
            return strftime ($date_format, strtotime($date));
		}

		if($format == 'datetime') {
            return strftime ($date_format.', %H:%M', strtotime($date));
		}

		if($format == 'datetimefull') {
			return date_format (new DateTime($date), $date_format. ', H:i:s');
		}

		if($format == 'daymonth') {
			return date_format (new DateTime($date), 'j M');
		}

		if($format == 'time') {
			return date_format (new DateTime($date), 'H:i');
		}

		if($format == 'timefull') {
			return date_format (new DateTime($date), 'H:i:s');
		}	

		return;
	}	
}

// Website general settings
if (!function_exists('site')) {
	function site() {

		$meta = DB::table('sys_lang')
			->where('id', active_lang()->id)
			->first();  
			
		$array = array('short_title' => $meta->site_short_title ?? null, 'meta_title' => $meta->homepage_meta_title ?? null, 'meta_description' => $meta->homepage_meta_description ?? null);
		
		return(json_decode(json_encode($array)));	        
    }
}


// Meta info from language / locale
if (!function_exists('lang_meta')) {
	function lang_meta() {
        $meta = DB::table('sys_lang')
			->where('id', active_lang()->id)
            ->first();   

        return $meta;                   
    }
}


// show content block 
if (!function_exists('block')) {
	function block($identificator) {			

		$active_lang_id = active_lang()->id ?? null;

		$block = array();

		if(! is_int($identificator)) {
			$identificator = str_replace('\'', '', $identificator);
			$identificator = str_replace('"', '', $identificator);
			$id = DB::table('blocks')->where('label', $identificator)->value('id');		
			if(! $id) {
				$block['content'] = null;
				$block['image'] = null;
				return(json_decode(json_encode($block)));	
			}	
		}
		else
			$id = $identificator;			
			
		$block = DB::table('blocks')
			->select(DB::raw("(SELECT content FROM blocks_content WHERE blocks_content.lang_id = $active_lang_id AND block_id = $id) as content"),
			DB::raw("(SELECT image FROM blocks_content WHERE blocks_content.lang_id = $active_lang_id AND block_id = $id) as image"))
			->where('id', $id)      
			->where('active', 1)  
			->first();  

		if(! $block) {
			$block['content'] = null;
			$block['image'] = null;
			return(json_decode(json_encode($block)));	
		}

		return $block;

		//return html_entity_decode($content);
	}	
}


// block group
if (!function_exists('block_group')) {
	function block_group($identificator) {	
		
		$group = array();
		$blocks = array();

		if(! is_int($identificator)) {
			$identificator = str_replace('\'', '', $identificator);
			$identificator = str_replace('"', '', $identificator);
			$id = DB::table('blocks_groups')->where('label', $identificator)->value('id');
			if(! $id) {
				$group['description'] = null;
				$group['blocks'] = array();
				return(json_decode(json_encode($group)));	
			}
		}
		else
			$id = $identificator;

		$group = DB::table('blocks_groups')				
			->where('id', $id)      
			->where('active', 1)  
			->first();  
		if(! $group) return json_decode(json_encode(array('description' => null, 'blocks' => array())));

		$items = DB::table('blocks_groups_content')				
			->where('group_id', $id)      
			->where('active', 1)  
			->orderBy('position', 'asc')  			
			->get();  

		foreach($items as $item) {
			$blocks[] = array('content' => $item->content, 'image' => $item->file);
		}
	
		$array = array('description' => $group->description, 'blocks' => $blocks);

		return json_decode(json_encode($array)); // array to object;

	}	
}



// Latest posts (all categories or category ID)
if (!function_exists('posts')) {
	function posts($categ_id = null) {
		
		$modules = DB::table('sys_modules')->where('status', 'active')->pluck('module')->toArray();   
		if(! in_array('posts', $modules)) return array();  

		$posts = DB::table('posts')
			->leftJoin('users', 'posts.user_id', '=', 'users.id')
			->leftJoin('posts_categ', 'posts.categ_id', '=', 'posts_categ.id')
			->select('posts.*', 'posts_categ.title as categ_title', 'posts_categ.slug as categ_slug', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar', 'users.slug as author_slug')
			->where('status', 'active')
			->where('posts_categ.active', 1) 
			->where('posts.lang_id', active_lang()->id ?? null);

		if($categ_id) {
			$categ = DB::table('posts_categ')->where('id', $categ_id)->where('posts_categ.active', 1)->first();  
        	$categ_tree_ids = $categ->tree_ids ?? null;
			if($categ_tree_ids) $categ_tree_ids_array = explode(',', $categ_tree_ids);
			$posts = $posts->whereIn('posts.categ_id', $categ_tree_ids_array ?? array());
		}

		$posts = $posts->orderBy('posts.featured', 'desc')          
			->orderBy('posts.id', 'desc')
			->paginate($config->posts_per_page ?? 24);

		return $posts;   
	}
}    


// generate URL for homepage
if (!function_exists('homepage')) {
	function homepage() {      		
		return route('homepage', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
	}	
}


// generate URL for docs category, using category ID
// generate URL for docs area, inf no category ID is passed
if (!function_exists('docs_url')) {
	function docs_url($categ_id = null) {      		
		if(! $categ_id)   		
			return route('docs', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
		else {
			$categ = DB::table('docs_categ')
            	->where('id', $categ_id)      
				->first();
			if(! $categ) return;	
			return route('docs.categ', ['lang' => (lang($categ->lang_id)->id == default_lang()->id) ? null : lang($categ->lang_id)->code, 'slug' => $categ->slug]);
		}
	}	
}


// generate URL for docs search results
if (!function_exists('search_docs_url')) {
	function search_docs_url() {   		
		return route('docs.search', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);		
	}	
}


// generate URL for post category, using category ID
// generate URL for posts area, inf no category ID is passed
if (!function_exists('posts_url')) {
	function posts_url($categ_id = null) {   
		if(! $categ_id)   		
			return route('posts', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
		else {
			$categ = DB::table('posts_categ')
            	->where('id', $categ_id)      
				->first();
			if(! $categ) return;	
			return route('posts.categ', ['lang' => (lang($categ->lang_id)->id == default_lang()->id) ? null : lang($categ->lang_id)->code, 'slug' => $categ->slug]);
		}
	}	
}


// generate URL for post, using post ID
if (!function_exists('post_url')) {
	function post_url($id) {
	
        $post = DB::table('posts')
            ->leftJoin('posts_categ', 'posts.categ_id', '=', 'posts_categ.id') 
            ->select('posts.lang_id', 'posts.slug', 'posts_categ.slug as categ_slug') 
			->where('posts.id', $id)      
			->where('posts.status', 'active')  
			->first();
		if(! $post) return null;

		// check if language is active
		if (! DB::table('sys_lang')->where('id', $post->lang_id)->where('status', 'active')->exists()) return null;
		
		//return route('post', ['lang' => (default_lang()->id != $post->lang_id) ? lang($post->lang_id)->code : null, 'categ_slug' => $post->categ_slug, 'slug' => $post->slug]);
		return route('post', ['lang' => (default_lang()->id == active_lang()->id) ? null : active_lang()->code, 'categ_slug' => $post->categ_slug, 'slug' => $post->slug]);

	}	
}


// generate URL for posts search results
if (!function_exists('posts_search_url')) {
	function posts_search_url() {   		
		return route('posts.search', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);		
	}	
}


// generate URL for posts tag
if (!function_exists('posts_tag_url')) {
	function posts_tag_url($tag) {   		
		if(! $tag) return null;
		return route('posts.tag', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code, 'slug' => $tag]);		
	}	
}


// generate URL for submitting a comment
if (!function_exists('posts_submit_comment_url')) {
	function posts_submit_comment_url($categ_slug, $post_slug) {   		
		if(! $categ_slug || ! $post_slug) return null;
		return route('post.comment', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code, 'categ_slug' => $categ_slug, 'slug' => $post_slug]);		
	}	
}


// generate URL for submitting a like
if (!function_exists('posts_submit_like_url')) {
	function posts_submit_like_url($categ_slug, $post_slug) {   		
		if(! $categ_slug || ! $post_slug) return null;
		return route('post.like', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code, 'categ_slug' => $categ_slug, 'slug' => $post_slug]);		
	}	
}


// generate URL for download item, using itemn ID
// generate URL for downloads index, inf no category ID is passed
if (!function_exists('download_url')) {
	function download_url($id = null) {   
			
		if(! $id)
			return route('downloads', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
		else {	
			$download = DB::table('downloads')
				->leftJoin('downloads_langs', 'downloads_langs.download_id', '=', 'downloads.id') 
				->select('downloads.*')			
				->where('downloads.id', $id)      
				->where('downloads.active', 1)      
				->first();
			if(! $download)	return null;			
			
			return route('download', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code, 'slug' => $download->slug]) ;
		}
		
	}	
}



// generate URL for profile, from user ID
if (!function_exists('profile_url')) {
	function profile_url($user_id) {
	
        $user = DB::table('users')
			->where('id', $user_id)      
			->where('active', 1)      
			->first();
		if(! $user) return null;
		
		return route('profile', ['lang' => (default_lang()->id == active_lang()->id) ? null : active_lang()->code, 'id' => $user->id, 'slug' => $user->slug]);

	}	
}



// SINGLE page details.
// if identificator is INTEGER, get page from ID
// if identificator is STRING, get page from badge. If there are multiple pages with same badge (same language), only first page is returned
if (!function_exists('page')) {
	function page($identificator) {

		if(! is_int($identificator)) {
			$page = DB::table('pages')
				->select('pages.*', 'pages.parent_id as parent_page_id', DB::raw('(SELECT slug FROM pages WHERE id = parent_page_id) as parent_slug'))  
				->where('active', 1)
				->whereRaw("FIND_IN_SET(?, badges) > 0", [$identificator])		
				->where('lang_id', active_lang()->id)
				->first();
		}
		else {
			$page = DB::table('pages')
				->select('pages.*', 'pages.parent_id as parent_page_id', DB::raw('(SELECT slug FROM pages WHERE id = parent_page_id) as parent_slug'))  
				->where('active', 1)
				->where('id', $identificator)
				->first();	
		}     


		if(! $page) return null;		

		if($page->parent_slug) // page is child of a parent page
			$page->url = route('child_page', ['lang' => (default_lang()->id != $page->lang_id) ? lang($page->lang_id)->code : null, 'slug' => $page->slug, 'parent_slug' => $page->parent_slug]);
		else			
			$page->url = route('page', ['lang' => (default_lang()->id != $page->lang_id) ? lang($page->lang_id)->code : null, 'slug' => $page->slug]);

        return $page;                   
    }
}


// Get page details with a specific badge
if (!function_exists('pages')) {
	function pages($badge) {							
        $items = DB::table('pages')
			->where('active', 1)
			->whereRaw("FIND_IN_SET(?, badges) > 0", [$badge])		
			->where('lang_id', active_lang()->id)
			->orderBy('title', 'asc')
            ->paginate(24);   

		return $items;      			
    }
}


// generate URL for static page, using page ID
if (!function_exists('page_url')) {
	function page_url($id) {
	
		$page = DB::table('pages')
			->select('pages.*', 'pages.parent_id as parent_page_id', DB::raw('(SELECT slug FROM pages WHERE id = parent_page_id) as parent_slug'))    
            ->where('id', $id)      
			->first();
		if(! $page) return null;	

		if($page->parent_slug) // page is child of a parent page
			return route('child_page', ['lang' => (default_lang()->id != $page->lang_id) ? lang($page->lang_id)->code : null, 'slug' => $page->slug, 'parent_slug' => $page->parent_slug]);
		else			
			return route('page', ['lang' => (default_lang()->id != $page->lang_id) ? lang($page->lang_id)->code : null, 'slug' => $page->slug]);

		//return route('page', ['lang' => (default_lang()->id == active_lang()->id) ? null : active_lang()->code, 'slug' => $page->slug]);
	}	
}


// get images gallery for a specific page
if (!function_exists('page_images')) {
	function page_images($id) {
	
		$images = DB::table('pages_images')
			->where('page_id', $id)      
			->orderBy('id', 'asc')      
			->get();
		if(! $images) return array();	

		return $images;

	}	
}



// generate URL for contact page
if (!function_exists('contact_url')) {
	function contact_url() {      		
		return route('contact', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
	}	
}


// generate URL for FAQ page
if (!function_exists('faq_url')) {
	function faq_url() {      		
		return route('faq', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
	}	
}


// FAQ items
if (!function_exists('faq_items')) {
	function faq_items() {	

		$active_lang_id = active_lang()->id ?? null;

		$items = DB::table('faq')
			->where('active', 1) 
			->where(function ($query) use ($active_lang_id) {
				$query->where('lang_id', $active_lang_id)
					->orWhereNull('lang_id');
				})
			->orderBy('position', 'asc')
			->get();  
					
		return $items;
	}	
}


// show slider 
if (!function_exists('slides')) {
	function slides() {	

		$active_lang_id = active_lang()->id ?? null;

		$slides = DB::table('slider')
			->where('active', 1) 
			->where(function ($query) use ($active_lang_id) {
				$query->where('lang_id', $active_lang_id)
					->orWhereNull('lang_id');
				})
			->orderBy('position', 'asc')
			->get();  
					
		return $slides;
	}	
}


// generate URL for commerce category, using category ID
// generate URL for commerce area, inf no category ID is passed
if (!function_exists('cart_url')) {
	function cart_url($categ_id = null) {      		
		if(! $categ_id)   		
			return route('cart', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
		else {			
			$categ = DB::table('cart_categ')
				->where('id', $categ_id)  
				->where('active', 1)        
				->first();
			if(! $categ) return;	
			return route('cart.categ', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code, 'slug' => $categ->slug]);
		}
	}	
}


// Latest products (all categories or category ID)
// If featured = 1, list featured items first
if (!function_exists('cart_products')) {
	function cart_products($categ_id = null, $order_option = null) {
		
		$modules = DB::table('sys_modules')->where('status', 'active')->pluck('module')->toArray();   
		if(! in_array('cart', $modules)) return array();  

		$products = DB::table('cart_products')
			->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
			->select('cart_products.*', 'cart_categ.title as categ_title', 'cart_categ.slug as categ_slug')
			->where('status', 'active')
			->where('hidden', 0)    
			->where('cart_categ.active', 1);

		if($categ_id) {
			$categ = DB::table('cart_categ')->where('id', $categ_id)->where('cart_categ.active', 1)->first();  
        	$categ_tree_ids = $categ->tree_ids ?? null;
			if($categ_tree_ids) $categ_tree_ids_array = explode(',', $categ_tree_ids);
			$products = $products->whereIn('cart.categ_id', $categ_tree_ids_array ?? array());
		}

		if(! $order_option) $order_option = 'featured';
		
		if($order_option == 'featured_only') $products = $products->where('cart_products.featured', 1);
		if($order_option == 'featured') $products = $products->orderBy('cart_products.featured', 'desc');
		if($order_option == 'latest') $products = $products->orderBy('cart_products.id', 'desc');

		$products = $products->orderBy('cart_products.id', 'desc')->paginate($config->posts_per_page ?? 12);

		return $products;   
	}
}    



// generate URL for cart search results
if (!function_exists('cart_search_url')) {
	function cart_search_url() {   		
		return route('cart.search', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
	}	
}


// generate URL for cart product, using cart ID
if (!function_exists('cart_product_url')) {
	function cart_product_url($id) {
	
        $product = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id') 
            ->select('cart_products.*', 'cart_categ.title as categ_title', 'cart_categ.slug as categ_slug') 
			->where('cart_products.id', $id)      
			->where('cart_products.status', 'active')      
			->first();
		if(! $product) return null;	
				
		return route('cart.product', ['lang' => (default_lang()->id == active_lang()->id) ? null : active_lang()->code, 'categ_slug' => $product->categ_slug, 'slug' => $product->slug]);

	}	
}


// Cart categories with a specific badge
if (!function_exists('badge_cart_categ')) {
	function badge_cart_categ($badge) {
        $items = DB::table('cart_categ')
			->where('active', 1)
			->whereRaw("FIND_IN_SET(?, badges) > 0", [$badge])		
			->orderBy('position', 'asc')
			->orderBy('title', 'asc')
            ->paginate(24);   

        return $items;                   
    }
}


// generate URL for community category, using category ID
// generate URL for community home, inf no category ID is passed
if (!function_exists('forum_url')) {
	function forum_url($categ_id = null) {      		
		if(! $categ_id)   		
			return route('forum', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
		else {
			$categ = DB::table('docs_categ')
            	->where('id', $categ_id)      
				->first();
			if(! $categ) return;	
			return route('forum.categ', ['lang' => (lang($categ->lang_id)->id == default_lang()->id) ? null : lang($categ->lang_id)->code, 'slug' => $categ->slug]);
		}
	}	
}


// latest forum topics
if (!function_exists('forum_topics')) {
	function forum_topics() {
        $topics = DB::table('forum_topics')
            ->leftJoin('forum_categ', 'forum_topics.categ_id', '=', 'forum_categ.id')            
            ->leftJoin('users', 'forum_topics.user_id', '=', 'users.id')
            ->select('forum_topics.*', 'forum_categ.title as categ_title', 'forum_categ.slug as categ_slug', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar')
        	->where('forum_topics.status', 'active')                
			->orderBy('forum_topics.id', 'desc')
			->paginate(24);		
        return $topics;                   
    }
}


// latest forum posts
if (!function_exists('forum_posts')) {
	function forum_posts() {
		$posts = DB::table('forum_posts')
			->leftJoin('forum_categ', 'forum_posts.categ_id', '=', 'forum_categ.id')
			->leftJoin('forum_topics', 'forum_posts.topic_id', '=', 'forum_topics.id')
			->leftJoin('users', 'forum_posts.user_id', '=', 'users.id')
			->select('forum_posts.*', 'forum_categ.title as categ_title', 'forum_categ.slug as categ_slug', 'forum_topics.id as topic_id', 'forum_topics.title as topic_title', 'forum_topics.slug as topic_slug', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar')
			->where('forum_topics.status', 'active')
			->orderBy('forum_posts.id', 'desc')
			->paginate(24);

        return $posts;                   
    }
}


if (!function_exists('account_url')) {
	function account_url() {

		if(logged_user()) {
			if(logged_user()->role == 'admin') return route('admin');
			else if(logged_user()->role == 'internal') return route('internal');
			else if(logged_user()->role == 'user') return route('user');
			else return route('homepage', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);
		}
		
		else return route('homepage', ['lang' => (active_lang()->id == default_lang()->id) ? null : active_lang()->code]);

	}
}

