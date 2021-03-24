<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/ 

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\Core;
use App\Models\Post;
use DB;
use Auth; 
use App; 

class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {        
        $this->PostModel = new Post();   
        $this->config = Core::config();  
    }


    /**
    * All posts
    */
    public function index(Request $request)
    {           
        if(! check_module('posts')) return redirect('/');             

        $posts = DB::table('posts')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('posts_categ', 'posts.categ_id', '=', 'posts_categ.id')
            ->select('posts.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar', 'users.slug as author_slug')
            ->where('posts.status', 'like', 'active') 
            ->where('posts_categ.active', 1) 
            ->where('posts.lang_id', active_lang()->id ?? null)     
            ->orderBy('featured', 'desc')       
            ->orderBy('id', 'desc')
            ->paginate($this->config->posts_per_page ?? 12);

        return view('frontend/'.$this->config->template.'/posts', [          
            'posts' => $posts,
        ]);
    }


    /**
    * ALL posts from a category
    */
    public function categ(Request $request)
    {              
        if(! check_module('posts')) return redirect('/');           

        $slug = $request->slug;                          
        
        $categ = DB::table('posts_categ')->where('slug', $slug)->where('active', 1)->where('lang_id', active_lang()->id)->first();          
        if(!$categ) abort(404);

        $categ_id = $categ->id;
        $categ_tree_ids = $categ->tree_ids ?? null;
        if($categ_tree_ids) $categ_tree_ids_array = explode(',', $categ_tree_ids);

        $posts = DB::table('posts')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar', 'users.slug as author_slug')
            ->where('posts.status', 'like', 'active') 
            ->whereIn('posts.categ_id', $categ_tree_ids_array)
            ->orderBy('posts.featured', 'desc')       
            ->orderBy('posts.id', 'desc')
            ->paginate($this->config->posts_per_page ?? 12);


        return view('frontend/'.$this->config->template.'/posts-categ', [              
            'posts' => $posts,
            'categ' => $categ,
        ]);
    }


    /**
    *  post
    */
    public function post(Request $request)
    {        
        if(! check_module('posts')) return redirect('/');          

        $categ_slug = $request->categ_slug;
        $slug = $request->slug;        
        if(! $categ_slug || ! $slug) abort(404);

        $post = DB::table('posts')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('posts_categ', 'posts.categ_id', '=', 'posts_categ.id')
            ->select('posts.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar', 'users.slug as author_slug', 'posts_categ.slug as categ_slug', 'posts_categ.title as categ_title')
            ->where('posts.status', 'like', 'active')    
            ->where('posts.slug', $slug)   
            ->where('posts_categ.active', 1)      
            ->where('posts_categ.lang_id', active_lang()->id)      
            ->where('posts_categ.slug', $categ_slug)      
            ->first();  
            
        if(! $post) abort(404);
        
        $comments = DB::table('posts_comments')
            ->leftJoin('users', 'posts_comments.user_id', '=', 'users.id')
            ->select('posts_comments.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar', 'users.slug as author_slug')
            ->where('post_id', $post->id);
        if(($this->config->posts_comments_order ?? null) == 'old')
            $comments = $comments->orderBy('id', 'asc');
        else
            $comments = $comments->orderBy('id', 'desc');    
        
        $comments = $comments->paginate($this->config->posts_comments_per_page ?? 20);  

        $images = DB::table('posts_images')
            ->where('post_id', $post->id)      
            ->orderBy('id', 'desc')  
            ->get();  

        $tags = DB::table('posts_tags')
            ->where('post_id', $post->id)                
            ->get();        

        // likes enabled
        if(($this->config->posts_likes_disabled ?? null) == 0 && $post->disable_likes != 1) $likes_enabled = true;
        else $likes_enabled = false;

        $related_posts = DB::table('posts')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->select('posts.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar')
            ->where('posts.status', 'like', 'active')        
            ->where('lang_id', $post->lang_id)  
            ->orderBy('id', 'desc')
            ->limit($this->config->posts_per_page ?? 12)
            ->get();            
            
        // update hits
        DB::table('posts')
            ->where('id', $post->id)
            ->increment('hits');                   
        
        return view('frontend/'.$this->config->template.'/post', [            
            'post' => $post,
            'related_posts' => $related_posts,
            'comments' => $comments,
            'images' => $images,
            'tags' => $tags,
            'likes_enabled' => $likes_enabled,
        ]);
    }

    
    /**
    * Search results
    */
    public function search(Request $request)
    {     
        if(! check_module('posts')) return redirect('/');         

        $s = $request->s;                
        $lang = $request->lang;  

        $posts = DB::table('posts')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('posts_categ', 'posts.categ_id', '=', 'posts_categ.id')
            ->select('posts.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar', 'users.slug as author_slug', 'posts_categ.title as categ_title', 'posts_categ.slug as categ_slug')
            ->where('posts.status', 'like', 'active') 
            ->where(function ($query) use ($s) {
                $query->where('posts.title', 'like', "%$s%")
                    ->orWhere('posts.search_terms', 'like', "%$s%");
                });

        if(! $lang) {
            $default_lang_id = DB::table('sys_lang')
                ->where('is_default', 1)      
			    ->value('id');  
            $posts = $posts->where('posts.lang_id', $default_lang_id);
        } else {
            $active_lang_id = DB::table('sys_lang')
                ->where('code', $lang)      
			    ->value('id'); 
            $posts = $posts->where('posts.lang_id', $active_lang_id); 
        }

        $posts = $posts->orderBy('posts.featured', 'desc')       
            ->orderBy('posts.id', 'desc')
            ->paginate($this->config->posts_per_page ?? 12);

        return view('frontend/'.$this->config->template.'/posts-search', [              
            'posts' => $posts,
            's' => $s,
        ]);
    }


    /**
    * Tag
    */
    public function tag(Request $request)
    {      
        if(! check_module('posts')) return redirect('/');         

        $tag_slug = $request->slug;                
        $lang = $request->lang;  

        $posts = DB::table('posts_tags')
            ->leftJoin('posts', 'posts_tags.post_id', '=', 'posts.id')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('posts_categ', 'posts.categ_id', '=', 'posts_categ.id')
            ->groupBy('posts_tags.post_id')
            ->select('posts.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar', 'users.slug as author_slug', 'posts_categ.title as categ_title', 'posts_categ.slug as categ_slug')
            ->where('posts_tags.slug', $tag_slug)     
            ->where('posts.status', 'like', 'active');        

        if(! $lang) {
            $default_lang_id = DB::table('sys_lang')
                ->where('is_default', 1)      
                ->value('id');  
            $posts = $posts->where('posts_tags.lang_id', $default_lang_id);

            $tag = DB::table('posts_tags')->where('slug', $tag_slug)->where('lang_id', $default_lang_id)->value('tag');
        } else {
            $active_lang_id = DB::table('sys_lang')
                ->where('code', $lang)      
                ->value('id'); 
            $posts = $posts->where('posts_tags.lang_id', $active_lang_id); 

            $tag = DB::table('posts_tags')->where('slug', $tag_slug)->where('lang_id', $active_lang_id)->value('tag');
        }

        $posts = $posts->orderBy('posts.featured', 'desc')       
            ->orderBy('posts.id', 'desc')
            ->paginate($this->config->posts_per_page ?? 12);

                
        return view('frontend/'.$this->config->template.'/posts-tag', [              
            'posts' => $posts,
            'tag' => $tag,
            'tag_slug' => $tag_slug,
        ]);
    }


    /**
    * Process star rating
    */
    public function like(Request $request)
    {     
        if(! check_module('posts')) return redirect('/');          

        $post_id = $request->post_id; // retrieve all of the input data as an array              
        
        // check if global rating is disabled
        if(($this->config->posts_likes_disabled ?? null) == 1) return response('like_disabled');

        // check if like is disabled
        $post = DB::table('posts')->where('id', $post_id)->first();  
        if(! $post) return response('invalid_content');
        if($post->disable_likes == 1) return response('like_disabled');

        // check if login is required
        if(($this->config->posts_likes_require_login ?? null) == 1 && !Auth::check()) return response('login_required');       
 
        $cookie = Cookie::get('post_like_'.$post_id);        

        if(!$cookie) {            
                DB::table('posts_likes')->insert([
                    'post_id' => $post_id, 
                    'created_at' =>  now(),
                    'ip' => $request->ip(),
                ]); 
                
                // recount likes
                $this->PostModel->recount_post_likes($post_id);   

                return response('liked')->cookie(
                    'post_like_'.$post_id, 1, 60*24*30, '/'
                );  
        } // end if not exist cookie
        else {
            return response('already_liked');
        }

    }    


    /**
    * Process comment
    */
    public function comment(Request $request)
    {
        if(! check_module('posts')) return redirect('/');          
        
        $categ_slug = $request->categ_slug;
        $slug = $request->slug;        
        $id = $request->id;   
        if(! $categ_slug || ! $slug) abort(404);

        $inputs = $request->all(); // retrieve all of the input data as an array      
        
        $post = DB::table('posts')->where('id', $id)->first();  
        if(! $post) return redirect('/');

        // check if global comments is disabled
        if($this->config->posts_comments_disabled == 1) return redirect(route('post', ['lang' => $request->lang, 'categ_slug' => $categ_slug, 'slug' => $slug]).'#comments');
                
        // check if comment is disabled
        if($post->disable_comments == 1) return redirect(route('post', ['lang' => $request->lang, 'categ_slug' => $categ_slug, 'slug' => $slug]).'#comments');

        // check if login is required
        if($this->config->posts_comments_require_login == 1 && !Auth::check()) return redirect(route('post', ['lang' => $request->lang, 'categ_slug' => $categ_slug, 'slug' => $slug]).'#comments')->with('error', 'login_required');

        // check antispam 
        if($this->config->posts_comments_antispam_enabled ?? null == 1) {
            // Build POST request:
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_secret = $this->config->google_recaptcha_secret_key ?? null;
            $recaptcha_response = $request->recaptcha_response;

            // Make and decode POST request:
            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $recaptcha = json_decode($recaptcha);
           
            // Take action based on the score returned:
            if ($recaptcha->success) {
                if($recaptcha->score < 0.5) return redirect(route('post', ['lang' => $request->lang, 'categ_slug' => $categ_slug, 'slug' => $slug]).'#comments')->with('error', 'recaptcha_error');
            }
            else return redirect(route('post', ['lang' => $request->lang, 'categ_slug' => $categ_slug, 'slug' => $slug]).'#comments')->with('error', 'recaptcha_error');
        }


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'email',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('post', ['lang' => $request->lang, 'categ_slug' => $categ_slug, 'slug' => $slug]))
                ->withErrors($validator)
                ->withInput();
        } 
 
        DB::table('posts_comments')->insert([
            'post_id' => $inputs['id'], 
            'name' => $inputs['name'], 
            'email' => $inputs['email'], 
            'comment' => $inputs['comment'], 
            'created_at' =>  now(),
            'ip' => $request->ip(),
            'user_id' => Auth::user()->id ?? null
        ]); 

        return redirect(route('post', ['lang' => $request->lang, 'categ_slug' => $categ_slug, 'slug' => $slug]).'#comments')->with('success', 'comment_added');
    }        


}
