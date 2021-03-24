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

class DomainsController extends Controller
{
    
    public function __construct()
    {
        if(! logged_user()) return redirect('/'); 
        if(logged_user()->role != 'user') return redirect('/');              
    } 


    /**
    * Display all user domains / websites
    */
    public function index()
    {       
        $domains = DB::table('custom_domains')
            ->where('user_id', Auth::user()->id)        
            ->orderBy('id', 'desc')
            ->paginate(20);                    
   
        return view('user/account', [
            'view_file'=>'custom.domains',
            'domains' => $domains,    
        ]);        
        
    }


    /**
    * Create new domain
    */
    public function create()
    {
        return view('user/account', [
            'view_file' => 'custom.new-domain', 
        ]); 
    }

    /**
    * Store 
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) exit('Demo mode'); 
        
        $domain = $request->domain;
        if(! $domain) return redirect(route('user.custom.domains'));        

        $domain = trim($domain);      
        $domain = str_replace('https://', '', $domain);
        $domain = str_replace('http://', '', $domain);
        $domain = str_replace('www', '', $domain);

        // check if only letters and numbers
        //if (preg_match('/[^A-Za-z0-9]/', $domain)) return redirect(route('user.custom.domains'))->with('error', 'invalid_domain');      
          
        if(! filter_var($domain, FILTER_VALIDATE_DOMAIN)) return redirect(route('user.custom.domains'))->with('error', 'invalid_domain');      

        // check if already registered
        if(DB::table('custom_domains')->where('user_id', Auth::user()->id)->where('domain', $domain)->exists())
            return redirect(route('user.custom.domains'))->with('error', 'duplicate');      
                                    
        // Create domain    
        DB::table('custom_domains')->insert([                
            'user_id' => Auth::user()->id,
            'domain' => $domain,
            'created_at' => now(),
        ]); 
           
        return redirect(route('user.custom.domains'))->with('success', 'created');       
    }   
   

    /**
    * Remove website
    */
    public function destroy(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('user.custom.domains'));

        $dom = $request->dom;
        if(! $dom) return redirect(route('user.custom.domains'));      

        $domain = DB::table('custom_domains')->where('user_id', Auth::user()->id)->where('domain', $dom)->first();
        if(! $domain) return redirect(route('user.custom.domains'));      
                
        // check if domain have licenses
        if(DB::table('custom_licenses')->where('user_id', Auth::user()->id)->where('domain_id', $domain->id)->exists())
            return redirect(route('user.custom.domains'))->with('error', 'exists_licenses');      
                                    
        
        DB::table('custom_domains')->where('user_id', Auth::user()->id)->where('domain', $dom)->delete();              

        return redirect(route('user.custom.domains'))->with('success', 'deleted'); 
    }

}
