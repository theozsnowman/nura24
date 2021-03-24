<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use App\Models\Core;
use App\Models\User;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth; 

class EmailCampaignsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();            
        $this->config = Core::config();      
        
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

        if(! check_access('email_marketing')) return redirect(route('admin'));

        $campaigns = DB::table('email_camp')
            ->select('email_camp.*', DB::raw('(SELECT COUNT(*) FROM email_camp_recipients WHERE email_camp_recipients.campaign_id = email_camp.id) as count_recipients'))
            ->orderBy('id', 'desc')
            ->paginate(20);       
                
        return view('/admin/account', [
            'view_file' => 'email-marketing.campaigns',
            'active_submenu' => 'email.campaigns',
            'campaigns' => $campaigns,
        ]); 
    }   

    
    /**
    * Create campaign
    */
    public function create(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));           
                
        return view('/admin/account', [
            'view_file' => 'email-marketing.create-campaign',
            'active_submenu' => 'email.campaigns',
        ]); 
    }   


    /**
    * Create new slide
    */
    public function store(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.email.campaigns'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); 

        if(DB::table('email_camp')->where('title', $inputs['title'])->exists()) return redirect(route('admin.email.campaigns'))->with('error', 'duplicate');  
        
        DB::table('email_camp')->insert([
            'title' => $inputs['title'],
            'description' => $inputs['description'],
            'subject' => $inputs['subject'],
            'content' => $inputs['content'],
            'created_at' => now(),            
        ]);
               
        return redirect($request->Url())->with('success', 'created'); 
    }


    /**
    * Update campaign
    */
    public function show(Request $request)
    {        
        if(! check_access('email_marketing')) return redirect(route('admin'));           
     
        $id = $request->id;  
        $campaign = DB::table('email_camp')->where('id', $id)->first();      
        if(! $campaign) return redirect(route('admin.email.campaigns')); 

        return view('/admin/account', [
            'view_file' => 'email-marketing.update-campaign',
            'active_submenu' => 'email.campaigns',
            'campaign' => $campaign,
        ]); 
    }   


    /**
    * Update the specified resource     
    */
    public function update(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));

        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.email.campaigns'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();
           
        if(DB::table('email_camp')->where('title', $inputs['title'])->where('id', '!=', $id)->exists()) return redirect(route('admin.email.campaigns'))->with('error', 'duplicate');  

        DB::table('email_camp')
            ->where('id', $id)
            ->update([
                'title' => $inputs['title'],
                'description' => $inputs['description'],
                'subject' => $inputs['subject'],
                'content' => $inputs['content'],
        ]);
                 
        return redirect(route('admin.email.campaigns'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));

        $id = $request->id;  

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $campaign = DB::table('email_camp')->where('id', $id)->first();                

        DB::table('email_camp')->where('id', $id)->delete();
        DB::table('email_camp_recipients')->where('campaign_id', $id)->delete();

        return redirect(route('admin.email.campaigns'))->with('success', 'deleted'); 
    }


    /**
    * Display campaign recipients
    */
    public function recipients(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));

        $id = $request->id;  // campaign ID
        $search_terms = $request->search_terms;

        $campaign = DB::table('email_camp')
            ->where('id', $id)
            ->first();       
        if(! $campaign) return redirect(route('admin.email.campaigns')); 

        $recipients = DB::table('email_camp_recipients')
            ->where('campaign_id', $id);

        if($search_terms) $recipients = $recipients->where('email', 'like', "%$search_terms%")->orWhere('name', 'like', "%$search_terms%");     

        $recipients = $recipients->orderBy('email', 'asc')->paginate(50);    

        $lists = DB::table('email_lists')
            ->select('email_lists.*', DB::raw('(SELECT COUNT(*) FROM email_lists_recipients WHERE email_lists_recipients.list_id = email_lists.id) as count_recipients'))
            ->orderBy('title', 'asc')
            ->get();       

        return view('/admin/account', [
            'view_file' => 'email-marketing.campaign-recipients',
            'active_submenu' => 'email.campaigns',
            'campaign' => $campaign,
            'recipients' => $recipients,
            'lists' => $lists,
            'search_terms'=> $search_terms,
        ]); 
    }   


    /**
    * Create new recipient
    */
    public function store_recipient(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;  // campaign ID
        $campaign = DB::table('email_camp')
            ->where('id', $id)
            ->first();       
        if(! $campaign) return redirect(route('admin.email.campaigns')); 

        $inputs = $request->all(); 


        // add recipients from list
        if($inputs['add_type'] == 'list') {

            $list = DB::table('email_lists')
                ->where('id', $inputs['list_id'])
                ->first();       
            if(! $list) return redirect(route('admin.email.campaigns')); 

            $list_recipients = DB::table('email_lists_recipients')->where('list_id', $list->id)->get();

            foreach($list_recipients as $recipient) {
                if($recipient->email && filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) {

                    // check if not exists in black list
                    if(! DB::table('email_camp_deny')->where('email', $recipient->email)->exists()) {

                        if(! DB::table('email_camp_recipients')->where('campaign_id', $id)->where('email', $recipient->email)->exists()) 
                        DB::table('email_camp_recipients')->insert([
                            'campaign_id' => $id,
                            'email' => $recipient->email,
                            'name' => $recipient->name ?? null,
                            'created_at' => now(),            
                        ]);
                    } // end if

                }  // end if
            } // end foreach
        } // end if list


        // manually add recipients
        if($inputs['add_type'] == 'input') {
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $inputs['recipients']) as $line){
                if($line) {
                    $recipient = explode(', ', $line);                
                    $recipient_email = trim($recipient[0]);
                    if(isset($recipient[1])) $recipient_name = trim($recipient[1]); else $recipient_name = null;

                    if($recipient_email && filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
                        if(! DB::table('email_camp_recipients')->where('campaign_id', $id)->where('email', $recipient_email)->exists()) 
                        DB::table('email_camp_recipients')->insert([
                            'campaign_id' => $id,
                            'email' => $recipient_email,
                            'name' => $recipient_name ?? null,
                            'created_at' => now(),            
                        ]);

                    } // end if

                } // end if line           
            } 
        }

        // import CSV file
        if($inputs['add_type'] == 'csv') {

            // process file
            if ($request->hasFile('file')) {       
                $validator = Validator::make($request->all(), ['file' => 'mimes:csv,txt']);
                if (! $validator->fails()) {
                
                    $import_file = $this->UploadModel->upload_file($request, 'file');    
                    dd($import_file);
                    if (($handle = fopen(asset('uploads/'.$import_file), "r")) !== FALSE) {
                        while (($line = fgetcsv($handle, 1000, ",")) !== FALSE) {                            
                            if($line) {
                                $recipient_email = trim($line[0]);
                                if(isset($line[1])) $recipient_name = trim($line[1]); else $recipient_name = null;
            
                                if($recipient_email && filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
                                    if(! DB::table('email_camp_recipients')->where('campaign_id', $id)->where('email', $recipient_email)->exists()) 
                                    DB::table('email_camp_recipients')->insert([
                                        'campaign_id' => $id,
                                        'email' => $recipient_email,
                                        'name' => $recipient_name ?? null,
                                        'created_at' => now(),            
                                    ]);        
                                } // end if            
                            } // end if line    
                        } // end while
                        fclose($handle);
                    }  
                }                
            } 

        }
        

        return redirect(route('admin.email.campaigns.recipients', ['id' => $id]))->with('success', 'created'); 
    }


    /**
    * Remove recipient from campaign
    */
    public function destroy_recipient(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));
       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;  // campaign ID
        $recipient_id = $request->recipient_id; 

        $campaign = DB::table('email_camp')
            ->where('id', $id)
            ->first();       
        if(! $campaign) return redirect(route('admin.email.campaigns')); 

        DB::table('email_camp_recipients')->where('id', $recipient_id)->delete();

        return redirect(route('admin.email.campaigns.recipients', ['id' => $id]))->with('success', 'deleted'); 
    }


     /**
    * Send emails
    */
    public function send(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));
        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(!isset($this->config->mailgun_domain) || !isset($this->config->mailgun_api_key) || !isset($this->config->mailgun_endpoint)) return redirect(route('admin.email.campaigns'))->with('error', 'config'); ; 

        $id = $request->id;  // campaign ID

        $campaign = DB::table('email_camp')
            ->where('id', $id)
            ->first();       
        if(! $campaign) return redirect(route('admin.email.campaigns')); 

        $recipients = DB::table('email_camp_recipients')->where('campaign_id', $id)->whereNull('sent_at')->orderBy('sent_at', 'asc')->limit(1000)->get();                   

        $recip_array = array();
        $recip_variables = array();

        foreach ($recipients as $recipient) {

            // check if not exists in black list
            if(! DB::table('email_camp_deny')->where('email', $recipient->email)->exists()) {
                array_push($recip_array, '<'.$recipient->email.'>');
                $recip_variables[$recipient->email] = array('id' => $recipient->id, 'name' => $recipient->name ?? null);

                DB::table('email_camp_recipients')->where('campaign_id', $id)->where('id', $recipient->id)->update(['sent_at' => now()]);
            }
        }
  
        if(count($recip_array)==0) return redirect(route('admin.email.campaigns')); 

        $MailData            = array();
        $MailData['from']    = $this->config->site_email_name." <".$this->config->site_email.">";          
        $MailData['to'] = implode(',',$recip_array);
        $MailData['recipient-variables'] = json_encode($recip_variables);
        
        $MailData['subject'] = $campaign->subject;
        //$MailData['text']    = 'Hello %recipient.name%. How are you man';    
        $MailData['html']    = $campaign->content;    
    
        //dd($MailData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:'.$this->config->mailgun_api_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, 'https://'.$this->config->mailgun_endpoint.'/v3/'.$this->config->mailgun_domain.'/messages');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $MailData);
        $result = curl_exec($ch);
        curl_close($ch);
                 
        DB::table('email_camp')
            ->where('id', $id)
            ->update([
                'sent_at' => now(),                
        ]);        

        return redirect(route('admin.email.campaigns'))->with('success', 'sent'); 
    }


    /**
    * Black list recipients
    */
    public function black_list(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));
      
        $search_terms = $request->search_terms;

        $recipients = DB::table('email_camp_deny');

        if($search_terms) $recipients = $recipients->where('email', 'like', "%$search_terms%");     

        $recipients = $recipients->orderBy('email', 'asc')->paginate(50);           

        return view('/admin/account', [
            'view_file' => 'email-marketing.black-list',
            'active_submenu' => 'email.campaigns',
            'recipients' => $recipients,
            'search_terms'=> $search_terms,
        ]); 
    }   


     /**
    * Add recipient in black list
    */
    public function store_black_list(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
       
        $inputs = $request->all(); 
        
        if($inputs['email'] && filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
            if(! DB::table('email_camp_deny')->where('email', $inputs['email'])->exists()) 
                DB::table('email_camp_deny')->insert([
                    'email' => $inputs['email'],
                    'reason' => $inputs['reason'],
                    'created_at' => now(),            
            ]);
        } // end if        

        return redirect(route('admin.email.black-list'))->with('success', 'created'); 
    }


    /**
    * Remove recipient from black list
    */
    public function destroy_black_list(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));
       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id; 
        
        DB::table('email_camp_deny')->where('id', $id)->delete();

        return redirect(route('admin.email.black-list'))->with('success', 'deleted'); 
    }


    /**
    * Email marketing config
    */
    public function config()
    {
        return view('admin/account', [
            'view_file' => 'email-marketing.config',
            'active_submenu' => 'email.campaigns',
        ]); 
    }   


    public function update_config(Request $request)
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



}
