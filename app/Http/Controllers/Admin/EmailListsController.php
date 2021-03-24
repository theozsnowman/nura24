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

class EmailListsController extends Controller
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

        $lists = DB::table('email_lists')
            ->select('email_lists.*', DB::raw('(SELECT COUNT(*) FROM email_lists_recipients WHERE email_lists_recipients.list_id = email_lists.id) as count_recipients'))
            ->orderBy('id', 'desc')
            ->paginate(20);       
                
        return view('/admin/account', [
            'view_file' => 'email-marketing.lists',
            'active_submenu' => 'email.campaigns',
            'lists' => $lists,
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
            return redirect(route('admin.email.lists'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); 

        if(DB::table('email_lists')->where('title', $inputs['title'])->exists()) return redirect(route('admin.email.lists'))->with('error', 'duplicate');  

        DB::table('email_lists')->insert([
            'title' => $inputs['title'],
            'description' => $inputs['description'],
            'created_at' => now(),            
        ]);
               
        return redirect($request->Url())->with('success', 'created'); 
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
            return redirect(route('admin.email.lists'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();
           
        if(DB::table('email_lists')->where('title', $inputs['title'])->where('id', '!=', $id)->exists()) return redirect(route('admin.email.lists'))->with('error', 'duplicate');  

        DB::table('email_lists')
            ->where('id', $id)
            ->update([
                'title' => $inputs['title'],
                'description' => $inputs['description'],
        ]);
                 
        return redirect(route('admin.email.lists'))->with('success', 'updated'); 
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

        DB::table('email_lists')->where('id', $id)->delete();
        DB::table('email_lists_recipients')->where('list_id', $id)->delete();

        return redirect(route('admin.email.lists'))->with('success', 'deleted'); 
    }


    /**
    * Display list recipients
    */
    public function recipients(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));

        $id = $request->id;  // list ID
        $search_terms = $request->search_terms;

        $list = DB::table('email_lists')
            ->where('id', $id)
            ->first();       
        if(! $list) return redirect(route('admin.email.lists')); 

        $recipients = DB::table('email_lists_recipients')
            ->where('list_id', $id);

        if($search_terms) $recipients = $recipients->where('email', 'like', "%$search_terms%")->orWhere('name', 'like', "%$search_terms%");     

        $recipients = $recipients->orderBy('email', 'asc')->paginate(50);    

        return view('/admin/account', [
            'view_file' => 'email-marketing.list-recipients',
            'active_submenu' => 'email.campaigns',
            'list' => $list,
            'recipients' => $recipients,
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

        $id = $request->id;  // list ID
        $list = DB::table('email_lists')
            ->where('id', $id)
            ->first();       
        if(! $list) return redirect(route('admin.email.lists')); 

        $inputs = $request->all(); 

        // manually add recipients
        if($inputs['add_type'] == 'input') {
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $inputs['recipients']) as $line){
                if($line) {
                    $recipient = explode(', ', $line);                
                    $recipient_email = trim($recipient[0]);
                    if(isset($recipient[1])) $recipient_name = trim($recipient[1]); else $recipient_name = null;

                    if($recipient_email && filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
                        if(! DB::table('email_lists_recipients')->where('list_id', $id)->where('email', $recipient_email)->exists()) 
                        DB::table('email_lists_recipients')->insert([
                            'list_id' => $id,
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
                if ($validator->fails()) exit('Invalid CSV or TXT file');
                if (! $validator->fails()) {
                                    
                    $import_file = $this->UploadModel->upload_file($request, 'file');    
                    
                    if (($handle = fopen(asset('uploads/'.$import_file), "r")) !== FALSE) {
                        while (($line = fgetcsv($handle, 1000, ",")) !== FALSE) {                            
                            if($line) {
                                $recipient_email = trim($line[0]);
                                if(isset($line[1])) $recipient_name = trim($line[1]); else $recipient_name = null;
            
                                if($recipient_email && filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
                                    if(! DB::table('email_lists_recipients')->where('list_id', $id)->where('email', $recipient_email)->exists()) 
                                    DB::table('email_lists_recipients')->insert([
                                        'list_id' => $id,
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
        

        return redirect(route('admin.email.lists.recipients', ['id' => $id]))->with('success', 'created'); 
    }


    /**
    * Remove recipient from list
    */
    public function destroy_recipient(Request $request)
    {

        if(! check_access('email_marketing')) return redirect(route('admin'));
       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;  // list ID
        $recipient_id = $request->recipient_id; 

        $list = DB::table('email_lists')
            ->where('id', $id)
            ->first();       
        if(! $list) return redirect(route('admin.email.lists')); 

        DB::table('email_lists_recipients')->where('id', $recipient_id)->delete();

        return redirect(route('admin.email.lists.recipients', ['id' => $id]))->with('success', 'deleted'); 
    }

}
