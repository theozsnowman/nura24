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

class DownloadsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->UploadModel = new Upload();   

        $this->extra_langs = DB::table('sys_lang')->where('is_default', 0)->orderBy('active', 'desc')->orderBy('name', 'asc')->get();

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
        if(! check_access('downloads')) return redirect(route('admin'));
        
        $search_terms = $request->search_terms;
        $search_badge = $request->search_badge;

        $downloads = DB::table('downloads')
            ->select('downloads.*', DB::raw('(SELECT count(*) FROM downloads_files WHERE downloads_files.download_id = downloads.id) as count_files'),  DB::raw('(SELECT SUM(count_downloads) FROM downloads_files WHERE downloads_files.download_id = downloads.id) as count_downloads'))      
            ->orderBy('active', 'asc')
            ->orderBy('id', 'desc');
                
        if($search_terms)
            $downloads = $downloads->where('title', 'like', "%$search_terms%");            

        if($search_badge)
            $downloads = $downloads->whereRaw("FIND_IN_SET(?, badges) > 0", [$search_badge]);                 
             
        $downloads = $downloads->paginate(15);       
                
        return view('admin/account', [
            'view_file'=>'downloads.downloads',
            'active_submenu'=>'downloads',
            'search_terms'=> $search_terms,
            'search_badge'=> $search_badge,
            'downloads' => $downloads,
        ]); 
    }


    /**
    * Show form to add new resource
    */
    public function create()
    {
        if(! check_access('downloads')) return redirect(route('admin'));

        return view('admin/account', [
            'view_file'=>'downloads.create',
            'active_submenu'=>'downloads',
        ]);
    }


    /**
    * Create new resource
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('downloads')) return redirect(route('admin'));

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.downloads.create'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');
        
        if(DB::table('downloads')->where('slug', $slug)->exists()) return redirect(route('admin.downloads.create'))->with('error', 'duplicate');  

        if ($request->has('login_required')) $login_required = 1; else $login_required = 0;

        DB::table('downloads')->insert([
            'title' => $inputs['title'],
            'summary' => $inputs['summary'],
            'content' => $inputs['content'],
            'slug' => $slug,
            'active' => $inputs['active'],
            'meta_title' => $inputs['meta_title'],
            'meta_description' => $inputs['meta_description'],
            'login_required' => $login_required,
            'custom_tpl' => $inputs['custom_tpl'],
            'badges' => str_replace(' ', '', $inputs['badges']),
            'created_at' => now(),
        ]);
           
        $download_id = DB::getPdo()->lastInsertId();  

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.downloads.create'))
                    ->withErrors($validator)
                    ->withInput();
            } 
                        
            $image_db = $this->UploadModel->upload_image($request, 'image', 'resize');    
            DB::table('downloads')->where('id', $download_id)->update(['image' => $image_db]);            
        }                              

        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Show form to edit resource     
    */
    public function show(Request $request)
    {
        if(! check_access('downloads')) return redirect(route('admin'));

        $download = DB::table('downloads')
            ->where('id', $request->id)
            ->first();  
        
        if(!$download) abort(404);
            
        return view('admin/account', [
            'view_file'=>'downloads.update',
            'menu_tab' => 'details',
            'active_submenu'=>'downloads',
            'download' => $download,
            'extra_langs' => $this->extra_langs,      
        ]);
    }


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id;          

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect($request->Url())
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        if(DB::table('downloads')->where('slug', $slug)->where('id', '!=', $id)->exists()) return redirect(route('admin.downloads.show', ['id'=>$id]))->with('error', 'duplicate');  
        
        if ($request->has('login_required')) $login_required = 1; else $login_required = 0;

        DB::table('downloads')
            ->where('id', $id)
            ->update([
                'title' => $inputs['title'],
                'summary' => $inputs['summary'],
                'content' => $inputs['content'],           
                'slug' => $slug,
                'active' => $inputs['active'],
                'meta_title' => $inputs['meta_title'],
                'meta_description' => $inputs['meta_description'],
                'login_required' => $login_required,
                'custom_tpl' => $inputs['custom_tpl'],
                'badges' => str_replace(' ', '', $inputs['badges']),
        ]);
              
         // process image        
         if ($request->hasFile('image')) {

            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.downloads.show', ['id' => $id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $image_db = $this->UploadModel->upload_image($request, 'image', 'resize');    
            DB::table('downloads')->where('id', $id)->update(['image' => $image_db]);            
        }                 

        return redirect(route('admin.downloads'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id;  
        
        $q = DB::table('downloads')
            ->where('id', $id)
            ->first(); 
        
        DB::table('downloads_files')->where('download_id', $id)->delete(); 
        DB::table('downloads_logs')->where('download_id', $id)->delete(); 
        DB::table('downloads_langs')->where('download_id', $id)->delete(); 
        DB::table('downloads')->where('id', $id)->delete(); 

        return redirect(route('admin.downloads'))->with('success', 'deleted'); 
    }



    /**
    * Display download files
    */
    public function files(Request $request)
    {
        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id;
        
        $download = DB::table('downloads')
            ->where('id', $id)      
            ->first();

        if(!$download) abort(404);    
                
        $files = DB::table('downloads_files')
            ->where('download_id', $id)
            ->orderBy('active', 'asc')
            ->orderBy('featured', 'desc')
            ->orderBy('version', 'asc')
            ->paginate(20);       
                
        return view('admin/account', [
            'view_file'=>'downloads.download-files',
            'active_submenu'=>'downloads',
            'download' => $download,
            'files' => $files,
            'menu_tab' => 'files',
            'extra_langs' => $this->extra_langs,      
        ]); 
    }


    /**
    * Create file
    */
    public function create_file(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id;

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.download.files', ['id'=>$id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        DB::table('downloads_files')->insert([
            'title' => $inputs['title'],
            'hash' => Str::random(16), 
            'download_id' => $id,
            'active' => $inputs['active'],
            'featured' => $inputs['featured'],
            'version' => $inputs['version'],
            'release_date' => $inputs['release_date'],
            'count_downloads' => 0,
            'created_at' => now(),
        ]);
           
        // process file
        if ($request->hasFile('file')) {
            $file_id = DB::getPdo()->lastInsertId(); 
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('downloads_files')->where('id', $file_id)->update(['file' => $file_db]);            
        } 

        return redirect(route('admin.download.files', ['id'=>$id]))->with('success', 'created'); 
    }

    
    /**
    * Update file     
    */
    public function update_file(Request $request)
    {        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id;  
        $file_id = $request->file_id;          

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect($request->Url())
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array          

        DB::table('downloads_files')
            ->where('id', $file_id)
            ->update([
                'title' => $inputs['title'],
                'hash' => Str::random(16), 
                'download_id' => $id,
                'active' => $inputs['active'],
                'featured' => $inputs['featured'],
                'version' => $inputs['version'],
                'release_date' => $inputs['release_date'],
        ]);
      
         // process file
         if ($request->hasFile('file')) {
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('downloads_files')->where('id', $file_id)->update(['file' => $file_db]);            
        } 

        return redirect(route('admin.download.files', ['id' => $id]))->with('success', 'updated'); 
    }


    /**
    * Delete file
    */
    public function delete_file(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id;  
        $file_id = $request->file_id;  
        
        DB::table('downloads_files')->where('id', $file_id)->delete(); 
        DB::table('downloads_logs')->where('file_id', $file_id)->delete(); 

        return redirect(route('admin.download.files', ['id'=>$id]))->with('success', 'deleted'); 
    }


    /**
    * Downloads logs
    */
    public function logs(Request $request)
    {
        if(! check_access('downloads')) return redirect(route('admin'));
        
        $search_download_id = $request->search_download_id;
        $search_file_id = $request->search_file_id;

        $logs = DB::table('downloads_logs')
            ->leftJoin('users', 'downloads_logs.user_id', '=', 'users.id') 
            ->leftJoin('downloads', 'downloads_logs.download_id', '=', 'downloads.id') 
            ->leftJoin('downloads_files', 'downloads_logs.file_id', '=', 'downloads_files.id') 
            ->select('downloads_logs.*', 'downloads.title as download_title', 'downloads_files.title as download_file_title', 'downloads_files.file as download_file', 'users.name as user_name', 'users.email as user_email', 'users.avatar as user_avatar')      
            ->orderBy('id', 'desc');
                
        if($search_download_id)
            $logs = $logs->where('downloads_logs.download_id', $search_download_id);            

        if($search_file_id)
            $logs = $logs->where('downloads_logs.file_id', $search_file_id);            
             
        $logs = $logs->paginate(25);       
                
        return view('admin/account', [
            'view_file'=>'downloads.logs',
            'active_submenu'=>'downloads',
            'search_download_id' => $search_download_id,
            'search_file_id' => $search_file_id,
            'logs' => $logs,
        ]); 
    }


    /**
    * Display product images
    */
    public function images(Request $request)
    {
        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id;

        $images = DB::table('downloads_images')
            ->where('download_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(25);       

        $download = DB::table('downloads')
            ->where('id', $id)
            ->first();  
        if(!$download) abort(404);
      
        return view('admin/account', [
            'view_file' => 'downloads.download-images',
            'active_submenu' => 'downloads',
            'menu_tab' => 'images',
            'images' => $images,
            'download' => $download,
            'id' => $id,
            'extra_langs' => $this->extra_langs,
        ]); 
    }


    /**
    * Create new image
    */
    public function store_image(Request $request)
    {
        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id;
        $description = $request->description;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.download.images', ['id' => $id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $image_db = $this->UploadModel->upload_image($request, 'image', 'resize');    
            DB::table('downloads_images')->insert([
                'download_id' => $id,
                'description' => $description,
                'file' => $image_db,               
            ]);
        }        
                 
        return redirect(route('admin.download.images', ['id' => $id]))->with('success', 'created'); 
    }   


    /**
    * Remove timage
    */
    public function destroy_image(Request $request)
    {
        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id;
        $image_id = $request->image_id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        // delete image
        $image = DB::table('downloads_images')->where('id', $image_id)->first();   
        delete_image($image->file);     
        
        DB::table('downloads_images')->where('id', $image_id)->delete(); 

        return redirect(route('admin.download.images', ['id' => $id]))->with('success', 'deleted'); 
    }

     /**
    * Translate
    */
    public function translate(Request $request)
    {
        if(! check_access('downloads')) return redirect(route('admin'));

        $id = $request->id; // product id
        $download = DB::table('downloads')          
            ->where('id', $id)
            ->first();
        if(! $download) return redirect(route('admin.downloads')); 

        $translate_langs = DB::table('sys_lang')
            ->select('sys_lang.*', 
                DB::raw('(SELECT title FROM downloads_langs WHERE lang_id = sys_lang.id AND download_id = '.$id.') as translated_title'),
                DB::raw('(SELECT summary FROM downloads_langs WHERE lang_id = sys_lang.id AND download_id = '.$id.') as translated_summary'),
                DB::raw('(SELECT meta_title FROM downloads_langs WHERE lang_id = sys_lang.id AND download_id = '.$id.') as translated_meta_title'),
                DB::raw('(SELECT content FROM downloads_langs WHERE lang_id = sys_lang.id AND download_id = '.$id.') as translated_content'),
                DB::raw('(SELECT meta_description FROM downloads_langs WHERE lang_id = sys_lang.id AND download_id = '.$id.') as translated_meta_description'))
            ->where('is_default', 0)
            ->orderBy('active', 'desc')
            ->orderBy('name', 'asc')
            ->get();         

        //dd($extra_langs);

        return view('admin/account', [
            'view_file' => 'downloads.download-translate',
            'active_submenu' => 'downloads',
            'menu_tab' => 'translates',
            'download' => $download,
            'translate_langs' => $translate_langs,
            'extra_langs' => $this->extra_langs,
        ]);
    }



    /**
    * Update translates
    */
    public function update_translate(Request $request)
    {        
        if(! check_access('downloads')) return redirect(route('admin'));
        
        $id = $request->id;  
        $download = DB::table('downloads')          
            ->where('id', $id)
            ->first();
        if(! $download) return redirect(route('admin.downloads')); 

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $inputs = $request->all(); // retrieve all of the input data as an array 
               
        $extra_langs = DB::table('sys_lang')->where('is_default', 0)->orderBy('active', 'desc')->orderBy('name', 'asc')->get();
        foreach($extra_langs as $lang) {                                   
                DB::table('downloads_langs')
                ->updateOrInsert(
                    ['download_id' => $download->id, 'lang_id' => $lang->id],
                    ['title' => $request['title_'.$lang->id], 'content' => $request['content_'.$lang->id], 'summary' => $request['summary_'.$lang->id], 'meta_title' => $request['meta_title_'.$lang->id], 'meta_description' => $request['meta_description_'.$lang->id]],
                );            
        }

        return redirect(route('admin.download.translate', ['id' => $id]))->with('success', 'updated'); 
    }

}
