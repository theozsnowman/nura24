<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
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

class TicketsDepartmentsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();      
        
        $this->roles = DB::table('users_roles')->where('active', 1)->orderBy('id', 'asc')->get();      
        $this->role_id_internal = $this->UserModel->get_role_id_from_role('internal');

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
    public function index(Request $request)
    {                    
        $departments = DB::table('tickets_departments')
            ->orderBy('active', 'desc')
            ->orderBy('title', 'asc')
            ->paginate(25);       
                
        return view('admin/account', [
            'view_file' => 'tickets.departments',
            'active_submenu' => 'tickets',
            'departments' => $departments,      
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
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.tickets.departments'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();     

        if(DB::table('tickets_departments')->where('title', $inputs['title'])->exists()) return redirect(route('admin.tickets.departments'))->with('error', 'duplicate'); 
        
        DB::table('tickets_departments')->insert([
            'title' => $inputs['title'],
            'description' => $inputs['description'],
            'active' => $inputs['active'],            
            'hidden' => $inputs['hidden'],            
        ]);                               

        return redirect(route('admin.tickets.departments'))->with('success', 'created'); 
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
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.tickets.departments', ['id' => $id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); 
                
        if(DB::table('tickets_departments')->where('title', $inputs['title'])->where('id', '!=', $id)->exists()) return redirect(route('admin.tickets.departments'))->with('error', 'duplicate');         

        DB::table('tickets_departments')
            ->where('id', $id)
            ->update([
            'title' => $inputs['title'],
            'description' => $inputs['description'],
            'active' => $inputs['active'],            
            'hidden' => $inputs['hidden'],   
        ]);                              

        return redirect(route('admin.tickets.departments'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('tickets')->where('department_id', $id)->update(['department_id' => null]); 
        DB::table('tickets_departments')->where('id', $id)->delete(); 
        
        return redirect(route('admin.tickets.departments'))->with('success', 'deleted'); 
    }
}
