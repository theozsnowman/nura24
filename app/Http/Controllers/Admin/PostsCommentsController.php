<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\Core;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth; 

class PostsCommentsController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
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
    * All resources
    */
    public function index(Request $request)
    {        
        if(! check_access('posts', 'manager')) return redirect(route('admin'));

        $search_post_id = $request->search_post_id; 

        $comments = DB::table('posts_comments')
            ->leftJoin('posts', 'posts_comments.post_id', '=', 'posts.id')
            ->leftJoin('users', 'posts_comments.user_id', '=', 'users.id')
            ->select('posts_comments.*', 'posts.title as post_title', 'posts.slug as post_slug', 'posts.image as post_image', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar', 'users.slug as author_slug');
    
        if($search_post_id) {
            $comments = $comments->where('posts.id', $search_post_id);
            $post = DB::table('posts')->where('id', $search_post_id)->first();
        }

        $comments = $comments->orderBy('posts_comments.id', 'desc')->paginate(25);       
          
        return view('admin/account', [
            'view_file' => 'posts.comments',
            'active_submenu' => 'posts',
            'search_post_id' => $search_post_id,
            'post' => $post ?? null,   
            'comments' => $comments,            
        ]); 
    }
    

    /**
    * Delete resource
    */
    public function destroy(Request $request)
    {
        if(! check_access('posts', 'manager')) return redirect(route('admin'));

        $id = $request->id; 
        $search_post_id = $request->search_post_id; 

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');                 

        DB::table('posts_comments')->where('id', $id)->delete(); 

        return redirect(route('admin.posts.comments', ['search_post_id' => $search_post_id]))->with('success', 'deleted'); 
    }

}
