<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Core;
use DB;
use Auth; 
use Illuminate\Support\Carbon; 

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
     * Show the admin dashboard.
     */
    public function index()
    {                
        $count_accounts = DB::table('users')->count();

        $count_accounts_last_month = DB::table('users')
            ->where('created_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 30 DAY'))
            ->count();

        $count_accounts_today = DB::table('users')
            ->whereDate('created_at', Carbon::today())
            ->count();                    

        $count_inbox = DB::table('inbox')
            ->count();   

        $count_inbox_unread = DB::table('inbox')
            ->where('is_read', 0)
            ->count();      

        $latest_inbox = DB::table('inbox')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();                        

        $latest_accounts = DB::table('users')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();  
            
        $count_open_tickets = DB::table('tickets')
            ->whereNull('closed_at')
            ->count();   

        $count_pending_tickets = DB::table('tickets')
            ->whereNull('closed_at')    
            ->where('last_response', 'client')
            ->count();   
        
        $count_paid_orders = DB::table('cart_orders')
            ->where('is_paid', 1)
            ->count();   

        $count_unpaid_orders = DB::table('cart_orders')
            ->where('is_paid', 0)
            ->count();   

        $count_paid_orders_last_month = DB::table('cart_orders')
            ->where('is_paid', 1)
            ->where('paid_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 30 DAY'))
            ->count();

        $count_forum_topics = DB::table('forum_topics')->count();
        $count_forum_topics_latest_24h = DB::table('forum_topics')
                ->where('created_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 1 DAY'))
                ->count();
    
        $count_forum_posts = DB::table('forum_posts')->count();
        $count_forum_posts_latest_24h = DB::table('forum_posts')
                ->where('created_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 1 DAY'))
                ->count();      
                
        $count_forum_unprocessed_reports = DB::table('forum_reports')->where('processed', '!=', 1)->count();
    
        $count_forum_reports_latest_24h = DB::table('forum_reports')
                ->where('created_at', '>=', DB::raw('DATE(NOW()) - INTERVAL 1 DAY'))
                ->count();                    
    
        $latest_forum_topics = DB::table('forum_topics')
                ->leftJoin('forum_categ', 'forum_topics.categ_id', '=', 'forum_categ.id')   
                ->leftJoin('users', 'forum_topics.user_id', '=', 'users.id')
                ->select('forum_topics.*',  'forum_categ.title as categ_title', 'forum_categ.slug as categ_slug', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar', DB::raw("(SELECT count(*) FROM forum_posts WHERE forum_topics.id = forum_posts.topic_id) count_posts"))
                ->where('forum_topics.status', 'like', 'active') 
                ->orderBy('forum_topics.id', 'desc')
                ->limit(10)
                ->get();
    
        $latest_forum_posts = DB::table('forum_posts')
                ->leftJoin('forum_categ', 'forum_posts.categ_id', '=', 'forum_categ.id')
                ->leftJoin('forum_topics', 'forum_posts.topic_id', '=', 'forum_topics.id')
                ->leftJoin('users', 'forum_posts.user_id', '=', 'users.id')
                ->select('forum_posts.*', 'forum_categ.title as categ_title', 'forum_categ.slug as categ_slug', 'forum_topics.id as topic_id', 'forum_topics.title as topic_title', 'forum_topics.slug as topic_slug', 'users.name as author_name', 'users.slug as author_slug', 'users.avatar as author_avatar')
                ->where('forum_posts.status', 'like', 'active') 
                ->orderBy('forum_posts.id', 'desc')
                ->limit(10)
                ->get();

        if($this->logged_user_role == 'admin') $view_file = 'core.dashboard';
        if($this->logged_user_role == 'internal') $view_file = 'core.dashboard-internal';

        // internal logged user permissions
        $user_permissions = DB::table('users_permissions')
            ->leftJoin('sys_modules', 'users_permissions.module_id', '=', 'sys_modules.id')
            ->leftJoin('sys_permissions', 'users_permissions.permission_id', '=', 'sys_permissions.id')
            ->select('users_permissions.*', 'sys_modules.module as module', 'sys_modules.label as module_label', 'sys_permissions.permission as permission', 'sys_permissions.label as permission_label', 'sys_permissions.description as permission_description')    
            ->where('user_id', Auth::user()->id)
            ->get();


        return view('admin/account', [
            'view_file' => $view_file, 
            'active_submenu' => 'dashboard', 

            'count_accounts' => $count_accounts,
            'count_accounts_last_month' => $count_accounts_last_month,
            'count_accounts_today' => $count_accounts_today,
            'count_inbox' => $count_inbox,
            'count_inbox_unread' => $count_inbox_unread,
            'latest_inbox' => $latest_inbox,
            'latest_accounts' => $latest_accounts,
            'count_open_tickets' => $count_open_tickets,
            'count_pending_tickets' => $count_pending_tickets,
            'count_paid_orders' => $count_paid_orders,
            'count_unpaid_orders' => $count_unpaid_orders,
            'count_paid_orders_last_month' => $count_paid_orders_last_month,
            'user_permissions' => $user_permissions,

            'count_forum_topics' => $count_forum_topics,
            'count_forum_topics_latest_24h' => $count_forum_topics_latest_24h,
            'count_forum_posts' => $count_forum_posts,
            'count_forum_posts_latest_24h' => $count_forum_posts_latest_24h,
            'count_forum_unprocessed_reports' => $count_forum_unprocessed_reports,
            'count_forum_reports_latest_24h' => $count_forum_reports_latest_24h,
            'latest_forum_topics' => $latest_forum_topics,
            'latest_forum_posts' => $latest_forum_posts,
        ]);
    }
}
