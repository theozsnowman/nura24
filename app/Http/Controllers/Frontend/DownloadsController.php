<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/ 

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Core;
use DB;
use Auth; 
use App; 
use Response;

class DownloadsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {        
        $this->config = Core::config();  
    }


    /**
    * Downloads section homepage
    */
    public function index(Request $request)
    {     
        if(! check_module('downloads')) return redirect('/');        

        $lang_id = (active_lang()->id == default_lang()->id) ? default_lang()->id : active_lang()->id;

        $downloads = DB::table('downloads')
            ->select('downloads.*', 
                DB::raw("(SELECT title FROM downloads_langs WHERE lang_id = $lang_id AND download_id = downloads.id) as translated_title"),
                DB::raw("(SELECT summary FROM downloads_langs WHERE lang_id = $lang_id AND download_id = downloads.id) as translated_summary") )
            ->where('active', 1)    
            ->orderBy('featured', 'desc')    
            ->orderBy('id', 'desc')    
            ->paginate(24);            

        return view('frontend/'.$this->config->template.'/downloads', [          
            'downloads' => $downloads,            
        ]);
    }



    /**
    * Download item page
    */
    public function show(Request $request)
    {              
        if(! check_module('downloads')) return redirect('/');        

        $slug = $request->slug;

        $lang_id = (active_lang()->id == default_lang()->id) ? default_lang()->id : active_lang()->id;

        $download = DB::table('downloads')
            ->leftJoin('downloads_langs', 'downloads_langs.download_id', '=', 'downloads.id') 
            ->select('downloads.*',
                DB::raw("(SELECT title FROM downloads_langs WHERE lang_id = $lang_id AND download_id = downloads.id) as translated_title"), 
                DB::raw("(SELECT summary FROM downloads_langs WHERE lang_id = $lang_id AND download_id = downloads.id) as translated_summary"), 
                DB::raw("(SELECT content FROM downloads_langs WHERE lang_id = $lang_id AND download_id = downloads.id) as translated_content"), 
                DB::raw("(SELECT meta_title FROM downloads_langs WHERE lang_id = $lang_id AND download_id = downloads.id) as translated_meta_title"), 
                DB::raw("(SELECT meta_description FROM downloads_langs WHERE lang_id = $lang_id AND download_id = downloads.id) as translated_meta_description"))
            ->where('downloads.slug', $slug)
            ->where('downloads.active', 1)
            ->first();                                

        if(! $download) return redirect('/');

        if($download->translated_title) $download->title = $download->translated_title;

        if($download->translated_summary) $download->summary = $download->translated_summary;

        if($download->translated_meta_title) $download->meta_title = $download->translated_meta_title;
        else $download->meta_title = $download->translated_title ?? $download->meta_title ?? $download->title;

        if($download->translated_meta_description) $download->meta_description = $download->translated_meta_description;
        else $download->meta_description = $download->meta_description ?? $download->description ?? $download->title;

        if($download->translated_content) $download->content = $download->translated_content;
        else $download->content = $download->content ?? null;

        $files = DB::table('downloads_files')
            ->where('download_id', $download->id)        
            ->where('active', 1)    
            ->orderBy('featured', 'desc')
            ->orderBy('release_date', 'desc')
            ->orderBy('version', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(25);    

        if($download->custom_tpl) $view_file = str_replace('.blade.php', '', $download->custom_tpl);
        else $view_file = 'download';

        return view('frontend/'.$this->config->template.'/'.$view_file, [          
            'download' => $download,            
            'files' => $files,            
        ]);
    }



    /**
    * Get the file (download)
    */
    public function get(Request $request)
    {      
        if(! check_module('downloads')) return redirect('/');        

        $hash = $request->hash;
                
        $file = DB::table('downloads_files')
            ->where('hash', $hash)        
            ->where('active', 1)    
            ->first();            
        if(!$file) if(! $download) return redirect('/');

        $download = DB::table('downloads')
            ->where('id', $file->download_id)        
            ->where('active', 1)    
            ->first();            
        if(!$download) if(! $download) return redirect('/');

        // check if login is required
        if($download->login_required == 1 && !Auth::user()) return redirect(route('download', ['slug'=>$download->slug]))->with('error', 'login_required');  
        if($download->login_required == 1 && ! logged_user()->email_verified_at) return redirect(route('download', ['slug'=>$download->slug]))->with('error', 'verify_email_required');  

        $location = 'uploads/'.$file->file;
        if (file_exists($location)) {       
            header("Pragma: public");
	        header("Expires: 0");
	        header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
	        header("Cache-Control: public");
	        header("Content-Description: File Transfer");
	        header("Content-Type: application/octet-stream");
	        header("Content-Transfer-Encoding: binary");
	        header('Content-Length: '.filesize($location)); 
	        header("Content-Disposition: attachment; filename=".basename($file->file));
	        header("refresh: 1; url=".route('downloads', ['slug'=>$download->slug]));

	        set_time_limit(0); // 0 - no limit
            ini_set('display_errors',false);
                
            //readfile($location);                                
            $myInputStream = fopen($location, 'rb');
            $myOutputStream = fopen('php://output', 'wb');
            stream_copy_to_stream($myInputStream, $myOutputStream);
            fclose($myOutputStream);
            fclose($myInputStream);

            // update downloads counter
            DB::table('downloads_files')
                ->where('hash', $hash)
                ->increment('count_downloads');            

            DB::table('downloads_logs')->insert([
                'download_id' => $download->id,
                'file_id' => $file->id,
                'user_id' => Auth::user()->id ?? null,
                'created_at' => now(),
                'ip' => request()->ip(),
            ]);    

            return redirect(route('download', ['slug'=>$download->slug]))->with('success', 'downloaded');
        }
        
        return redirect(route('download', ['slug'=>$download->slug]))->with('error', 'no_file');

    }
}
