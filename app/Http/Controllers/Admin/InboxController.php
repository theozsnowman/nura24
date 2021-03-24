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
use Illuminate\Support\Str;
use DB;
use Auth; 
use Image;

class InboxController extends Controller
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
    * Display all messages
    */
    public function index(Request $request)
    {
        if(! check_access('inbox')) return redirect(route('admin'));

        $search_terms = $request->search_terms;
        $search_status = $request->search_status;
        $search_replied = $request->search_replied;
        $search_important = $request->search_important;

        $messages = DB::table('inbox');

        if($search_status=='unread') 
            $messages = $messages->where('inbox.is_read', 0);     
        if($search_status=='read') 
            $messages = $messages->where('inbox.is_read', 1);                 

        if($search_replied=='yes') 
            $messages = $messages->where('inbox.is_responded', 1);    
        if($search_replied=='no') 
            $messages = $messages->where('inbox.is_responded', 0);    

        if($search_important=='1') 
            $messages = $messages->where('inbox.is_important', 1);    

        if($search_terms) $messages = $messages->where(function ($query) use ($search_terms) {
            $query->where('name', 'like', "%$search_terms%")
                ->orWhere('email', 'like', "%$search_terms%");
            }); 

        $messages = $messages->orderBy('id', 'desc')->paginate(25);       
                
        $count_inbox_unread = DB::table('inbox')
            ->where('is_read', 0)
            ->count();      
            
        return view('admin/account', [
            'view_file'=>'inbox.messages',
            'active_submenu'=>'inbox',
            'search_terms'=> $search_terms,
            'search_status'=> $search_status,
            'search_replied'=> $search_replied,
            'search_important'=> $search_important,
            'messages' => $messages,
            'count_inbox_unread' => $count_inbox_unread,
        ]); 
    }

    
    /**
    * Show message     
    */
    public function show(Request $request)
    {
        if(! check_access('inbox')) return redirect(route('admin'));

        $id = $request->id;

        $message = DB::table('inbox')
            ->where('id', $id)
            ->first();
            
        if(!$message) abort(404);
            
        DB::table('inbox')->where('id', $id)->update(['is_read' => 1]); 

        $replies = DB::table('inbox_reply')
            ->leftJoin('users', 'inbox_reply.sender_user_id', '=', 'users.id') 
            ->select('inbox_reply.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar')
            ->where('msg_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(25);              
        
        return view('admin/account', [
            'view_file'=>'inbox.message',
            'active_submenu'=>'inbox',
            'message' => $message,
            'replies' => $replies,
        ]);
    }


    /**
    * Delete message
    */
    public function destroy(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        if(! check_access('inbox')) return redirect(route('admin'));

        $id = $request->id;
        
        DB::table('inbox')->where('id', $id)->delete(); 
        DB::table('inbox_reply')->where('msg_id', $id)->delete();

        return redirect(route('admin.inbox'))->with('success', 'deleted'); 
    }


    /**
    * Mark important
    */
    public function important(Request $request)
    {
        if(! check_access('inbox')) return redirect(route('admin'));

        $id = $request->id;
        $action = $request->action;

        if($action=='set') $is_important = 1;
        if($action=='unset') $is_important = 0;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('inbox')->where('id', $id)->update(['is_important' => $is_important]); 

        return redirect(route('admin.inbox'))->with('success', 'updated'); 
    }


    /**
    * Mark spam
    */
    public function spam(Request $request)
    {
        if(! check_access('inbox')) return redirect(route('admin'));

        $id = $request->id;
        $action = $request->action;

        if($action=='set') $is_spam = 1;
        if($action=='unset') $is_spam = 0;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('inbox')->where('id', $id)->update(['is_spam' => $is_spam]); 

        return redirect(route('admin.inbox'))->with('success', 'updated'); 
    }


    /**
    *  Reply to message
    */
    public function reply(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
                
        if(! check_access('inbox')) return redirect(route('admin'));
                
        $id = $request->id;

        $message = DB::table('inbox')
            ->where('id', $id)
            ->first();    
            
        if(!$message) abort(404);

        $validator = Validator::make($request->all(), [
            'reply' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.inbox.show', ['id'=>$id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $reply = $request->reply;

        DB::table('inbox_reply')->insert([
            'msg_id' => $id,
            'sender_user_id' => Auth::user()->id,          
            'message' => $reply,
            'created_at' => now()
        ]);
         

        DB::table('inbox')->where('id', $id)->update(['is_responded' => 1]);

        // send email
        if($this->config->mail_sending_option=='smtp') {

            $emailModel = new Email();

            $mail_args = array('to_email'=>$message->email, 'subject'=>'Contacm form reply - '.config('app.name'), 'body'=>"<p>$reply</p><hr>Your message:<br>$message->message");
            //$attachments = array("xxx", "yyy");
            $attachments = null;
            $emailModel -> send_email($mail_args, $attachments);           
        }
        else {
            // PHP MAILER	
            //----------------------------------------------------------------------------------------------------------
            $subject = 'Contact form reply - '.config('app.name');
            $html = '           
            <div style="font-size:12px;font-family:arial;">
            <p>'.$reply.'</p><hr>Your message:<br>'.$message->message.'
            </div>
            ';

            // HTML mail
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            $headers .= 'From: '.$this->config->site_email."\r\n" .
                'Reply-To: '.$this->config->site_email."\r\n" .
                'X-Mailer: PHP/' . phpversion();
            mail($message->email, $subject, $html, $headers);
        }

        return redirect(route('admin.inbox'))->with('success', 'replied'); 
    }   

}
