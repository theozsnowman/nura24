<?php
namespace App\Http\Middleware;

use Closure;
use App;
use Cookie;
use DB;
use View;
use App\Models\Core;
use App\Models\User;
use Auth;
use Session;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class Nura
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {           

        config(['nura.version' => '1.2']);

        $CoreModel = new Core(); 
                
        // config
        $config = Core::config();      
                       
        
        // license key
        $sys_license_plan = null;
        $sys_license_expire = null;
        $sys_valid_license_key = false;

        $license_key = $config->license_key ?? null;

        if($license_key) {
            $license_cipher = "BF-OFB";
            $license_iv = 'n24_!f97';
            $license_secret = 'FR_GRc34Q]Vd.+UAfg8';
            $decripted_license_key = openssl_decrypt($license_key, $license_cipher, $license_secret, $options=0, $license_iv);    

            $license_exploded = explode('#', $decripted_license_key);
            $license_domain = $license_exploded[0] ?? null;
            $license_plan = $license_exploded[1] ?? null;
            $license_expire = $license_exploded[2] ?? null;                        

            if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == 'dev.'.$license_domain || $_SERVER['SERVER_NAME'] == $license_domain)
                {
                    $sys_license_plan = $license_plan;
                    $sys_license_expire = $license_expire;
                    if(! $license_expire or $license_expire >= date("Y-m-d")) $sys_valid_license_key = true;
                }            

            if($license_expire) {
                // check if valid date
                $d = \DateTime::createFromFormat('Y-m-d', $license_expire);    
                $valid_date =  $d && $d->format('Y-m-d') === $license_expire;
                if(! $valid_date) $sys_valid_license_key = false;
            }
        }

        // if not PRO license, disable multilanguages and internal accounts
        if(! $sys_valid_license_key) {
            DB::table('sys_lang')->where('is_default', 0)->update(['status' => 'inactive']);     

            if(($logged_user_role ?? null) && $logged_user_role == 'internal') {
                Auth::logout();
                Session::flush();
                return redirect('login')->with('error', 'invalid_license_key');
            }
        }


            
        if(config('app.demo_mode')) {                    
            $template = $request->template;            

            if($template) {
                $config->template = $template;
                $template_cookie = cookie('template', $template, 60);              
            }         

            if(Cookie::get('template') && !$template) {                
                $config->template = Cookie::get('template');
            }
        }
            

        $lang = $request->lang;
       
        if($lang) {                           
            $sys_lang_query = DB::table('sys_lang')->where('code', $lang)->where('active', 1)->first();   
            if(! $sys_lang_query) return redirect('/');
            
            $locale = $sys_lang_query->code ?? null;        
            $setlocale = $sys_lang_query->locale ?? null;       
            
            // SEO and homepage
            $site_short_title = $sys_lang_query->site_short_title ?? null;        
            $homepage_meta_title = $sys_lang_query->homepage_meta_title ?? null;        
            $homepage_meta_description = $sys_lang_query->homepage_meta_description ?? null;        
        } else {
            $locale = default_lang()->code;
            $setlocale = default_lang()->locale;    

            // SEO and homepage
            $site_short_title = default_lang()->site_short_title ?? null;        
            $homepage_meta_title = default_lang()->homepage_meta_title ?? null;        
            $homepage_meta_description = default_lang()->homepage_meta_description ?? null;     
        }               


        setlocale(LC_ALL, $setlocale);    

        App::setLocale($setlocale); 
                  
        View::share('locale', $locale ?? null);
        
        View::share('lang', $lang ?? null);
        
        View::share('config', $config);
        
        View::share('template_path', 'templates/frontend/'.$config->template);

        View::share('template_view', 'frontend/'.$config->template);
        
        View::share('request_path', $request->path() ?? null);

        View::share('site_short_title', $site_short_title ?? null);
        
        View::share('homepage_meta_title', $homepage_meta_title ?? null);
        
        View::share('homepage_meta_description', $homepage_meta_description ?? null);

        View::share('sys_license_plan', $sys_license_plan);
        
        View::share('sys_license_expire', $sys_license_expire);
        
        View::share('sys_valid_license_key', $sys_valid_license_key);

        if($template_cookie ?? null) return $next($request)->cookie($template_cookie);
        else return $next($request);
    }
}