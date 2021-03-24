<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;
use App\Models\User;
use App\Models\Core;
use DB;

class ConfigVariablesController extends Controller
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
            
            if(! ($this->logged_user_role == 'admin')) return redirect('/'); 
            return $next($request);
        });
    }
   

    /**
    * Show all resources
    */
    public function index()
    {         

        $variables = DB::table('sys_config')            
            ->where('is_custom', 1)    
            ->orderBy('name', 'asc')
            ->paginate(25);                       
        
        return view('admin/account', [
            'view_file'=>'core.config-variables',
            'active_submenu'=>'config.general',
            'menu_section' => 'variables',
            'variables' => $variables,
        ]);
    }


    /**
    * Create resource
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.config.variables'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();     

        $name = Str::slug($inputs['name'], '_');

        if(DB::table('sys_config')->where('name', $name)->exists()) return redirect(route('admin.config.variables'))->with('error', 'duplicate');             

        DB::table('sys_config')->insert([
                'name' => $name,
                'value' => $inputs['value'],            
                'is_custom' => 1,                 
        ]);                               

        return redirect(route('admin.config.variables'))->with('success', 'created'); 
    }   


    /**
    * Update resource
    */
    public function update(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.config.variables'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); 
               
        $name = Str::slug($inputs['name'], '_');

        if(DB::table('sys_config')->where('name', $name)->where('id', '!=', $id)->exists()) return redirect(route('admin.config.variables'))->with('error', 'duplicate');         
       
        DB::table('sys_config')->where('id', $id)->update(            
            [
                'name' => $name,      
                'value' => $inputs['value'],                            
            ]
        );            

        return redirect(route('admin.config.variables'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
                
        DB::table('sys_config')->where('id', $id)->delete(); 
        
        return redirect(route('admin.config.variables'))->with('success', 'deleted'); 
    }

}
