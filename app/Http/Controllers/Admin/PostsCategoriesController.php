<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 

class PostsCategoriesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->PostModel = new Post();                        

        $this->middleware(function ($request, $next) {
            $this->logged_user_role_id = Auth::user()->role_id;
            $this->logged_user_id = Auth::user()->id;            
            $this->logged_user_role = $this->UserModel->get_role_from_id ($this->logged_user_role_id);                

            if(! ($this->logged_user_role == 'admin')) return redirect('/'); 
            return $next($request);
        });
    } 


    /**
    * Display all resources
    */
    public function index(Request $request)
    {             

        $search_lang_id = $request->search_lang_id;

        $count_categories = DB::table('posts_categ')->count();

        $categories = Post::whereNull('parent_id')->with('childCategories')
            ->orderBy('active', 'desc')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc');

        if($search_lang_id)
            $categories = $categories->where('lang_id', $search_lang_id);              
        
        $categories = $categories->get();                        
            
        return view('admin/account', [
            'view_file'=>'posts.categories',
            'active_submenu'=>'posts',
            'categories' => $categories,
            'count_categories' => $count_categories,
            'search_lang_id'=> $search_lang_id,
        ]); 
    }


    /**
    * Create new resource
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.posts.categ'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        $lang_id = $inputs['lang_id'] ?? default_lang()->id;

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');
        if(strlen($slug)<3) return redirect(route('admin.posts.categ'))->with('error', 'length');

        if(DB::table('posts_categ')->where('slug', $slug)->where('lang_id', $lang_id)->exists()) return redirect(route('admin.articpostsles.categ'))->with('error', 'duplicate'); 

        DB::table('posts_categ')->insert([
            'lang_id' => $inputs['lang_id'] ?? default_lang()->id,
            'title' => $inputs['title'],
            'parent_id' => $inputs['parent_id'] ?? null,
            'slug' => $slug,
            'description' => $inputs['description'],
            'active' => $inputs['active'],
            'position' => $inputs['position'],   
            'icon' => $inputs['icon'],
            'badges' => str_replace(' ', '', $inputs['badges']),
            'meta_title' => $inputs['meta_title'],
            'meta_description' => $inputs['meta_description'],
        ]);

        $categ_id = DB::getPdo()->lastInsertId();  
              
        $this->PostModel->regenerate_tree_ids();
        $this->PostModel->recount_categ_items($categ_id);

        return redirect($request->Url())->with('success', 'created'); 
    }   


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.posts.categ'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        $lang_id = $inputs['lang_id'] ?? default_lang()->id;

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');
        if(strlen($slug)<3) return redirect(route('admin.posts.categ'))->with('error', 'length');
        if($inputs['slug'] == 'uncategorized') $slug = 'uncategorized';

        if(DB::table('posts_categ')->where('slug', $slug)->where('lang_id', $lang_id)->where('id', '!=', $id)->exists()) return redirect(route('admin.posts.categ'))->with('error', 'duplicate'); 

        DB::table('posts_categ')
            ->where('id', $id)
            ->update([
                'lang_id' => $lang_id,
                'title' => $inputs['title'],
                'parent_id' => $inputs['parent_id'] ?? null,
                'slug' => $slug,
                'description' => $inputs['description'],
                'active' => $inputs['active'],
                'position' => $inputs['position'],   
                'icon' => $inputs['icon'],
                'badges' => str_replace(' ', '', $inputs['badges']),
                'meta_title' => $inputs['meta_title'],
                'meta_description' => $inputs['meta_description'],
        ]);
          

        $categ = DB::table('posts_categ')
            ->where('id', $id)
            ->first();            
        DB::table('posts_categ')
            ->where('parent_id', $categ->id)
            ->update([
                'lang_id' => $lang_id,                
        ]);
        DB::table('posts')
            ->where('categ_id', $categ->id)
            ->update([
                'lang_id' => $lang_id,                
        ]);       

        $this->PostModel->regenerate_tree_ids();
        $this->PostModel->recount_all_categs_items();

        return redirect(route('admin.posts.categ'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');       

        $uncategorized_categ_id = $this->PostModel->get_uncategorized_categ_id();

        if($id == $uncategorized_categ_id) return redirect(route('admin.posts.categ'));       

        DB::table('posts_categ')->where('id', $id)->delete(); 
        DB::table('posts')->where('categ_id', $id)->update(['lang_id' => default_lang()->id]);
        DB::table('posts')->where('categ_id', $id)->update(['categ_id' => $uncategorized_categ_id]);

        $this->PostModel->regenerate_tree_ids();
        $this->PostModel->recount_all_categs_items();

        return redirect(route('admin.posts.categ'))->with('success', 'deleted'); 
    }

}
