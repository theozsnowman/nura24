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

class BlocksController extends Controller
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

            if(! ($this->logged_user_role == 'admin')) return redirect('/'); 
            return $next($request);
        });
    } 


    /**
    * Display all resources
    */
    public function index(Request $request)
    {
        $search_terms = $request->search_terms;
        
        $blocks = DB::table('blocks')
            ->orderBy('active', 'desc')
            ->orderBy('label', 'asc');
                
        if($search_terms)
            $blocks = $blocks->where('label', 'like', "%$search_terms%");            

        $blocks = $blocks->paginate(15);       
                
        return view('admin/account', [
            'view_file' => 'blocks.blocks',
            'active_submenu' => 'blocks',
            'search_terms' => $search_terms,
            'blocks' => $blocks,
        ]); 
    }


    /**
    * Show form to add new resource
    */
    public function create()
    {
        return view('admin/account', [
            'view_file' => 'blocks.create',
            'active_submenu' => 'blocks',
        ]);
    }


    /**
    * Create new resource
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $inputs = $request->all(); 

        $validator = Validator::make($request->all(), [
            'label' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.blocks.create'))
                ->withErrors($validator)
                ->withInput();
        } 

        $label = Str::slug($inputs['label'], '-');        

        if(DB::table('blocks')->where('label', $label)->exists()) return redirect(route('admin.blocks.create'))->with('error', 'duplicate');  

        DB::table('blocks')->insert([
            'label' => $label,
            'description' => $inputs['description'],
            'active' => $inputs['active'],
        ]);          

        $block_id = DB::getPdo()->lastInsertId();  

        $langs = DB::table('sys_lang')->get();  
        foreach($langs as $lang) {
            DB::table('blocks_content')
                ->insert([
                    'block_id' => $block_id, 
                    'lang_id' => $lang->id,
                    'content' => $request['content_'.$lang->id],
                ]);

            $block_content_id = DB::getPdo()->lastInsertId();  

            // process image        
            if ($request->hasFile('image_'.$lang->id)) {
                $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
                if (! $validator->fails()) {                                       
                    $image_db = $this->UploadModel->upload_image($request, 'image_'.$lang->id);    
                    DB::table('blocks_content')->where('id', $block_content_id)->update(['image' => $image_db]);            
                }
            }       
        }              
                 
        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Show form to edit resource     
    */
    public function show(Request $request)
    {
        $block = DB::table('blocks')
            ->where('id', $request->id)
            ->first();          
        if(!$block) abort(404);

        $contents = DB::table('blocks_content')
            ->leftJoin('sys_lang', 'blocks_content.lang_id', '=', 'sys_lang.id')
            ->select('blocks_content.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang', 'sys_lang.is_default as lang_is_default', 'sys_lang.status as lang_status')    
            ->orderBy('sys_lang.is_default', 'desc')
            ->orderBy('sys_lang.status', 'asc')
            ->get(); 

        $langs = DB::table('sys_lang')
            //->leftJoin('blocks_content', 'blocks_content.lang_id', '=', 'sys_lang.id')
            ->select('sys_lang.*', 
                DB::raw('(SELECT content FROM blocks_content WHERE blocks_content.lang_id = sys_lang.id AND block_id = '.$block->id.') as block_content'), 
                DB::raw('(SELECT image FROM blocks_content WHERE blocks_content.lang_id = sys_lang.id AND block_id = '.$block->id.') as block_image'))    
            //->groupBy('blocks_content.lang_id')
            ->where('status', '!=', 'disabled')
            ->orderBy('is_default', 'desc')
            ->orderBy('status', 'asc')
            ->get();  
                    
        return view('admin/account', [
            'view_file'=>'blocks.update',
            'active_submenu'=>'blocks',
            'block' => $block,
            'langs' => $langs,
            'contents' => $contents,
        ]);
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
            'label' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect($request->Url())
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
              
        $label = Str::slug($inputs['label'], '-');        
        if(DB::table('blocks')->where('label', $label)->where('id', '!=', $id)->exists()) return redirect(route('admin.blocks.show', ['id'=>$id]))->with('error', 'duplicate');  
                
        DB::table('blocks')
            ->where('id', $id)
            ->update([
            'label' => $label,            
            'description' => $inputs['description'],         
            'active' => $inputs['active'],            
        ]);       

        $langs = DB::table('sys_lang')->get();  
        foreach($langs as $lang) {
            DB::table('blocks_content')
                ->updateOrInsert(
                    ['block_id' => $id, 'lang_id' => $lang->id],
                    ['content' => $request['content_'.$lang->id]]
                );

            // process image        
            if ($request->hasFile('image_'.$lang->id)) {
                $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
                if (! $validator->fails()) {                                       
                    $image_db = $this->UploadModel->upload_image($request, 'image_'.$lang->id);                        
                    DB::table('blocks_content')->updateOrInsert(['block_id' => $id, 'lang_id' => $lang->id], ['image' => $image_db]);                
                }
            }  
        }
                 
        return redirect(route('admin.blocks'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('blocks')->where('id', $id)->delete(); 
        DB::table('blocks_content')->where('block_id', $id)->delete(); 

        return redirect(route('admin.blocks'))->with('success', 'deleted'); 
    }

    /**
    * Remove block image
    */
    public function delete_image(Request $request)
    {
        $id = $request->id; // block ID
        $lang_id = $request->lang_id; // lang ID

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
      
        // delete image
        $block_image = DB::table('blocks_content')->where('block_id', $id)->where('lang_id', $lang_id)->value('image');          

        if($block_image) delete_image($block_image);

        DB::table('blocks_content')->where('block_id', $id)->where('lang_id', $lang_id)->update(['image' => null]); 
        
        return redirect(route('admin.blocks.show', ['id' => $id]))->with('success', 'image_deleted'); 
    }

}
