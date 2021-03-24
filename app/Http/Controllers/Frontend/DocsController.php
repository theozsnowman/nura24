<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/ 

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Core;
use App\Models\Doc;
use DB;
use Auth; 
use App;

class DocsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {        
        $this->DocModel = new Doc();   
        $this->config = Core::config();        
        
        $this->categories = Doc::whereNull('parent_id')->where('active', 1)->with('childCategories')->select('docs_categ.*')->orderBy('position', 'asc')->get();  
    }

    /**
    * Docs index
    */
    public function index(Request $request)
    {            
        if(! check_module('docs')) return redirect('/');            

        $featured_articles = DB::table('docs')
            ->leftJoin('docs_categ', 'docs.categ_id', '=', 'docs_categ.id')
            ->select('docs.*', 'docs_categ.title as categ_title', 'docs_categ.slug as categ_slug')
            ->where('docs.active', 1)
            ->where('docs_categ.active', 1)
            ->where('docs.featured', 1)
            ->where('docs.lang_id', active_lang()->id)
            ->orderBy('docs.position', 'asc')
            ->orderBy('docs.id', 'desc')
            ->paginate(12);

        return view('frontend/'.$this->config->template.'/docs', [
            'featured_articles' => $featured_articles
        ]);
    }


    /**
    * Docs categ
    */
    public function categ(Request $request)
    {       
        if(! check_module('docs')) return redirect('/');    

        $slug = $request->slug; 

        $categ = DB::table('docs_categ')
            ->where('slug', $slug)            
            ->where('active', 1)
            ->where('lang_id', active_lang()->id)
            ->first();          
        if(!$categ) abort(404);

        if($categ->redirect_url) return redirect()->away($categ->redirect_url);
        
        $categ_id = $categ->id;
        $categ_tree_ids = $categ->tree_ids ?? null;
        if($categ_tree_ids) $categ_tree_ids_array = explode(',', $categ_tree_ids);

        $tree_articles = DB::table('docs')
            ->where('active', 1) 
            ->whereIn('docs.categ_id', $categ_tree_ids_array)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(24);

        $categ_articles = DB::table('docs')
            ->where('active', 1) 
            ->where('categ_id', $categ->id)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('frontend/'.$this->config->template.'/docs-categ', [                                 
            'categ' => $categ, 
            'tree_articles' => $tree_articles, 
            'categ_articles' => $categ_articles, 
        ]);
    }


    /**
    * Docs search
    */
    public function search(Request $request)
    {        
        if(! check_module('docs')) return redirect('/');    
             
        $s = $request->s; 
        $lang = $request->lang;  

        $articles = DB::table('docs')
            ->leftJoin('docs_categ', 'docs_categ.id', '=', 'docs.categ_id') 
            ->select('docs.*', 'docs_categ.title as categ_title', 'docs_categ.slug as categ_slug') 
            ->where('docs.active', 1) 
            ->where(function ($query) use ($s) {
                $query->where('docs.title', 'like', "%$s%")
                    ->orWhere('docs.search_terms', 'like', "%$s%");
                });          

            if(! $lang) {
                $default_lang_id = DB::table('sys_lang')
                    ->where('is_default', 1)      
                    ->value('id');  
                $articles = $articles->where('docs.lang_id', $default_lang_id);
            } else {
                $active_lang_id = DB::table('sys_lang')
                    ->where('code', $lang)      
                    ->value('id'); 
                $articles = $articles->where('docs.lang_id', $active_lang_id); 
            }

        $articles = $articles->orderBy('docs.featured', 'desc')
            ->orderBy('docs.id', 'desc')
            ->paginate(12);

        return view('frontend/'.$this->config->template.'/docs-search', [
            'categories' => $this->categories,
            'articles' => $articles,
            's' => $s,
        ]);
    }

}
