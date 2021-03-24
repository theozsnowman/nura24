<?php
/**
 * Copyright Nura24: #1 Free CMS for businesses, communities, bloggers and personal websites
 * Nura24 is a free CMS suite with eCommerce, Community Forum, HelpDesk, Blog, Booking System, Classifieds, CRM and Marketing.
 * Author: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Core;
use DB;
use Auth; 
use App; 

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {        
        $this->config = Core::config();  
    }

    /**
    * Display static page
    */
    public function index(Request $request)
    {                         
        $id = $request->id;
        $slug = $request->slug;

        $user = DB::table('users')
            ->where('id', $id)        
            ->where('slug', $slug)        
            ->where('active', 1)    
            ->first();            
        if(!$user) abort(404); 
       
        $posts = DB::table('posts')
            ->where('user_id', $id)        
            ->where('status', 'active')    
            ->paginate($config->posts_per_page ?? 12);

        $bio = user_extra($user->id, 'bio');

        return view('frontend/'.$this->config->template.'/profile', [          
            'user' => $user,            
            'posts' => $posts,   
            'bio' => $bio,   
        ]);
    }

}
