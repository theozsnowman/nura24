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
use Image;

class ForumActivityController extends Controller
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

            if(! ($this->logged_user_role == 'admin' || $this->logged_user_role == 'internal')) return redirect('/'); 
            return $next($request);
        });
    } 


    /**
    * Display all topics
    */
    public function topics(Request $request)
    {

        if(! check_access('forum')) return redirect(route('admin'));

        $search_terms = $request->search_terms;        
        $search_status = $request->search_status;  
        $search_sticky = $request->search_sticky;  
        $search_type = $request->search_type;  

        $topics = DB::table('forum_topics')
            ->leftJoin('forum_categ', 'forum_topics.categ_id', '=', 'forum_categ.id')            
            ->leftJoin('users', 'forum_topics.user_id', '=', 'users.id')
            ->select('forum_topics.*',  'forum_categ.slug as categ_slug', 'forum_categ.title as categ_title', 'forum_categ.slug as categ_slug', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar', DB::raw("(SELECT count(*) FROM forum_posts WHERE forum_topics.id = forum_posts.topic_id) count_posts"));

        if($search_terms) $topics = $topics->where('users.name', 'like', "%$search_terms%");
        if($search_status) $topics = $topics->where('forum_topics.status', 'like', $search_status);
        if($search_type) $topics = $topics->where('forum_topics.type', 'like', $search_type);
        if($search_sticky==1) $topics = $topics->where('forum_topics.sticky', 1);

        $topics = $topics->orderBy('forum_topics.id', 'desc')->paginate(20);

        return view('admin/account', [
            'view_file' => 'forum.topics',
            'active_submenu' => 'forum.topics',
            'topics' => $topics,
            'search_terms' => $search_terms,
            'search_status' => $search_status,
            'search_sticky' => $search_sticky,
            'search_type' => $search_type,
        ]); 
    }



    /**
    * Display all posts
    */
    public function posts(Request $request)
    {
        if(! check_access('forum')) return redirect(route('admin'));

        $search_terms = $request->search_terms;        

        $posts = DB::table('forum_posts')
            ->leftJoin('forum_categ', 'forum_posts.categ_id', '=', 'forum_categ.id')
            ->leftJoin('forum_topics', 'forum_posts.topic_id', '=', 'forum_topics.id')
            ->leftJoin('users', 'forum_posts.user_id', '=', 'users.id')
            ->select('forum_posts.*', 'forum_categ.title as categ_title', 'forum_categ.slug as categ_slug', 'forum_topics.id as topic_id', 'forum_topics.title as topic_title', 'forum_topics.slug as topic_slug', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar');

        if($search_terms) $posts = $posts->where('users.name', 'like', "%$search_terms%");
        
        $posts = $posts->orderBy('forum_posts.id', 'desc')->paginate(20);

        return view('admin/account', [
            'view_file' => 'forum.posts',
            'active_submenu' => 'forum.posts',
            'posts' => $posts,
            'search_terms' => $search_terms,
        ]); 
    }


    /**
    * Delete post
    */
    public function delete_post(Request $request)
    {
        if(! check_access('forum')) return redirect(route('admin'));

        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $post = DB::table('forum_posts')->where('id', $id)->first();  
        $categ_id = $post->categ_id ?? null;
        
        DB::table('forum_posts')->where('id', $id)->delete(); 

        $this->ForumModel->update_topic_last_activity($post->topic_id);   
        $this->ForumModel->recount_categ_items($categ_id);
        $this->ForumModel->recount_user_activity($post->user_id);

        return redirect(route('admin.forum.posts'))->with('success', 'deleted'); 
    }


    /**
    * Update topic
    */
    public function update_topic(Request $request)
    {
        if(! check_access('forum')) return redirect(route('admin'));

        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $inputs = $request->all(); // retrieve all of the input data as an array  

         DB::table('forum_topics')
            ->where('id', $id)
            ->update([
                'title' => $inputs['title'],
                'content' => $inputs['content'],
                'type' => $inputs['type'],
                'status' => $inputs['status'],            
                'updated_at' => now(),
        ]);  
        
        return redirect(route('admin.forum.topics'))->with('success', 'updated'); 
    }



    /**
    * Delete topic
    */
    public function delete_topic(Request $request)
    {
        if(! check_access('forum')) return redirect(route('admin'));

        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $topic = DB::table('forum_topics')->where('id', $id)->first();  
        $categ_id = $topic->categ_id ?? null;

        // get topic users (used to recount activity for each user)
        $topic_posts = DB::table('forum_posts')->where('topic_id', $id)->get();
        $topic_users = array();
        foreach($topic_posts as $post) {
            $post_user_id = $post->user_id;
            array_push($topic_users, $post_user_id);
        }
        $topic_users = array_unique($topic_users);

        DB::table('forum_posts')->where('topic_id', $id)->delete();
        DB::table('forum_topics')->where('id', $id)->delete();
        DB::table('forum_reports')->where('topic_id', $id)->delete();
                
        $this->ForumModel->recount_categ_items($categ_id);

        // recount user activity for topic author
        $this->ForumModel->recount_user_activity($topic->user_id);            

        // recount users activity for topic posts users            
        foreach($topic_users as $post_user_id) {                
            echo($post_user_id);
            $this->ForumModel->recount_user_activity($post_user_id);
        }
      
        return redirect(route('admin.forum.topics'))->with('success', 'deleted'); 
    }

}

