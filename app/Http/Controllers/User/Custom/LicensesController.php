<?php
namespace App\Http\Controllers\User\Custom;

use App\Models\Core;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Auth; 

use App\Models\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class LicensesController extends Controller
{
    
    public function __construct()
    {        
        $this->config = Core::config();   

        if(! logged_user()) return redirect('/'); 
        if(logged_user()->role != 'user') return redirect('/');                            
    } 


    /**
    * Display domain licenses
    */
    public function index(Request $request)
    {       
        $dom = $request->dom;
        if(! $dom) return redirect(route('user.custom.domains'));      

        $domain = DB::table('custom_domains')->where('user_id', Auth::user()->id)->where('domain', $dom)->first();
        if(! $domain) return redirect(route('user.custom.domains'));      


        // delete licenses without orders (if order was deleted)
        $licenses = DB::table('custom_licenses')
            ->leftJoin('cart_orders', 'custom_licenses.order_id', '=', 'cart_orders.id')     
            ->select('custom_licenses.*', 'cart_orders.id as exists_order_id') 
            ->where('custom_licenses.user_id', Auth::user()->id)        
            ->where('custom_licenses.domain_id', $domain->id)        
            ->get();             
        foreach($licenses as $license)   {
            $license_id = $license->id;
            if (! $license->exists_order_id) DB::table('custom_licenses')->where('user_id', Auth::user()->id)->where('id', $license_id)->delete();
        }


        $licenses = DB::table('custom_licenses')
            ->leftJoin('custom_domains', 'custom_licenses.domain_id', '=', 'custom_domains.id')     
            ->leftJoin('cart_orders', 'custom_licenses.order_id', '=', 'cart_orders.id')     
            ->select('custom_licenses.*', 'custom_domains.domain as domain', 'cart_orders.is_paid as is_paid', 'cart_orders.id as exists_order_id') 
            ->where('custom_licenses.user_id', Auth::user()->id)        
            ->where('custom_licenses.domain_id', $domain->id)        
            ->orderBy('custom_licenses.expire_at', 'desc')
            ->orderBy('custom_licenses.id', 'desc')
            ->paginate(20);     
            

        return view('user/account', [
            'view_file' => 'custom.licenses', 
            'licenses' => $licenses,
            'dom' => $dom,
        ]);        
        
    }


    /**
    * Create new license 
    */
    public function create(Request $request)
    {
        $dom = $request->dom;
        if(! $dom) return redirect(route('user.custom.domains'));      

        $domain = DB::table('custom_domains')->where('user_id', Auth::user()->id)->where('domain', $dom)->first();
        if(! $domain) return redirect(route('user.custom.domains'));      

        $license_plans = DB::table('custom_licenses_plans')            
            ->where('active', 1)
            ->orderBy('price', 'asc')
            ->get();   

        return view('user/account', [
            'view_file' => 'custom.new-license', 
            'license_plans' => $license_plans,
            'dom' => $dom,
        ]); 
    }


    /**
    * Store 
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) exit('Demo mode'); 
        
        $dom = $request->dom;
        if(! $dom) return redirect(route('user.custom.domains'));      

        $domain = DB::table('custom_domains')->where('user_id', Auth::user()->id)->where('domain', $dom)->first();
        if(! $domain) return redirect(route('user.custom.domains'));      

        $plan_id = $request->plan_id;                    
        if(! $plan_id) return redirect(route('user.custom.licenses.new', ['dom' => $dom]))->with('error', 'select_option'); 
        
        $plan = DB::table('custom_licenses_plans')        
            ->where('id', $plan_id)
            ->where('active', 1)
            ->first();          
        if(! $plan) return redirect(route('user.custom.licenses.new', ['dom' => $dom]))->with('error', 'select_option');     

       
        if($plan->months > 0) $item_name = 'License key for '.$dom.' - '.$plan->months.' months';
        if(! $plan->months && $plan->no_expire == 1) $item_name = 'License key for '.$dom.' - lifetime';

        // EVERYTHING OK:             
        if($plan->no_expire == 1) $expire = null;
        else {
            $expire = date('Y-m-d', strtotime('+'.$plan->months.' months'));
        }

        $sData = $dom.'#pro#'.$expire;
        $secretKey = 'FR_GRc34Q]Vd.+UAfg8';
        $cipher = "BF-OFB";
        $iv = 'n24_!f97';
             
        $license_key = openssl_encrypt($sData, $cipher, $secretKey, $options=0, $iv);                    
       
        // Create order    
        $due_date = date('Y-m-d', strtotime('+ 3 days'));

        DB::table('cart_orders')->insert([     
            'code' => Str::random(12),           
            'user_id' => Auth::user()->id,
            'is_paid' => 0,
            'total' => $plan->price,
            'currency_id' => 1,
            'created_by_user_id' => Auth::user()->id,
            'created_at' => now(),
            'due_date' => $due_date,
        ]); 
        
        $order_id = DB::getPdo()->lastInsertId();          
        DB::table('cart_orders_items')->insert([
            'order_id' => $order_id,
            'is_paid' => 0,
            'is_delivered' => 0,            
            'item_name' => $item_name,            
            'price' => $plan->price,            
            'quantity' => 1
        ]);
    

        // add license info    
        DB::table('custom_licenses')->insert([
            'user_id' => Auth::user()->id,
            'order_id' => $order_id,
            'domain_id' => $domain->id,            
            'license_key' => $license_key,            
            'secret' => $secretKey,            
            'cipher' => $cipher,                     
            'iv' => $iv,        
            'created_at' => now(),            
            'expire_at' => $expire  
        ]);
    
    
            
        // sent notification email
        $subject = 'New Nura24 license for '.$dom.'</p>';
        $message_html = '
                <html>
                <head>
                    <title>Your Nura24 license has beeen created!</title>
                </head>
                <body>                    
                    <div style="font-size:14px;font-family:arial;">                    
                    <p>Your Nura24 license for <b>'.$dom.'</b> has beeen created!</b></p>
                    <p>A new order was created in your account. You must pay this order to use this license key on your domain.</p>
                    </div>
                </body>
                </html>
                ';
         
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
          
      
        return redirect(route('user.custom.licenses', ['dom' => $dom]))->with('success', 'created');       
    }   


}
