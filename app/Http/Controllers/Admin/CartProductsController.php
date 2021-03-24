<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Upload;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 
use Image;

class CartProductsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();    
        $this->UploadModel = new Upload();   
        $this->CartModel = new Cart();   

        $this->extra_langs = DB::table('sys_lang')->where('is_default', 0)->orderBy('status', 'asc')->orderBy('name', 'asc')->get();
        $this->categories = Cart::whereNull('parent_id')->with('childCategories')->select('cart_categ.*')->orderBy('title', 'asc')->get();  

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

        if(! check_access('cart')) return redirect(route('admin'));

        $search_terms = $request->search_terms;
        $search_status = $request->search_status;
        $search_categ_id = $request->search_categ_id;
        $search_featured = $request->search_featured;
        $orderby = $request->orderby;
       
        $products = DB::table('cart_products')
            ->leftJoin('users', 'cart_products.created_by_user_id', '=', 'users.id')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
            ->select('cart_products.*', 'users.name as author_name', 'cart_categ.product_type as type', 
                DB::raw("(SELECT count(*) FROM cart_images WHERE cart_products.id = cart_images.product_id) count_images"), 
                DB::raw("(SELECT sum(price*quantity) FROM cart_orders_items WHERE cart_products.id = cart_orders_items.product_id AND cart_orders_items.is_paid = 1) amount_total"),
                DB::raw("(SELECT count(*) FROM cart_orders_items WHERE cart_products.id = cart_orders_items.product_id AND cart_orders_items.is_paid = 1) count_paid_orders"),
                DB::raw("(SELECT count(*) FROM cart_orders_items WHERE cart_products.id = cart_orders_items.product_id AND cart_orders_items.is_paid != 1) count_unpaid_orders"));         
                
        if($search_terms) $products = $products->where(function ($query) use ($search_terms) {
            $query->where('cart_products.title', 'like', "%$search_terms%")
                ->orWhere('cart_products.sku', 'like', "%$search_terms%")
                ->orWhere('cart_products.search_terms', 'like', "%$search_terms%");                    
        }); 
            
        if($search_status)
            $products = $products->where('cart_products.status', 'like', $search_status);                     
        if($search_featured==1)
            $products = $products->where('cart_products.featured', 1);       
        if($search_categ_id) {
            $categ = DB::table('cart_categ')->where('id', $search_categ_id)->first();              
            $categ_id = $categ->id;
            $categ_tree_ids = $categ->tree_ids ?? null;
            if($categ_tree_ids) $categ_tree_ids_array = explode(',', $categ_tree_ids);
            $products = $products->whereIn('cart_products.categ_id', $categ_tree_ids_array);   
        }
           
        
        if(!$orderby) $products = $products->orderBy('status', 'asc')->orderBy('id', 'desc');   
        if($orderby=='latest') $products = $products->orderBy('id', 'desc');   
        if($orderby=='price_low') $products = $products->orderBy('price', 'asc');   
        if($orderby=='price_high') $products = $products->orderBy('price', 'desc');   
        if($orderby=='amount_earned_low') $products = $products->orderBy('amount_total', 'asc');   
        if($orderby=='amount_earned_high') $products = $products->orderBy('amount_total', 'desc');   
        
        $products = $products->paginate(25);       
                
        return view('admin/account', [
            'view_file' => 'cart.products',
            'active_submenu' => 'cart.products',
            'search_terms' => $search_terms,
            'products' => $products,
            'search_status' => $search_status,
            'search_categ_id' => $search_categ_id,
            'search_featured' => $search_featured,
            'orderby' => $orderby,
            'categories' => $this->categories,
        ]); 
    }


    /**
    * Show form to add new resource
    */
    public function create()
    {      
        if(! check_access('cart')) return redirect(route('admin'));

        return view('admin/account', [
            'view_file' => 'cart.create-product',
            'active_submenu' => 'cart.products',
            'categories' => $this->categories,
        ]);
    }


    /**
    * Create new resource
    */
    public function store(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'categ_id' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.cart.products.create'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 

        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');
        
        if ($request->has('featured')) $featured = 1; else $featured = 0;
        if ($request->has('hidden')) $hidden = 1; else $hidden = 0;
        if ($request->has('disable_orders')) $disable_orders = 1; else $disable_orders = 0;
        
        DB::table('cart_products')->insert([
            'categ_id' => $inputs['categ_id'],
            'title' => $inputs['title'],
            'slug' => $slug,
            'summary' => $inputs['summary'],
            'price' => floatval($inputs['price']),
            'content' => $inputs['content'],
            'help_info' => $inputs['help_info'],
            'status' => $inputs['status'],
            'hidden' => $hidden,
            'featured' => $featured,
            'disable_orders' => $disable_orders,
            'disable_orders_notes' => $inputs['disable_orders_notes'],
            'meta_title' => $inputs['meta_title'],
            'meta_description' => $inputs['meta_description'],
            'search_terms' => $inputs['search_terms'],                
            'sku' => $inputs['sku'] ?? strtoupper(Str::random(9)),
            'custom_tpl' => $inputs['custom_tpl'],
            'created_by_user_id' => Auth::user()->id,
            'created_at' => now(),
        ]);

        $id = DB::getPdo()->lastInsertId(); 

        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.cart.products.create'))
                    ->withErrors($validator)
                    ->withInput();
            } 
                        
            $image_db = $this->UploadModel->upload_image($request, 'image', 'resize');    
            DB::table('cart_products')->where('id', $id)->update(['image' => $image_db]);            
        }                              

        $this->CartModel->recount_categ_items($inputs['categ_id']);

        return redirect(route('admin.cart.products'))->with('success', 'created'); 
    }


    /**
    * Show form to edit resource     
    */
    public function show(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $id = $request->id;  

        $product = DB::table('cart_products')            
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
            ->select('cart_products.*', 'cart_categ.product_type as type')
            ->where('cart_products.id', $id)
            ->first();          
        if(! $product) abort(404);       

        return view('admin/account', [
            'view_file' => 'cart.update-product',
            'active_submenu' => 'cart.products',
            'menu_tab' => 'details',
            'product' => $product,
            'categories' => $this->categories,  
            'extra_langs' => $this->extra_langs,          
        ]);
    }


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'categ_id' => 'required',
            'price' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.cart.products.show', ['id' => $id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        if($inputs['slug']) $slug = Str::slug($inputs['slug'], '-');
        else $slug = Str::slug($inputs['title'], '-');

        if ($request->has('featured')) $featured = 1; else $featured = 0;
        if ($request->has('hidden')) $hidden = 1; else $hidden = 0;
        if ($request->has('disable_orders')) $disable_orders = 1; else $disable_orders = 0;       

        DB::table('cart_products')
            ->where('id', $id)
            ->update([
                'categ_id' => $inputs['categ_id'],
                'title' => $inputs['title'],
                'slug' => $slug,
                'summary' => $inputs['summary'],
                'price' => floatval($inputs['price']),
                'content' => $inputs['content'],
                'help_info' => $inputs['help_info'],
                'status' => $inputs['status'],
                'hidden' => $hidden,
                'featured' => $featured,
                'disable_orders' => $disable_orders,
                'disable_orders_notes' => $inputs['disable_orders_notes'],
                'meta_title' => $inputs['meta_title'],
                'meta_description' => $inputs['meta_description'],
                'search_terms' => $inputs['search_terms'],                
                'sku' => $inputs['sku'] ?? strtoupper(Str::random(9)),
                'custom_tpl' => $inputs['custom_tpl'],
                'updated_at' => now(),            
            ]);

        // process image        
        if ($request->hasFile('image')) {

            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.cart.products.show', ['id' => $id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $image_db = $this->UploadModel->upload_image($request, 'image', 'resize');    
            DB::table('cart_products')->where('id', $id)->update(['image' => $image_db]);            
        }                            

        $this->CartModel->recount_categ_items($inputs['categ_id']);

        return redirect(route('admin.cart.products'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        if(! check_access('cart', 'manager')) return redirect(route('admin'));

        $id = $request->id;  
        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $product = DB::table('cart_products')
            ->where('id', $id)
            ->first(); 

        DB::table('cart_products')->where('id', $id)->delete(); // delete page

        $this->CartModel->recount_categ_items($product->categ_id);

        return redirect(route('admin.cart.products'))->with('success', 'deleted'); 
    }



    /**
    * Display product images
    */
    public function images(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $id = $request->id;

        $images = DB::table('cart_images')
            ->where('product_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(25);       

        $product = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
            ->select('cart_products.*', 'cart_categ.product_type as type')
            ->where('cart_products.id', $id)
            ->first();  
        if(!$product) abort(404);
      
        return view('admin/account', [
            'view_file' => 'cart.product-images',
            'active_submenu' => 'cart.products',
            'menu_tab' => 'images',
            'images' => $images,
            'product' => $product,
            'id' => $id,
            'extra_langs' => $this->extra_langs,
        ]); 
    }


    /**
    * Create new image
    */
    public function store_image(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $id = $request->id;
        $description = $request->description;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        // process image        
        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), ['image' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.cart.product.images', ['id' => $id]))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $image_db = $this->UploadModel->upload_image($request, 'image', 'resize');    
            DB::table('cart_images')->insert([
                'product_id' => $id,
                'description' => $description,
                'file' => $image_db,               
            ]);
        }        
                 
        return redirect(route('admin.cart.product.images', ['id' => $id]))->with('success', 'created'); 
    }   


    /**
    * Remove timage
    */
    public function destroy_image(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $id = $request->id;
        $image_id = $request->image_id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        // delete image
        $image = DB::table('cart_images')->where('id', $image_id)->first();   
        delete_image($image->file);     
        
        DB::table('cart_images')->where('id', $image_id)->delete(); 

        return redirect(route('admin.cart.product.images', ['id' => $id]))->with('success', 'deleted'); 
    }



     /**
    * Display all files
    */
    public function files(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $id = $request->id;

        $product = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
            ->select('cart_products.*', 'cart_categ.product_type as type')
            ->where('cart_products.id', $id)      
            ->first();
        if(! $product) return redirect(route('admin.cart.products')); 

        $files = DB::table('cart_files')
            ->where('product_id', $id)
            ->orderBy('active', 'asc')
            ->orderBy('version', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(20);       
      
        return view('admin/account', [
            'view_file' => 'cart.product-files',
            'active_submenu' => 'cart.products',
            'menu_tab' => 'files',
            'files' => $files,
            'product' => $product,
            'extra_langs' => $this->extra_langs,
        ]); 
    }


    /**
    * Create new file
    */
    public function store_file(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $product_id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.cart.product.files', ['id' => $product_id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        DB::table('cart_files')->insert([
            'title' => $inputs['title'],
            'hash' => Str::random(16), 
            'product_id' => $product_id,
            'description' => $inputs['description'],
            'active' => $inputs['active'],
            'featured' => $inputs['featured'],
            'version' => $inputs['version'],
            'release_date' => $inputs['release_date'],
            'count_downloads' => 0,
            'created_at' => now(),
        ]);

        // process file
        if ($request->hasFile('file')) {
            $file_id = DB::getPdo()->lastInsertId(); 
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('cart_files')->where('id', $file_id)->update(['file' => $file_db]);            
        }      
                 
        return redirect(route('admin.cart.product.files', ['id' => $product_id]))->with('success', 'created'); 
    }   


    /**
    * Update file
    */
    public function update_file(Request $request)
    {        
        if(! check_access('cart')) return redirect(route('admin'));

        $product_id = $request->id;  
        $file_id = $request->file_id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect($request->Url())
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array          

        DB::table('cart_files')
            ->where('id', $file_id)
            ->update([
                'title' => $inputs['title'],
                'hash' => Str::random(16), 
                'product_id' => $product_id,
                'description' => $inputs['description'],
                'active' => $inputs['active'],
                'featured' => $inputs['featured'],
                'version' => $inputs['version'],
                'release_date' => $inputs['release_date'],
        ]);
      
         // process file
         if ($request->hasFile('file')) {
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('cart_files')->where('id', $file_id)->update(['file' => $file_db]);            
        } 

        return redirect(route('admin.cart.product.files', ['id' => $product_id]))->with('success', 'updated'); 
    }


    /**
    * Remove the file
    */
    public function destroy_file(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $product_id = $request->id;
        $file_id = $request->file_id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        $file = DB::table('cart_files')->where('id', $file_id)->first();   

        if($file) {
            @unlink('uploads/'.$file->file);
        }          

        DB::table('cart_files')->where('id', $file_id)->delete(); 

        return redirect(route('admin.cart.product.files', ['id' => $product_id]))->with('success', 'deleted'); 
    }



     /**
    * Translate
    */
    public function translate(Request $request)
    {
        if(! check_access('cart')) return redirect(route('admin'));

        $id = $request->id; // product id
        $product = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
            ->select('cart_products.*', 'cart_categ.product_type as type')
            ->where('cart_products.id', $id)
            ->first();
        if(! $product) return redirect(route('admin.cart.products')); 

        $translate_langs = DB::table('sys_lang')
            ->select('sys_lang.*', 
                DB::raw('(SELECT title FROM cart_products_langs WHERE lang_id = sys_lang.id AND product_id = '.$id.') as translated_title'),
                DB::raw('(SELECT meta_title FROM cart_products_langs WHERE lang_id = sys_lang.id AND product_id = '.$id.') as translated_meta_title'),
                DB::raw('(SELECT content FROM cart_products_langs WHERE lang_id = sys_lang.id AND product_id = '.$id.') as translated_content'),
                DB::raw('(SELECT help_info FROM cart_products_langs WHERE lang_id = sys_lang.id AND product_id = '.$id.') as translated_help_info'),
                DB::raw('(SELECT meta_description FROM cart_products_langs WHERE lang_id = sys_lang.id AND product_id = '.$id.') as translated_meta_description'))
            ->where('is_default', 0)
            ->orderBy('active', 'desc')
            ->orderBy('name', 'asc')
            ->get();         

        //dd($extra_langs);

        return view('admin/account', [
            'view_file' => 'cart.product-translate',
            'active_submenu' => 'cart.categ',
            'menu_tab' => 'translates',
            'product' => $product,
            'translate_langs' => $translate_langs,
            'extra_langs' => $this->extra_langs,
        ]);
    }



    /**
    * Update translates
    */
    public function update_translate(Request $request)
    {        
        if(! check_access('cart')) return redirect(route('admin'));
        
        $id = $request->id;  
        $product = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')
            ->select('cart_products.*', 'cart_categ.product_type as type')
            ->where('cart_products.id', $id)
            ->first();
        if(! $product) return redirect(route('admin.cart.products')); 

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $inputs = $request->all(); // retrieve all of the input data as an array 
               
        $extra_langs = DB::table('sys_lang')->where('is_default', 0)->orderBy('active', 'desc')->orderBy('name', 'asc')->get();
        foreach($extra_langs as $lang) {
            DB::table('cart_products_langs')
                ->updateOrInsert(
                    ['product_id' => $product->id, 'lang_id' => $lang->id],
                    ['title' => $request['title_'.$lang->id], 'content' => $request['content_'.$lang->id], 'help_info' => $request['help_info_'.$lang->id], 'meta_title' => $request['meta_title_'.$lang->id], 'meta_description' => $request['meta_description_'.$lang->id]],
                );
        }

        return redirect(route('admin.cart.product.translate', ['id' => $id]))->with('success', 'updated'); 
    }


}
