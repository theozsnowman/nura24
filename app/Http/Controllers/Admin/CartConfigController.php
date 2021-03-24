<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\User;
use App\Models\Core;
use App\Models\Upload;
use App\Models\Email;
use DB;

class CartConfigController extends Controller
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
    * Cart config
    */
    public function general(Request $request)
    {
        $tickets_departments = DB::table('tickets_departments')
            ->orderBy('active', 'desc')
            ->orderBy('title', 'asc')
            ->paginate(25);   

        return view('admin/account', [
            'view_file' => 'cart.config',
            'active_submenu' => 'cart.config',
            'menu_section' => 'config.cart',
            'tickets_departments' => $tickets_departments, // for services
        ]); 
    }   


    public function update_general(Request $request)
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



    /**
    * Show all currencies
    */
    public function currencies()
    {         
        $default = DB::table('cart_currencies')->where('is_default', 1)->first();

        $extra_currencies = DB::table('cart_currencies')            
            ->where('is_default', 0)    
            ->orderBy('active', 'desc')
            ->orderBy('code', 'asc')
            ->get();                       
        
        return view('admin/account', [
            'view_file' => 'cart.config-currencies',
            'active_submenu' => 'cart.config',
            'menu_section' => 'config.currencies',
            'default' => $default,
            'extra_currencies' => $extra_currencies,
        ]);
    }


   /**
    * Create currency
    */
    public function store_currency(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'symbol' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.cart.config.currencies'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();     

        $exist = DB::table('cart_currencies')->where('code', $inputs['code'])->first();
        if($exist) return redirect(route('admin.cart.config.currencies'))->with('error', 'duplicate');             

        DB::table('cart_currencies')->updateOrInsert(
            ['is_default' => 1],
            [
                'code' => $inputs['code'],            
                'symbol' => $inputs['symbol'],            
                'label' => $inputs['label'],            
                'style' => $inputs['style'],    
                't_separator' => $inputs['t_separator'],    
                'd_separator' => $inputs['d_separator'],    
                'condensed' => $inputs['condensed'],
                'active' => $inputs['active'],            
                'hidden' => $inputs['hidden'],            
                'is_default' => $inputs['is_default'],            
                'conversion_rate' => $inputs['conversion_rate'],       
            ]
        );                               

        return redirect(route('admin.cart.config.currencies'))->with('success', 'created'); 
    }   


    /**
    * Update currency
    */
    public function update_currency(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'symbol' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.cart.config.currencies'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); 
        
        $exist = DB::table('cart_currencies')->where('code', $inputs['code'])->where('id', '!=', $id)->first();
        if($exist) return redirect(route('admin.cart.config.currencies'))->with('error', 'duplicate');         

       
        DB::table('cart_currencies')->where('id', $id)->update(            
            [
                'code' => $inputs['code'],            
                'symbol' => $inputs['symbol'],            
                'label' => $inputs['label'],     
                'style' => $inputs['style'],    
                't_separator' => $inputs['t_separator'],    
                'd_separator' => $inputs['d_separator'],    
                'condensed' => $inputs['condensed'],       
                'active' => $inputs['active'],            
                'hidden' => $inputs['hidden'],            
                'is_default' => $inputs['is_default'],            
                'conversion_rate' => $inputs['conversion_rate'],       
            ]
        );            

        return redirect(route('admin.cart.config.currencies'))->with('success', 'updated'); 
    }


    /**
    * Remove currency
    */
    public function destroy_currency(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $q = DB::table('cart_currencies')->where('id', $id)->first();
        if ($q->is_default==1) return redirect(route('admin.cart.config.currencies'))->with('error', 'delete_default'); 

        DB::table('cart_currencies')->where('id', $id)->delete(); 
        
        return redirect(route('admin.cart.config.currencies'))->with('success', 'deleted'); 
    }


    /**
    * Show all gateways
    */
    public function gateways()
    {              
        $gateways = DB::table('cart_gateways')            
            ->orderBy('active', 'desc')
            ->orderBy('position', 'asc')
            ->paginate(20);                       
        
        return view('admin/account', [
            'view_file' => 'cart.config-gateways',
            'active_submenu' => 'cart.config',
            'menu_section' => 'config.gateways',
            'gateways' => $gateways,
        ]);
    }


   /**
    * Create gateway
    */
    public function store_gateway(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.cart.config.gateways'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();     

        if(DB::table('cart_gateways')->where('title', $inputs['title'])->exists()) return redirect(route('admin.cart.config.gateways'))->with('error', 'duplicate');             

        DB::table('cart_gateways')->insert([
            'title' => $inputs['title'],
            'vendor_email' => $inputs['vendor_email'],
            'client_info' => $inputs['client_info'],            
            'active' => $inputs['active'],            
            'hidden' => $inputs['hidden'],            
            'instant' => $inputs['instant'],            
            'position' => $inputs['position'],            
            'checkout_file' => $inputs['checkout_file'],        
        ]);             
        
        // process image        
        if ($request->hasFile('logo')) {

            $extension = $request->file('logo')->extension();
            if($extension=='jpg' or $extension=='jpeg' or $extension=='png' or $extension=='gif' or $extension=='webp') {}
            else return redirect(route('admin.cart.config.gateways'))->with('error', 'invalid_image');   

            $id = DB::getPdo()->lastInsertId(); 
            $file_db = $this->UploadModel->upload_file($request, 'logo');    
            DB::table('cart_gateways')->where('id', $id)->update(['logo' => $file_db]);            
        }    

        return redirect(route('admin.cart.config.gateways'))->with('success', 'created'); 
    }   


    /**
    * Update gateway
    */
    public function update_gateway(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.cart.config.gateways'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); 
        
        if(DB::table('cart_gateways')->where('title', $inputs['title'])->where('id', '!=', $id)->exists()) return redirect(route('admin.gateways'))->with('error', 'duplicate');                 

        DB::table('cart_gateways')
            ->where('id', $id)
            ->update([
                'title' => $inputs['title'],
                'vendor_email' => $inputs['vendor_email'],
                'client_info' => $inputs['client_info'],            
                'active' => $inputs['active'],            
                'hidden' => $inputs['hidden'],            
                'instant' => $inputs['instant'],            
                'position' => $inputs['position'],            
                'checkout_file' => $inputs['checkout_file'],              
        ]);                              

        // process image        
        if ($request->hasFile('logo')) {

            $extension = $request->file('logo')->extension();
            if($extension=='jpg' or $extension=='jpeg' or $extension=='png' or $extension=='gif' or $extension=='webp') {}
            else return redirect(route('admin.gateways'))->with('error', 'invalid_image');   

            $file_db = $this->UploadModel->upload_file($request, 'logo');    
            DB::table('cart_gateways')->where('id', $id)->update(['logo' => $file_db]);            
        }  
        
        return redirect(route('admin.cart.config.gateways'))->with('success', 'updated'); 
    }
   

    /**
    * Remove gateway
    */
    public function destroy_gateway(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $gateway = DB::table('cart_gateways')->where('id', $id)->first();
        if ($gateway->protected==1) return redirect(route('admin.cart.config.gateways'))->with('error', 'delete_protected'); 
        
        if(DB::table('cart_invoices')->where('gateway_id', $id)->exists()) return redirect(route('admin.cart.config.gateways'))->with('error', 'exists_invoices'); 
                
        DB::table('cart_gateways')->where('id', $id)->delete(); 
        
        return redirect(route('admin.cart.config.gateways'))->with('success', 'deleted'); 
    }



}
