<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\User;

use App\Models\Core;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Upload;
use App\Models\User;
use DB;
use Auth;

class TicketsController extends Controller
{
    public function __construct()
    {        
        $this->middleware('auth');
        $this->UserModel = new User(); 
        $this->UploadModel = new Upload(); 
        $this->config = Core::config();  
        
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;                 
        
            $role = $this->UserModel->get_role_from_id ($this->role_id);    
            if($role != 'user') return redirect('/'); 
            return $next($request);
        }); 
    }

    
    /**
     * Show all resources
     */
    public function index(Request $request)
    {                           
        $tickets = DB::table('tickets')            
            ->leftJoin('cart_orders', 'tickets.order_id', '=', 'cart_orders.id')
            ->select('tickets.*', 'cart_orders.code as order_code', 'cart_orders.total as order_total', 'cart_orders.currency_id as order_currency_id', 'cart_orders.is_paid as order_is_paid', 'cart_orders.due_date as order_due_date', 
                    DB::raw('(SELECT count(*) FROM tickets_responses WHERE tickets_responses.ticket_id = tickets.id) as count_responses'),                     
                    DB::raw('(SELECT created_at FROM tickets_responses WHERE tickets_responses.ticket_id = tickets.id ORDER BY id DESC LIMIT 1) as latest_response_created_at'),
                    DB::raw('(SELECT name FROM users WHERE tickets.closed_by_user_id = users.id) as closed_by_user_name')
            )            
            ->where('tickets.user_id', Auth::user()->id)           
            ->orderBy('tickets.closed_at', 'asc')
            ->orderBy('tickets.id', 'desc')
            ->paginate(25);        

        return view('user/account', [
            'view_file' => 'tickets.tickets', 
            'tickets' => $tickets,
        ]);
    }
    

    /**
    * Create resource
    */
    public function create(Request $request)
    {               
        $departments = DB::table('tickets_departments')             
            ->where('active', 1)        
            ->where('hidden', 0)
            ->orderBy('title', 'asc')
            ->get();  

        return view('user/account', [
            'view_file' => 'tickets.create',  
            'departments' => $departments,         
        ]); 
    }
    
 
    
    /**
    * Store resource
    */
    public function store(Request $request)
    {        
         // disable action in demo mode:
         if(config('app.demo_mode')) return redirect(route('user'))->with('error', 'demo');      
       
        $validator = Validator::make($request->all(), [
            'subject' => 'required',   
            'message' => 'required',            
        ]);

        if ($validator->fails()) {
            return redirect(route('user.tickets.create'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();            

        DB::table('tickets')->insert([
            'code' => strtoupper(Str::random(9)), 
            'priority' => $inputs['priority'],
            'department_id' => $inputs['department_id'] ?? null,
            'subject' => $inputs['subject'],
            'message' => $inputs['message'],
            'created_at' => now(),
            'user_id' => Auth::user()->id,
        ]);      
               
        $ticket_id = DB::getPdo()->lastInsertId(); 
       
        // process file
        if ($request->hasFile('file')) {            
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('tickets')->where('id', $ticket_id)->update(['file' => $file_db]);            
        } 

        return redirect(route('user.tickets'))->with('success', 'created'); 
    }  


    /**
     * Show resource
     */
    public function show(Request $request)
    {   
        $lang = $request->lang;                 
        $code = $request->code;
        
        $responses_search_terms = $request->responses_search_terms;
        $responses_search_author = $request->responses_search_author;
        $responses_search_important = $request->responses_search_important;

        $ticket = DB::table('tickets')      
            ->leftJoin('cart_orders', 'tickets.order_id', '=', 'cart_orders.id')
            ->select('tickets.*', 'cart_orders.code as order_code', 'cart_orders.total as order_total', 'cart_orders.currency_id as order_currency_id', 'cart_orders.is_paid as order_is_paid', 'cart_orders.due_date as order_due_date', 
                DB::raw('(SELECT count(*) FROM tickets_responses WHERE tickets_responses.ticket_id = tickets.id) as count_responses'),                     
                DB::raw('(SELECT created_at FROM tickets_responses WHERE tickets_responses.ticket_id = tickets.id ORDER BY id DESC LIMIT 1) as latest_response_created_at'),
                DB::raw('(SELECT name FROM users WHERE tickets.closed_by_user_id = users.id) as closed_by_user_name')) 
            ->where('tickets.user_id', Auth::user()->id)        
            ->where('tickets.code', $code)
            ->first();  
                        
        if(! $ticket) return redirect(route('user.tickets', ['lang' => $lang]));
      
        $responses = DB::table('tickets_responses')
            ->leftJoin('users', 'tickets_responses.user_id', '=', 'users.id')
            ->select('tickets_responses.*', 'users.name as author_name', 'users.email as author_email')
            ->where('ticket_id', $ticket->id);

        if($responses_search_terms) $responses = $responses->where('tickets_responses.message', 'like', "%$responses_search_terms%");                
        if($responses_search_important=='important') $responses = $responses->where('important_for_client', 1);             
        if($responses_search_author=='client') $responses = $responses->where('user_id', $ticket->user_id);             
        if($responses_search_author=='operator') $responses = $responses->where('user_id', '!=', $ticket->user_id);             

        $responses = $responses->orderBy('id', 'desc')
            ->paginate(25); 

        return view('user/account', [
            'view_file' => 'tickets.ticket', 
            'ticket' => $ticket,            
            'responses' => $responses,    
            'responses_search_terms'=> $responses_search_terms,
            'responses_search_author'=> $responses_search_author,
            'responses_search_important'=> $responses_search_important,      
        ]);

    }
    

    /**
    * Update resource
    */
    public function update(Request $request)
    {
         // disable action in demo mode:
         if(config('app.demo_mode')) return redirect(route('user'))->with('error', 'demo');  

        $lang = $request->lang;   

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('user', ['lang' => $lang]))->with('error', 'demo'); 

        $code = $request->code;

        $ticket = DB::table('tickets')             
            ->where('user_id', Auth::user()->id)        
            ->where('code', $code)
            ->first();  
        if(! $ticket) return redirect(route('user.tickets', ['lang' => $lang])); 
        
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('user.tickets.show', ['lang' => $lang, 'code' => $code]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array            

        DB::table('tickets')
            ->where('code', $code)
            ->where('user_id', Auth::user()->id)     
            ->update([
                'subject' => $inputs['subject'],
                'message' => $inputs['message'],
                'priority' => $inputs['priority'],
        ]);    
                         
        return redirect(route('user.tickets.show', ['lang' => $lang, 'code' => $code]))->with('success', 'updated'); 
    }


    /**
    * Reply to ticket
    */
    public function reply(Request $request)
    {        
         // disable action in demo mode:
         if(config('app.demo_mode')) return redirect(route('user'))->with('error', 'demo');  

        $lang = $request->lang;   

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('user', ['lang' => $lang]))->with('error', 'demo'); 

        $code = $request->code;
        $ticket = DB::table('tickets')             
            ->where('user_id', Auth::user()->id)        
            ->where('code', $code)
            ->first();  
        if(! $ticket) return redirect(route('user.tickets', ['lang' => $lang])); 

        $validator = Validator::make($request->all(), [
            'message' => 'required',            
        ]);

        if ($validator->fails()) {
            return redirect(route('user.tickets.show', ['lang' => $lang, 'code' => $code]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();            

        DB::table('tickets_responses')->insert([
            'ticket_id' => $ticket->id,
            'message' => $inputs['message'],
            'created_at' => now(),
            'user_id' => Auth::user()->id,
        ]);      
               
        $response_id = DB::getPdo()->lastInsertId(); 

        DB::table('tickets')
            ->where('id', $ticket->id)
            ->update([
            'last_response' => 'client',            
        ]);   

        // process file
        if ($request->hasFile('file')) {            
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('tickets_responses')->where('id', $response_id)->update(['file' => $file_db]);            
        } 

        return redirect(route('user.tickets.show', ['lang' => $lang, 'code' => $code]))->with('success', 'reply_created'); 
    }   


    /**
    * Flag response as important
    */
    public function mark_important_response(Request $request)
    {        
        $lang = $request->lang;   

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('user', ['lang' => $lang]))->with('error', 'demo'); 

        $code = $request->code;
        $response_id = $request->response_id;
                       
        $ticket = DB::table('tickets')             
            ->where('user_id', Auth::user()->id)        
            ->where('code', $code)
            ->first();  
        if(! $ticket) return redirect(route('user.tickets', ['lang' => $lang])); 

        DB::table('tickets_responses')
            ->where('id', $response_id)
            ->update([
            'important_for_client' => 1,            
        ]);   

        return back()->withInput();
    }   


    /**
    * Unflag response as important
    */
    public function unmark_important_response(Request $request)
    {     
        $lang = $request->lang;   
           
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('user', ['lang' => $lang]))->with('error', 'demo'); 

        $code = $request->code;
        $response_id = $request->response_id;
                       
        $ticket = DB::table('tickets')             
            ->where('user_id', Auth::user()->id)        
            ->where('code', $code)
            ->first();  
        if(! $ticket) return redirect(route('user.tickets', ['lang' => $lang])); 

        DB::table('tickets_responses')
            ->where('id', $response_id)
            ->update([
            'important_for_client' => 0,            
        ]);   

        return back()->withInput();
    }       
   

    /**
    * Close ticket
    */
    public function close(Request $request)
    {    
         // disable action in demo mode:
         if(config('app.demo_mode')) return redirect(route('user'))->with('error', 'demo');  

        $lang = $request->lang;   

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('user', ['lang' => $lang]))->with('error', 'demo'); 

        $code = $request->code;
        
        $ticket = DB::table('tickets')             
            ->where('user_id', Auth::user()->id)        
            ->where('code', $code)
            ->first();  
        if(! $ticket) return redirect(route('user.tickets', ['lang' => $lang])); 
         
        DB::table('tickets')
            ->where('code', $code)
            ->update([
            'closed_at' => now(),
            'closed_by_user_id' => Auth::user()->id,
        ]);   

        return redirect(route('user.tickets', ['lang' => $lang]))->with('success', 'closed'); 
    } 


    /**
    * Reopen ticket
    */
    public function open(Request $request)
    {     
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('user'))->with('error', 'demo');  

        $lang = $request->lang;   
         
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('user', ['lang' => $lang]))->with('error', 'demo'); 

        $code = $request->code;

        $ticket = DB::table('tickets')             
            ->where('user_id', Auth::user()->id)        
            ->where('code', $code)
            ->first();  
        if(! $ticket) return redirect(route('user.tickets', ['lang' => $lang])); 
                       
        DB::table('tickets')
            ->where('id', $ticket->id)
            ->update([
            'closed_at' => null,
            'closed_by_user_id' => null,
        ]);   

        return redirect(route('user.tickets.show', ['lang' => $lang, 'code' => $code]))->with('success', 'reopened'); 
    } 

}
