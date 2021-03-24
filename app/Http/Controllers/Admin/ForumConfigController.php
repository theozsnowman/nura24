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
use DB;
use Auth; 
use Image;

class ForumConfigController extends Controller
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

            if(! ($this->logged_user_role == 'admin')) return redirect('/'); 
            return $next($request);
        });
    } 


    /**
    * Display all resources
    */
    public function index(Request $request)
    {
        return view('admin/account', [
            'view_file' => 'forum.config',
            'active_submenu' => 'forum.config',
        ]); 
    }   


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $input = $request->all();
 
        foreach ($input as $key => $value) {
            if($key!='_token') {
                DB::table('sys_config')->updateOrInsert(
                    ['name' => $key],
                    ['value' => $value]
                );
            }            
        }      
                          
        return redirect($request->Url())->with('success', 'updated');
    }

}
