<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Core;
use App\Models\Forum;
use DB;
use Auth; 

class ForumDashboardController extends Controller
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
     * Show the admin dashboard.
     */
    public function index()
    {                
        if(! check_access('forum')) return redirect(route('admin'));

        $count_topics = DB::table('forum_topics')->count();
        $count_topics_latest_24h = DB::table('forum_topics')
            ->where('created_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 1 DAY'))
            ->count();

        $count_posts = DB::table('forum_posts')->count();
        $count_posts_latest_24h = DB::table('forum_posts')
            ->where('created_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 1 DAY'))
            ->count();      
            
        $count_unprocessed_reports = DB::table('forum_reports')->where('processed', '!=', 1)->count();

        $count_reports_latest_24h = DB::table('forum_reports')
            ->where('created_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 1 DAY'))
            ->count();                    

        $latest_topics = DB::table('forum_topics')
            ->leftJoin('forum_categ', 'forum_topics.categ_id', '=', 'forum_categ.id')   
            ->leftJoin('users', 'forum_topics.user_id', '=', 'users.id')
            ->select('forum_topics.*',  'forum_categ.title as categ_title', 'forum_categ.slug as categ_slug', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar', DB::raw("(SELECT count(*) FROM forum_posts WHERE forum_topics.id = forum_posts.topic_id) count_posts"))
            ->where('forum_topics.status', 'like', 'active') 
            ->orderBy('forum_topics.id', 'desc')
            ->limit(10)
            ->get();

        $latest_posts = DB::table('forum_posts')
            ->leftJoin('forum_categ', 'forum_posts.categ_id', '=', 'forum_categ.id')
            ->leftJoin('forum_topics', 'forum_posts.topic_id', '=', 'forum_topics.id')
            ->leftJoin('users', 'forum_posts.user_id', '=', 'users.id')
            ->select('forum_posts.*', 'forum_categ.title as categ_title', 'forum_categ.slug as categ_slug', 'forum_topics.id as topic_id', 'forum_topics.title as topic_title', 'forum_topics.slug as topic_slug', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar')
            ->where('forum_posts.status', 'like', 'active') 
            ->orderBy('forum_posts.id', 'desc')
            ->limit(10)
            ->get();

        return view('admin/account', [
            'view_file' => 'forum.dashboard', 
            'active_submenu' => 'forum_dashboard', 

            'count_topics' => $count_topics,
            'count_topics_latest_24h' => $count_topics_latest_24h,
            'count_posts' => $count_posts,
            'count_posts_latest_24h' => $count_posts_latest_24h,
            'count_unprocessed_reports' => $count_unprocessed_reports,
            'count_reports_latest_24h' => $count_reports_latest_24h,

            'latest_topics' => $latest_topics,
            'latest_posts' => $latest_posts,
        ]);
    }
}
