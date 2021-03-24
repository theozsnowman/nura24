<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\Core;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth; 

class FaqController extends Controller
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
    * Display all resources
    */
    public function index(Request $request)
    {
        if(! check_access('faq')) return redirect(route('admin'));

        $search_lang_id = $request->search_lang_id;

        $faqs = DB::table('faq')         
            ->leftJoin('sys_lang', 'faq.lang_id', '=', 'sys_lang.id')
            ->select('faq.*', 'sys_lang.name as lang_name')                    
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc');

        if($search_lang_id)
            $faqs = $faqs->where('lang_id', $search_lang_id);                

        $faqs = $faqs->paginate(15);        
                
        return view('/admin/account', [
            'view_file'=>'faq.faq',
            'active_submenu'=>'faq',
            'faqs' => $faqs,
            'search_lang_id'=> $search_lang_id, 
        ]); 
    }   


    /**
    * Show form to add new resource
    */
    public function create()
    {
        if(! check_access('faq')) return redirect(route('admin'));

        return view('admin/account', [
            'view_file' => 'faq.create',
            'active_submenu' => 'faq',
        ]);
    }


    /**
    * Create new item
    */
    public function store(Request $request)
    {

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('faq')) return redirect(route('admin'));        

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.faq'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array 
        
        if(!$inputs['position']) {
            $q = DB::table('faq')->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position'];

        DB::table('faq')->insert([
            'lang_id' => $inputs['lang_id'] ?? default_lang()->id,
            'title' => $inputs['title'],
            'content' => $inputs['content'],
            'active' => $inputs['active'],
            'position' => $position ?? 1,
        ]);         
                 
        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Show form to edit resource     
    */
    public function show(Request $request)
    {
        if(! check_access('faq')) return redirect(route('admin'));

        $faq = DB::table('faq')->where('id', $request->id)->first();          
        if(!$faq) abort(404);       
               
        return view('admin/account', [
            'view_file' => 'faq.update',
            'active_submenu' => 'faq',
            'faq' => $faq,        
        ]);
    }


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('faq')) return redirect(route('admin'));

        $id = $request->id;
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.faq'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if(!$inputs['position']) {
            $q = DB::table('faq')->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position'];
        
        DB::table('faq')
            ->where('id', $id)
            ->update([
                'lang_id' => $inputs['lang_id'] ?? default_lang()->id,
                'title' => $inputs['title'],
                'content' => $inputs['content'],
                'active' => $inputs['active'],
                'position' => $position ?? 1,
            ]);              
                 
        return redirect(route('admin.faq'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('faq')) return redirect(route('admin'));
        
        $id = $request->id;

        DB::table('faq')->where('id', $id)->delete(); // delete page

        return redirect(route('admin.faq'))->with('success', 'deleted'); 
    }  

}
