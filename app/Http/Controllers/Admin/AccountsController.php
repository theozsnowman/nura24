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

class AccountsController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
        $this->UserModel = new User();      
        $this->UploadModel = new Upload();    

        $this->roles = DB::table('users_roles')->where('active', 1)->orderBy('id', 'asc')->get();      
        $this->role_id_internal = $this->UserModel->get_role_id_from_role('internal');
        $this->role_id_user = $this->UserModel->get_role_id_from_role('user');

        $this->middleware(function ($request, $next) {
            $this->role_id = Auth::user()->role_id;            
            $role = $this->UserModel->get_role_from_id($this->role_id);    
            if(! ($role == 'admin' || $this->logged_user_role == 'internal')) return redirect('/'); 
            return $next($request);
        });               
    }

    
    /**
     * Show all resources
     */
    public function index(Request $request)
    {                
        
        if(! check_access('accounts')) return redirect(route('admin'));

        $search_terms = $request->search_terms;
        $search_active = $request->search_active;
        $search_email_verified = $request->search_email_verified;
        $search_role_id = $request->search_role_id;
        $search_tag_id = $request->search_tag_id; // for internal only

        $accounts = DB::table('users')
            ->leftJoin('users_roles', 'users.role_id', '=', 'users_roles.id')
            ->select('users.*', 'users_roles.role as role', 
                DB::raw('(SELECT GROUP_CONCAT(users_tags.id, "@", users_tags.tag, "@", users_tags.color) FROM users_tags_accounts LEFT JOIN users_tags ON users_tags_accounts.tag_id = users_tags.id WHERE users_tags_accounts.user_id = users.id) as user_tags'), 
                DB::raw('(SELECT COUNT(*) FROM tickets WHERE tickets.user_id = users.id AND tickets.closed_at IS NOT NULL) as count_closed_tickets'), 
                DB::raw('(SELECT COUNT(*) FROM tickets WHERE tickets.user_id = users.id AND tickets.closed_at IS NULL) as count_open_tickets'), 
                DB::raw('(SELECT count(*) FROM cart_orders WHERE cart_orders.user_id = users.id AND is_paid = 1) as count_paid_orders'), 
                DB::raw('(SELECT count(*) FROM cart_orders WHERE cart_orders.user_id = users.id AND is_paid = 0) as count_unpaid_orders'),
        );
        
        if($search_role_id) $accounts = $accounts->where('users.role_id', $search_role_id);                      
        if(isset($search_active)) $accounts = $accounts->where('users.active', $search_active);                      
        if($search_email_verified=='1') $accounts = $accounts->whereNotNull('email_verified_at');                      
        if($search_email_verified=='0') $accounts = $accounts->whereNull('email_verified_at');     
        if($search_terms) $accounts = $accounts->where(function ($query) use ($search_terms) {
            $query->where('users.name', 'like', "%$search_terms%")
                ->orWhere('users.email', 'like', "%$search_terms%")
                ->orWhere('users.code', 'like', "%$search_terms%");
            });    
        
        if($search_tag_id) $accounts = $accounts->join('users_tags_accounts', function ($join) use ($search_tag_id) { 
            $join->on('users.id', '=', 'users_tags_accounts.user_id')
                 ->where('users_tags_accounts.tag_id', $search_tag_id);
        });

        $accounts = $accounts->orderBy('id', 'desc')->paginate(20);       
                       
        $tags = DB::table('users_tags')
            ->leftJoin('users_roles', 'users_tags.role_id', '=', 'users_roles.id')
            ->select('users_tags.*', 'users_roles.role as role')
            ->orderBy('users_roles.role')
            ->orderBy('tag', 'asc')
            ->get();               

        return view('admin/account', [
            'view_file'=>'accounts.accounts',
            'active_submenu'=>'accounts',
            'search_role_id'=> $search_role_id,
            'search_terms'=> $search_terms,
            'search_active'=> $search_active,
            'search_tag_id'=> $search_tag_id,
            'search_email_verified'=> $search_email_verified,
            'accounts' => $accounts,            
            'tags' => $tags,     
            'roles' => $this->roles,      
        ]);
    }


    /**
     * Show resource
     */
    public function show(Request $request)
    {                     

        if(! (check_access('accounts', 'manager') || check_access('accounts', 'operator'))) return redirect(route('admin'));

        $id = $request->id;
        
        $account = DB::table('users')
            ->where('id', $id)
            ->first();
        if(!$account) return redirect(route('admin.accounts'));
              
        return view('admin/account', [
            'view_file'=>'accounts.account',
            'active_submenu'=>'accounts',            
            'account' => $account,                        
            'roles' => $this->roles,     
            'menu_section' => 'details',   
            'is_internal' => ($account->role_id == $this->role_id_internal) ? true : false,
            'is_user' => ($account->role_id == $this->role_id_user) ? true : false,
        ]);
    }


    /**
    * Create resource
    */
    public function store(Request $request)
    {
        if(! (check_access('accounts', 'manager'))) return redirect(route('admin'));
        
        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $inputs = $request->all();     

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.accounts'))
                ->withErrors($validator)
                ->withInput();
        } 

        if(DB::table('users')->where('email', $inputs['email'])->exists()) return redirect(route('admin.accounts'))->with('error', 'duplicate'); 

        if($inputs['email_verified']==0) $email_verified_at = NULL;
        else $email_verified_at = now();

        DB::table('users')->insert([
            'name' => $inputs['name'],
            'code' => Str::random(10),
            'slug' => Str::slug($inputs['name'], '-'),
            'email' => $inputs['email'],      
            'role_id' => $inputs['role_id'],     
            'password' => Hash::make($inputs['password']),
            'active' => $inputs['active'],
            'email_verified_at' => $email_verified_at,
            'created_at' => now(),
        ]);      
                 
        // process avatar        
        if ($request->hasFile('avatar')) {
            $validator = Validator::make($request->all(), ['avatar' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.accounts'))
                    ->withErrors($validator)
                    ->withInput();
            } 

            $id = DB::getPdo()->lastInsertId(); 
            $image_db = $this->UploadModel->avatar($request, 'avatar');    
            DB::table('users')->where('id', $id)->update(['avatar' => $image_db]);            
        }

        return redirect(route('admin.accounts'))->with('success', 'created'); 
    }   
    

    /**
    * Update resource
    */
    public function update(Request $request)
    {        
        if(! (check_access('accounts', 'manager'))) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;
        $inputs = $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.accounts', ['id' => $id]))
                ->withErrors($validator)
                ->withInput();
        } 
        
        $user = DB::table('users')->where('id', $id)->first();

        if(DB::table('users')->where('email', $inputs['email'])->where('id', '!=', $id)->exists()) return redirect(route('admin.accounts'))->with('error', 'duplicate'); 

        if($inputs['email_verified']==0) $email_verified_at = null;
        elseif($user->email_verified_at) $email_verified_at = $user->email_verified_at;
        else $email_verified_at = now();

        DB::table('users')
            ->where('id', $id)
            ->update([
            'name' => $inputs['name'],
            'email' => $inputs['email'],
            'role_id' => $inputs['role_id'],
            'slug' => Str::slug($inputs['name'], '-'),
            'active' => $inputs['active'],
            'email_verified_at' => $email_verified_at,
            'updated_at' => now(),
        ]);    
                 
        // change password
        if($inputs['password']) {
            DB::table('users')
            ->where('id', $id)
            ->update(['password' => Hash::make($inputs['password'])]);  
        }

        // process image        
        if ($request->hasFile('avatar')) {
            $validator = Validator::make($request->all(), ['avatar' => 'mimes:jpeg,bmp,png,gif,webp']);
            if ($validator->fails()) {
                return redirect(route('admin.accounts'))
                    ->withErrors($validator)
                    ->withInput();
            } 
            
            $image_db = $this->UploadModel->avatar($request, 'avatar');    
            DB::table('users')->where('id', $id)->update(['avatar' => $image_db]);            
        }    

        return redirect(route('admin.accounts.show', ['id' => $id]))->with('success', 'updated'); 
    }


    /**
    * Remove the specified resource
    */
    public function destroy(Request $request)
    {
        if(! (check_access('accounts', 'manager'))) return redirect(route('admin'));        

        $id = $request->id;

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 
        
        DB::table('users')->where('id', $id)->delete(); // delete user
        DB::table('users_extra_values')->where('user_id', $id)->delete(); // delete user extra data
        DB::table('posts')->where('user_id', $id)->update(['user_id' => NULL]); // update posts with this user
        DB::table('posts_comments')->where('user_id', $id)->update(['user_id' => NULL]); // update posts with this user

        return redirect(route('admin.accounts'))->with('success', 'deleted'); 
    }


    /**
     * Account tags
     */
    public function tags(Request $request)
    {                    
        if(! (check_access('accounts'))) return redirect(route('admin'));

        $id = $request->id;
        
        $account = DB::table('users')
            ->where('id', $id)
            ->first();
        if(!$account) abort(404);
                
        $account_tags = DB::table('users_tags_accounts')
            ->leftJoin('users_tags', 'users_tags_accounts.tag_id', '=', 'users_tags.id')
            ->select('users_tags_accounts.*', 'users_tags.tag as tag', 'users_tags.color as color')
            ->where('user_id', $id)
            ->orderBy('tag', 'asc')
            ->paginate(25);               

        $role_id = DB::table('users')->where('id', $id)->value('role_id');

        $all_tags = DB::table('users_tags')
            ->where('role_id', $role_id)
            ->orderBy('tag', 'asc')
            ->get();    
        
        return view('admin/account', [
            'view_file'=>'accounts.account-tags',
            'active_submenu'=>'accounts',            
            'account' => $account,                        
            'account_tags' => $account_tags,     
            'all_tags' => $all_tags,                 
            'menu_section' => 'tags',   
            'is_internal' => ($account->role_id == $this->role_id_internal) ? true : false,
            'is_user' => ($account->role_id == $this->role_id_user) ? true : false,
        ]);
    }


    /**
    * Add tag to account
    */
    public function create_tag(Request $request)
    {
        if(! (check_access('accounts', 'manager'))) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;     
        $inputs = $request->all();     
        
        if(DB::table('users_tags_accounts')->where('user_id', $id)->where('tag_id', $inputs['tag_id'])->exists()) return redirect(route('admin.account.tags', ['id' => $id]))->with('error', 'duplicate'); 

        DB::table('users_tags_accounts')->insert([
            'tag_id' => $inputs['tag_id'],
            'user_id' => $id,                    
        ]);      
                 
        return redirect(route('admin.account.tags', ['id' => $id]))->with('success', 'created'); 
    }   
    

    /**
    * Remove tag from account
    */
    public function delete_tag(Request $request)
    {
        if(! (check_access('accounts', 'manager'))) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;     
        $inputs = $request->all();  
                
        DB::table('users_tags_accounts')->where('id', $inputs['tag_id'])->delete(); 
                 
        return redirect(route('admin.account.tags', ['id' => $id]))->with('success', 'deleted'); 
    }   


    /**
     * Account internal notes
     */
    public function notes(Request $request)
    {           
        if(! (check_access('accounts'))) return redirect(route('admin'));

        $id = $request->id;
        
        $account = DB::table('users')
            ->where('id', $id)
            ->first();
        if(!$account) abort(404);
                
        $notes = DB::table('users_internal_notes')
            ->leftJoin('users', 'users_internal_notes.created_by_user_id', '=', 'users.id')
            ->select('users_internal_notes.*', 'users.name as author_name', 'users.email as author_email', 'users.avatar as author_avatar')
            ->where('user_id', $id)
            ->orderBy('sticky', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25);                      

        return view('admin/account', [
            'view_file'=>'accounts.account-notes',
            'active_submenu'=>'accounts',            
            'account' => $account,                        
            'notes' => $notes,     
            'menu_section' => 'notes',   
            'is_internal' => ($account->role_id == $this->role_id_internal) ? true : false,
            'is_user' => ($account->role_id == $this->role_id_user) ? true : false,
        ]);
    }


    /**
    * Add internal to account
    */
    public function create_note(Request $request)
    {
        if(! (check_access('accounts'))) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;     
        $inputs = $request->all();             

        if($request->input('sticky')=='on') $sticky = 1;

        DB::table('users_internal_notes')->insert([
            'note' => $inputs['note'],
            'sticky' => $sticky ?? 0,
            'user_id' => $id,                    
            'created_at' => now(),                    
            'created_by_user_id' => Auth::user()->id,                                
        ]);      
                
        if ($request->hasFile('file')) {
            $note_id = DB::getPdo()->lastInsertId(); 
            $file_db = $this->UploadModel->upload_file($request, 'file');    
            DB::table('users_internal_notes')->where('id', $note_id)->update(['file' => $file_db]);            
        }

        return redirect(route('admin.account.notes', ['id' => $id]))->with('success', 'created'); 
    }   
    

    /**
    * Remove internal note from account
    */
    public function delete_note(Request $request)
    {
        if(! (check_access('accounts', 'manager'))) return redirect(route('admin'));

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo'); 

        $id = $request->id;     
        $inputs = $request->all();     
        
        DB::table('users_internal_notes')->where('id', $inputs['note_id'])->delete(); 
                 
        return redirect(route('admin.account.notes', ['id' => $id]))->with('success', 'deleted'); 
    }   


    /**
     * Account tickets notes
     */
    public function tickets(Request $request)
    {           
        if(! (check_access('tickets'))) return redirect(route('admin'));

        $id = $request->id;
        
        $account = DB::table('users')
            ->where('id', $id)
            ->first();
        if(!$account) redirect(route('admin.accounts'));
                
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
            )
            ->where('tickets.user_id', $id)
            ->orderBy('tickets.closed_at', 'asc')->orderBy('tickets.id', 'desc')
            ->paginate(25);

        return view('admin/account', [
            'view_file'=>'accounts.account-tickets',
            'active_submenu'=>'accounts',            
            'account' => $account,                        
            'tickets' => $tickets,     
            'menu_section' => 'tickets',   
            'is_internal' => ($account->role_id == $this->role_id_internal) ? true : false,
            'is_user' => ($account->role_id == $this->role_id_user) ? true : false,
        ]);
    }


    /**
     * Accounts permissions
     */
    public function permissions(Request $request)
    {      
        if(! ($this->logged_user_role == 'admin')) return redirect(route('admin')); 

        $search_user_id = $request->search_user_id;
        $search_terms = $request->search_terms;                    

        $internal_accounts = DB::table('users')
            ->where('role_id', $this->role_id_internal)
            ->where('active', 1);
            
        if($search_terms) $internal_accounts = $internal_accounts->where(function ($query) use ($search_terms) {
            $query->where('users.name', 'like', "%$search_terms%")
                ->orWhere('users.email', 'like', "%$search_terms%")
                ->orWhere('users.code', 'like', "%$search_terms%");
            });  

        if($search_user_id) $internal_accounts = $internal_accounts->where('id', $search_user_id); 

        $internal_accounts = $internal_accounts->orderBy('name', 'asc')->get(); 
                
        //$permissions = DB::table('sys_permissions')->orderBy('permission', 'asc')->get();                          

        $modules_permissions = array();        

        $modules = DB::table('sys_modules')->orderBy('active', 'desc')->orderBy('module')->get();

        foreach($modules as $module) {
            
            $permissions = array();

            $module_perms = DB::table('sys_permissions')->where('module_id', $module->id)->orderBy('level', 'asc')->orderBy('permission', 'asc')->get();

            foreach($module_perms as $perm) {
                $permissions[] = array('id' => $perm->id, 'label' => $perm->label, 'level' => $perm->level, 'permission' => $perm->permission, 'description' => $perm->description);

            }

            $modules_permissions[] = array('module_id' => $module->id, 'module' => $module->module, 'module_label' => $module->label, 'module_active' => $module->active, 'permissions' => $permissions);

        }

        //dd($modules_permissions);

        return view('admin/account', [
            'view_file'=>'accounts.permissions',
            'active_submenu'=>'permissions',            
            'internal_accounts' => $internal_accounts,                        
            'modules_permissions' => json_decode(json_encode($modules_permissions)), // array to object
            'search_user_id' => $search_user_id,    
            'search_terms' => $search_terms,    
        ]);
    }


    /**
    * Update accounts permissions
    */
    public function update_permissions(Request $request) {
        
        if(! ($this->logged_user_role == 'admin')) return redirect(route('admin')); 

        // disable action in demo mode:
        if(config('app.demo_mode')) return redirect(route('admin'))->with('error', 'demo');       
        
        $inputs = $request->all(); // retrieve all of the input data as an array 
        
        $internal_accounts = DB::table('users')->where('role_id', $this->role_id_internal)->where('active', 1)->orderBy('name', 'asc')->get();   
        $modules = DB::table('sys_modules')->orderBy('active', 'desc')->orderBy('module')->get();               
        $permissions = DB::table('sys_permissions')->orderBy('permission', 'asc')->get();                    

        foreach($internal_accounts as $account) {

            foreach($modules as $module) {

                $name = $module->id.'_'.$account->id;
                
                $permission_id = $request->$name;

                if($permission_id > 0) {
                    DB::table('users_permissions')
                    ->updateOrInsert(
                        ['module_id' => $module->id, 'user_id' => $account->id],
                        ['module_id' => $module->id, 'permission_id' => $permission_id, 'user_id' => $account->id],
                    );
                } else {
                    DB::table('users_permissions')->where('module_id', $module->id)->where('user_id', $account->id)->delete(); 
                }
            }

        }
        
        return redirect(route('admin.accounts.permissions'))->with('success', 'updated');
    }

}
