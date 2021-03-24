<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

class Cart extends Model
{
    protected $fillable = ['parent_id', 'title', 'slug', 'active'];
    protected $table = 'cart_categ';    

    public function children() 
    {        
        return $this->hasMany('App\Models\Cart', 'parent_id')->orderBy('position', 'asc')->orderBy('title', 'asc');
    }


    public function childCategories()
    {
        return $this->hasMany('App\Models\Cart', 'parent_id')->with('children')->orderBy('position', 'asc')->orderBy('title', 'asc');
    }


    public function active_children() 
    {        
        return $this->hasMany('App\Models\Cart', 'parent_id')->where('active', 1)->orderBy('position', 'asc')->orderBy('title', 'asc');
    }


    public function active_childCategories()
    {
        return $this->hasMany('App\Models\Cart', 'parent_id')->where('active', 1)->orderBy('position', 'asc')->orderBy('title', 'asc')->with('active_children');
    }


    public static function get_uncategorized_categ_id()
    {
        $q = DB::table('cart_categ')
            ->where('slug', 'uncategorized')
            ->first();        

        return $q->id ?? null;
    }

    
    public static function recount_categ_items($categ_id)
    {
        // count categ products
        $counter = DB::table('cart_products')
            ->where('categ_id', $categ_id)
            ->count();        

        // count categ products
        $q = DB::table('cart_categ')
            ->where('id', $categ_id)
            ->first();   
        if($q) {
            $tree_ids = $q->tree_ids;
            $categ_tree_counter = 0;

            $array_tree = explode(',', $tree_ids);
            foreach($array_tree as $tree_categ_id) {
                $tree_counter = DB::table('cart_products')
                    ->where('categ_id', $tree_categ_id)
                    ->count();  
                $categ_tree_counter = $categ_tree_counter + $tree_counter;    
            }            
        }

        DB::table('cart_categ')
            ->where('id', $categ_id)
            ->update([
            'count_items' => $counter ?? 0,
            'count_tree_items' => $categ_tree_counter ?? 0,           
        ]);    

        return;
    }
    

    public static function regenerate_product_types($categ_id)
    {    
        $parent_id = DB::table('cart_categ')->where('id', $categ_id)->value('parent_id');   
        if($parent_id) {
            $tree_ids = DB::table('cart_categ')->where('id', $parent_id)->value('tree_ids');   
            $product_type = DB::table('cart_categ')->where('id', $parent_id)->value('product_type');  
            $array_tree = explode(',', $tree_ids);
            foreach($array_tree as $tree_categ_id) {
                DB::table('cart_categ')->where('id', $tree_categ_id)->update(['product_type' => $product_type]);    
            }            
        }

        return;
    }


    public function regenerate_tree_ids()
    {        
        $root_categories = DB::table('cart_categ')->get();     
        foreach($root_categories as $root) {

            $id = $root->id;            

            $tree = array($id);
            
            $q = DB::table('cart_categ')->where('parent_id', $id)->first();                                                
                
            if($q) {                            
                $tree = array_unique(array_merge($tree, array($q->id)));      

                $q2 = DB::table('cart_categ')->where('parent_id', $q->id)->orWhere('parent_id', $q->parent_id)->get();                  

                foreach($q2 as $item)  {
                    $tree = array_unique(array_merge($tree, array($item->id)));    
                    
                    $q3 = DB::table('cart_categ')->where('parent_id', $item->id)->orWhere('parent_id', $item->parent_id)->get();   
                    foreach($q3 as $item2)  {      
                        $tree = array_unique(array_merge($tree, array($item2->id)));      

                        $q4 = DB::table('cart_categ')->where('parent_id', $item2->id)->orWhere('parent_id', $item2->parent_id)->get();   
                        foreach($q4 as $item3)  {           
                            $tree = array_unique(array_merge($tree, array($item3->id)));      

                            $q5 = DB::table('cart_categ')->where('parent_id', $item3->id)->orWhere('parent_id', $item3->parent_id)->get();   
                            foreach($q5 as $item4)  {          
                                $tree = array_unique(array_merge($tree, array($item4->id)));      

                                $q6 = DB::table('cart_categ')->where('parent_id', $item4->id)->orWhere('parent_id', $item4->parent_id)->get();   
                                foreach($q6 as $item5)  {          
                                    $tree = array_unique(array_merge($tree, array($item5->id)));      
                                }
                            }
                        }
                    } 
                }   
            }

            $values = implode (",", $tree);

            DB::table('cart_categ')
                ->where('id', $id)
                ->update([
                    'tree_ids' => $values ?? null,                
            ]);                                                                     

        } // end foreach        


        $inactive_categs = DB::table('cart_categ')->where('active', 0)->get();     
        foreach($inactive_categs as $categ) {
            $inactive_tree = DB::table('cart_categ')->where('id', $categ->id)->first();     
            $inactive_tree_ids = $inactive_tree->tree_ids;
            
            $myArray = explode(',', $inactive_tree_ids);
            
            foreach($myArray as $categ_id) {
                DB::table('cart_categ')->where('id', $categ_id)->update(['active' => 0]);      
            }            
        }
                
        return;
    }
 


    public static function shopping_cart($user_id)
    {
        $cart = array();
        $products = array();        

        $cart_subtotal = 0; // total cart without discounts and taxes
        $total = 0;

        $shopping_cart = DB::table('cart_shopping_cart')                
            ->leftJoin('cart_products', 'cart_products.id', '=', 'cart_shopping_cart.product_id')
            ->select('cart_shopping_cart.*', 'cart_products.title as product_title', 'cart_products.content as product_content', 'cart_products.slug as product_slug', 'cart_products.image as product_image', 'cart_products.status as product_status', 'cart_products.price as product_price', 'cart_products.disable_orders as product_orders_disabled', 'cart_products.sku as product_sku', 'cart_products.categ_id as product_categ_id', 
            DB::raw('(SELECT product_type FROM cart_categ WHERE cart_products.categ_id = cart_categ.id) as product_type'))
            ->where('cart_shopping_cart.user_id', $user_id)    
            ->get();            

        if(count($shopping_cart)>0) {
            foreach($shopping_cart as $item) {
                
                $quantity = $item->quantity;            
                $product_id = $item->product_id;
                $product_type = $item->product_type;
                $product_title = $item->product_title;
                $product_content = $item->product_content;
                $product_slug = $item->product_slug;
                $product_sku = $item->product_sku;
                $product_categ_id = $item->product_categ_id;
                $product_price = $item->product_price;
                $product_status = $item->product_status;
                $product_orders_disabled = $item->product_orders_disabled;
                $quantity = $item->quantity;                     

                $products[] = array('item_id' => $item->id, 'id' => $product_id, 'type' => $product_type, 'title' => $product_title, 'content' => $product_content, 'slug' => $product_slug, 'sku' => $product_sku, 'status' => $product_status, 'orders_disabled' => $product_orders_disabled, 'price' => $product_price, 'quantity' => $quantity);
                    
                if($product_status=='active' and $product_orders_disabled!=1) $cart_subtotal = $cart_subtotal + ($quantity * $product_price);                
                else continue;

            }                  
        }
        
        $total = $cart_subtotal;

        $cart = array('products' => json_decode(json_encode($products)), 'subtotal' => $cart_subtotal, 'total' => $total);      

        return $cart;
    }
    



    public static function gateway_payment_accepted($order_code, $gateway, $args)
    {
    
        if(! $order_code) Log::error('No order code');

        $gateway = DB::table('cart_gateways')->where('slug', $gateway)->first();      
        if(! $gateway) Log::error('Invalid gateway: '.$gateway);

        $order = DB::table('cart_orders')->where('code', $order_code)->first();          
        if(! $order) Log::error('Invalid order code: '.$order_code);

        if(! $args['total'] || ! $args['currency']) Log::error('Payment response error. Amount total or amount currency are not defined');

        if($order->total == $args['total'] && corrency($order->currency_id)->code == $args['currency'])  {

            // process order                                                    
            DB::table('cart_orders')
            ->where('id', $order->id)
            ->update([
                'is_paid' => 1,
                'paid_at' => now(),  
                'gateway_id' => $gateway->id, 
                'gateway_code' => $args['id'] ?? null,
                'gateway_data' => serialize($args) ?? null,
            ]);     
                    
            // process order items status
            $order_items = DB::table('cart_orders_items')->where('order_id', $order->id)->get();
            foreach($order_items as $order_item) {
                $product = DB::table('cart_products')->where('id', $order_item->product_id)->first();
                if($product->type == 'download') $is_delivered = 1;
                else $is_delivered = 0;
                DB::table('cart_orders_items')->where('id', $order_item->id)->update(['is_delivered' => $is_delivered]);    
                
                // if type is task, create ticket:                
                $department_id = DB::table('sys_config')->where('name', 'cart_default_ticket_department_id')->value('value');
                if($product->type == 'task') {
                    DB::table('tickets')->insert([
                        'code' => strtoupper(Str::random(12)),
                        'department_id' => $department_id ?? null, 
                        'order_id' => $order->id, 
                        'product_id' => $product->id,                     
                        'is_paid' => 1,
                        'subject' => $product->title,
                        'message' => $product->content,
                        'created_at' =>  now(),
                        'user_id' => Auth::user()->id,
                    ]); 

                    $ticket_id = DB::getPdo()->lastInsertId();                  
                    DB::table('cart_orders_items')->where('id', $order_item->id)->update(['ticket_id' => $ticket_id]);      
                   
                } // end if

            } // end foreach

        }

        else {
            $message_error = "Invalid payment amount received. Total: ".$args['total'] ?? null." | Currency: ".$args['currency'] ?? null;
            Log::error($message_error);
        }
     
    }


}
