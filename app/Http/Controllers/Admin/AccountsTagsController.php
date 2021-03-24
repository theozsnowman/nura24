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
use App\Models\User;
use DB;
use Auth;

class AccountsTagsController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();      
        
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;
            
            $role = $this->UserModel->get_role_from_id ($this->role_id);    
            if(! ($role == 'admin')) return redirect('/'); 
            return $next($request);
        });
    }

    
    /**
     * Show all resources
     */
    public function index(Request $request)
    {                              
        $search_terms = $request->search_terms;
        $search_role_id = $request->search_role_id;

        $tags = DB::table('users_tags')
            ->leftJoin('users_roles', 'users_tags.role_id', '=', 'users_roles.id') 
            ->select('users_tags.*', 'users_roles.role as role', DB::raw('(SELECT count(*) FROM users_tags_accounts WHERE users_tags_accounts.tag_id = users_tags.id) as count_accounts'));

        if($search_terms) $tags = $tags->where('tag', 'like', "%$search_terms%");                              
        if($search_role_id) $tags = $tags->where('role_id', $search_role_id);                              
        $tags = $tags->orderBy('tag', 'asc')->paginate(20);       
        
        $active_roles = DB::table('users_roles')->where('active', 1)->orderBy('role', 'asc')->get();

        return view('admin/account', [
            'view_file'=>'accounts.tags',
            'active_submenu'=>'accounts',
            'search_terms'=> $search_terms,
            'search_role_id'=> $search_role_id,
            'tags' => $tags,            
            'active_roles' => $active_roles,    
        ]);
    }


    /**
    * Create resource
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $inputs = $request->all();     

        $validator = Validator::make($request->all(), [
            'tag' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.accounts.tags'))
                ->withErrors($validator)
                ->withInput();
        } 

        if(DB::table('users_tags')->where('tag', $inputs['tag'])->where('role_id', $inputs['role_id'])->exists()) return redirect(route('admin.accounts.tags'))->with('error', 'duplicate');        

        DB::table('users_tags')->insert([
            'tag' => str_replace(',', '', $inputs['tag']),
            'color' => $inputs['color'] ?? 'b7b7b7',                  
            'role_id' => $inputs['role_id'],   
        ]);                               

        return redirect(route('admin.accounts.tags'))->with('success', 'created'); 
    }   
    

    /**
    * Update resource
    */
    public function update(Request $request)
    {        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;
        $inputs = $request->all();

        $validator = Validator::make($request->all(), [
            'tag' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.accounts.tags'))
                ->withErrors($validator)
                ->withInput();
        } 
        
        if(DB::table('users_tags')->where('tag', $inputs['tag'])->where('role_id', $inputs['role_id'])->where('id', '!=', $id)->exists()) return redirect(route('admin.accounts.tags'))->with('error', 'duplicate'); 
    
        DB::table('users_tags')
            ->where('id', $id)
            ->update([
                'tag' => str_replace(',', '', $inputs['tag']),
                'color' => $inputs['color'] ?? 'b7b7b7',   
                'role_id' => $inputs['role_id'],            
        ]);                            

        return redirect(route('admin.accounts.tags'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('users_tags')->where('id', $id)->delete(); 
        DB::table('users_tags_accounts')->where('tag_id', $id)->delete(); 
        
        return redirect(route('admin.accounts.tags'))->with('success', 'deleted'); 
    }
}
