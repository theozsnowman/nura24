<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/ 

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Core;
use App\Models\Cart;
use DB;
use Auth; 
use App; 

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {        
        $this->CartModel = new Cart(); 
        $this->config = Core::config();                     
    }


    /**
    * Cart index
    */
    public function index(Request $request)
    {                
        if(! check_module('cart')) return redirect('/');     

        $lang_id = active_lang()->id;
        
        $products = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')            
            ->select('cart_products.*', 'cart_categ.title as categ_title', 'cart_categ.slug as categ_slug', 
                DB::raw("(SELECT title FROM cart_products_langs WHERE cart_products_langs.lang_id = $lang_id AND cart_products_langs.product_id = cart_products.id) as translated_title") )
            ->where('cart_products.status', 'active')
            ->where('cart_products.hidden', 0)    
			->orderBy('cart_products.featured', 'desc')
            ->orderBy('cart_products.id', 'desc')
            ->paginate(24);   

        return view('frontend/'.$this->config->template.'/cart', [              
                'products' => $products,
        ]);
    }



    /**
    * ALL products from a category
    */
    public function categ(Request $request)
    {                         
        if(! check_module('cart')) return redirect('/');        

        $slug = $request->slug;      
        $lang_id = active_lang()->id;

        $categ = DB::table('cart_categ')        
            ->select('cart_categ.*', 
				DB::raw("(SELECT title FROM cart_categ_langs WHERE cart_categ_langs.lang_id = $lang_id AND cart_categ_langs.categ_id = cart_categ.id) as translated_title"), 
				DB::raw("(SELECT description FROM cart_categ_langs WHERE cart_categ_langs.lang_id = $lang_id AND cart_categ_langs.categ_id = cart_categ.id) as translated_description"), 
				DB::raw("(SELECT meta_title FROM cart_categ_langs WHERE cart_categ_langs.lang_id = $lang_id AND cart_categ_langs.categ_id = cart_categ.id) as translated_meta_title"), 
				DB::raw("(SELECT meta_description FROM cart_categ_langs WHERE cart_categ_langs.lang_id = $lang_id AND cart_categ_langs.categ_id = cart_categ.id) as translated_meta_description"))		    
            ->where('cart_categ.slug', $slug)
            ->where('cart_categ.active', 1)
            ->first();  
        if(!$categ) abort(404);               

        if($categ->translated_title) $categ->title = $categ->translated_title;

        if($categ->translated_meta_title) $categ->meta_title = $categ->translated_meta_title;
        else $categ->meta_title = $categ->meta_title ?? $categ->title;

        if($categ->translated_meta_description) $categ->meta_description = $categ->translated_meta_description;
        else $categ->meta_description = $categ->meta_description ?? $categ->description ?? $categ->title;

        if($categ->translated_description) $categ->description = $categ->translated_description;
        else $categ->description = $categ->description ?? null;
                

        $categ_id = $categ->id;
        $categ_tree_ids = $categ->tree_ids ?? null;
        if($categ_tree_ids) $categ_tree_ids_array = explode(',', $categ_tree_ids);

        $products = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
            ->select('cart_products.*', 'cart_categ.title as categ_title', 'cart_categ.slug as categ_slug', 
                DB::raw("(SELECT title FROM cart_products_langs WHERE cart_products_langs.lang_id = $lang_id AND cart_products_langs.product_id = cart_products.id) as translated_title") )
            ->where('cart_products.status', 'active') 
            ->where('cart_products.hidden', 0)    
            ->whereIn('cart_products.categ_id', $categ_tree_ids_array)
            ->orderBy('cart_products.featured', 'desc')       
            ->orderBy('cart_products.id', 'desc')
            ->paginate(12);            
    
            
        if($categ->custom_tpl) $view_file = str_replace('.blade.php', '', $categ->custom_tpl);
            else $view_file = 'cart-categ';

        return view('frontend/'.$this->config->template.'/'.$view_file, [              
            'products' => $products,
            'categ' => $categ,
        ]);
    }



    /**
    * Cart product
    */
    public function product(Request $request)
    {        
        if(! check_module('cart')) return redirect('/');        

        $lang_id = active_lang()->id;

        $categ_slug = $request->categ_slug;
        $slug = $request->slug;        
        if(! $categ_slug || ! $slug) abort(404);
 
        $product = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
            ->select('cart_products.*', 'cart_categ.slug as categ_slug', 'cart_categ.title as categ_title', 
                DB::raw("(SELECT title FROM cart_products_langs WHERE lang_id = $lang_id AND product_id = cart_products.id) as translated_title"), 
                DB::raw("(SELECT help_info FROM cart_products_langs WHERE lang_id = $lang_id AND product_id = cart_products.id) as translated_help_info"), 
				DB::raw("(SELECT content FROM cart_products_langs WHERE lang_id = $lang_id AND product_id = cart_products.id) as translated_content"), 
				DB::raw("(SELECT meta_title FROM cart_products_langs WHERE lang_id = $lang_id AND product_id = cart_products.id) as translated_meta_title"), 
				DB::raw("(SELECT meta_description FROM cart_products_langs WHERE lang_id = $lang_id AND product_id = cart_products.id) as translated_meta_description"))
            ->where('cart_products.status', 'active')    
            ->where('cart_products.hidden', 0)    
            ->where('cart_products.slug', $slug)   
            ->where('cart_categ.active', 1)      
            ->where('cart_categ.slug', $categ_slug)      
            ->first();                 

        if(! $product) abort(404);             

        if($product->translated_title) $product->title = $product->translated_title;

        if($product->translated_help_info) $product->help_info = $product->translated_help_info;

        if($product->translated_meta_title) $product->meta_title = $product->translated_meta_title;
        else $product->meta_title = $product->translated_title ?? $product->meta_title ?? $product->title;

        if($product->translated_meta_description) $product->meta_description = $product->translated_meta_description;
        else $product->meta_description = $product->meta_description ?? $product->description ?? $product->title;

        if($product->translated_content) $product->content = $product->translated_content;
        else $product->content = $product->content ?? null;


        $images = DB::table('cart_images')
            ->where('product_id', $product->id)            
            ->get();   
       
        $related_products = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
            ->select('cart_products.*', 'cart_categ.slug as categ_slug', 'cart_categ.title as categ_title')
            ->where('cart_products.categ_id', $product->categ_id)        
            ->where('cart_products.id', '!=', $product->id)
            ->where('cart_products.status', 'active')    
            ->where('cart_products.hidden', 0)    
            ->where('cart_categ.active', 1)   
            ->orderBy('id', 'desc')
            ->limit(24)
            ->get();

        if($product->custom_tpl) $view_file = str_replace('.blade.php', '', $product->custom_tpl);
        else $view_file = 'cart-product';
      
        return view('frontend/'.$this->config->template.'/'.$view_file, [
            'product' => $product,            
            'images' => $images,      
            'related_products' => $related_products,    
        ]); 
    }  


     
    /**
    * Search results
    */
    public function search(Request $request)
    {             
        if(! check_module('cart')) return redirect('/');          
                    
        $s = $request->s;                
        $lang_id = active_lang()->id;
        
        $products = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')            
            ->select('cart_products.*', 'cart_categ.title as categ_title', 'cart_categ.slug as categ_slug', 
                DB::raw("(SELECT title FROM cart_products_langs WHERE cart_products_langs.lang_id = $lang_id AND cart_products_langs.product_id = cart_products.id) as translated_title") )
            ->where('cart_products.status', 'active')
            ->where('cart_products.hidden', 0)    
            ->where(function ($query) use ($s) {
                $query->where('cart_products.title', 'like', "%$s%")
                    ->orWhere('cart_products.search_terms', 'like', "%$s%");
                })
            ->orderBy('cart_products.featured', 'desc')       
            ->orderBy('cart_products.id', 'desc')
            ->paginate(12);

        return view('frontend/'.$this->config->template.'/cart-search', [              
            'products' => $products,
            's' => $s,
        ]);
    }
   
}
