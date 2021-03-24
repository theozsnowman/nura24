<?php
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Core;

if (!function_exists('logged_user')) {
	function logged_user() 	{

		//$logged_user = array('id' => null, 'name' => null, 'role' => null, 'role_id' => null, 'avatar' => null, 'email_verified_at' => null, 'count_basket_items' => null, 'count_unpaid_orders' => null);

        // auth
        if (Auth::check()) {              
            $UserModel = new User();

            $logged_user_role = $UserModel->get_role_from_id(Auth::user()->role_id);        

            DB::table('users')->where('id', Auth::user()->id)->update(['last_activity' => now()]);                

            if($logged_user_role == 'user') {
                $count_basket_items = DB::table('cart_shopping_cart')->where('user_id', Auth::user()->id)->count();                     
                $count_unpaid_orders = DB::table('cart_orders')->where('user_id', Auth::user()->id)->where('is_paid', 0)->count();  
            }

			$logged_user = array('id' => Auth::user()->id, 'name' => Auth::user()->name, 'role' => $logged_user_role, 'role_id' => Auth::user()->role_id, 'avatar' => Auth::user()->avatar, 'email_verified_at' => Auth::user()->email_verified_at, 'count_basket_items' => $count_basket_items ?? null, 'count_unpaid_orders' => $count_unpaid_orders ?? null);
		
			return (object)$logged_user;
		}   
			 
		else return null;		

	}
}


// get active languages
if (!function_exists('languages')) {
	function languages() {
		$languages = DB::table('sys_lang')->where('status', 'active')->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();              
		return (object)$languages;
	}
}


// get all languages (active and inactive)
if (!function_exists('sys_langs')) {
	function sys_langs() {
		$sys_langs = DB::table('sys_lang')->where('status', 'active')->orWhere('status', 'inactive')->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();              
		return (object)$sys_langs;
	}
}


// check if a module is active
if (!function_exists('check_module')) {
	function check_module($module) {

		if (! $module) return false;

		$modules = DB::table('sys_modules')
			->where('status', 'active')    
			->pluck('module')
			->toArray();         

		if(in_array($module, $modules)) return true;
		else return false;
	}
}



// check if a module is active or inactive (not disabled)
if (!function_exists('check_admin_module')) {
	function check_admin_module($module) {

		if (! $module) return false;

		$modules = DB::table('sys_modules')
			->where('status', 'active')    
			->orWhere('status', 'inactive') 
			->pluck('module')
			->toArray();         

		if(in_array($module, $modules)) return true;
		else return false;
	}
}

// check if user can post signature
if (!function_exists('check_forum_signature')) {
	function check_forum_signature($user_id) {

		if (! $user_id) return false;
		
		$user = DB::table('users')->where('active', 1)->where('id', $user_id)->whereNotNull('email_verified_at')->first();        
		if (! $user) return false;

		$UserModel = new User();
		$config = Core::config();  

		$user_created_at = $user->created_at;
        $dCreated = new \DateTime($user_created_at);
        $dNow  = new \DateTime(now());
        $dDiff = $dCreated->diff($dNow);    
		$registration_days = $dDiff->format('%a') ?? 0;
		
		$count_forum_posts = $UserModel->get_user_extra($user_id, 'count_forum_posts') ?? 0;
        $count_forum_topics = $UserModel->get_user_extra($user_id, 'count_forum_topics') ?? 0;
        $count_forum_likes_received = $UserModel->get_user_extra($user_id, 'count_forum_likes_received') ?? 0;					
		
		if(($config->forum_signatures_enabled ?? null) =='no') return false;
		elseif(isset($config->forum_signature_min_posts_required) and ($config->forum_signature_min_posts_required ?? null) > $count_forum_posts) return false;
		elseif(isset($config->forum_signature_min_topics_required) and ($config->forum_signature_min_topics_required ?? null) > $count_forum_topics) return false;
		elseif(isset($config->forum_signature_min_likes_required) and ($config->forum_signature_min_likes_required ?? null) > $count_forum_likes_received) return false;
		elseif(isset($config->forum_signature_min_days_required) and ($config->forum_signature_min_days_required ?? null) > $registration_days) return false;

		else return true;
	}
}

/*
// get language from ID (if $term is numeric) or code (if $term is not numeric)
// return Array
*/
if (!function_exists('lang')) {
	function lang($term) {
			
		$lang = DB::table('sys_lang');
		
		if(is_numeric($term))
			$lang = $lang->where('id', $term);     
		else	
			$lang = $lang->where('code', $term);
			
		$lang = $lang->first();  

		return $lang;
	}	
}


// get active language
if (!function_exists('active_lang')) {
	function active_lang() {
	
		$lang = DB::table('sys_lang')
            ->where('locale', App::getLocale() ?? null)      
			->first();  
			
		return $lang;
	}	
}


// get default language
if (!function_exists('default_lang')) {
	function default_lang() {
	
		$lang = DB::table('sys_lang')
            ->where('is_default', 1)      
			->first();  
			
		return $lang;
	}	
}


// get user extra details
if (!function_exists('user_extra')) {    
    function user_extra ($user_id, $extra_key) {         

        // get key id
        $q = DB::table('users_extra_keys')->where('extra_key', $extra_key)->first();     
        if($q) $key_id = $q->id;
        else return null;
                
        // get value
        $value = DB::table('users_extra_values')->where('key_id', $key_id)->where('user_id', $user_id)->value('value');     
        
        if(!isset($value) or $value=='') return null;
        else return $value;
	}
}


// create breadcrumb for categories
if (!function_exists('breadcrumb_items')) {   
	function breadcrumb_items($categ_id, $section = null) {
			
		if(! $section) $section = 'posts';
		
		if($section == 'posts') $table = 'posts_categ';
		elseif($section == 'docs') $table = 'docs_categ';
		elseif($section == 'cart') $table = 'cart_categ';
		elseif($section == 'forum') $table = 'forum_categ';
		else return array();

		$lang_id = (active_lang()->id == default_lang()->id) ? default_lang()->id : active_lang()->id;

		if($section=='forum') {
			$categ = DB::table($table)				
				->where($section.'_categ.id', $categ_id)
				->first();    
			if(!$categ) return array();
		}
		elseif ($section == 'cart') {
			$categ = DB::table('cart_categ')	
				->select('cart_categ.*', 
					DB::raw("(SELECT title FROM cart_categ_langs WHERE cart_categ_langs.lang_id = $lang_id AND cart_categ_langs.categ_id = cart_categ.id) as translated_title"))
				->where('cart_categ.id', $categ_id)
				->first();  
			if(! $categ) return array();
			if($categ->translated_title) $categ->title = $categ->translated_title;  
		} 
		else {
			$categ = DB::table($table)
				->leftJoin('sys_lang', $table.'.lang_id', '=', 'sys_lang.id')    
				->select($table.'.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang_code')
				->where($section.'_categ.id', $categ_id)
				->first();    
			if(!$categ) return array();
		}		

		$items[] = array('id'=>$categ->id, 'title' => $categ->title, 'slug' => $categ->slug, 'active' => $categ->active, 'icon' => $categ->icon, 'count_tree_items' => $categ->count_tree_items ?? null, 'lang_name' => $categ->lang_name ?? null, 'lang_code' => $categ->lang_code ?? null, 'count_tree_topics' => $categ->count_tree_topics ?? null, 'count_tree_posts' => $categ->count_tree_posts ?? null);			

		$parent_id = $categ->parent_id;			
		if($parent_id) {
			$items =  array_merge($items, breadcrumb_items($parent_id, $section));					
		}

		$items = json_decode(json_encode($items)); // array to object;
		return ($items);
	}
}


if (!function_exists('breadcrumb')) {   
	function breadcrumb($categ_id, $section = null) {	
		
		if(! $section) $section = 'posts';

		if(!$categ_id) return array();
		if(! is_array(breadcrumb_items($categ_id, $section))) return array();

		return array_reverse(breadcrumb_items($categ_id, $section));
	}
}


// create categ tree of the given category for docs 
if (!function_exists('docs_categ_tree')) {   
	function docs_categ_tree($categ_id = null) {

		$items = array();

		// get active lang
		$lang = DB::table('sys_lang')
            ->where('code', App::getLocale())      
			->first();  

		$q = DB::table('docs_categ')->where('lang_id', active_lang()->id)->where('parent_id', $categ_id)->where('active', 1)->orderBy('position', 'asc')->get();  
		foreach($q as $categ) {  
			$items[] = array('id' => $categ->id, 'title' => $categ->title, 'description' => $categ->description, 'icon' => $categ->icon, 'slug' => $categ->slug, 'count_items' => $categ->count_items, 'count_tree_items' => $categ->count_tree_items, 'tree_ids' => explode(',', $categ->tree_ids), 'children' => docs_categ_tree($categ->id));			
		}
		
		return json_decode(json_encode($items)); // array to object;
	}
}


// create categ tree of the given category for eCommerce 
if (!function_exists('cart_categ_tree')) {   
	function cart_categ_tree($categ_id = null) {

		$items = array();
		$lang_id = (active_lang()->id == default_lang()->id) ? default_lang()->id : active_lang()->id;

		$q = DB::table('cart_categ')			
			->select('cart_categ.*', 
				DB::raw("(SELECT title FROM cart_categ_langs WHERE cart_categ_langs.lang_id = $lang_id AND cart_categ_langs.categ_id = cart_categ.id) as translated_title"), 
				DB::raw("(SELECT description FROM cart_categ_langs WHERE cart_categ_langs.lang_id = $lang_id AND cart_categ_langs.categ_id = cart_categ.id) as translated_description"), 
				DB::raw("(SELECT meta_title FROM cart_categ_langs WHERE cart_categ_langs.lang_id = $lang_id AND cart_categ_langs.categ_id = cart_categ.id) as translated_meta_title"), 
				DB::raw("(SELECT meta_description FROM cart_categ_langs WHERE cart_categ_langs.lang_id = $lang_id AND cart_categ_langs.categ_id = cart_categ.id) as translated_meta_description"))		
			->where('parent_id', $categ_id)
			->where('active', 1)
			->orderBy('position', 'asc')
			->get();  
		foreach($q as $categ) {  
			$items[] = array('id' => $categ->id, 'title' => $categ->translated_title ?? $categ->title, 'description' => $categ->translated_description ?? $categ->description, 'meta_title' => $categ->translated_meta_title ?? $categ->meta_title, 'meta_description' => $categ->translated_meta_description ?? $categ->meta_description, 'icon' => $categ->icon, 'slug' => $categ->slug, 'count_items' => $categ->count_items, 'count_tree_items' => $categ->count_tree_items, 'tree_ids' => explode(',', $categ->tree_ids), 'children' => docs_categ_tree($categ->id));			
		}
		
		return json_decode(json_encode($items)); // array to object;
	}
}

// create categ tree of the given category for downloads 
if (!function_exists('downloads_categ_tree')) {   
	function downloads_categ_tree($categ_id = null) {

		$items = array();	

		$q = DB::table('downloads_categ')->where('parent_id', $categ_id)->where('active', 1)->orderBy('position', 'asc')->get();  
		foreach($q as $categ) {  
			$items[] = array('id' => $categ->id, 'title' => $categ->title, 'description' => $categ->description, 'icon' => $categ->icon, 'slug' => $categ->slug, 'count_items' => $categ->count_items, 'count_tree_items' => $categ->count_tree_items, 'tree_ids' => explode(',', $categ->tree_ids), 'children' => downloads_categ_tree($categ->id));			
		}
		
		return json_decode(json_encode($items)); // array to object;
	}
}


// create categ tree of the given category for forum 
if (!function_exists('forum_structure')) {   
	function forum_categ_tree($categ_id = null) {

		$items = array();		

		$q = DB::table('forum_categ')->where('parent_id', $categ_id)->where('active', 1)->orderBy('position', 'asc')->get();  
		foreach($q as $categ) {
			$latest_topic = array();
			$latest_post = array();
			$latest_activity = null;
			$categ_tree_ids = $categ->tree_ids ?? null;
			if($categ_tree_ids) $categ_tree_ids_array = explode(',', $categ_tree_ids);
		
			$latest_topic_q = DB::table('forum_topics')
				->leftJoin('users', 'forum_topics.user_id', '=', 'users.id') 
            	->select('forum_topics.*', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar') 
				->whereIn('forum_topics.categ_id', $categ_tree_ids_array)
				->where('status', '!=', 'deleted')
				->orderBy('forum_topics.id', 'desc')
				->first();  

			if($latest_topic_q) {
				$latest_topic = array('id'=>$latest_topic_q->id, 'slug'=>$latest_topic_q->slug, 'title'=>$latest_topic_q->title, 'created_at'=>$latest_topic_q->created_at, 'author_name'=>$latest_topic_q->author_name, 'author_slug'=>$latest_topic_q->author_slug, 'author_avatar'=>$latest_topic_q->author_avatar);
			}

			$latest_post_q = DB::table('forum_posts')
				->leftJoin('users', 'forum_posts.user_id', '=', 'users.id') 
				->leftJoin('forum_topics', 'forum_posts.topic_id', '=', 'forum_topics.id') 
            	->select('forum_posts.*', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar', 'forum_topics.title as topic_title', 'forum_topics.slug as topic_slug') 
				->whereIn('forum_posts.categ_id', $categ_tree_ids_array)
				->orderBy('forum_posts.id', 'desc')
				->first();  

			if($latest_post_q) {
				$latest_post = array('id'=>$latest_post_q->id, 'created_at'=>$latest_post_q->created_at, 'author_name'=>$latest_post_q->author_name, 'author_slug'=>$latest_post_q->author_slug, 'author_avatar'=>$latest_post_q->author_avatar, 'topic_id'=>$latest_post_q->topic_id, 'topic_title'=>$latest_post_q->topic_title, 'topic_slug'=>$latest_post_q->topic_slug);
			}
			
			// latest activity
			if($latest_post_q and $latest_topic_q) {			
				if($latest_post_q->created_at >= $latest_topic_q->created_at) $latest_activity = 'post';
				else $latest_activity = 'topic';
			}
			if($latest_post_q and !$latest_topic_q)  $latest_activity = 'post';
			if(!$latest_post_q and $latest_topic_q)  $latest_activity = 'topic';
			
			
			$items[] = array(
				'id' => $categ->id, 
				'title' => $categ->title, 
				'description' => $categ->description, 
				'icon' => $categ->icon, 
				'slug' => $categ->slug, 
				'count_topics' => $categ->count_topics, 
				'count_tree_topics' => $categ->count_tree_topics, 
				'count_posts' => $categ->count_posts, 
				'count_tree_posts' => $categ->count_tree_posts, 
				'latest_topic' => $latest_topic, 
				'latest_post' => $latest_post, 
				'latest_activity' => $latest_activity ?? null, 
				'tree_ids'=> explode(',', $categ->tree_ids), 
				'children' => forum_categ_tree($categ->id)
			);			
		}
		
		return json_decode(json_encode($items)); // array to object;
	}
}


if (!function_exists('chekbox_permissions')) {
	function chekbox_permissions($permission_id, $user_id){		
				
		$exists = DB::table('users_permissions')->where('permission_id', $permission_id)->where('user_id', $user_id)->exists();

		if($exists == 1)
			return true;
		else    
			return false;

	}
}


if (!function_exists('check_access')) {
	function check_access($module, $permission = null) {
	
		$UserModel = new User(); 
		$logged_user_role = $UserModel->get_role_from_id(Auth::user()->role_id);   
		if($logged_user_role == 'admin') return true;

		$module = DB::table('sys_modules')->where('module', $module)->first();
		if(! $module) return false;		

		if($permission) {
			$permission = DB::table('sys_permissions')->where('module_id', $module->id)->where('permission', $permission)->first();
			if(! $permission) return false;

			if(DB::table('users_permissions')->where('module_id', $module->id)->where('permission_id', $permission->id)->where('user_id', Auth::user()->id)->exists())
				return true;
			else	
				return false;
		} else {
			if(DB::table('users_permissions')->where('module_id', $module->id)->where('user_id', Auth::user()->id)->exists())
				return true;
			else	
				return false;
		}

	}	
}


// generate template languages
if (!function_exists('template_langs')) {
	function template_langs(){		
				
		$navigation = array();
		
		$active_langs = DB::table('sys_lang')->where('status', 'active')->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get();

		foreach ($active_langs as $active_lang) {

			if($active_lang->is_default == 1)
				//$url = preg_replace("/$current_lang/", '', $current_path, 1);
				$url = route('homepage' );
			else
				$url = route('homepage', ['lang' => $active_lang->code ?? null]);

			array_push($navigation, ['name' => $active_lang->name, 'lang' => $active_lang->code, 'is_default' => $active_lang->is_default, 'url' => $url ?? null] );
		}
		
		return json_decode(json_encode($navigation)); // array to object;;

	}
}


if (!function_exists('delete_image')) {
	function delete_image($file) 	{

		// delete main image
		$filename = getcwd().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$file;

		if (file_exists($filename)) @unlink($filename);			
				
		// delete thumb, if exists
		$pos = strrpos($file, DIRECTORY_SEPARATOR);
		if($pos !== false)
		{
			$file = substr_replace($file, DIRECTORY_SEPARATOR.'thumb_', $pos, 1);
		}

		// delete thumb square, if exists
		$pos = strrpos($file, DIRECTORY_SEPARATOR);
		if($pos !== false)
		{
			$file = substr_replace($file, DIRECTORY_SEPARATOR.'thumb_square_', $pos, 1);
		}

		$filename = getcwd().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$file; // thumb
		if (file_exists($filename)) @unlink($filename);
		
		return;
	}
}


if (!function_exists('delete_file')) {
	function delete_file($file) 	{
		// delete file
		$filename = getcwd().DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$file;
		if (file_exists($filename)) @unlink($filename);									
		return;
	}
}


// prepend lang code if active language is not default language. Return null if active language is default language
if (!function_exists('prepend_lang')) {
	function prepend_lang($lang_id) {		

		if(default_lang()->id == $lang_id) return null;
		else return lang($lang_id)->code;
	}	
}


if (!function_exists('estimated_reading_time')) {
	function estimated_reading_time($post_id) {

		$post = DB::table('posts')->where('id', $post_id)->first();
		if(! $post) return null;

		$words = str_word_count( strip_tags( $post->content ) );
		$minutes = (int)( $words / 120 );
		$seconds = (int)( $words % 120 / ( 120 / 60 ) );	

		if($minutes == 0 && $seconds > 0) $minutes = 1;
		return $minutes;
	}
}


// doc images
if (!function_exists('doc_images')) {
	function doc_images($id){		
				
		$images = array();
		
		$images = DB::table('docs_images')->where('docs_images.doc_id', $id)->orderBy('id', 'asc')->get();

		return $images; 

	}
}


// default currency
if (!function_exists('default_currency')) {
	function default_currency() {
		$currency = DB::table('cart_currencies')
			->where('is_default', 1)      
			->where('active', 1)      
			->first();  
			
		return $currency;
	}	
}


// default currency
if (!function_exists('currency')) {
	function currency($id) {
		$currency = DB::table('cart_currencies')
			->where('id', $id)      
			->first();  
			
		return $currency;
	}	
}


// price format
if (!function_exists('price')) {
	function price($amount, $currency_id = null) {		

        // active currency 
        if(! $currency_id) 
            $currency = DB::table('cart_currencies')->where('is_default', 1)->first();                   
        else
			$currency = DB::table('cart_currencies')->where('id', $currency_id)->first(); 		  		       			

		$value = number_format($amount,2,$currency->d_separator ?? '.', $currency->t_separator ?? ',');		
		//$value = $value + 0;

		switch ($currency->style) {
			case 'value_code':
				$price = $value.' '.$currency->code;
				break;
			case 'code_value':
				$price = $currency->code.' '.$value;
				break;
			case 'value_symbol':
				$price = $value.' '.$currency->symbol;
				break;
			case 'symbol_value':
				$price = $currency->symbol.' '.$value;
				break;
			default:
				$price = $value.' '.$currency->code;
				break;
		}

		if($currency->condensed==1) $price = str_replace(' ', '', $price);
		
		return $price;
	}	
}


// order items
if (!function_exists('cart_order_items')) {
	function cart_order_items($order_id){		
				
		$items = array();
		
		$items = DB::table('cart_orders_items')			
			->where('order_id', $order_id)
			->orderBy('price', 'desc')
			->get();

		return $items; 

	}
}


if (!function_exists('forum_topic_reports')) {    
    function forum_topic_reports ($topic_id) {         
		return  DB::table('forum_reports')
			->leftJoin('users', 'forum_reports.from_user_id', '=', 'users.id')	
			->select('forum_reports.*', 'users.name as from_name', 'users.email as from_email', 'users.avatar as from_avatar')
            ->where('topic_id', $topic_id)
            ->orderBy('id', 'desc')
			->paginate(20);             
	}
}


if (!function_exists('forum_post_reports')) {    
    function forum_post_reports ($post_id) {         
		return  DB::table('forum_reports')
			->leftJoin('users', 'forum_reports.from_user_id', '=', 'users.id')	
			->select('forum_reports.*', 'users.name as from_name', 'users.email as from_email', 'users.avatar as from_avatar')
            ->where('post_id', $post_id)
            ->orderBy('id', 'desc')
			->paginate(20);             
	}
}


// forum user statistics
if (!function_exists('forum_user_info')) {   
	function forum_user_info($user_id) {
				
		$user = DB::table('users')->where('id', $user_id)->first();    

		if(!$user) return null;

		$count_topics = DB::table('forum_topics')->where('user_id', $user_id)->where('status', 'active')->count();
		$count_posts = DB::table('forum_posts')->where('user_id', $user_id)->count();

		$items[] = array('count_topics'=>$count_topics ?? 0, 'count_posts'=>$count_posts ?? 0, 'slug'=>$categ->slug);			

		$parent_id = $categ->parent_id;			
		if($parent_id) {
			$items =  array_merge($items, nura_forum_categ_breadcrumb($parent_id));		
		}

		return json_decode(json_encode($items)); // array to object;
	}
}


// check if user like forum cointent (post or topic)
if (!function_exists('forum_check_like')) {    
    function forum_check_like ($type, $content_id) {         

		if(!Auth::check()) return;

		if($type=='post') {
			$check = DB::table('forum_likes')->where('user_id', Auth::user()->id)->where('post_id', $content_id)->first();     
			if($check) return true;
			else return false;				
		}
		
		if($type=='topic') {
			$check = DB::table('forum_likes')->where('user_id', Auth::user()->id)->where('topic_id', $content_id)->first();     
			if($check) return true;
			else return false;				
		}

		return;
	}
}


// check if user mark a topic as best answer
if (!function_exists('forum_check_best_answer')) {    
    function forum_check_best_answer ($post_id) {         

		if(!Auth::check()) return;
		
		$check = DB::table('forum_best_answers')->where('user_id', Auth::user()->id)->where('post_id', $post_id)->first();     
		if($check) return true;
		else return false;	
	}				
}



if (!function_exists('forum_attachments')) {
	function forum_attachments($id, $type) {

		if($type=='topic') {
			$images = DB::table('forum_attachments')
				->where('topic_id', $id)
				->whereNull('post_id')
	            ->orderBy('id', 'desc')
				->paginate(24);   
		}

		if($type=='post') {
			$images = DB::table('forum_attachments')
				->where('post_id', $id)
	            ->orderBy('id', 'desc')
				->paginate(24);   
		}

        return $images ?? null;                   
    }
}	


if (!function_exists('generate_sitemap')) {
	function generate_sitemap() {
			//header('Content-type: application/xml');
					
			$file_xml = public_path('sitemap.xml');						

			$fp = fopen($file_xml,'w');		

			$data_header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
			fwrite($fp,$data_header);


			// First page
			$data = "\n
			<url>
				<loc>".config('app.url')."</loc>
				<changefreq>daily</changefreq>
				<priority>1</priority>
			</url>";
			fwrite($fp,$data);

			// pages
			$pages = DB::table('pages')
				->leftJoin('sys_lang', 'pages.lang_id', '=', 'sys_lang.id')
				->select('pages.id')
				->where('pages.active', 1)
				->where('sys_lang.status', 'active')
				->orderBy('pages.id', 'desc')
				->get();   
			foreach ($pages as $page) {
				$data = "\n
				<url>
					<loc>".page_url($page->id)."</loc>
					<changefreq>monthly</changefreq>
					<priority>0.8</priority>
				</url>";
				fwrite($fp,$data);
			}


			// posts
			if(check_module('posts')) {
				
				// posts section
				$data = "\n
					<url>
						<loc>".posts_url()."</loc>
						<changefreq>daily</changefreq>
						<priority>0.8</priority>
					</url>";
					fwrite($fp,$data);

				// categories
				$posts_categories = DB::table('posts_categ')
					->leftJoin('sys_lang', 'posts_categ.lang_id', '=', 'sys_lang.id')
					->select('posts_categ.id')
					->where('sys_lang.status', 'active')
					->where('posts_categ.active', 1)
					->orderBy('posts_categ.id', 'desc')
					->get();   
				foreach ($posts_categories as $posts_categ) {
					$data = "\n
					<url>
						<loc>".posts_url($posts_categ->id)."</loc>
						<changefreq>weekly</changefreq>
						<priority>0.8</priority>
					</url>";
					fwrite($fp,$data);
				}

				// posts
				$posts = DB::table('posts')
					->leftJoin('sys_lang', 'posts.lang_id', '=', 'sys_lang.id')
					->select('posts.id')
					->where('sys_lang.status', 'active')
					->where('posts.status', 'active')
					->orderBy('posts.id', 'desc')
					->get();   
				foreach ($posts as $post) {
					$data = "\n
					<url>
						<loc>".post_url($post->id)."</loc>
						<changefreq>weekly</changefreq>
						<priority>0.7</priority>
					</url>";
					fwrite($fp,$data);
				}
			}


			// cart
			if(check_module('cart')) {

				// cart section
				$data = "\n
					<url>
						<loc>".cart_url()."</loc>
						<changefreq>daily</changefreq>
						<priority>0.8</priority>
					</url>";
					fwrite($fp,$data);

				// categories
				$cart_categories = DB::table('cart_categ')					
					->where('cart_categ.active', 1)
					->orderBy('cart_categ.id', 'desc')
					->get();   
				foreach ($cart_categories as $cart_categ) {
					$data = "\n
					<url>
						<loc>".cart_url($cart_categ->id)."</loc>
						<changefreq>weekly</changefreq>
						<priority>0.7</priority>
					</url>";
					fwrite($fp,$data);
				}

				// products
				$cart_products = DB::table('cart_products')
					->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
					->select('cart_products.id')
					->where('cart_products.status', 'active')
					->where('cart_products.hidden', 0)
					->where('cart_categ.active', 1)
					->orderBy('cart_products.id', 'asc')
					->get();   

				foreach ($cart_products as $product) {
					$data = "\n
					<url>
						<loc>".cart_product_url($product->id)."</loc>
						<changefreq>weekly</changefreq>
						<priority>0.8</priority>
					</url>";
					fwrite($fp,$data);
				}
			}			

			$data_footer = "\n</urlset>";
			fwrite($fp,$data_footer);
			fclose ($fp);

	}
}




if (!function_exists('recurseCopy')) {

	function recurseCopy($src,$dst, $childFolder='') { 

		$dir = opendir($src); 
		echo $dst;
		if(! is_dir($dst)) mkdir($dst);
		if ($childFolder!='') {
			if(! is_dir($dst.'/'.$childFolder)) mkdir($dst.'/'.$childFolder);

			while(false !== ( $file = readdir($dir)) ) { 
				if (( $file != '.' ) && ( $file != '..' )) { 
					if ( is_dir($src . '/' . $file) ) { 
						recurseCopy($src . '/' . $file,$dst.'/'.$childFolder . '/' . $file); 
					} 
					else { 
						copy($src . '/' . $file, $dst.'/'.$childFolder . '/' . $file); 
					}  
				} 
			}
		}else{
				// return $cc; 
			while(false !== ( $file = readdir($dir)) ) { 
				if (( $file != '.' ) && ( $file != '..' )) { 
					if ( is_dir($src . '/' . $file) ) { 
						recurseCopy($src . '/' . $file,$dst . '/' . $file); 
					} 
					else { 
						copy($src . '/' . $file, $dst . '/' . $file); 
					}  
				} 
			} 
		}
    
    	closedir($dir); 
	}
}
