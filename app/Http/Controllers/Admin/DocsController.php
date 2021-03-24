<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\Core;
use App\Models\User;
use App\Models\Upload;
use App\Models\Doc;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 
use Image;

class DocsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->UploadModel = new Upload();    
        $this->DocModel = new Doc(); 
        $this->config = Core::config();      
        
        $this->categories = Doc::whereNull('parent_id')
            ->with('childCategories')
            ->leftJoin('sys_lang', 'docs_categ.lang_id', '=', 'sys_lang.id')			
            ->select('docs_categ.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang')
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
        if(! check_access('docs')) return redirect(route('admin'));

        $search_terms = $request->search_terms;
        $search_categ_id = $request->search_categ_id;
        $search_lang_id = $request->search_lang_id;

        $docs = DB::table('docs')
            ->leftJoin('docs_categ', 'docs.categ_id', '=', 'docs_categ.id')        
            ->leftJoin('sys_lang', 'docs.lang_id', '=', 'sys_lang.id')    
            ->select('docs.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang_code', 'docs_categ.title as categ_title', 'docs_categ.slug as categ_slug', DB::raw('(SELECT count(*) FROM docs_images WHERE docs_images.doc_id = docs.id) as count_images'))
            ->orderBy('id', 'desc');            

        if($search_terms) $docs = $docs->where(function ($query) use ($search_terms) {
            $query->where('docs.title', 'like', "%$search_terms%")
                ->orWhere('docs.search_terms', 'like', "%$search_terms%");                    
        }); 

        if($search_lang_id)
            $docs = $docs->where('docs.lang_id', $search_lang_id);      

        if($search_categ_id) {
            $categ = DB::table('docs_categ')->where('id', $search_categ_id)->first();              
            $categ_id = $categ->id;
            $categ_tree_ids = $categ->tree_ids ?? null;
            if($categ_tree_ids) $categ_tree_ids_array = explode(',', $categ_tree_ids);
            $docs = $docs->whereIn('docs.categ_id', $categ_tree_ids_array);   
        }               

        $docs = $docs->paginate(20);       
                
        return view('/admin/account', [
            'view_file'=>'docs.docs',
            'active_submenu'=>'docs',
            'search_terms'=> $search_terms,
            'search_categ_id'=> $search_categ_id,
            'search_lang_id'=> $search_lang_id,
            'docs' => $docs,
            'categories' => $this->categories,
        ]); 
    }   


    /**
    * Show form to add new resource
    */
    public function create()
    {
        if(! check_access('docs')) return redirect(route('admin'));

        return view('admin/account', [
            'view_file'=>'docs.create',
            'active_submenu'=>'docs',
            'categories' => $this->categories,
        ]);
    }


    /**
    * Create new item
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('docs')) return redirect(route('admin'));

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'categ_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.docs'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array        
                
        if($request->featured == 'on')
             $featured = 1;  
        else    
             $featured = 0;  
             
        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        $categ = DB::table('docs_categ')
            ->where('id', $inputs['categ_id'])    
            ->first(); 

        if(DB::table('docs')->where('slug', $slug)->where('lang_id', $categ->lang_id)->exists()) return redirect(route('admin.docs.create'))->with('error', 'duplicate');  

        DB::table('docs')->insert([
            'lang_id' => $categ->lang_id ?? null,
            'title' => $inputs['title'],
            'slug' => $slug,
            'content' => $inputs['content'],
            'categ_id' => $inputs['categ_id'],
            'active' => $inputs['active'],
            'position' => $inputs['position'],
            'created_at' => now(),
            'search_terms' => $inputs['search_terms'],
            'featured' => $featured,
        ]);         
                 
        $this->DocModel->recount_categ_items($inputs['categ_id']);

        return redirect($request->Url())->with('success', 'created'); 
    }



    /**
    * Show form to edit resource     
    */
    public function show(Request $request)
    {
        if(! check_access('docs')) return redirect(route('admin'));

        $doc = DB::table('docs')->where('id', $request->id)->first();          
        if(!$doc) return redirect(route('admin'));
        
        // check permission
        if($this->logged_user_role != 'admin' && check_access('docs', 'author') && $this->logged_user_id != $doc->user_id) return redirect(route('admin'));

        return view('admin/account', [
            'view_file'=>'docs.update',
            'active_submenu'=>'docs',
            'doc' => $doc,
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

        if(! check_access('docs')) return redirect(route('admin'));

        $id = $request->id;
        $doc = DB::table('docs')->where('id', $id)->first();  
        if(!$doc) return redirect(route('admin'));
        $original_categ_id = $doc->categ_id; // used if article change category
        
        // check permission
        if($this->logged_user_role != 'admin' && check_access('docs', 'author') && $this->logged_user_id != $doc->user_id) return redirect(route('admin'));

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'categ_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.docs'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
       
        if($request->featured == 'on')
             $featured = 1;  
        else    
             $featured = 0;  

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        $categ = DB::table('docs_categ')
            ->where('id', $inputs['categ_id'])    
            ->first(); 

        if(DB::table('docs')->where('slug', $slug)->where('lang_id', $categ->lang_id)->where('id', '!=', $id)->exists()) return redirect(route('admin.docs.show', ['id'=>$id]))->with('error', 'duplicate');  

        DB::table('docs')
            ->where('id', $id)
            ->update([
                'lang_id' => $categ->lang_id ?? null,
                'title' => $inputs['title'],
                'slug' => $slug,
                'content' => $inputs['content'],            
                'categ_id' => $inputs['categ_id'], 
                'active' => $inputs['active'],
                'position' => $inputs['position'],
                'search_terms' => $inputs['search_terms'],
                'featured' => $featured,
        ]);              
                 
        $this->DocModel->recount_categ_items($inputs['categ_id']);
        $this->DocModel->recount_categ_items($original_categ_id); // for original categfory, if article change category

        return redirect(route('admin.docs'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {        

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');         

        if(! check_access('docs')) return redirect(route('admin'));        

        $id = $request->id;
        $doc = DB::table('docs')->where('id', $id)->first();          
        if(!$doc) return redirect(route('admin'));                

        // check permission        
        if($this->logged_user_role != 'admin' && check_access('docs', 'author') && $this->logged_user_id != $doc->user_id) return redirect(route('admin'));       

        DB::table('docs')->where('id', $id)->delete();
        DB::table('docs_images')->where('doc_id', $id)->delete();

        $this->DocModel->recount_categ_items($doc->categ_id);

        return redirect(route('admin.docs'))->with('success', 'deleted'); 
    }  


    
     /**
    * Display all images
    */
    public function images(Request $request)
    {

        if(! check_access('docs')) return redirect(route('admin'));

        $id = $request->id; // article ID
        $doc = DB::table('docs')->where('id', $id)->first();  
        if(!$doc) return redirect(route('admin'));

        $images = DB::table('docs_images')
            ->where('doc_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(20);          
      
        return view('admin/account', [
            'view_file' => 'docs.images',
            'active_submenu' => 'docs',
            'images' => $images,
            'doc' => $doc,
            'id' => $id,
        ]); 
    }


    /**
    * Create new resource
    */
    public function create_image(Request $request)
    {

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('docs')) return redirect(route('admin'));        

        $id = $request->id; 
        $description = $request->description;        
        
        $doc = DB::table('docs')->where('id', $id)->first();  
        if(!$doc) return redirect(route('admin'));
        
        // check permission
        if($this->logged_user_role != 'admin' && check_access('docs', 'author') && $this->logged_user_id != $doc->user_id) return redirect(route('admin'));      

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.docs.images', ['id' => $id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $image_db = $this->UploadModel->upload_file($request, 'image');    
            DB::table('docs_images')->insert([
                'doc_id' => $id,
                'description' => $description,
                'file' => $image_db,               
            ]);
        }        
                 
        return redirect(route('admin.docs.images', ['id' => $id]))->with('success', 'created'); 
    }   


    /**
    * Remove the specified resource
    */
    public function delete_image(Request $request)
    {

         // disable action in demo mode:
         if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      
         
        if(! check_access('docs')) return redirect(route('admin'));       

        $id = $request->id;
        $image_id = $request->image_id;       

        $doc = DB::table('docs')->where('id', $request->id)->first();          
        if(!$doc) return redirect(route('admin'));

        if($this->logged_user_role != 'admin' && check_access('docs', 'author') && $this->logged_user_id != $doc->user_id) return redirect(route('admin'));      

        // delete images
        $file = DB::table('docs_images')->where('id', $image_id)->value('file');   

        if($file) delete_image($file);                
        
        DB::table('docs_images')->where('id', $image_id)->delete(); 

        return redirect(route('admin.docs.images', ['id' => $id]))->with('success', 'deleted'); 
    }


}
