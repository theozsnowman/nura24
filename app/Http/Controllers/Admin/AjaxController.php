<?php
/**
 * Copyright Nura24: #1 Free CMS for businesses, communities, bloggers and personal websites
 * Nura24 is a free CMS suite with eCommerce, Community Forum, HelpDesk, Blog, Booking System, Classifieds, CRM and Marketing.
 * Author: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\Core;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Upload;
use App\Models\User;
use DB;
use Auth;

class AjaxController extends Controller
{
    /**
     * Create a new controller instance.
     * Check if logged user role is 'admin'. If not, redirect to home
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();      
        $this->UploadModel = new Upload();    
        
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;
            
            $role = $this->UserModel->get_role_from_id ($this->role_id);    
            if(! ($role == 'admin')) return redirect('/'); 
            return $next($request);
        });
    }

    
    /**
     * Search in registered users accounts
     */
    public function users(Request $request)
    {                    
        $role_id = $this->UserModel->get_role_id_from_role('user');

        $term = $request->input('term', '');

        if (empty($term)) {
           return null;
        }

        $users = DB::table('users')   
            ->where('role_id', $role_id)
            ->where(function($query) use ($term) {
                $query->where('name', 'like', "%$term%")
                      ->orwhere('email', 'like', "%$term%")
                      ->orwhere('code', 'like', "%$term%");
                })
            ->limit(25)
            ->get(['id', DB::raw('CONCAT(`name`, " - ", `email`, " - ", UPPER(`code`)) AS text')]);

        return ['results' => $users];
        
    }


    /**
     * Search in internals accounts
     */
    public function internals(Request $request)
    {                    
        $role_id = $this->UserModel->get_role_id_from_role('internal');

        $term = $request->input('term', '');

        if (empty($term)) {
           return null;
        }

        $users = DB::table('users')   
            ->where('role_id', $role_id)
            ->where(function($query) use ($term) {
                $query->where('name', 'like', "%$term%")
                      ->orwhere('email', 'like', "%$term%")
                      ->orwhere('code', 'like', "%$term%");
                })
            ->limit(25)
            ->get(['id', DB::raw('CONCAT(`name`, " - ", `email`, " - ", UPPER(`code`)) AS text')]);

        return ['results' => $users];
        
    }


    /**
     * Search in staff accounts (admins)
     */
    public function admins(Request $request)
    {                    
        $role_id_admin = $this->UserModel->get_role_id_from_role('admin');

        $term = $request->input('term', '');

        if (empty($term)) {
           return null;
        }

        $users = DB::table('users')   
            ->where('role_id', $role_id_admin)
            ->where(function($query) use ($term) {
                $query->where('name', 'like', "%$term%")
                      ->orwhere('email', 'like', "%$term%")
                      ->orwhere('code', 'like', "%$term%");
                })
            ->limit(25)
            ->get(['id', DB::raw('CONCAT(`name`, " - ", `email`, " - ", UPPER(`code`)) AS text')]);

        return ['results' => $users];
        
    }


    /**
     * Search in all accounts
     */
    public function accounts(Request $request)
    {                    
        $term = $request->input('term', '');

        if (empty($term)) {
           return null;
        }

        $users = DB::table('users')   
            ->where(function($query) use ($term) {
                $query->where('name', 'like', "%$term%")
                      ->orwhere('email', 'like', "%$term%")
                      ->orwhere('code', 'like', "%$term%");
                })
            ->limit(25)
            ->get(['id', DB::raw('CONCAT(`name`, " - ", `email`, " - ", UPPER(`code`)) AS text')]);

        return ['results' => $users];
        
    }


    /**
     * Search in tags
     */
    public function tags(Request $request)
    {                    
        $term = $request->input('term', '');
        $term = trim($term);

        $array = array();

        if (empty($term)) {
           return array();
        }
        
        $tags = DB::table('posts_tags')               
            ->where('tag', 'like', "%$term%")
            ->orderBy('counter', 'desc')            
            ->limit(25)
            ->get();

        foreach($tags as $tag) {
            if (! in_array($tag->tag, $array))
                array_push($array, $tag->tag);
        }


        return json_encode($array);
        
    }
}
