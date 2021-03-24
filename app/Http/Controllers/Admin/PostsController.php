<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Post;
use App\Models\Upload;
use App\Models\Core;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 
use Image;

class PostsController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->UploadModel = new Upload();    
        $this->PostModel = new Post();   
                
        $this->categories = Post::whereNull('parent_id')
            ->with('childCategories')
            ->leftJoin('sys_lang', 'posts_categ.lang_id', '=', 'sys_lang.id')			
            ->select('posts_categ.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang')
            ->orderBy('title', 'asc')->get();  

        $this->middleware(function ($request, $next) {
            $this->logged_user_role_id = Auth::user()->role_id;
            $this->logged_user_id = Auth::user()->id;            
            $this->logged_user_role = $this->UserModel->get_role_from_id ($this->logged_user_role_id);    
            
            if(! ($this->logged_user_role == 'admin' || $this->logged_user_role == 'internal')) return redirect('/'); 
            return $next($request);
        });

    } 


    /**
    * Display all posts
    */
    public function index(Request $request)
    {

        if(! check_access('posts')) return redirect(route('admin'));
        
        $search_terms = $request->search_terms;
        $search_status = $request->search_status;
        $search_categ_id = $request->search_categ_id;
        $search_lang_id = $request->search_lang_id;
       
        $posts = DB::table('posts')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('posts_categ', 'posts.categ_id', '=', 'posts_categ.id')			
            ->leftJoin('sys_lang', 'posts.lang_id', '=', 'sys_lang.id')
            ->select('posts.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang_code', 'sys_lang.status as lang_status', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar', DB::raw("(SELECT count(*) FROM posts_comments WHERE posts.id = posts_comments.post_id) count_comments"), DB::raw("(SELECT count(*) FROM posts_likes WHERE posts.id = posts_likes.post_id) count_likes"), DB::raw("(SELECT count(*) FROM posts_images WHERE posts.id = posts_images.post_id) count_images"));

        if($search_lang_id)
            $posts = $posts->where('posts.lang_id', $search_lang_id);      
        if($search_status)
            $posts = $posts->where('posts.status', 'like', $search_status);             
            
        if($search_terms) $posts = $posts->where(function ($query) use ($search_terms) {
            $query->where('posts.title', 'like', "%$search_terms%")
                ->orWhere('posts.search_terms', 'like', "%$search_terms%");                    
        }); 
                     
        if($search_categ_id) {
            $categ = DB::table('posts_categ')->where('id', $search_categ_id)->first();              
            $categ_id = $categ->id;
            $categ_tree_ids = $categ->tree_ids ?? null;
            if($categ_tree_ids) $categ_tree_ids_array = explode(',', $categ_tree_ids); else $categ_tree_ids_array = array();
            $posts = $posts->whereIn('posts.categ_id', $categ_tree_ids_array);   
        }

        $posts = $posts->orderBy('posts.featured', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);                    
           
        return view('admin/account', [
            'view_file'=>'posts.posts',
            'active_submenu'=>'posts',
            'search_terms'=>$search_terms,
            'search_status'=>$search_status,
            'search_categ_id'=>$search_categ_id,
            'search_lang_id'=> $search_lang_id,
            'posts' => $posts,
            'categories' => $this->categories,
        ]); 
    }


    /**
    * Show page to add new resource
    */
    public function create()
    {
        if(! check_access('posts')) return redirect(route('admin'));

        return view('admin/account', [
            'view_file'=>'posts.create',
            'active_submenu'=>'posts',
            'categories' => $this->categories,
        ]);
    }


    /**
    * Create new resource
    */
    public function store(Request $request)
    {
        if(! check_access('posts')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'categ_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.posts.create'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        $categ = DB::table('posts_categ')
            ->where('id', $inputs['categ_id'])    
            ->first(); 

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        // check if there is another post with this slug (same language)
        if(DB::table('posts')->where('slug', $slug)->where('lang_id', $categ->lang_id)->exists()) {
            // if exists, add post ID in slug
            $latestID = $posts->latest('id')->first();     
            $slug = $slug.'-'.$latestID;
        }
        
        if($request->input('featured')=='on') $featured = 1;
        if($request->input('disable_comments')=='on') $disable_comments = 1;
        if($request->input('disable_likes')=='on') $disable_likes = 1;        

        DB::table('posts')->insert([
            'lang_id' => $categ->lang_id ?? null,
            'title' => $inputs['title'],
            'categ_id' => $inputs['categ_id'],
            'slug' => $slug,
            'user_id' => Auth::user()->id,
            'summary' => $inputs['summary'],
            'content' => $inputs['content'],
            'status' => $inputs['status'],
            'search_terms' => $inputs['search_terms'],
            'meta_title' => $inputs['meta_title'],
            'meta_description' => $inputs['meta_description'],
            'custom_tpl' => $inputs['custom_tpl'],
            'disable_comments' => $disable_comments ?? 0,
            'disable_likes' => $disable_likes ?? 0,
            'featured' => $featured ?? 0,
            'created_at' => now()
        ]);
        
        $id = DB::getPdo()->lastInsertId(); 

        // seconds to read
        DB::table('posts')->where('id', $id)->update(['minutes_to_read' => estimated_reading_time($id)]);      

        // process tags
        if($inputs['tags'] and $inputs['status'] == 'active') {
            $tags_array = explode(',', $inputs['tags']);
            foreach($tags_array as $tag) {
                $this->PostModel->add_tag($tag, $id);
            }
        }

        // process image        
        if ($request->hasFile('image')) {
            $image_db = $this->UploadModel->upload_image($request, 'image');                
            DB::table('posts')->where('id', $id)->update(['image' => $image_db]);            
        }        
                 
        $this->PostModel->recount_categ_items($inputs['categ_id']);

        return redirect(route('admin.posts'))->with('success', 'created'); 
    }   


    /**
    * Show form to edit resource     
    */
    public function show(Request $request)
    {
        if(! check_access('posts')) return redirect(route('admin'));

        $id = $request->id;

        $post = DB::table('posts')
            ->where('id', $id)
            ->first();      
        if(!$post) abort(404);
        
        $tags_array = DB::table('posts_tags')
            ->where('post_id', $id)
            ->orderBy('tag', 'asc')
            ->pluck('tag')->toArray();
                                
        $tags = implode (", ", $tags_array);

        return view('admin/account', [
            'view_file'=>'posts.update',
            'active_submenu'=>'posts',
            'post' => $post,
            'tags' => $tags,            
            'categories' => $this->categories,
        ]);
    }


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        if(! check_access('posts')) return redirect(route('admin'));

        $id = $request->id;
        $post = DB::table('posts')->where('id', $id)->first(); 
        if(! $post) return redirect(route('admin'));

        // check if author own post
        if(check_access('posts', 'author') && $post->user_id != Auth::user()->id) return redirect(route('admin.posts'));

        // check if contributor own post
        if(check_access('posts', 'contributor') && $post->user_id != Auth::user()->id) return redirect(route('admin.posts'));
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'categ_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.posts.show', ['id' => $id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        $categ = DB::table('posts_categ')
            ->where('id', $inputs['categ_id'])    
            ->first(); 

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        // check if there is another post with this slug (same language)
        if(DB::table('posts')->where('slug', $slug)->where('lang_id', $categ->lang_id)->where('id', '!=', $id)->exists()) {
            // if exists, add post ID in slug
            $slug = $slug.'-'.$id;
        }        

        if($request->input('featured')=='on') $featured = 1;
        if($request->input('disable_comments')=='on') $disable_comments = 1;
        if($request->input('disable_likes')=='on') $disable_likes = 1;       
                
        DB::table('posts')
            ->where('id', $id)
            ->update([
                'lang_id' => $categ->lang_id ?? null,
                'title' => $inputs['title'],
                'categ_id' => $inputs['categ_id'],
                'slug' => $slug,
                'summary' => $inputs['summary'],
                'content' => $inputs['content'],
                'status' => $inputs['status'],
                'search_terms' => $inputs['search_terms'],
                'custom_tpl' => $inputs['custom_tpl'],
                'meta_title' => $inputs['meta_title'],
                'meta_description' => $inputs['meta_description'],
                'disable_comments' => $disable_comments ?? 0,
                'disable_likes' => $disable_likes ?? 0,
                'featured' => $featured ?? 0,
                'minutes_to_read' => estimated_reading_time($id),
                'updated_at' => now(),
        ]);

        // process tags
        DB::table('posts_tags')->where('post_id', $id)->delete(); // delete existing post tags
        if($inputs['tags'] and $inputs['status'] == 'active') {
            $tags_array = explode(',', $inputs['tags']);
            foreach($tags_array as $tag) {
                $this->PostModel->add_tag($tag, $id);
            }
        }

        // process image        
        if ($request->hasFile('image')) {
            $image_db = $this->UploadModel->upload_image($request, 'image');    
            DB::table('posts')->where('id', $id)->update(['image' => $image_db]);            
        }        
                 
        $this->PostModel->recount_categ_items($inputs['categ_id']);

        return redirect(route('admin.posts'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        if(! check_access('posts', 'manager')) return redirect(route('admin'));

        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $q = DB::table('posts')
            ->where('id', $id)
            ->first(); 

        // delete images
        $post = DB::table('posts')->where('id', $id)->first();   
        if($post->image) delete_image($post->image);             
        
        // delete image
        $post_images = DB::table('posts_images')->where('post_id', $id)->get();   
        foreach($post_images as $image) {
            if($image->file) delete_image($image->file);
        }    

        DB::table('posts')->where('id', $id)->delete(); // delete post
        DB::table('posts_comments')->where('post_id', $id)->delete(); // delete comments
        DB::table('posts_likes')->where('post_id', $id)->delete(); // delete likes
        DB::table('posts_images')->where('post_id', $id)->delete(); // delete media        
        DB::table('posts_tags')->where('post_id', $id)->delete(); // delete tags    

        $this->PostModel->recount_categ_items($q->categ_id);

        return redirect(route('admin.posts'))->with('success', 'deleted'); 
    }


    /**
    * Remove post main image
    */
    public function delete_main_image(Request $request)
    {        
        if(! check_access('posts', 'manager')) return redirect(route('admin'));

        $id = $request->id; // post ID

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
      
        // delete image
        $post = DB::table('posts')->where('id', $id)->first();   
        if($post->image) delete_image($post->image);

        DB::table('posts')->where('id', $id)->update(['image' => null]); 
        
        return redirect(route('admin.posts.show', ['id' => $id]))->with('success', 'main_image_deleted'); 
    }

}
