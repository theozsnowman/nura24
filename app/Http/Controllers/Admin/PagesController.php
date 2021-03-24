<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 
use Image;
use Storage;

class PagesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->UploadModel = new Upload();   
        
        $this->root_pages = DB::table('pages')
            ->leftJoin('sys_lang', 'pages.lang_id', '=', 'sys_lang.id')
            ->select('pages.*', 'sys_lang.name as lang_name')            
            ->whereNull('parent_id')
            ->orderBy('active', 'desc')
            ->orderBy('title', 'asc')
            ->get();

        $this->middleware(function ($request, $next) {
            $this->logged_user_role_id = Auth::user()->role_id;
            $this->logged_user_id = Auth::user()->id;            
            $this->logged_user_role = $this->UserModel->get_role_from_id ($this->logged_user_role_id);    
            
            if(! ($this->logged_user_role == 'admin' || $this->logged_user_role == 'internal')) return redirect('/'); 
            return $next($request);
        });
    } 


    /**
    * Display all resources
    */
    public function index(Request $request)
    {

        if(! check_access('pages')) return redirect(route('admin'));

        $search_terms = $request->search_terms;
        $search_badge = $request->search_badge;
        $search_lang_id = $request->search_lang_id;
        
        $pages = DB::table('pages')
            ->leftJoin('users', 'pages.user_id', '=', 'users.id')
            ->leftJoin('sys_lang', 'pages.lang_id', '=', 'sys_lang.id')
            ->select('pages.*', 'users.name as author_name', 'users.avatar as author_avatar', 'sys_lang.name as lang_name', 'pages.parent_id as parent_page_id', 
                DB::raw('(SELECT count(*) FROM pages_images WHERE pages_images.page_id = pages.id) as count_images'), 
                DB::raw('(SELECT title FROM pages WHERE id = parent_page_id) as parent_page_title'), 
                DB::raw('(SELECT slug FROM pages WHERE id = parent_page_id) as parent_page_slug') )                         
            ->orderBy('active', 'desc')
            ->orderBy('id', 'desc');
                
        if($search_terms)
            $pages = $pages->where('title', 'like', "%$search_terms%");            

        if($search_badge)
            $pages = $pages->whereRaw("FIND_IN_SET(?, badges) > 0", [$search_badge]);

        if($search_lang_id)
            $pages = $pages->where('lang_id', $search_lang_id);                

        $pages = $pages->paginate(25);                           

        return view('admin/account', [
            'view_file'=>'pages.pages',
            'active_submenu'=>'pages',
            'search_terms'=> $search_terms,
            'search_badge'=> $search_badge,
            'search_lang_id'=> $search_lang_id,
            'pages' => $pages,
        ]); 
    }


    /**
    * Show form to add new resource
    */
    public function create()
    {
        if(! check_access('pages')) return redirect(route('admin'));       

        return view('admin/account', [
            'view_file' => 'pages.create',
            'active_submenu' => 'pages',
            'root_pages' => $this->root_pages,
        ]);
    }


    /**
    * Create new page
    */
    public function store(Request $request)
    {
        if(! check_access('pages')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.pages.create'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        $lang_id = $inputs['lang_id'] ?? default_lang()->id;

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');
        
        if(DB::table('pages')->where('slug', $slug)->where('lang_id', $lang_id)->exists()) return redirect(route('admin.pages.create'))->with('error', 'duplicate');  

        DB::table('pages')->insert([
            'parent_id' => $inputs['parent_id'],
            'lang_id' => $lang_id,
            'user_id' => Auth::user()->id,
            'title' => $inputs['title'],
            'slug' => $slug,
            'content' => $inputs['content'],
            'active' => $inputs['active'],
            'meta_title' => $inputs['meta_title'],
            'meta_description' => $inputs['meta_description'],
            'redirect_url' => $inputs['redirect_url'],
            'custom_tpl_file' => $inputs['custom_tpl_file'],
            'label' => $inputs['label'],
            'badges' => str_replace(' ', '', $inputs['badges']),
            'created_at' => now(),
        ]);

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.pages.create'))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $id = DB::getPdo()->lastInsertId(); 
            $image_db = $this->UploadModel->upload_image($request, 'image');    
            DB::table('pages')->where('id', $id)->update(['image' => $image_db]);            
        }        
                 
        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Show form to edit resource     
    */
    public function show(Request $request)
    {
        if(! check_access('pages')) return redirect(route('admin'));

        $page = DB::table('pages')->where('id', $request->id)->first();          
        if(!$page) return redirect(route('admin'));

        // check permission
        if(check_access('pages', 'author') && $this->logged_user_id != $page->user_id) return redirect(route('admin'));                

        return view('admin/account', [
            'view_file'=>'pages.update',
            'active_submenu'=>'pages',
            'page' => $page,   
            'root_pages' => $this->root_pages,        
        ]);
    }


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {        

        if(! check_access('pages')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;  
        $page = DB::table('pages')->where('id', $id)->first();    
        if(!$page) return redirect(route('admin'));     

        // check permission
        if(check_access('pages', 'author') && $this->logged_user_id != $page->user_id) return redirect(route('admin'));

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect($request->Url())
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        $lang_id = $inputs['lang_id'] ?? default_lang()->id;

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        if(DB::table('pages')->where('slug', $slug)->where('lang_id', $lang_id)->where('id', '!=', $id)->exists()) return redirect(route('admin.pages.show', ['id'=>$id]))->with('error', 'duplicate');  
        
        DB::table('pages')
            ->where('id', $id)
            ->update([
                'parent_id' => $inputs['parent_id'],
                'lang_id' => $lang_id,
                'title' => $inputs['title'],
                'slug' => $slug,
                'content' => $inputs['content'],
                'active' => $inputs['active'],
                'meta_title' => $inputs['meta_title'],
                'meta_description' => $inputs['meta_description'],
                'redirect_url' => $inputs['redirect_url'],
                'custom_tpl_file' => $inputs['custom_tpl_file'],
                'label' => $inputs['label'],
                'badges' => str_replace(' ', '', $inputs['badges']),
                'updated_at' => now(),
            ]);

        DB::table('pages')->where('parent_id', $id)->update(['lang_id' => $lang_id]); // for sub-pages

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.pages.show', ['id'=>$id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $image_db = $this->UploadModel->upload_image($request, 'image');    
            DB::table('pages')->where('id', $id)->update(['image' => $image_db]);            
        }        
                 
        return redirect(route('admin.pages'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        if(! check_access('pages', 'manager')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');         
        
        $id = $request->id;  
        $page = DB::table('pages')->where('id', $id)->first();  
        if(!$page) return redirect(route('admin'));

        delete_image($page->image);        

        $images = DB::table('pages_images')->where('page_id', $id)->get();     
        foreach($images as $image) {
            if($image->file) delete_image($image->file);   
        }

        DB::table('pages')->where('parent_id', $id)->update(['parent_id' => null]);
        DB::table('pages_images')->where('page_id', $id)->delete();
        DB::table('pages')->where('id', $id)->delete();
        

        return redirect(route('admin.pages'))->with('success', 'deleted'); 
    }



     /**
    * Display all images
    */
    public function images(Request $request)
    {

        if(! check_access('pages')) return redirect(route('admin'));

        $id = $request->id; // page ID

        $images = DB::table('pages_images')
            ->where('page_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(20);       

        $page = DB::table('pages')
            ->where('id', $id)
            ->first();  
        if(!$page) return redirect(route('admin'));
      
        return view('admin/account', [
            'view_file'=>'pages.images',
            'active_submenu'=>'pages',
            'images' => $images,
            'page' => $page,
            'id' => $id,
        ]); 
    }


    /**
    * Create new resource
    */
    public function create_image(Request $request)
    {

        if(! check_access('pages')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id; // page ID
        $description = $request->description;        
        
        $page = DB::table('pages')->where('id', $request->id)->first();          
        if(!$page) return redirect(route('admin'));

        // check permission
        if(check_access('pages', 'author') && $this->logged_user_id != $page->user_id) return redirect(route('admin'));

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.pages.images', ['id' => $id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $image_db = $this->UploadModel->upload_image($request, 'image');    
            DB::table('pages_images')->insert([
                'page_id' => $id,
                'description' => $description,
                'file' => $image_db,               
            ]);
        }        
                 
        return redirect(route('admin.pages.images', ['id' => $id]))->with('success', 'created'); 
    }   


    /**
    * Remove the specified resource
    */
    public function delete_image(Request $request)
    {

        if(! check_access('pages')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        $id = $request->id;
        $image_id = $request->image_id;       

        $page = DB::table('pages')->where('id', $request->id)->first();          
        if(!$page) return redirect(route('admin'));

        // check permission
        if(check_access('pages', 'author') && $this->logged_user_id != $page->user_id) return redirect(route('admin'));

        // delete images
        $file = DB::table('pages_images')->where('id', $image_id)->value('file');   

        if($file) delete_image($file);                
        
        DB::table('pages_images')->where('id', $image_id)->delete(); 

        return redirect(route('admin.pages.images', ['id' => $id]))->with('success', 'deleted'); 
    }


    /**
    * Remove page main image
    */
    public function delete_main_image(Request $request)
    {
        if(! check_access('pages')) return redirect(route('admin'));

        $id = $request->id; // page ID

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
      
        // delete image
        $page = DB::table('pages')->where('id', $id)->first();   
        if(!$page) return redirect(route('admin'));

        if(check_access('pages', 'author') && $this->logged_user_id != $page->user_id) return redirect(route('admin'));

        if($page->image) delete_image($page->image);

        DB::table('pages')->where('id', $id)->update(['image' => null]); 
        
        return redirect(route('admin.pages.show', ['id' => $id]))->with('success', 'main_image_deleted'); 
    }
}
