<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Forum;
use App\Models\Core;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 

class ForumCategoriesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->ForumModel = new Forum();  
        $this->config = Core::config();      
        
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

        $count_categories = DB::table('forum_categ')->count();

        $categories = Forum::whereNull('parent_id')->with('childCategories')->orderBy('active', 'desc')->orderBy('position', 'asc')->orderBy('title', 'asc')->get();            
      
        return view('admin/account', [
            'view_file'=>'forum.categories',
            'active_submenu'=>'forum.categ',
            'categories' => $categories,
            'count_categories' => $count_categories,
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
            return redirect(route('admin.forum.categ'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        $exist_slug = DB::table('forum_categ')->where('slug', $slug)->first();
        if($exist_slug) return redirect(route('admin.forum.categ'))->with('error', 'duplicate'); 

        DB::table('forum_categ')->insert([
            'title' => $inputs['title'],
            'parent_id' => $inputs['parent_id'] ?? null,
            'slug' => $slug,
            'type' => $inputs['type'],
            'allow_topics' => $inputs['allow_topics'],
            'description' => $inputs['description'],
            'active' => $inputs['active'],
            'position' => $inputs['position'],   
            'icon' => $inputs['icon'],
            'badges' => $inputs['badges'],
            'meta_title' => $inputs['meta_title'],
            'meta_description' => $inputs['meta_description'],
        ]);
                 
        $categ_id = DB::getPdo()->lastInsertId();  

        $this->ForumModel->regenerate_tree_ids();
        $this->ForumModel->recount_categ_items($categ_id);
        $this->ForumModel->regenerate_types($categ_id);

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
            return redirect(route('admin.forum.categ'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        $exist_slug = DB::table('forum_categ')->where('slug', $slug)->where('id', '!=', $id)->first();
        if($exist_slug) return redirect(route('admin.forum.categ'))->with('error', 'duplicate'); 

        DB::table('forum_categ')
            ->where('id', $id)
            ->update([
                'title' => $inputs['title'],
                'parent_id' => $inputs['parent_id'] ?? null,
                'slug' => $slug,
                'type' => $inputs['type'],
                'allow_topics' => $inputs['allow_topics'],
                'description' => $inputs['description'],
                'active' => $inputs['active'],
                'position' => $inputs['position'],   
                'icon' => $inputs['icon'],
                'badges' => $inputs['badges'],
                'meta_title' => $inputs['meta_title'],
                'meta_description' => $inputs['meta_description'],
        ]);

        $this->ForumModel->regenerate_tree_ids();
        $this->ForumModel->recount_categ_items($id);
        $this->ForumModel->regenerate_types($id);

        return redirect(route('admin.forum.categ'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');   
        
        $uncategorized_categ_id = $this->ForumModel->get_uncategorized_categ_id();

        DB::table('forum_categ')->where('id', $id)->delete(); 
        DB::table('forum_topics')->where('categ_id', $id)->update(['categ_id' => $uncategorized_categ_id]);
        DB::table('forum_posts')->where('categ_id', $id)->update(['categ_id' => $uncategorized_categ_id]);

        $this->ForumModel->regenerate_tree_ids();
        $this->ForumModel->recount_categ_items($id);
        
        return redirect(route('admin.forum.categ'))->with('success', 'deleted'); 
    }

}
