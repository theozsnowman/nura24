<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 
use Image;
use Storage;

class BlocksGroupsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->UploadModel = new Upload();                  

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

        if(! check_access('blocks_groups')) return redirect(route('admin'));
        
        $groups = DB::table('blocks_groups')
            ->select('blocks_groups.*', DB::raw('(SELECT count(*) FROM blocks_groups_content WHERE blocks_groups_content.group_id = blocks_groups.id) as count_blocks'))
            ->orderBy('active', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25);                           

        return view('admin/account', [
            'view_file' => 'blocks-groups.groups',
            'active_submenu' => 'blocks.groups',
            'groups' => $groups,
        ]); 
    }   


    /**
    * Create new group
    */
    public function store(Request $request)
    {
        if(! check_access('blocks_groups')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'label' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.blocks.groups'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 

        $label = Str::slug($inputs['label'], '-');        
        if(DB::table('blocks_groups')->where('label', $label)->exists()) return redirect(route('admin.blocks.groups'))->with('error', 'duplicate');  

        DB::table('blocks_groups')->insert([
            'label' => $label,          
            'description' => $inputs['description'],          
            'active' => $inputs['active'],            
        ]);
        
        return redirect($request->Url())->with('success', 'created'); 
    }
   


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {        

        if(! check_access('blocks_groups')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;  
        $group = DB::table('blocks_groups')->where('id', $id)->first();    
        if(!$group) return redirect(route('admin'));     

        $validator = Validator::make($request->all(), [
            'label' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect($request->Url())
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 

        $label = Str::slug($inputs['label'], '-');        
        
        if(DB::table('blocks_groups')->where('label', $label)->where('id', '!=', $id)->exists()) return redirect(route('admin.blocks.groups'))->with('error', 'duplicate');  
        
        DB::table('blocks_groups')
            ->where('id', $id)
            ->update([              
                'label' => $label,          
                'description' => $inputs['description'],    
                'active' => $inputs['active'],                
            ]);
                 
        return redirect(route('admin.blocks.groups'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        if(! check_access('blocks_groups')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');         
        
        $id = $request->id;  
        $group = DB::table('blocks_groups')->where('id', $id)->first();  
        if(!$group) return redirect(route('admin'));

        $images = DB::table('blocks_groups_content')->where('group_id', $id)->get();     
        foreach($images as $image) {
            if($image->file) delete_image($image->file);   
        }

        DB::table('blocks_groups_content')->where('group_id', $id)->delete();
        DB::table('blocks_groups')->where('id', $id)->delete();
        
        return redirect(route('admin.blocks.groups'))->with('success', 'deleted'); 
    }



     /**
    * Display all images
    */
    public function blocks(Request $request)
    {

        if(! check_access('blocks_groups')) return redirect(route('admin'));

        $id = $request->id; // group ID

        $blocks = DB::table('blocks_groups_content')
            ->where('group_id', $id)
            ->orderBy('active', 'desc')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(20);       

        $group = DB::table('blocks_groups')->where('id', $id)->first();  
        if(!$group) return redirect(route('admin'));
      
        return view('admin/account', [
            'view_file' => 'blocks-groups.blocks',
            'active_submenu' => 'blocks.groups',
            'blocks' => $blocks,
            'group' => $group,
        ]); 
    }


    /**
    * Show form to add new resource
    */
    public function create_block(Request $request)
    {
        if(! check_access('blocks_groups')) return redirect(route('admin'));       

        $id = $request->id; // group ID
        $group = DB::table('blocks_groups')->where('id', $id)->first();  
        if(!$group) return redirect(route('admin'));

        return view('admin/account', [
            'view_file' => 'blocks-groups.block-create',
            'active_submenu' => 'blocks.groups',
            'group' => $group,
        ]);
    }



    /**
    * Create new block
    */
    public function store_block(Request $request)
    {

        if(! check_access('blocks_groups')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id; // group ID
        $inputs = $request->all();        
        
        $group = DB::table('blocks_groups')->where('id', $request->id)->first();          
        if(!$group) return redirect(route('admin'));

        DB::table('blocks_groups_content')->insert([
            'group_id' => $id,
            'content' => $inputs['content'],
            'active' => $inputs['active'],
            'position' => $inputs['position'],
        ]);

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.blocks.groups.content', ['id' => $id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $content_id = DB::getPdo()->lastInsertId(); 
            $image_db = $this->UploadModel->upload_image($request, 'image');    
            DB::table('blocks_groups_content')->where('id', $content_id)->update(['file' => $image_db]);               
        }        
                 
        return redirect(route('admin.blocks.groups.content', ['id' => $id]))->with('success', 'created'); 
    }   



     /**
    * Show form to edit resource     
    */
    public function show_block(Request $request)
    {
        if(! check_access('blocks_groups')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id; // group ID
        $block_id = $request->block_id; // block ID
        $inputs = $request->all();        
        
        $group = DB::table('blocks_groups')->where('id', $id)->first();          
        if(!$group) return redirect(route('admin'));

        $block = DB::table('blocks_groups_content')->where('id', $block_id)->first();          
        if(!$block) return redirect(route('admin'));      

        return view('admin/account', [
            'view_file' => 'blocks-groups.block-update',
            'active_submenu' => 'blocks.groups',
            'group' => $group,
            'block' => $block,
        ]);
    }



    /**
    * Update block
    */
    public function update_block(Request $request)
    {
        if(! check_access('blocks_groups')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        $id = $request->id; // group ID
        $block_id = $request->block_id;       

        $group = DB::table('blocks_groups')->where('id', $request->id)->first();          
        if(!$group) return redirect(route('admin'));  

        $inputs = $request->all();        
        
       
        DB::table('blocks_groups_content')
        ->where('id', $block_id)
        ->update([
            'content' => $inputs['content'],
            'active' => $inputs['active'],
            'position' => $inputs['position'],
        ]);

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.blocks.groups.content', ['id' => $id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $image_db = $this->UploadModel->upload_image($request, 'image');    
            DB::table('blocks_groups_content')->where('id', $block_id)->update(['file' => $image_db]);               
        }        
                 
        return redirect(route('admin.blocks.groups.content', ['id' => $id]))->with('success', 'updated'); 
    }   



    /**
    * Remove the specified resource
    */
    public function destroy_block(Request $request)
    {

        if(! check_access('blocks_groups')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        $id = $request->id; // group ID
        $block_id = $request->block_id;       

        $group = DB::table('blocks_groups')->where('id', $request->id)->first();          
        if(!$group) return redirect(route('admin'));      

        // delete content
        $file = DB::table('blocks_groups_content')->where('id', $block_id)->value('file');   
        if($file) delete_image($file);                
        
        DB::table('blocks_groups_content')->where('id', $block_id)->delete(); 

        return redirect(route('admin.blocks.groups.content', ['id' => $id]))->with('success', 'deleted'); 
    }


    /**
    * Remove block image
    */
    public function block_delete_image(Request $request)
    {
        if(! check_access('blocks_groups')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id; // group ID
        $block_id = $request->block_id;             
      
        $group = DB::table('blocks_groups')->where('id', $id)->first();          
        if(!$group) return redirect(route('admin'));      

        $block = DB::table('blocks_groups_content')->where('id', $block_id)->first();          
        if(!$block) return redirect(route('admin'));      

        if($block->file) delete_image($block->file);

        DB::table('blocks_groups_content')->where('id', $block_id)->update(['file' => null]); 
        
        return redirect(route('admin.blocks.groups.content', ['id' => $id, 'block_id' => $block_id]))->with('success', 'deleted'); 
    }

}
