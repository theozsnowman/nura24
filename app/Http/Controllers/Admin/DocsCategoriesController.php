<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Doc;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 

class DocsCategoriesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->DocModel = new Doc();                        

        $this->middleware(function ($request, $next) {
            $this->logged_user_role_id = Auth::user()->role_id;
            $this->logged_user_id = Auth::user()->id;            
            $this->logged_user_role = $this->UserModel->get_role_from_id ($this->logged_user_role_id);    
            
            if(! $this->logged_user_role == 'admin') return redirect('/'); 
            return $next($request);         
        });

    } 


    /**
    * Display all resources
    */
    public function index(Request $request)
    {             

        $search_lang_id = $request->search_lang_id;

        $count_categories = DB::table('docs_categ')->count();

        $categories = Doc::whereNull('parent_id')->with('childCategories')
            ->leftJoin('sys_lang', 'docs_categ.lang_id', '=', 'sys_lang.id')    
            ->select('docs_categ.*', 'sys_lang.name as lang_name')         
            ->orderBy('docs_categ.active', 'desc')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc');
            
        if($search_lang_id)
            $categories = $categories->where('lang_id', $search_lang_id);              
        
        $categories = $categories->get();              

        return view('admin/account', [
            'view_file'=>'docs.categories',
            'active_submenu'=>'docs',
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
            return redirect(route('admin.docs.categ'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        $search_lang_id = $request->search_lang_id;

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        if(DB::table('docs_categ')->where('slug', $slug)->where('lang_id', $inputs['lang_id'])->exists()) return redirect(route('admin.docs.categ'))->with('error', 'duplicate');         

        DB::table('docs_categ')->insert([
            'lang_id' => $inputs['lang_id'] ?? default_lang()->id,
            'title' => $inputs['title'],
            'parent_id' => $inputs['parent_id'] ?? null,
            'slug' => $slug,
            'description' => $inputs['description'],
            'active' => $inputs['active'],            
            'position' => $inputs['position'],
            'icon' => $inputs['icon'],
            'badges' => str_replace(' ', '', $inputs['badges']),
            'redirect_url' => $inputs['redirect_url'],
            'meta_title' => $inputs['meta_title'],
            'meta_description' => $inputs['meta_description'],
        ]);
                 
        $categ_id = DB::getPdo()->lastInsertId();  

        $this->DocModel->regenerate_tree_ids();
        $this->DocModel->recount_categ_items($categ_id);

        return redirect(route('admin.docs.categ', ['search_lang_id' => $search_lang_id]))->with('success', 'created'); 
    }   


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {
        $id = $request->id;
        $search_lang_id = $request->search_lang_id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.docs.categ'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');
        if($inputs['slug'] == 'uncategorized') $slug = 'uncategorized';

        if(DB::table('docs_categ')->where('slug', $slug)->where('lang_id', $inputs['lang_id'])->where('id', '!=', $id)->exists()) return redirect(route('admin.docs.categ'))->with('error', 'duplicate'); 

        DB::table('docs_categ')
            ->where('id', $id)
            ->update([
                'lang_id' => $inputs['lang_id'] ?? default_lang()->id,
                'title' => $inputs['title'],
                'parent_id' => $inputs['parent_id'] ?? null,
                'slug' => $slug,
                'description' => $inputs['description'],
                'active' => $inputs['active'],     
                'position' => $inputs['position'],   
                'icon' => $inputs['icon'],
                'badges' => str_replace(' ', '', $inputs['badges']),
                'redirect_url' => $inputs['redirect_url'],
                'meta_title' => $inputs['meta_title'],
                'meta_description' => $inputs['meta_description'],        
        ]);

        $categ = DB::table('docs_categ')
            ->where('id', $id)
            ->first();            
        DB::table('docs_categ')
            ->where('parent_id', $categ->id)
            ->update([
                'lang_id' => $inputs['lang_id'] ?? null,                
        ]);
        DB::table('docs')
            ->where('categ_id', $categ->id)
            ->update([
                'lang_id' => $inputs['lang_id'] ?? null,                
        ]);

        $this->DocModel->regenerate_tree_ids();
        $this->DocModel->recount_categ_items($id);
          
        return redirect(route('admin.docs.categ', ['search_lang_id' => $search_lang_id]))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $search_lang_id = $request->search_lang_id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');   
        
        $uncategorized_categ_id = $this->DocModel->get_uncategorized_categ_id();
        if($uncategorized_categ_id == $id) return redirect(route('admin.docs.categ'));   

        DB::table('docs_categ')->where('id', $id)->delete(); 
        DB::table('docs')->where('categ_id', $id)->update(['categ_id' => $uncategorized_categ_id]);

        $this->DocModel->regenerate_tree_ids();
        $this->DocModel->recount_categ_items($id);

        return redirect(route('admin.docs.categ', ['search_lang_id' => $search_lang_id]))->with('success', 'deleted'); 
    }

}
