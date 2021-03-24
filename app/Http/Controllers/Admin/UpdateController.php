<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Auth;
use App\Models\User;
use App\Models\Core;
use DB;
use Artisan;

class UpdateController extends Controller
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
            
            if(! ($this->logged_user_role == 'admin')) return redirect('/'); 
            return $next($request);
        });
    }

   
    public function index()
    {                                              
        return view('admin/account', [
            'view_file' => 'core.update',
            'active_submenu' => 'config.general',
            'menu_section' => 'tools.update',
        ]);
    }


    public function check_update()
    {                                           

        $checked_version = file_get_contents('https://version.nura24.com');

        DB::table('sys_config')->updateOrInsert(
            ['name' => 'last_update_check'],
            ['value' => now()]
        ); 

        if($checked_version == config('nura.version')) return redirect(route('admin.tools.update'))->with('success', 'update_not_available');  
        else return redirect(route('admin.tools.update'))->with('success', 'update_available');  
    }


    public function update()
    {                        

        //dd(base_path());
        
        $updatesFolder = storage_path().'/updates';

        // get latest update file                           
        $url  = 'https://version.nura24.com/updates/nura24-latest.zip';
        $zip_file = $updatesFolder.'/nura24-latest.zip';

        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    
        $data = curl_exec($ch);    
        curl_close($ch);    
        file_put_contents($zip_file, $data);        
        

        // unzip
        $fnNoExt = basename($zip_file, ".zip");
        $destinationFolder = storage_path().'/updates';

        $zip = new \ZipArchive;
        if ($zip->open($zip_file, \ZipArchive::CREATE) === TRUE) {
            if(!is_dir($updatesFolder)){
                mkdir($updatesFolder,  0777);
            }
            $zip->extractTo($updatesFolder);
            $zip->close();
        } else {
            return FALSE;
        }


        // move migration file
        $migration_filename = date("Y_m_d_").Carbon::now()->timestamp.'_latest.php';
        copy($updatesFolder."/migrations/latest.php", database_path()."/migrations/update/".$migration_filename);

        // update database tables
        //Artisan::call('migrate --force --path=database/migrations/update/'.$migration_filename);

        // copy public folders and files
        recurseCopy($updatesFolder."/public", public_path());

        // copy app folders and files
        recurseCopy($updatesFolder."/nura24", base_path());

        return redirect(route('admin.tools.update'))->with('success', 'updated');          
    }
   

}
