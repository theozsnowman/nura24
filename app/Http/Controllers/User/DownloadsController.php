<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\User;

use App\Models\Core;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth; 


class DownloadsController extends Controller
{    
    public function __construct()
    {        
        $this->middleware('auth');
        $this->UserModel = new User(); 
        $this->config = Core::config();  
        
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;                 
        
            $role = $this->UserModel->get_role_from_id ($this->role_id);    
            if($role != 'user') return redirect('/'); 
            return $next($request);
        }); 
    }

    /**
    * Display all items
    */
    public function index(Request $request)
    {        
        $downloads = array();
        $order_items = DB::table('cart_orders_items')
            ->leftJoin('cart_orders', 'cart_orders_items.order_id', '=', 'cart_orders.id')         
            ->where('cart_orders.is_paid', 1)  
            ->where('cart_orders.user_id', Auth::user()->id)                  
            ->orderBy('cart_orders.id', 'desc')
            ->get();  

        foreach($order_items as $order_item){            
            $order_item_product_id = $order_item->product_id;    
            
            $files = array();            
            $files_query = DB::table('cart_files')
                ->where('active', 1)
                ->where('product_id', $order_item_product_id)
                ->orderBy('version', 'desc')
                ->orderBy('id', 'desc')
                ->get();     
            
                foreach($files_query as $file){      
                    $file_id = $file->id;                    
                    $file_version = $file->version;
                    $file_title = $file->title;
                    $file_description = $file->description;
                    $file_file = $file->file;

                    $downloads[] = array("id" => $file_id, "version" => $file_version, "title" => $file_title, "description" => $file_description, "file" => $file_file);
                }
        }

            
        return view('user/account', [
            'view_file'=>'cart.downloads',
            'downloads' => json_decode(json_encode($downloads)), // array to object
        ]); 
    }


 
    /**
    * Download item  
    */
    public function download($id)
    {        
        $file_query = DB::table('cart_files')
            ->where('active', 1)
            ->where('id', $id)
            ->select('*')
            ->first();   

        if(! $file_query) return redirect(route('user.downloads'))->with('error', 'error_file');  
        
        $file = $file_query->file;
        $product_id = $file_query->product_id;
     
        // check if have access to this product        
        $check = DB::table('cart_orders_items')
            ->leftJoin('cart_orders', 'cart_orders_items.order_id', '=', 'cart_orders.id')     
            ->select('cart_orders_items.*', 'cart_orders.user_id as user_id')      
            ->where('user_id', Auth::user()->id)    
            ->where('product_id', $product_id)
            ->where('cart_orders_items.is_paid', 1)
            ->first();      
           
        if($check) {        

            $location = 'uploads/'.$file; 
            
            if (file_exists($location)) { 
	            header("Pragma: public");
	            header("Expires: 0");
	            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	            header("Cache-Control: public");
	            header("Content-Description: File Transfer");
	            header("Content-Type: application/octet-stream");
	            header("Content-Transfer-Encoding: binary");
	            header('Content-Length: '.filesize($location)); 
                header("Content-Disposition: attachment; filename=".basename($file));
                header("refresh: 1; url=".route('user.downloads'));	            

	            set_time_limit(0); // 0 - no limit
	            ini_set('display_errors',false);
                readfile($location);
                
                // process payment in database
                DB::table('cart_files_downloads')->insert([
                    'file_id' => $id,       
                    'product_id' => $product_id,       
                    'user_id'=> Auth::user()->id,
                    'downloaded_at' =>  now(),
                    'ip' => \Request::ip(),
                ]); 


	            return redirect(route('user.downloads')); 
	        }
            else return redirect(route('user.downloads'))->with('error', 'error_file'); 	            
        
        } // end check
        else return redirect(route('user.downloads'))->with('error', 'error_file'); 	  

    }

}
