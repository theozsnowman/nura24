<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\User;
use App\Models\Core;
use App\Models\Upload;
use App\Models\Email;
use DB;
use Artisan;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ConfigController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();     
        $this->UploadModel = new Upload();     
        $this->config = Core::config(); 
        
        $this->middleware(function ($request, $next) {
            $this->logged_user_role_id = Auth::user()->role_id;
            $this->logged_user_id = Auth::user()->id;            
            $this->logged_user_role = $this->UserModel->get_role_from_id ($this->logged_user_role_id);    
            
            if(! ($this->logged_user_role == 'admin')) return redirect('/'); 
            return $next($request);
        });
    }

    
    /**
    * Update backend lang
    */
    public function update_backend_lang(Request $request)
    {
        $code = $request->code;
        $lang = DB::table('sys_lang')->where('code', $code)->where('active_backend', 1)->first();
        if(! $lang) return redirect(route('admin'));       
        
        $inputs = $request->all(); // retrieve all of the input data as an array 
              
        DB::table('users')
            ->where('id', Auth::user()->id)   
            ->update([               
                'backend_lang_code' => $lang->code,
        ]);       
                 
        return redirect(route('admin', ['lang' => $lang->code]));       
    } 




    /**
    * General config.
    * Show General config page
    */
    public function general()
    {                                              
        return view('admin/account', [
            'view_file'=>'core.config-general',
            'active_submenu'=>'config.general',
            'menu_section' => 'general',
        ]);
    }


    /**
    * Email config.
    */
    public function email()
    {                                              
        return view('admin/account', [
            'view_file'=>'core.config-email',
            'active_submenu'=>'config.general',
            'menu_section' => 'email',
        ]);
    }


    /**
    * Contact poge config.
    */
    public function contact()
    {                                              
        return view('admin/account', [
            'view_file'=>'core.config-contact-page',
            'active_submenu'=>'config.general',
            'menu_section' => 'contact-page',            
        ]);
    }
    

    /**
    * Registration
    */
    public function registration()
    {                              

        return view('admin/account', [
            'view_file'=>'core.config-registration',
            'active_submenu'=>'config.general',
            'menu_section' => 'registration',
        ]);
    }


    /**
    * Site offline
    */
    public function site_offline()
    {                              

        return view('admin/account', [
            'view_file'=>'core.config-site-offline',
            'active_submenu'=>'config.general',
            'menu_section' => 'site-offline',
        ]);
    }


    /**
    * Contact poge config.
    */
    public function antispam()
    {                                              
        return view('admin/account', [
            'view_file'=>'core.config-antispam',
            'active_submenu'=>'config.general',
            'menu_section' => 'antispam',            
        ]);
    }
    

    /**
    * Template config page
    */
    public function template()
    {                                    
        $templates = array_filter(glob("templates/frontend/"."*"), 'is_dir');

        $templates_xml = array();
        
        foreach($templates as $template_path) {
            if (file_exists($template_path.'/template.xml')) {
                $xml = simplexml_load_file($template_path.'/template.xml');
                             
                $templates_xml[] = array("path" => $template_path, "title" => $xml->title, "description" => $xml->description, "version" => $xml->version, "author" => $xml->author, "screenshot" => $xml->screenshot);
            }
        }

        return view('admin/account', [
            'view_file'=>'core.config-template',
            'active_submenu'=>'config.template',
            'menu_section' => 'template',
            'templates' => $templates,
            'templates_xml' => $templates_xml,
        ]);
    }
    

    /**
    * Logos config page
    */
    public function logo()
    {                                            

        return view('admin/account', [
            'view_file'=>'core.config-logo',
            'active_submenu'=>'config.template',
            'menu_section' => 'logo',
        ]);
    }


    
    /**
    * Template tools
    */
    public function template_tools()
    {                                            

        return view('admin/account', [
            'view_file'=>'core.config-template-tools',
            'active_submenu'=>'config.template',
            'menu_section' => 'tools',
        ]);
    }



    /**
    * Tols
    */
    public function tools()
    {                                              
        return view('admin/account', [
            'view_file'=>'core.server-info',
            'active_submenu'=>'config.tools',
            'menu_section' => 'tools.server',
        ]);
    }


    public function system()
    {                                              
        return view('admin/account', [
            'view_file'=>'core.tools-system',
            'active_submenu'=>'config.tools',
            'menu_section' => 'tools.system',
        ]);
    }

    public function backup()
    {                                              
        return view('admin/account', [
            'view_file' => 'core.tools-backup',
            'active_submenu' => 'config.tools',
            'menu_section' => 'tools.backup',
        ]);
    }

    public function process_backup(Request $request)
    {                            
        //export_database();

        $option = $request->option;
        
        if($option == 'db') Artisan::call('backup:run --only-db --only-to-disk=backups');
        if($option == 'full') Artisan::call('backup:run --only-to-disk=backups');

        return redirect(route('admin.tools.backup'))->with('success', 'updated');  
    }



    public function modules(Request $request)
    {
        $modules = DB::table('sys_modules')->where('have_frontend', 1)->orderBy('module', 'asc')->get();      

        return view('admin/account', [
            'view_file' => 'core.config-modules',
            'active_submenu' => 'config.modules',            
            'modules' => $modules,
        ]); 
    }


    public function sitemap()
    {                                              
        return view('admin/account', [
            'view_file' => 'core.tools-sitemap',
            'active_submenu' => 'config.tools',
            'menu_section' => 'sitemap',
        ]);
    }


    public function process_sitemap()
    {                            
        generate_sitemap();

        return redirect(route('admin.tools.sitemap'))->with('success', 'updated');  
    }


    /**
    * Update module
    */
    public function update_module(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');       

        $inputs = $request->all(); // retrieve all of the input data as an array 
              
        DB::table('sys_modules')
            -> where('id', $id)   
            -> update([               
                'active' => $inputs['active'],
        ]);       
                 
        return redirect(route('admin.config.modules'))->with('success', 'updated');     
    } 


    /**
    * Process General config form
    */
    public function update_general(Request $request)
    {       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');       
               
        $input = $request->all();

        foreach ($input as $key => $value) {
            if($key!='_token') {
                DB::table('sys_config')->updateOrInsert(
                    ['name' => $key],
                    ['value' => $value]
                );
            }            
        }      
                         
        return redirect($request->Url())->with('success', 'updated');
    }


    /**
    * Process Email config form
    */
    public function update_email(Request $request)
    {         
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'site_email' => 'required|email:rfc',
            'site_email_name' => 'required|max:100'
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.config.email'))
                ->withErrors($validator)
                ->withInput();
        }
               
        $input = $request->all(); // retrieve all of the input data as an array 

        foreach ($input as $key => $value) {
            if($key!='_token') {
                DB::table('sys_config')->updateOrInsert(
                    ['name' => $key],
                    ['value' => $value]
                );
            }            
        }
             
        return redirect($request->Url())->with('success', 'updated');
    }



    /**
    * Process contact page config form
    */
    public function update_contact(Request $request)
    {     
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $input = $request->all(); // retrieve all of the input data as an array 

        foreach ($input as $key => $value) {
            if($key!='_token') {
                DB::table('sys_config')->updateOrInsert(
                    ['name' => $key],
                    ['value' => $value]
                );
            }            
        }
             
        return redirect($request->Url())->with('success', 'updated');
    }

    
    /**
    * Process registration 
    */
    public function update_registration(Request $request)
    {       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $input = $request->all(); // retrieve all of the input data as an array        

        foreach ($input as $key => $value) {
            if($key!='_token') {
                DB::table('sys_config')->updateOrInsert(
                    ['name' => $key],
                    ['value' => $value]
                );
            }            
        }
             
        return redirect($request->Url())->with('success', 'updated');
    }


    /**
    * Process site offline form
    */
    public function update_site_offline(Request $request)
    {    
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $input = $request->all(); // retrieve all of the input data as an array 

        foreach ($input as $key => $value) {
            if($key!='_token') {
                DB::table('sys_config')->updateOrInsert(
                    ['name' => $key],
                    ['value' => $value]
                );
            }            
        }
             
        return redirect($request->Url())->with('success', 'updated');
    }
    
    
    /**
    * Process antispam form
    */
    public function update_antispam(Request $request)
    {         
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $input = $request->all(); // retrieve all of the input data as an array 

        foreach ($input as $key => $value) {
            if($key!='_token') {
                DB::table('sys_config')->updateOrInsert(
                    ['name' => $key],
                    ['value' => $value]
                );
            }            
        }
             
        return redirect($request->Url())->with('success', 'updated');
    }
    


    /**
    * Process Template 
    */
    public function activate_template(Request $request)
    {  
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');                        
   
        $template = $request->input('template');

        DB::table('sys_config')->updateOrInsert(
            ['name' => 'template'],
            ['value' => $template]
        ); 
                
        return redirect(route('admin.config.template'))->with('success', 'updated');
    }



    public function license()
    {                                              
        return view('admin/account', [
            'view_file' => 'core.config-license',
            'active_submenu' => 'config.license',
        ]);
    }

    /**
    * Process License
    */
    public function update_license(Request $request)
    {  
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');                        
   
        $license_key = $request->input('license_key');

            DB::table('sys_config')->updateOrInsert(
                ['name' => 'license_key'],
                ['value' => $license_key]
            ); 
          
        
        // process Backend image        
        if ($request->hasFile('logo_backend')) {            
            $validator = Validator::make($request->all(), [
                'logo_backend' => 'mimes:jpeg,jpg,png,gif',
            ]);            

            if ($validator->fails()) {
                return redirect($request->Url())
                    ->withErrors($validator)
                    ->withInput();
            }

            $logo_db = $this->UploadModel->upload_file($request, 'logo_backend');    
            DB::table('sys_config')->updateOrInsert(['name' => 'logo_backend'], ['value' => $logo_db]);
        }        
        

         // process auth logo
         if ($request->hasFile('logo_auth')) {            
            $validator = Validator::make($request->all(), [
                'logo_auth' => 'mimes:jpeg,jpg,png,gif',
            ]);            

            if ($validator->fails()) {
                return redirect($request->Url())
                    ->withErrors($validator)
                    ->withInput();
            }

            $logo_db = $this->UploadModel->upload_file($request, 'logo_auth');    
            DB::table('sys_config')->updateOrInsert(['name' => 'logo_auth'], ['value' => $logo_db]);
        }  


        // process meta author NAME
        $site_meta_author = $request->site_meta_author;        
            DB::table('sys_config')->updateOrInsert(
                ['name' => 'site_meta_author'],
                ['value' => $site_meta_author ?? null]
            );


        // process meta author URL
        $site_meta_author_url = $request->site_meta_author_url;        
            DB::table('sys_config')->updateOrInsert(
                ['name' => 'site_meta_author_url'],
                ['value' => $site_meta_author_url ?? null ]
            );


        return redirect($request->Url())->with('success', 'updated');
    }


    
    /**
    * Process Template logos
    */
    public function update_logo(Request $request)
    {  
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
                       
        // process Main logo image
        if ($request->hasFile('logo')) {

            $validator = Validator::make($request->all(), [
                'logo' => 'mimes:jpeg,jpg,png,gif',
            ]);            

            if ($validator->fails()) {
                return redirect($request->Url())
                    ->withErrors($validator)
                    ->withInput();
            }

            $logo_db = $this->UploadModel->upload_file($request, 'logo');    
            DB::table('sys_config')->updateOrInsert(['name' => 'logo'], ['value' => $logo_db]);
        }        
        

         // process alt logo image
         if ($request->hasFile('logo_alt')) {
            
            $validator = Validator::make($request->all(), [
                'logo_alt' => 'mimes:jpeg,jpg,png,gif',
            ]);            

            if ($validator->fails()) {
                return redirect($request->Url())
                    ->withErrors($validator)
                    ->withInput();
            }

            $logo_db = $this->UploadModel->upload_file($request, 'logo_alt');    
            DB::table('sys_config')->updateOrInsert(['name' => 'logo_alt'], ['value' => $logo_db]);
        }  


        // pricess favicon image
        if ($request->hasFile('favicon')) {            

            $validator = Validator::make($request->all(), [
                'favicon' => 'mimes:jpeg,jpg,png,gif,ico',
            ]);            

            if ($validator->fails()) {
                return redirect($request->Url())
                    ->withErrors($validator)
                    ->withInput();
            }

            $favicon_db = $this->UploadModel->upload_file($request, 'favicon');    
            DB::table('sys_config')->updateOrInsert(['name' => 'favicon'], ['value' => $favicon_db]);
        }        
                            
        return redirect($request->Url())->with('success', 'updated');
    }



    /**
    * Process temmplate tools page
    */
    public function update_template_tools(Request $request)
    {         
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        $input = $request->all(); // retrieve all of the input data as an array 

        foreach ($input as $key => $value) {
            if($key!='_token') {
                DB::table('sys_config')->updateOrInsert(
                    ['name' => $key],
                    ['value' => $value]
                );
            }            
        }
             
        return redirect($request->Url())->with('success', 'updated');
    }
    


    /**
    * Send test email
    */
    public function send_test_email(Request $request)
    {      
        $test_email = $request->test_email;

        if($this->config->mail_sending_option=='smtp') {

            $emailModel = new Email();

            $mail_args = array('to_email'=>$test_email, 'subject'=>'Test email Nura24', 'body'=>"<p>This is a test email from '.config('app.url').'. Congratulations! Your email settings works fine.</p>");
            $attachments = array("/home/nura24/public_html/uploads/202002/6ArfA41zjw-Website Logo (2).png", "/home/nura24/public_html/uploads/202002/ZROZGliKbE-egypt.jpg");
            $emailModel -> send_email($mail_args, $attachments);           
        }
        else {
            // PHP MAILER	
            //----------------------------------------------------------------------------------------------------------
            $to      = $test_email;
            $subject = 'Test email from '.config('app.url');
            $message = '
            <html>
            <head>
            <title>Test email</title>
            </head>
            <body>
            <div style="font-size:12px;font-family:arial;">
            <p>This is a test email from '.config('app.url').'. Congratulations! Your email settings works fine.</p>
            </div>
            </body>
            </html>
            ';

            // HTML mail
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            $headers .= 'From: '.$this->config->site_email."\r\n" .
                'Reply-To: '.$this->config->site_email."\r\n" .
                'X-Mailer: PHP/' . phpversion();
            mail($test_email, $subject, $message, $headers);
        }

    }
    

    /**
    * Process clear cache
    */
    public function clear_cache(Request $request)
    {       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $section = $request->section;

        if($section == 'views') Artisan::call('view:clear');
        if($section == 'routes') Artisan::call('route:clear');
        if($section == 'config') { Artisan::call('config:clear'); }
                         
        return redirect(route('admin.tools.system'))->with('success', 'updated');
    }

    /**
    * Process clear logs
    */
    public function clear_logs(Request $request)
    {       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $section = $request->section;

        if($section == 'downloads') {
            $start_date = date('Y-m-d', strtotime('-1 months'));
            DB::table('downloads_logs')->whereDate('created_at', '<', $start_date)->delete(); 
        }
                         
        return redirect(route('admin.tools.system'))->with('success', 'updated');
    }


}
