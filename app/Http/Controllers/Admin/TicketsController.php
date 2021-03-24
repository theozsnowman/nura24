<?php
namespace App\Http\Controllers\Admin;

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
        
        $this->roles = DB::table('users_roles')->where('active', 1)->orderBy('id', 'asc')->get();      
        $this->role_id_internal = $this->UserModel->get_role_id_from_role('internal');

        $this->middleware(function ($request, $next) {
            $this->logged_user_role_id = Auth::user()->role_id;
            $this->logged_user_id = Auth::user()->id;            
            $this->logged_user_role = $this->UserModel->get_role_from_id ($this->logged_user_role_id);    

            if(! ($this->logged_user_role == 'admin' || $this->logged_user_role == 'internal')) return redirect('/'); 
            return $next($request);
        }); 
    }

    
    /**
     * Show all resources
     */
    public function index(Request $request)
    {                  
        if(! check_access('tickets')) return redirect(route('admin'));

        $role_id = $this->UserModel->get_role_id_from_role('internal');

        $search_terms = $request->search_terms;
        $search_status = $request->search_status;
        $search_priority = $request->search_priority; 
        $search_department_id = $request->search_department_id; 
        
        $tickets = DB::table('tickets')            
            ->leftJoin('users', 'tickets.user_id', '=', 'users.id')
            ->leftJoin('tickets_departments', 'tickets.department_id', '=', 'tickets_departments.id')
            ->leftJoin('cart_orders', 'tickets.order_id', '=', 'cart_orders.id')
            ->select('tickets.*', 'tickets_departments.title as department_title', 'users.name as client_name', 'users.email as client_email', 'users.avatar as client_avatar', 
                    'cart_orders.code as order_code', 'cart_orders.total as order_total', 'cart_orders.currency_id as order_currency_id', 'cart_orders.is_paid as order_is_paid', 'cart_orders.due_date as order_due_date',
                    DB::raw('(SELECT count(*) FROM tickets_responses WHERE tickets_responses.ticket_id = tickets.id) as count_responses'),                     
                    DB::raw('(SELECT count(*) FROM tickets WHERE tickets.user_id = users.id) as count_client_tickets'),                     
                    DB::raw('(SELECT count(*) FROM tickets WHERE tickets.user_id = users.id AND tickets.closed_at IS NULL) as count_client_open_tickets'),                     
                    DB::raw('(SELECT created_at FROM tickets_responses WHERE tickets_responses.ticket_id = tickets.id ORDER BY id DESC LIMIT 1) as latest_response_created_at'),
                    DB::raw('(SELECT name FROM users WHERE tickets.closed_by_user_id = users.id) as closed_by_user_name')
            );
                          
        if($search_terms) $tickets = $tickets->where(function ($query) use ($search_terms) {
            $query->where('tickets.subject', 'like', "%$search_terms%")
                ->orWhere('tickets.code', 'like', "%$search_terms%");
        });             

        if($search_status) {
            if($search_status == 'new') $tickets = $tickets->whereNull('tickets.closed_at')->whereNull('tickets.last_response');              
            if($search_status == 'closed') $tickets = $tickets->whereNotNull('tickets.closed_at');              
            if($search_status == 'waiting_client') $tickets = $tickets->whereNull('tickets.closed_at')->where('tickets.last_response', 'operator');              
            if($search_status == 'waiting_operator') $tickets = $tickets->whereNull('tickets.closed_at')->where('tickets.last_response', 'client');              
        }

        if(isset($search_priority)) $tickets = $tickets->where('tickets.priority', $search_priority);                                        
        if($search_department_id) $tickets = $tickets->where('tickets.department_id', $search_department_id);              

        $tickets = $tickets->orderBy('tickets.closed_at', 'asc')->orderBy('tickets.id', 'desc')->paginate(25); 

        $count_new_tickets = DB::table('tickets')            
            ->whereNull('closed_at')
            ->whereNull('last_response')
            ->count();

        $count_waiting_operator_tickets = DB::table('tickets')   
            ->whereNull('closed_at')         
            ->where('last_response', 'client')
            ->count();            

        $departments = DB::table('tickets_departments')   
            ->orderBy('active', 'desc')
            ->orderBy('title', 'asc')
            ->get();        

        return view('admin/account', [
            'view_file' => 'tickets.tickets',
            'active_submenu' => 'tickets',
            'search_terms' => $search_terms,
            'search_department_id' => $search_department_id,
            'search_status' => $search_status,
            'search_priority' => $search_priority,            
            'tickets' => $tickets,            
            'departments' => $departments,  
            'count_new_tickets' => $count_new_tickets,           
            'count_waiting_operator_tickets' => $count_waiting_operator_tickets,
        ]);
    }
    


    /**
     * Show resource
     */
    public function show(Request $request)
    {                 
        if(! check_access('tickets')) return redirect(route('admin'));

        $id = $request->id;
        
        $ticket = DB::table('tickets')      
            ->leftJoin('users', 'tickets.user_id', '=', 'users.id')         
            ->leftJoin('tickets_departments', 'tickets.department_id', '=', 'tickets_departments.id')    
            ->select('tickets.*', 'users.name as client_name', 'users.email as client_email', 'users.avatar as client_avatar', 'tickets_departments.title as department_title',  DB::raw('(SELECT name FROM users WHERE tickets.closed_by_user_id = users.id) as closed_by_user_name'))     
            ->where('tickets.id', $id)
            ->first();  
            
        if(!$ticket) return redirect(route('admin'));

        $departments = DB::table('tickets_departments')
            ->where('active', 1)
            ->orderBy('title', 'asc')
            ->get();          
            
        $responses_search_terms = $request->responses_search_terms;
        $responses_search_author = $request->responses_search_author;
        $responses_search_important = $request->responses_search_important;

        $responses = DB::table('tickets_responses')
            ->leftJoin('users', 'tickets_responses.user_id', '=', 'users.id')
            ->select('tickets_responses.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar')
            ->where('ticket_id', $id);

        if($responses_search_terms) $responses = $responses->where('tickets_responses.message', 'like', "%$responses_search_terms%");                
        if($responses_search_important == 'important') $responses = $responses->where('important_for_operator', 1);             
        if($responses_search_author == 'client') $responses = $responses->where('user_id', $ticket->user_id);             
        if($responses_search_author == 'operator') $responses = $responses->where('user_id', '!=', $ticket->user_id);             

        $responses = $responses->orderBy('id', 'desc')
            ->paginate(25); 

        $count_internal_info = DB::table('tickets_internal_info')->where('ticket_id', $id)->count(); 

        return view('admin/account', [
            'view_file' => 'tickets.ticket',
            'active_submenu' => 'tickets',
            'menu_tab' => 'details',
            'ticket' => $ticket,            
            'departments' => $departments,            
            'responses' => $responses,    
            'count_internal_info' => $count_internal_info,    
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
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        if(! check_access('tickets')) return redirect(route('admin'));

        $id = $request->id;

        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.tickets.show', ['id' => $id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array            

        DB::table('tickets')
            ->where('id', $id)
            ->update([
            'subject' => $inputs['subject'],
            'message' => $inputs['message'],
            'department_id' => $inputs['department_id'],
            'priority' => $inputs['priority'],
        ]);    
                         
        return redirect(route('admin.tickets.show', ['id' => $id]))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        if(! check_access('tickets', 'manager')) return redirect(route('admin'));

        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('tickets')->where('id', $id)->delete(); 
        DB::table('tickets_responses')->where('ticket_id', $id)->delete(); 
        DB::table('tickets_ratings')->where('ticket_id', $id)->delete(); 
        DB::table('tickets_internal_info')->where('ticket_id', $id)->delete(); 

        return redirect(route('admin.tickets'))->with('success', 'deleted'); 
    }


    /**
    * Reply to ticket
    */
    public function reply(Request $request)
    {       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        if(! check_access('tickets')) return redirect(route('admin'));
        
        $id = $request->id;

        $validator = Validator::make($request->all(), [
            'message' => 'required',            
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.tickets.show', ['id' => $id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();            

        DB::table('tickets_responses')->insert([
            'ticket_id' => $id,
            'message' => $inputs['message'],
            'created_at' => now(),
            'user_id' => Auth::user()->id,
        ]);      
               
        $response_id = DB::getPdo()->lastInsertId(); 

        DB::table('tickets')
            ->where('id', $id)
            ->update([
            'last_response' => 'operator',            
        ]);   

        // process file
        if ($request->hasFile('file')) {            
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('tickets_responses')->where('id', $response_id)->update(['file' => $file_db]);            
        } 

        return redirect(route('admin.tickets.show', ['id' => $id]))->with('success', 'reply_created'); 
    }   


    /**
    * Flag response as important
    */
    public function mark_important_response(Request $request)
    {        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('tickets')) return redirect(route('admin'));

        $id = $request->id;
        $response_id = $request->response_id;
                       
        DB::table('tickets_responses')
            ->where('id', $response_id)
            ->update([
            'important_for_operator' => 1,            
        ]);   

        return back()->withInput();
    }   


    /**
    * Unflag response as important
    */
    public function unmark_important_response(Request $request)
    {        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('tickets')) return redirect(route('admin'));

        $id = $request->id;
        $response_id = $request->response_id;
                       
        DB::table('tickets_responses')
            ->where('id', $response_id)
            ->update([
            'important_for_operator' => 0,            
        ]);   

        return back()->withInput();
    }       


    /**
    * Remove the specified resource
    */
    public function delete_response(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      
        
        if(! check_access('tickets', 'manager')) return redirect(route('admin'));

        $id = $request->id;
        $response_id = $request->response_id;    

        DB::table('tickets_responses')->where('id', $response_id)->delete(); 
        
        return redirect(route('admin.tickets.show', ['id' => $id]))->with('success', 'response_deleted'); 
    }


    /**
    * Close ticket
    */
    public function close(Request $request)
    {        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('tickets')) return redirect(route('admin'));

        $id = $request->id;
                       
        DB::table('tickets')
            ->where('id', $id)
            ->update([
            'closed_at' => now(),
            'closed_by_user_id' => Auth::user()->id,
        ]);   

        return redirect(route('admin.tickets'))->with('success', 'closed'); 
    } 


    /**
    * Reopen ticket
    */
    public function open(Request $request)
    {        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        if(! check_access('tickets')) return redirect(route('admin'));
        
        $id = $request->id;
                       
        DB::table('tickets')
            ->where('id', $id)
            ->update([
            'closed_at' => null,
            'closed_by_user_id' => null,
        ]);   

        return redirect(route('admin.tickets.show', ['id' => $id]))->with('success', 'reopened'); 
    } 


    /**
     * Show all resources
     */
    public function internal_info(Request $request)
    {   
        if(! check_access('tickets')) return redirect(route('admin'));

        $id = $request->id;        
        
        $ticket = DB::table('tickets')                  
            ->where('id', $id)
            ->first();              
        if(! $ticket) redirect(route('admin.tickets')); 
                           
        $infos = DB::table('tickets_internal_info')
            ->leftJoin('users', 'tickets_internal_info.user_id', '=', 'users.id')   
            ->select('tickets_internal_info.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar')     
            ->where('ticket_id', $id)
            ->orderBy('id', 'desc')
            ->paginate(25); 


        return view('admin/account', [
            'view_file' => 'tickets.ticket-internal-info',
            'active_submenu' => 'tickets',
            'menu_tab'=>'internal',
            'ticket' => $ticket,            
            'infos' => $infos,    
            'count_internal_info' => $infos->total(),            
        ]);
    }
   


    /**
    * Create new resource
    */
    public function store_internal_info(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        if(! check_access('tickets')) return redirect(route('admin'));

        $id = $request->id;
        
        $inputs = $request->all(); // retrieve all of the input data as an array               

        DB::table('tickets_internal_info')->insert([
            'message' => $inputs['message'],  
            'ticket_id' => $id,          
            'created_at' => now(),
            'user_id' => Auth::user()->id,
        ]);
                
        // process file
        if ($request->hasFile('file')) {
            $id = DB::getPdo()->lastInsertId(); 
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('tickets_internal_info')->where('id', $id)->update(['file' => $file_db]);            
        } 

        return redirect($request->Url())->with('success', 'created'); 
    }   


    /**
    * Remove the specified resource
    */
    public function destroy_internal_info(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');      

        if(! check_access('tickets', 'manager')) return redirect(route('admin'));

        $id = $request->id;
        $info_id = $request->info_id;
        
        DB::table('tickets_internal_info')->where('id', $info_id)->delete(); 

        return redirect($request->Url())->with('success', 'deleted'); 
    }


    /**
    * tickets config.
    */
    public function config()
    {                                              
        return view('admin/account', [
            'view_file' => 'tickets.config',
            'active_submenu' => 'tickets',

        ]);
    } 

     
    /**
    * Update config
    */
    public function update_config(Request $request)
    {       
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');       
               
        if(! ($this->logged_user_role == 'admin')) return redirect(route('admin'));    

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
