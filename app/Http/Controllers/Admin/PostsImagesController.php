<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth; 
use App\Models\Upload;
use Illuminate\Support\Facades\Storage;

class PostsImagesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->UploadModel = new Upload();    
        
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

        if(! check_access('posts')) return redirect(route('admin'));

        $id = $request->id;

        $images = DB::table('posts_images')
            ->where('post_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(20);       

        $post = DB::table('posts')
            ->where('id', $id)
            ->first();  

        if(!$post) abort(404);
      
        return view('admin/account', [
            'view_file'=>'posts.images',
            'active_submenu'=>'posts',
            'images' => $images,
            'post' => $post,
            'id' => $id,
        ]); 
    }


    /**
    * Create new resource
    */
    public function store(Request $request)
    {
        if(! check_access('posts')) return redirect(route('admin'));

        $id = $request->id;
        $description = $request->description;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.posts.images', ['id' => $id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $image_db = $this->UploadModel->upload_image($request, 'image');    
            DB::table('posts_images')->insert([
                'post_id' => $id,
                'description' => $description,
                'file' => $image_db,               
            ]);
        }        
                 
        return redirect(route('admin.posts.images', ['id' => $id]))->with('success', 'created'); 
    }   


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        if(! check_access('posts', 'manager')) return redirect(route('admin'));

        $id = $request->id;
        $image_id = $request->image_id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        // delete images
        $image = DB::table('posts_images')->where('id', $image_id)->first();   

        delete_image($image->file);     
        
        DB::table('posts_images')->where('id', $image_id)->delete(); 

        return redirect(route('admin.posts.images', ['id' => $id]))->with('success', 'deleted'); 
    }

}
