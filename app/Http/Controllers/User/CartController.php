<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Core;
use App\Models\Cart;
use DB;
use Auth; 
use App; 

use App\Models\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {        
        $this->middleware('auth');
        $this->UserModel = new User(); 
        $this->CartModel = new Cart(); 
        $this->config = Core::config();  
        
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;                 
        
            $role = $this->UserModel->get_role_from_id ($this->role_id);    
            if($role != 'user') return redirect('/'); 
            return $next($request);
        }); 
    }


    /**
    * Add product to cart
    */
    public function cart_add(Request $request)
    {                         
                
        $lang = $request->lang;   

        // if user is not logged, redirect to login area (to login or register)
        if(!Auth::user()) return redirect(route('login', ['lang' => $lang]))->with('error', 'login_required');  

        $id = $request->id;
        $quantity = $request->quantity;
        
        $product = DB::table('cart_products')
            ->leftJoin('cart_categ', 'cart_products.categ_id', '=', 'cart_categ.id')            
            ->select('cart_products.*', 'cart_categ.title as categ_title', 'cart_categ.slug as categ_slug')
            ->where('cart_products.id', $id)     
            ->where('cart_products.status', 'active')                                
            ->where('cart_categ.active', 1)         
            ->first();  
            
        if(!$product) abort(404);

        if($product->disable_orders) return redirect(route('cart.product', ['lang' => $lang, 'slug' => $product->slug, 'categ_slug' => $product->categ_slug]))->with('error', 'orders_disabled');  

        DB::table('cart_shopping_cart')->insert([
            'product_id' => $id, 
            'user_id' => Auth::user()->id ?? NULL,
            'quantity' => $quantity ?? 1, 
            'created_at' =>  now(),
            'ip' => $request->ip(),
        ]); 

        return redirect(route('cart.product', ['lang' => $lang, 'slug' => $product->slug, 'categ_slug' => $product->categ_slug]))->with('success', 'added_to_cart');  

    }


    /**
    * Shopping cart page
    */
    public function cart()
    {                         

        $shopping_cart = $this->CartModel->shopping_cart(Auth::user()->id);     

        return view('user/account', [
            'view_file'=>'cart.shopping-cart',
            'shopping_cart' => $shopping_cart ?? NULL,    
        ]);
    }

    
    /**
    * Remove item from shopping cart
    */
    public function cart_delete(Request $request)
    {    
        $lang = $request->lang;   
        $id = $request->id; 

        DB::table('cart_shopping_cart')->where('id', $id)->where('user_id', Auth::user()->id)->delete(); // delete item
        return redirect(route('cart.basket', ['lang' => $lang]))->with('success', 'deleted');  
    }
 


    /**
    * Create new order
    */
    public function store_order(Request $request)
    {           
        $lang = $request->lang;   

        $inputs = $request->all(); // retrieve all of the input data as an array  

        $shopping_cart = $this->CartModel->shopping_cart(Auth::user()->id);                

        // Order due date
        if(isset($this->config->cart_invoices_due_date_hours)) {
            $hours = $this->config->cart_invoices_due_date_hours;
            $due_date = date('Y-m-d H:i', strtotime("+ $hours hours"));
        }
        
        // create order
        $order_code = strtoupper(Str::random(12));

        DB::table('cart_orders')->insert([
            'code' => $order_code, 
            'user_id' => Auth::user()->id, 
            'created_at' =>  now(),
            'total' => $shopping_cart['total'],
            'currency_id' =>  default_currency()->id,
            'is_paid' => 0,
            'due_date' => $due_date ?? null,
            'shopping_cart_data' => serialize($shopping_cart),
            'created_by_user_id' => Auth::user()->id, 
        ]); 

        $order_id = DB::getPdo()->lastInsertId();                  
        if(count($shopping_cart['products'])>0) {            
            foreach($shopping_cart['products'] as $product) {                
                DB::table('cart_orders_items')->insert([
                    'order_id' => $order_id, 
                    'product_id' => $product->id, 
                    'price' => $product->price,
                    'item_name' => $product->title,
                    'item_description' => $product->content,
                    'quantity' => $product->quantity ?? 1,
                ]);                 

            }
        }             

        // delete items from shopping cart
        DB::table('cart_shopping_cart')->where('user_id', Auth::user()->id)->delete(); // delete items

        // sent notification email
        $subject = 'New Order';
        $message_html = '
                <html>
                <head>
                    <title>New Order</title>
                </head>
                <body>                    
                    <div style="font-size:14px;font-family:arial;">                    
                    <p>Your order <b>'.$order_code.'</b> has beeen created!</b></p>
                    <p><a class="font-weight-bold text-danger" href="'.route("user.orders", ["lang" => $lang]).'"><b>GO TO ORDERS AREA</b></a> to pay this order.</p>                 
                    <hr>
                    Products or services related to unpaid invoice will be delivered after you pay the invoice<br>
                    If you ordered downloadable products (software), the downloads will be available automatically right after payment.<br>       
                    <hr>
                    Thank you.
                    </div>
                </body>
                </html>
                ';
         
        // disable action in demo mode:
        if(! config('app.demo_mode')) {
            if($this->config->mail_sending_option=='smtp') {
                // SMTP mailer
                $emailModel = new Email();                                                                                 
                $mail_args = array('to_email' => Auth::user()->email, 'subject' => $subject, 'body' => $message_html);            
                $attachments = null;
                $emailModel->send_email($mail_args, $attachments);                               
            }
            else {
                // PHP MAILER	
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: '.$this->config->site_email."\r\n" .
                    'Reply-To: '.$this->config->site_email."\r\n" .
                    'X-Mailer: PHP/' . phpversion();
                mail(Auth::user()->email, $subject, $message_html, $headers);
            }       
        }

        return redirect(route('user.orders.show', ['lang' => $lang, 'code' => $order_code]))->with('success', 'created');          
    }



    
    /**
    * Checkout
    */
    public function checkout(Request $request)
    {         
        $lang = $request->lang;   

        $inputs = $request->all(); // retrieve all of the input data as an array  
        
        $code = $request->code;     
        $order = DB::table('cart_orders')
            ->where('user_id', Auth::user()->id)        
            ->where('code', $code)
            ->where('is_paid', 0)
            ->first();  
        if(! $order) return redirect(route('user.orders', ['lang' => $lang]));   

        $billing_address = array(
            'name' => $inputs['billing_name'],
            'country' => $inputs['billing_country'],
            'company' => $inputs['billing_company'],
            'address' => $inputs['billing_address'],             
        );       

        // check gateway
        $gateway_id = $inputs['gateway_id'];        
        $gateway = DB::table('cart_gateways')
            ->where('active', 1)      
            ->where('hidden', 0)      
            ->where('id', $gateway_id)              
            ->first();
        if(! $gateway) return redirect(route('user.orders', ['lang' => $lang]))->with('error', 'no_gateway');      


        return view('user/account', [
            'view_file' => 'cart.checkout.'.$gateway->checkout_file, 
            'order' => $order,
            'gateway' => $gateway,
        ]);  
    }



    public function orders()
    {        
        $orders = DB::table('cart_orders')                
            ->where('user_id', Auth::user()->id)    
            ->orderBy('is_paid', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(20);          

        return view('user/account', [
            'view_file' => 'cart.orders', 
            'orders' => $orders,
        ]);  
    }


    /**
    * Show invoice
    */
    public function order(Request $request)
    {       
        $lang = $request->lang;   
        $code = $request->code;   

        $order = DB::table('cart_orders')
            ->leftJoin('cart_gateways', 'cart_orders.gateway_id', '=', 'cart_gateways.id')            
            ->select('cart_orders.*', 'cart_gateways.title as gateway_title')
            ->where('user_id', Auth::user()->id)        
            ->where('code', $code)
            ->first();  
        if(! $order) return redirect(route('user.orders', ['lang' => $lang]));   

        $gateways = DB::table('cart_gateways')
            ->where('active', 1)      
            ->where('hidden', 0)                  
            ->orderBy('position', 'asc')  
            ->orderBy('id', 'desc')  
            ->get();                               

        return view('user/account', [
            'view_file' => 'cart.order', 
            'gateways' => $gateways,
            'order' => $order,
            'country' => $this->UserModel->get_user_extra (Auth::user()->id, 'country') ?? null,
            'company' => $this->UserModel->get_user_extra (Auth::user()->id, 'company') ?? null,
            'billing_address' => $this->UserModel->get_user_extra (Auth::user()->id, 'billing_address') ?? null,
        ]);               
        
    }


    /**
    * Remove unpaid order
    */
    public function destroy_order(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('user'))->with('error', 'demo');  

        $lang = $request->lang;   
        $code = $request->code;

        $order = DB::table('cart_orders')
            ->where('user_id', Auth::user()->id)        
            ->where('code', $code)
            ->where('is_paid', 0)
            ->first();          
        if(! $order) return redirect(route('user.orders', ['lang' => $lang]))->with('error', 'error_order');         
        
        DB::table('cart_orders')->where('code', $code)->where('user_id', Auth::user()->id)->delete();                         
        DB::table('cart_orders_items')->where('order_id', $order->id)->delete();                                 

        DB::table('tickets')->where('order_id', $order->id)->where('user_id', Auth::user()->id)->delete();                         

        return redirect(route('user.orders', ['lang' => $lang]))->with('success', 'deleted'); 
    } 



}
