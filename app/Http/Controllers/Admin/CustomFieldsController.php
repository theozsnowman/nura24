<?php
/**
 * Copyright Nura24: #1 Free CMS for businesses, communities, bloggers and personal websites
 * Nura24 is a free CMS suite with eCommerce, Community Forum, HelpDesk, Blog, Booking System, Classifieds, CRM and Marketing.
 * Author: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\Core;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 

class CustomFieldsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->config = Core::config();      
        
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;
            
            $role = $this->UserModel->get_role_from_id ($this->role_id);    
            if($role!='admin') return redirect('/'); 
            return $next($request);
        });

    } 


    /**
    * Custom fields groups
    */
    public function index(Request $request)
    {

        $groups = DB::table('custom_fields_groups')         
            ->select('custom_fields_groups.*', 
                DB::raw('(SELECT count(*) FROM custom_fields_sections WHERE custom_fields_sections.group_id = custom_fields_groups.id) as count_sections'), 
                DB::raw('(SELECT count(*) FROM custom_fields WHERE custom_fields.group_id = custom_fields_groups.id) as count_custom_fields'))
            ->orderBy('active', 'desc')
            ->orderBy('title', 'asc')
            ->paginate(15);        
                
        return view('/admin/account', [
            'view_file'=>'custom-fields.groups',
            'active_submenu' => 'articles',
            'groups' => $groups,
        ]); 
    }   


    /**
    * Create new group
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.custom-fields-groups'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array 
                
        if(DB::table('custom_fields_groups')->where('title', $inputs['title'])->exists()) return redirect(route('admin.custom-fields-groups'))->with('error', 'duplicate'); 

        DB::table('custom_fields_groups')->insert([
            'title' => $inputs['title'],
            'active' => $inputs['active'],
        ]);         
                 
        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Update group
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
            return redirect(route('admin.custom-fields-groups'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if(DB::table('custom_fields_groups')->where('title', $inputs['title'])->where('id', '!=', $id)->exists()) return redirect(route('admin.custom-fields-groups'))->with('error', 'duplicate'); 
        
        DB::table('custom_fields_groups')
            ->where('id', $id)
            ->update([
                'title' => $inputs['title'],
                'active' => $inputs['active'],
            ]);              
                 
        return redirect(route('admin.custom-fields-groups'))->with('success', 'updated'); 
    }


    /**
    * Remove group
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        DB::table('custom_fields_sections')->where('group_id', $id)->delete();
        DB::table('custom_fields')->where('group_id', $id)->delete();
        DB::table('custom_fields_values')->where('group_id', $id)->delete();
        DB::table('custom_fields_groups')->where('id', $id)->delete();

        return redirect(route('admin.custom-fields-groups'))->with('success', 'deleted'); 
    }  



    /**
    * Display all sections
    */
    public function sections(Request $request)
    {
        $group_id = $request->group_id;
        $search_lang_id = $request->search_lang_id;

        $group = DB::table('custom_fields_groups')                     
            ->where('id', $group_id)
            ->first();  
        if(! $group) abort(404);

        $sections = DB::table('custom_fields_sections')    
            ->leftJoin('sys_lang', 'custom_fields_sections.lang_id', '=', 'sys_lang.id')     
            ->select('custom_fields_sections.*', 'sys_lang.name as lang_name', 
                DB::raw('(SELECT count(*) FROM custom_fields WHERE custom_fields.section_id = custom_fields_sections.id) as count_custom_fields'))
            ->where('group_id', $group_id)
            ->orderBy('active', 'desc')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc');

        if($search_lang_id)
            $sections = $sections->where('lang_id', $search_lang_id);    
        
        $sections = $sections->paginate(20);        
                
        return view('/admin/account', [
            'view_file'=>'custom-fields.sections',
            'active_submenu'=>'custom-fields',
            'group' => $group,
            'sections' => $sections,
            'search_lang_id'=> $search_lang_id, 
        ]); 
    }   


    /**
    * Create new section
    */
    public function store_section(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $group_id = $request->group_id;

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.custom-fields-sections', ['group_id' => $group_id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array 
        $lang_id = $inputs['lang_id'] ?? default_lang()->id;

        if(!$inputs['position']) {
            $q = DB::table('custom_fields_sections')->where('group_id', $group_id)->where('lang_id', $lang_id)->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position'];

        if(DB::table('custom_fields_sections')->where('title', $inputs['title'])->where('group_id', $group_id)->where('lang_id', $lang_id)->exists()) return redirect(route('admin.custom-fields-sections', ['group_id' => $group_id]))->with('error', 'duplicate'); 

        DB::table('custom_fields_sections')->insert([
            'group_id' => $group_id,
            'lang_id' => $lang_id,
            'title' => $inputs['title'],
            'slug' => Str::slug($inputs['title'], '-'),
            'active' => $inputs['active'],
            'position' => $position ?? 1,
        ]);         
                 
        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Update section
    */
    public function update_section(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $group_id = $request->group_id;
        $section_id = $request->section_id;

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.custom-fields-sections', ['group_id' => $group_id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array 
        $lang_id = $inputs['lang_id'] ?? default_lang()->id;

        if(!$inputs['position']) {
            $q = DB::table('custom_fields_sections')->where('group_id', $group_id)->where('lang_id', $lang_id)->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position'];

        if(DB::table('custom_fields_sections')->where('title', $inputs['title'])->where('group_id', $group_id)->where('lang_id', $lang_id)->where('id', '!=', $section_id)->exists()) return redirect(route('admin.custom-fields-sections', ['group_id' => $group_id]))->with('error', 'duplicate'); 

        DB::table('custom_fields_sections')
            ->where('id', $section_id)
            ->update([
                'lang_id' => $lang_id,
                'title' => $inputs['title'],
                'slug' => Str::slug($inputs['title'], '-'),
                'active' => $inputs['active'],
                'position' => $position ?? 1,
            ]);         
                 
        DB::table('custom_fields')->where('section_id', $section_id)->update(['lang_id' => $lang_id]);   
        DB::table('custom_fields_values')->where('section_id', $section_id)->update(['lang_id' => $lang_id]);   

        return redirect($request->Url())->with('success', 'updated'); 
    }



    /**
    * Remove section
    */
    public function destroy_section(Request $request)
    {
        $id = $request->id;
        $group_id = $request->group_id;
        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        DB::table('custom_fields')->where('section_id', $id)->delete();
        DB::table('custom_fields_values')->where('section_id', $id)->delete();
        DB::table('custom_fields_sections')->where('id', $id)->delete();

        return redirect(route('admin.custom-fields-sections', ['group_id' => $group_id]))->with('success', 'deleted'); 
    }  


    /**
    * Display custom fields
    */
    public function custom_fields(Request $request)
    {
        $group_id = $request->group_id;
        $section_id = $request->section_id;

        $group = DB::table('custom_fields_groups')                     
            ->where('id', $group_id)
            ->first();  
        if(! $group) abort(404);

        $section = DB::table('custom_fields_sections')                     
            ->where('id', $section_id)
            ->first();  
        if(! $section) abort(404);

        $custom_fields = DB::table('custom_fields')    
            ->where('lang_id', $section->lang_id)
            ->where('section_id', $section->id)
            ->orderBy('active', 'desc')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc')
            ->paginate(20);        
                
        return view('/admin/account', [
            'view_file'=>'custom-fields.fields',
            'active_submenu'=>'custom-fields',
            'group' => $group,
            'section' => $section,
            'custom_fields' => $custom_fields,
        ]); 
    }   


    /**
    * Create new custom field
    */
    public function store_custom_field(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $group_id = $request->group_id;
        $section_id = $request->section_id;

        $section = DB::table('custom_fields_sections')                     
            ->where('id', $section_id)
            ->first();  

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.custom-fields', ['group_id' => $group_id, 'section_id' => $section_id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array 
                
        if(!$inputs['position']) {
            $q = DB::table('custom_fields')->where('section_id', $section_id)->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position'];

        if(DB::table('custom_fields')->where('title', $inputs['title'])->where('section_id', $section_id)->exists()) return redirect(route('admin.custom-fields', ['group_id' => $group_id, 'section_id' => $section_id]))->with('error', 'duplicate'); 

        DB::table('custom_fields')->insert([
            'group_id' => $group_id,
            'section_id' => $section_id,
            'lang_id' => $section->lang_id,
            'title' => $inputs['title'],
            'type' => $inputs['type'],
            'slug' => Str::slug($inputs['title'], '-'),
            'active' => $inputs['active'],
            'position' => $position ?? 1,
        ]);         
                 
        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Update custom field
    */
    public function update_custom_field(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $field_id = $request->field_id;

        $group_id = $request->group_id;
        $section_id = $request->section_id;

        $section = DB::table('custom_fields_sections')                     
            ->where('id', $section_id)
            ->first(); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.custom-fields', ['group_id' => $group_id, 'section_id' => $section_id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array 
                
        if(!$inputs['position']) {
            $q = DB::table('custom_fields')->where('section_id', $section_id)->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position'];

        if(DB::table('custom_fields')->where('title', $inputs['title'])->where('section_id', $section_id)->where('id', '!=', $field_id)->exists()) return redirect(route('admin.custom-fields', ['group_id' => $group_id, 'section_id' => $section_id]))->with('error', 'duplicate'); 

        DB::table('custom_fields')
            ->where('id', $field_id)
            ->update([
                'lang_id' => $section->lang_id,
                'title' => $inputs['title'],
                'type' => $inputs['type'],
                'slug' => Str::slug($inputs['title'], '-'),
                'active' => $inputs['active'],
                'position' => $position ?? 1,
            ]);         
                 
        return redirect($request->Url())->with('success', 'updated'); 
    }



    /**
    * Remove custom field
    */
    public function destroy_custom_field(Request $request)
    {
        $id = $request->id;
        $group_id = $request->group_id;
        $section_id = $request->section_id;
        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('custom_fields_values')->where('field_id', $id)->delete();        
        DB::table('custom_fields_options')->where('field_id', $id)->delete();     
        DB::table('custom_fields')->where('id', $id)->delete();

        return redirect(route('admin.custom-fields', ['group_id' => $group_id, 'section_id' => $section_id]))->with('success', 'deleted'); 
    }  


    /**
    * Display custom field options
    */
    public function custom_fields_options(Request $request)
    {
        $group_id = $request->group_id;
        $section_id = $request->section_id;
        $field_id = $request->field_id;

        $group = DB::table('custom_fields_groups')                     
            ->where('id', $group_id)
            ->first();  
        if(! $group) abort(404);

        $section = DB::table('custom_fields_sections')                     
            ->where('id', $section_id)
            ->first();  
        if(! $section) abort(404);

        $field = DB::table('custom_fields')                     
            ->where('id', $field_id)
            ->first();  
        if(! $field) abort(404);

        $options = DB::table('custom_fields_options')    
            ->where('field_id', $field->id)
            ->orderBy('active', 'desc')
            ->orderBy('position', 'asc')
            ->orderBy('value', 'asc')
            ->paginate(20);        
                
        return view('/admin/account', [
            'view_file'=>'custom-fields.options',
            'active_submenu'=>'custom-fields',
            'group' => $group,
            'section' => $section,
            'field' => $field,
            'options' => $options,
        ]); 
    }   


    /**
    * Create custom field option
    */
    public function store_custom_field_option(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $group_id = $request->group_id;
        $section_id = $request->section_id;
        $field_id = $request->field_id;

        $section = DB::table('custom_fields_sections')                     
            ->where('id', $section_id)
            ->first();  

        $validator = Validator::make($request->all(), [
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.custom-fields-options', ['group_id' => $group_id, 'section_id' => $section_id, 'field_id' => $field_id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array 
                
        if(!$inputs['position']) {
            $q = DB::table('custom_fields_options')->where('field_id', $field_id)->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position'];

        $slug = Str::slug($inputs['value'], '-');

        if(DB::table('custom_fields_options')->where('slug', $slug)->where('field_id', $field_id)->exists()) return redirect(route('admin.custom-fields-options', ['group_id' => $group_id, 'section_id' => $section_id, 'field_id' => $field_id]))->with('error', 'duplicate'); 

        DB::table('custom_fields_options')->insert([
            'group_id' => $group_id,
            'section_id' => $section_id,
            'field_id' => $field_id,
            'lang_id' => $section->lang_id,
            'value' => $inputs['value'],            
            'slug' => $slug,
            'active' => $inputs['active'],
            'position' => $position ?? 1,
        ]);         
                 
        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Update custom field
    */
    public function update_custom_field_option(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $option_id = $request->option_id;

        $group_id = $request->group_id;
        $section_id = $request->section_id;
        $field_id = $request->field_id;

        $section = DB::table('custom_fields_sections')                     
            ->where('id', $section_id)
            ->first(); 

        $validator = Validator::make($request->all(), [
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.custom-fields-options', ['group_id' => $group_id, 'section_id' => $section_id, 'field_id' => $field_id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data (without image) as an array 
                
        if(!$inputs['position']) {
            $q = DB::table('custom_fields_options')->where('field_id', $field_id)->orderBy('position', 'desc')->first();
            if($q) $position = $q->position + 1;
        } else 
            $position = $inputs['position'];

        $slug = Str::slug($inputs['value'], '-');

        if(DB::table('custom_fields_options')->where('slug', $slug)->where('field_id', $field_id)->where('id', '!=', $option_id)->exists()) return redirect(route('admin.custom-fields-options', ['group_id' => $group_id, 'section_id' => $section_id, 'field_id' => $field_id]))->with('error', 'duplicate'); 

        DB::table('custom_fields_options')
            ->where('id', $option_id)
            ->update([
                'value' => $inputs['value'],
                'slug' => $slug,
                'active' => $inputs['active'],
                'position' => $position ?? 1,
            ]);         
                 
        return redirect($request->Url())->with('success', 'updated'); 
    }


    /**
    * Remove custom field
    */
    public function destroy_custom_field_option(Request $request)
    {
        $id = $request->id;
        $group_id = $request->group_id;
        $section_id = $request->section_id;
        $field_id = $request->field_id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('custom_fields_options')->where('id', $id)->delete();

        return redirect(route('admin.custom-fields-options', ['group_id' => $group_id, 'section_id' => $section_id, 'field_id' => $field_id]))->with('success', 'deleted'); 
    }  


}
