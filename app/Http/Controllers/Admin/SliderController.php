<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\Core;
use App\Models\User;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth; 

class SliderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->UploadModel = new Upload();    
        $this->config = Core::config();      
        
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

        if(! check_access('slider')) return redirect(route('admin'));

        $search_lang_id = $request->search_lang_id;

        $slides = DB::table('slider')
            ->leftJoin('sys_lang', 'slider.lang_id', '=', 'sys_lang.id')
            ->select('slider.*', 'sys_lang.name as lang_name');

        if($search_lang_id)
            $slides = $slides->where('lang_id', $search_lang_id);                                    
       
        $slides = $slides->orderBy('position', 'asc')
            ->orderBy('title', 'asc')
            ->paginate(15);       
                
        return view('/admin/account', [
            'view_file'=>'slider.slider',
            'active_submenu'=>'slider',
            'slides' => $slides,
            'search_lang_id'=> $search_lang_id,
        ]); 
    }   


    /**
    * Create new slide
    */
    public function store(Request $request)
    {

        if(! check_access('slider')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.slider'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array 
        
        if(!$inputs['position']) {
            $q = DB::table('slider')->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position']; 

        DB::table('slider')->insert([
            'lang_id' => $inputs['lang_id'] ?? default_lang()->id,
            'title' => $inputs['title'],
            'content' => $inputs['content'],
            'active' => $inputs['active'],
            'url' => $inputs['url'],
            'target' => $inputs['target'],
            'position' => $position ?? 1,
        ]);

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.slider'))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $id = DB::getPdo()->lastInsertId(); 
            $image_db = $this->UploadModel->upload_image($request, 'image');    
            DB::table('slider')->where('id', $id)->update(['image' => $image_db]);            
        }        
                 
        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {

        if(! check_access('slider')) return redirect(route('admin'));

        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.slider'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if(!$inputs['position']) {
            $q = DB::table('slider')->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position']; 
        
        DB::table('slider')
            ->where('id', $id)
            ->update([
                'lang_id' => $inputs['lang_id'] ?? default_lang()->id,
                'title' => $inputs['title'],
                'content' => $inputs['content'],
                'active' => $inputs['active'],
                'url' => $inputs['url'],
                'target' => $inputs['target'],
                'position' => $position ?? 1,
        ]);

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.slider'))
                    ->withErrors($validator)
                    ->withInput();
            } 
            
            $image_db = $this->UploadModel->upload_image($request, 'image');    
            DB::table('slider')->where('id', $id)->update(['image' => $image_db]);            
        }        
                 
        return redirect(route('admin.slider'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {

        if(! check_access('slider')) return redirect(route('admin'));

        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $slide = DB::table('slider')->where('id', $id)->first();                
        delete_image($slide->image);      

        DB::table('slider')->where('id', $id)->delete(); // delete slide

        return redirect(route('admin.slider'))->with('success', 'deleted'); 
    }


    /**
    * Update slider config
    */
    public function config(Request $request)
    {          
        
        if(! ($this->logged_user_role == 'admin')) return redirect(route('admin')); 

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $inputs = $request->except('_token'); // retrieve all of the input data as an array 

        // slider enabled / disabled
        if($request->slider_enabled == 'on')
            $slider_enabled = 1;  
        else    
            $slider_enabled = 0;  

        DB::table('sys_config')->updateOrInsert(
            ['name' => 'slider_enabled'],
            ['value' => $slider_enabled]
        );
        

        // slider bg color
        DB::table('sys_config')->updateOrInsert(
            ['name' => 'slider_background_color'],
            ['value' => $inputs['slider_background_color']]
        );

        // process background image        
        if ($request->hasFile('main_bg')) {
            $image_db = $this->UploadModel->upload_file($request, 'main_bg');  
            DB::table('sys_config')->updateOrInsert(['name' => 'slider_main_background'], ['value' => $image_db]);      
        }        
                 
        return redirect(route('admin.slider'))->with('success', 'updated'); 
    }

}
