<?php

/**
    * Nura24: #1 Open Source Suite for Businesses, Communities, Teams, Collaboration and Personal Websites.
    *
    * Copyright (C) 2021  Chimilevschi Iosif-Gabriel, https://nura24.com.
    *
    * LICENSE:
    * Nura24 is licensed under the GNU General Public License v3.0
    * Permissions of this strong copyleft license are conditioned on making available complete source code 
    * of licensed works and modifications, which include larger works using a licensed work, under the same license. 
    * Copyright and license notices must be preserved. Contributors provide an express grant of patent rights.
    *    
    * @copyright   Copyright (c) 2021, Chimilevschi Iosif-Gabriel, https://nura24.com.
    * @license     https://opensource.org/licenses/GPL-3.0  GPL-3.0 License.
    * @version     2.1.1
    * @author      Chimilevschi Iosif-Gabriel <office@nura24.com>
*/

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

        config(['nura.version' => '2.1.1']);

        $CoreModel = new Core(); 
                
        // config
        $config = Core::config();      
                                
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
            $locale = default_lang()->code ?? 'en';
            $setlocale = default_lang()->locale ?? 'en';    

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
        
        View::share('template_path', 'templates/frontend/'.($config->template ?? 'nura24_default'));

        View::share('template_view', 'frontend/'.($config->template ?? 'nura24_default'));
        
        View::share('request_path', $request->path() ?? null);

        View::share('site_short_title', $site_short_title ?? null);
        
        View::share('homepage_meta_title', $homepage_meta_title ?? null);
        
        View::share('homepage_meta_description', $homepage_meta_description ?? null);

        if($template_cookie ?? null) return $next($request)->cookie($template_cookie);
        else return $next($request);
    }
    
}