<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 

class CartCategoriesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->CartModel = new Cart();                        

        $this->extra_langs = DB::table('sys_lang')->where('is_default', 0)->orderBy('status', 'asc')->orderBy('name', 'asc')->get();
        $this->categories = Cart::whereNull('parent_id')->with('childCategories')->orderBy('active', 'desc')->orderBy('position', 'asc')->orderBy('title', 'asc')->get();          

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

        $count_categories = DB::table('cart_categ')->count();
      
        return view('admin/account', [
            'view_file'=>'cart.categories',
            'active_submenu'=>'cart.categ',
            'categories' => $this->categories,
            'count_categories' => $count_categories,
            'extra_langs' => $this->extra_langs,
        ]); 
    }


    /**
    * Create new resource
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $inputs = $request->all(); // retrieve all of the input data as an array 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.cart.categ'))
                ->withErrors($validator)
                ->withInput();
        }        
        
        if(! $inputs['parent_id']) {
            $validator = Validator::make($request->all(), ['product_type' => 'required']);
            if ($validator->fails()) { return redirect(route('admin.cart.categ'))->withErrors($validator)->withInput(); }     
            $product_type = $inputs['product_type'];
        } else {
            $parent_categ = DB::table('cart_categ')->where('id', $inputs['parent_id'])->first(); 
            $product_type = $parent_categ->product_type;
        }

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        if(DB::table('cart_categ')->where('slug', $slug)->exists()) return redirect(route('admin.cart.categ'))->with('error', 'duplicate'); 

        DB::table('cart_categ')->insert([
            'title' => $inputs['title'],
            'product_type' => $product_type,
            'parent_id' => $inputs['parent_id'] ?? null,
            'custom_tpl' => $inputs['custom_tpl'],
            'slug' => $slug,
            'description' => $inputs['description'],
            'active' => $inputs['active'],
            'position' => $inputs['position'],   
            'icon' => $inputs['icon'],
            'badges' => $inputs['badges'],
            'meta_title' => $inputs['meta_title'],
            'meta_description' => $inputs['meta_description'],
        ]);
                 
        $categ_id = DB::getPdo()->lastInsertId();  

        $this->CartModel->regenerate_tree_ids();
        $this->CartModel->recount_categ_items($categ_id);
        $this->CartModel->regenerate_product_types($categ_id);

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
            'title' => 'required',
            'product_type' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.cart.categ'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');
        if($inputs['slug'] == 'uncategorized') $slug = 'uncategorized';

        if(DB::table('cart_categ')->where('slug', $slug)->where('id', '!=', $id)->exists()) return redirect(route('admin.cart.categ'))->with('error', 'duplicate'); 

        DB::table('cart_categ')
            ->where('id', $id)
            ->update([                
                'title' => $inputs['title'],
                'product_type' => $inputs['product_type'],
                'parent_id' => $inputs['parent_id'] ?? null,
                'custom_tpl' => $inputs['custom_tpl'],
                'slug' => $slug,
                'description' => $inputs['description'],
                'active' => $inputs['active'],
                'position' => $inputs['position'],   
                'icon' => $inputs['icon'],
                'badges' => $inputs['badges'],
                'meta_title' => $inputs['meta_title'],
                'meta_description' => $inputs['meta_description'],
        ]);

        $this->CartModel->regenerate_tree_ids();
        $this->CartModel->recount_categ_items($id);
        $this->CartModel->regenerate_product_types($id);

        return redirect(route('admin.cart.categ'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');   
        
        $uncategorized_categ_id = $this->CartModel->get_uncategorized_categ_id();
        if($id == $uncategorized_categ_id) return redirect(route('admin.cart.categ'));       

        DB::table('cart_categ')->where('id', $id)->delete(); 
        DB::table('cart_products')->where('categ_id', $id)->update(['categ_id' => $uncategorized_categ_id]);

        $this->CartModel->regenerate_tree_ids();
        $this->CartModel->recount_categ_items($id);
        
        return redirect(route('admin.cart.categ'))->with('success', 'deleted'); 
    }


    
    /**
    * Translate
    */
    public function translate(Request $request)
    {

        $id = $request->id;
        $categ = DB::table('cart_categ')->where('id', $id)->first();
        if(! $categ) return redirect(route('admin.cart.categ')); 

        $translate_langs = DB::table('sys_lang')
            ->select('sys_lang.*', 
                DB::raw('(SELECT title FROM cart_categ_langs WHERE cart_categ_langs.lang_id = sys_lang.id AND categ_id = '.$categ->id.') as translated_title'),
                DB::raw('(SELECT meta_title FROM cart_categ_langs WHERE cart_categ_langs.lang_id = sys_lang.id AND categ_id = '.$categ->id.') as translated_meta_title'),
                DB::raw('(SELECT description FROM cart_categ_langs WHERE cart_categ_langs.lang_id = sys_lang.id AND categ_id = '.$categ->id.') as translated_description'),
                DB::raw('(SELECT meta_description FROM cart_categ_langs WHERE cart_categ_langs.lang_id = sys_lang.id AND categ_id = '.$categ->id.') as translated_meta_description'))
            ->where('is_default', 0)
            ->orderBy('active', 'desc')
            ->orderBy('name', 'asc')
            ->get();         

        return view('admin/account', [
            'view_file' => 'cart.categ-translate',
            'active_submenu' => 'cart.categ',
            'categ' => $categ,
            'translate_langs' => $translate_langs,
        ]);
    }



    /**
    * Update translates
    */
    public function update_translate(Request $request)
    {        
        $id = $request->id;  
        $categ = DB::table('cart_categ')->where('id', $id)->first();
        if(! $categ) return redirect(route('admin.cart.categ')); 

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $inputs = $request->all(); // retrieve all of the input data as an array 
               
        $extra_langs = DB::table('sys_lang')->where('is_default', 0)->orderBy('active', 'desc')->orderBy('name', 'asc')->get();
        foreach($extra_langs as $lang) {
            DB::table('cart_categ_langs')
                ->updateOrInsert(
                    ['categ_id' => $categ->id, 'lang_id' => $lang->id],
                    ['title' => $request['title_'.$lang->id], 'description' => $request['description_'.$lang->id], 'meta_title' => $request['meta_title_'.$lang->id], 'meta_description' => $request['meta_description_'.$lang->id]],
                );
        }

        return redirect(route('admin.cart.categ'))->with('success', 'updated'); 
    }

}
