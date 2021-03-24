<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\Core;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use DB;
use Auth;

class CartOrdersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();      
        
        $this->middleware(function ($request, $next) {
            $this->logged_user_role_id = Auth::user()->role_id;
            $this->logged_user_id = Auth::user()->id;            
            $this->logged_user_role = $this->UserModel->get_role_from_id ($this->logged_user_role_id);                

            if(! ($this->logged_user_role == 'admin')) return redirect('/'); 
            return $next($request);
        });
    }

    
    /**
     * Show all resources
     */
    public function index(Request $request)
    {                              
        $search_user = $request->search_user;
        $search_terms = $request->search_terms;        
        $search_status = $request->search_status;        
        $search_payment_status = $request->search_payment_status;       

        $orders = DB::table('cart_orders')
            ->leftJoin('users', 'cart_orders.user_id', '=', 'users.id')
            ->leftJoin('cart_gateways', 'cart_orders.gateway_id', '=', 'cart_gateways.id')            
            ->select('cart_orders.*', 'users.name as customer_name' , 'users.email as customer_email', 'users.avatar as customer_avatar', 'cart_gateways.title as gateway_title', 
                DB::raw('(SELECT count(*) FROM cart_orders WHERE cart_orders.user_id = users.id) as count_orders'), 
                DB::raw('(SELECT count(*) FROM cart_orders WHERE cart_orders.user_id = users.id AND is_paid = 0) as count_unpaid_orders')
            );
                         
        if($search_user) $orders = $orders->where(function ($query) use ($search_user) {
            $query->where('users.name', 'like', "%$search_user%")
                ->orWhere('users.email', 'like', "%$search_user%")
                ->orWhere('users.code', 'like', "%$search_user%");
            });  

        if($search_status) $orders = $orders->where('status', $search_status);    
        if($search_payment_status=='paid') $orders = $orders->where('is_paid', 1);                      
        if($search_payment_status=='unpaid') $orders = $orders->where('is_paid', 0);                      
        if($search_terms) $orders = $orders->where('cart_orders.code', 'like', "%$search_terms%");    

        $orders = $orders->orderBy('id', 'desc')->paginate(20);       
        
        return view('admin/account', [
            'view_file' => 'cart.orders',
            'active_submenu' => 'orders',
            'search_terms' => $search_terms,
            'search_user' => $search_user,
            'search_status' => $search_status,
            'search_payment_status' => $search_payment_status,
            'orders' => $orders,            
        ]);
    }


     /**
     * Show resource
     */
    public function show(Request $request)
    {                              
        $id = $request->id;

        $order = DB::table('cart_orders')
            ->leftJoin('users', 'cart_orders.user_id', '=', 'users.id')
            ->leftJoin('cart_gateways', 'cart_orders.gateway_id', '=', 'cart_gateways.id')  
            ->select('cart_orders.*', 'users.name as customer_name' , 'users.email as customer_email', 'users.avatar as customer_avatar', 'cart_gateways.title as gateway_title', 
                DB::raw('(SELECT count(*) FROM cart_orders WHERE cart_orders.user_id = users.id) as count_orders'), 
                DB::raw('(SELECT count(*) FROM cart_orders WHERE cart_orders.user_id = users.id AND is_paid = 0) as count_unpaid_orders')
            );                               
        $order = $order->where('cart_orders.id', $id)->first();       
        if(! $order) redirect(route('admin.cart.orders')); 
        
        $gateways = DB::table('cart_gateways')                    
            ->orderBy('position', 'asc')  
            ->orderBy('id', 'desc')  
            ->get();     

        return view('admin/account', [
            'view_file' => 'cart.order',
            'active_submenu' => 'orders',            
            'menu_tab' => 'details',      
            'order' => $order,            
            'gateways' => $gateways,            
        ]);
    }



    /**
    * Update order items
    */
    public function update_order_items(Request $request)
    {
        $id = $request->id;  

        $inputs = $request->all(); // retrieve all of the input data as an array 

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $total = 0;

        foreach(cart_order_items($id) as $order_item) {
            $request_item_name = 'item_name_'.$order_item->id;
            $request_price = 'price_'.$order_item->id;

            $total = $total + $request->$request_price;

            DB::table('cart_orders_items')
            ->where('order_id', $id)
            ->where('id', $order_item->id)
            ->update([
                'item_name' => $request->$request_item_name,
                'price' => $request->$request_price,
            ]);
        }

        DB::table('cart_orders')
            ->where('id', $id)
            ->update([
                'due_date' => $inputs['due_date'],                    
                'total' => $total,
            ]);                               

        return redirect(route('admin.cart.orders.show', ['id' => $id]))->with('success', 'updated'); 
    }



    /**
    * Update order notes
    */
    public function update_order_notes(Request $request)
    {
        $id = $request->id;  

        $inputs = $request->all(); // retrieve all of the input data as an array 

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        
        DB::table('cart_orders')
            ->where('id', $id)
            ->update([
                'staff_notes' => $inputs['staff_notes'],                    
                'client_notes' => $inputs['client_notes'],                    
            ]);                               

        return redirect(route('admin.cart.orders.show', ['id' => $id]))->with('success', 'updated'); 
    }


    /**
    * Update order notes
    */
    public function update_order_payment(Request $request)
    {
        $id = $request->id;  

        $inputs = $request->all(); // retrieve all of the input data as an array 

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $order = DB::table('cart_orders')->where('id', $id)->first(); 
        if(! $order) return redirect(route('admin'));          
        
        // process order                                                    
        if(! $inputs['gateway_code']) $gateway_code = strtoupper(Str::random(12));
        else $gateway_code = $inputs['gateway_code'];

        DB::table('cart_orders')
        ->where('id', $id)
        ->update([
             'is_paid' => 1,
             'paid_at' => now(),  
             'gateway_id' => $inputs['gateway_id'], 
             'gateway_code' => $gateway_code,
         ]);     
       
         $order_items = DB::table('cart_orders_items')->where('order_id', $id)->get();
         foreach($order_items as $order_item) {
            DB::table('cart_orders_items')->where('id', $order_item->id)->update(['is_paid' => 1]);
            $product = DB::table('cart_products')->where('id', $order_item->product_id)->first();
            $product_categ = DB::table('cart_categ')->where('id', $product->categ_id ?? null)->first();
            if($product_categ->product_type ?? null == 'download') DB::table('cart_orders_items')->where('id', $order_item->id)->update(['is_delivered' => 1]);                            
         }

         return redirect(route('admin.cart.orders.show', ['id' => $id]))->with('success', 'updated'); 
    }



    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        if(DB::table('cart_orders')->where('id', $id)->where('is_paid', 1)->exists()) return redirect(route('admin.orders'))->with('error', 'is_paid'); 

        DB::table('cart_orders')->where('id', $id)->delete(); 
        DB::table('cart_orders_items')->where('order_id', $id)->delete();                  
        DB::table('tickets')->where('order_id', $id)->delete(); 

        return redirect(route('admin.cart.orders'))->with('success', 'deleted'); 
    }
}
