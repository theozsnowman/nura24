<?php
/**
 * Copyright: Nura24 - https://www.nura24.com  
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Locale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use DB;
use Auth; 

class LangsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->LocaleModel = new Locale();    
        
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;
            
            $role = $this->UserModel->get_role_from_id ($this->role_id);    
            if($role!='admin') return redirect('/'); 
            return $next($request);
        });

    } 


    /**
    * Display all resources
    */
    public function index(Request $request)
    {
        $langs = DB::table('sys_lang')->orderBy('is_default', 'desc')->orderBy('status', 'asc')->paginate(25);         
        $templates = glob('templates/frontend/' . '*' , GLOB_ONLYDIR);

        return view('admin/account', [
            'view_file'=>'core/config-langs',
            'active_submenu'=>'config.langs',
            'langs' => $langs,
            'templates' => $templates,
            'locales_array' => $this->LocaleModel->locales_array(),
            'lang_codes_array' => $this->LocaleModel->lang_codes_array(), 
        ]); 
    }


    /**
    * Create new resource
    */
    public function store(Request $request)
    {       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.config.langs'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if(DB::table('sys_lang')->where('name', $inputs['name'])->exists()) return redirect(route('admin.config.langs'))->with('error', 'duplicate'); 
        if(DB::table('sys_lang')->where('code', $inputs['code'])->exists()) return redirect(route('admin.config.langs'))->with('error', 'duplicate'); 

        // only one language can be default
        if($inputs['is_default']==1) {      
            DB::table('sys_lang')
                ->where('is_default', 1)    
                ->update(['is_default' => 0]
            );  
        }

        DB::table('sys_lang')->insert([
            'name' => $inputs['name'],
            'code' => $inputs['code'],
            'locale' => $inputs['locale'],
            'is_default' => $inputs['is_default'],
            'status' => $inputs['status'],
            'timezone' => $inputs['timezone'] ?? 'Europe/London',
            'date_format' => $inputs['date_format'] ?? 'j F Y',
            //'currency_display_style' => $inputs['currency_display_style'] ?? 'value_code',
            'site_short_title' => $inputs['site_short_title'],
            'homepage_meta_title' => $inputs['homepage_meta_title'],
            'homepage_meta_description' => $inputs['homepage_meta_description'],
        ]);       
                 
        return redirect($request->Url())->with('success', 'created'); 
    }

    
    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.config.langs'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if(DB::table('sys_lang')->where('name', $inputs['name'])->where('id', '!=', $id)->exists()) return redirect(route('admin.config.langs'))->with('error', 'duplicate'); 
        if(DB::table('sys_lang')->where('code', $inputs['code'])->where('id', '!=', $id)->exists()) return redirect(route('admin.config.langs'))->with('error', 'duplicate'); 

        // only one language can be default
        if($inputs['is_default']==1) {
            DB::table('sys_lang')
                ->where('is_default', 1)    
                ->update(['is_default' => 0]
            );  
        }

        DB::table('sys_lang')
            -> where('id', $id)   
            -> update([
                'name' => $inputs['name'],
                'code' => $inputs['code'],
                'locale' => $inputs['locale'],
                'is_default' => $inputs['is_default'],
                'status' => $inputs['status'],
                'timezone' => $inputs['timezone'] ?? 'Europe/London',
                'date_format' => $inputs['date_format'] ?? 'j F Y',
                //'currency_display_style' => $inputs['currency_display_style'] ?? 'value_code',
                'site_short_title' => $inputs['site_short_title'],
                'homepage_meta_title' => $inputs['homepage_meta_title'],
                'homepage_meta_description' => $inputs['homepage_meta_description'],
        ]);       
                 
        return redirect(route('admin.config.langs'))->with('success', 'updated');     
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        if(DB::table('sys_lang')->where('id', $id)->value('is_default') == 1) redirect(route('admin.config.langs'))->with('error', 'default');
        
        if(DB::table('posts')->where('lang_id', $id)->exists()) redirect(route('admin.config.langs'))->with('error', 'exists_content');
        if(DB::table('blocks_content')->where('lang_id', $id)->exists()) redirect(route('admin.config.langs'))->with('error', 'exists_content');
        if(DB::table('docs')->where('lang_id', $id)->exists()) redirect(route('admin.config.langs'))->with('error', 'exists_content');
        if(DB::table('pages')->where('lang_id', $id)->exists()) redirect(route('admin.config.langs'))->with('error', 'exists_content');
        if(DB::table('faq')->where('lang_id', $id)->exists()) redirect(route('admin.config.langs'))->with('error', 'exists_content');
        if(DB::table('slider')->where('lang_id', $id)->exists()) redirect(route('admin.config.langs'))->with('error', 'exists_content');

        DB::table('sys_lang')->where('id', $id)->delete();

        DB::table('posts')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('posts_categ')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('posts_tags')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('blocks_content')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('custom_fields')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('custom_fields_sections')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('docs')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('docs_categ')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('pages')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('slider')->where('lang_id', $id)->update(['lang_id' => null]);
        DB::table('faq')->where('lang_id', $id)->update(['lang_id' => null]);

        return redirect(route('admin.config.langs'))->with('success', 'deleted');   
    }
}
