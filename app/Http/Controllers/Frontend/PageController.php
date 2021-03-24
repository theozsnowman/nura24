<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/  

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Core;
use DB;
use Auth; 
use App; 

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {        
        $this->config = Core::config();  
    }

    /**
    * Display static page
    */
    public function index(Request $request)
    {                         

        $slug = $request->slug;     
        
        $page = DB::table('pages')
            ->where('slug', $slug)    
            ->where('lang_id', active_lang()->id)        
            ->where('active', 1)
            ->first();          
        if(! $page) abort(404);        

        if($page->redirect_url) return redirect()->away($page->redirect_url);

        $images = DB::table('pages_images')
            ->where('page_id', $page->id)      
            ->orderBy('id', 'asc')  
            ->get();  
             
        if($page->custom_tpl_file) $view_file = str_replace('.blade.php', '', $page->custom_tpl_file);
        else $view_file = 'page';

        return view('frontend/'.$this->config->template.'/'.$view_file, [          
            'page' => $page,       
            'images' => $images,      
        ]);
    }

}
