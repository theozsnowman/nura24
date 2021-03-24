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

class TasksController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();      
        $this->UploadModel = new Upload();    
        
        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;
            
            $role = $this->UserModel->get_role_from_id ($this->role_id);    
            if($role!='admin') return redirect('/'); 
            return $next($request);
        });
    }

    
    /**
     * Show all resources
     */
    public function index(Request $request)
    {                    

        $search_terms = $request->search_terms;
        $search_status = $request->search_status;
        $search_priority = $request->search_priority; 
        $search_product_id = $request->search_product_id; 
        
        $tasks = DB::table('tasks')            
            ->select('tasks.*', 
                    DB::raw('(SELECT name FROM users WHERE tasks.created_by_user_id = users.id) as author_name'), 
                    DB::raw('(SELECT avatar FROM users WHERE tasks.created_by_user_id = users.id) as author_avatar'),
                    DB::raw('(SELECT name FROM users WHERE tasks.operator_user_id = users.id) as operator_name'), 
                    DB::raw('(SELECT avatar FROM users WHERE tasks.operator_user_id = users.id) as operator_avatar'),
                    DB::raw('(SELECT name FROM users WHERE tasks.client_user_id = users.id) as client_name'), 
                    DB::raw('(SELECT avatar FROM users WHERE tasks.client_user_id = users.id) as client_avatar')
            );
                          
        if($search_status) $tasks = $tasks->where('tasks.status', $search_status);              
        if($search_product_id) $tasks = $tasks->where('tasks.product_id', $search_product_id);                      
        if(isset($search_priority)) $tasks = $tasks->where('tasks.priority', $search_priority);                                        
        if($search_terms) $tasks = $tasks->where('title', 'like', "%$search_terms%");    

        $tasks = $tasks->orderBy('tasks.id', 'desc')->paginate(25); 
        
        $products = DB::table('cart_products')            
            ->where('type', 'task') 
            ->where('status', 'active')
            ->orderBy('title', 'asc')
            ->get();

        return view('admin/account', [
            'view_file'=>'tasks.tasks',
            'active_submenu'=>'tasks',
            'search_terms'=> $search_terms,
            'search_status'=> $search_status,
            'search_priority'=> $search_priority,
            'search_product_id'=> $search_product_id,
            'tasks' => $tasks,            
            'products' => $products,                        
        ]);
    }


    /**
    * Show form to add new resource
    */
    public function create()
    {
        $internals = $this->UserModel->get_module_internals('tasks'); 

        return view('admin/account', [
            'view_file'=>'tasks.create',
            'active_submenu'=>'tasks',
            'internals'=>json_decode(json_encode($internals)),
        ]);
    }


    /**
     * Show resource
     */
    public function show(Request $request)
    {                    
        $id = $request->id;
        
        $task = DB::table('tasks')                    
            ->where('id', $id)
            ->first();  
            
        if(!$task) abort(404);

        $internals = $this->UserModel->get_module_employees('tasks'); 

        return view('admin/account', [
            'view_file'=>'tasks.update',
            'active_submenu'=>'tasks',
            'task' => $task,     
            //'employees'=>json_decode(json_encode($employees)),       
        ]);
    }

    /**
    * Create resource
    */
    public function store(Request $request)
    {
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',            
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.tasks'))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all();            

        DB::table('tasks')->insert([
            'title' => $inputs['title'],
            'description' => $inputs['description'],
            'operator_user_id' => $inputs['operator_user_id'],
            'client_user_id' => $inputs['client_user_id'],
            'priority' => $inputs['priority'],      
            'due_date' => $inputs['due_date'],
            'status' => $inputs['status'],
            'created_at' => now(),
            'created_by_user_id' => Auth::user()->id,
        ]);      
                 
        if ($request->hasFile('file')) {
            $id = DB::getPdo()->lastInsertId(); 
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('tasks')->where('id', $id)->update(['file' => $file_db]);            
        }

        return redirect(route('admin.tasks'))->with('success', 'created'); 
    }   
    

    /**
    * Update resource
    */
    public function update(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.tasks', ['id' => $id]))
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        $exist_email = DB::table('users')->where('email', $inputs['email'])->where('id', '!=', $id)->first();
        if($exist_email) return redirect(route('admin.company.accounts'))->with('error', 'duplicate'); 

        if($inputs['email_verified']==0) $email_verified_at = NULL;
        else $email_verified_at = now();

        DB::table('tasks')
            ->where('id', $id)
            ->update([
                'title' => $inputs['title'],
                'description' => $inputs['description'],
                'operator_user_id' => $inputs['operator_user_id'],
                'priority' => $inputs['priority'],      
                'due_date' => $inputs['due_date'],
                'status' => $inputs['status'],
        ]);    
                 
        // process avatar        
        if ($request->hasFile('file')) {
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('tasks')->where('id', $id)->update(['file' => $file_db]);            
        }   

        return redirect(route('admin.tasks'))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('tasks')->where('id', $id)->delete(); 
        DB::table('tasks_activity')->where('task_id', $id)->delete(); 
        DB::table('tasks_chat')->where('task_id', $id)->delete();         

        return redirect(route('admin.tasks'))->with('success', 'deleted'); 
    }
}
