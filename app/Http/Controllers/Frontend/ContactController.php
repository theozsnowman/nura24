<?php
/**
 * Copyright: Nura24 - https://www.nura24.com
*/ 

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Core;
use DB;
use Auth; 
use App; 

class ContactController extends Controller
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
    * Display contact page
    */
    public function index(Request $request)
    {                                 
        if(! check_module('contact')) return redirect('/');           

        return view('frontend/'.$this->config->template.'/contact', [
                  
        ]);
    }


    /**
    * Process contact form
    */
    public function send(Request $request)
    {
        
        if(! check_module('contact')) return redirect('/');                

        if($this->config->contact_recaptcha_enabled ?? null == 1) {
            // Build POST request:
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_secret = $this->config->google_recaptcha_secret_key ?? null;
            $recaptcha_response = $request->recaptcha_response;

            // Make and decode POST request:
            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $recaptcha = json_decode($recaptcha);

            // Take action based on the score returned:
            if ($recaptcha->success) {
                if($recaptcha->score < 0.5) return redirect($request->Url())->with('error', 'recaptcha_error');            
            }
            else return redirect($request->Url())->with('error', 'recaptcha_error');
        }


        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'name' => 'required',
            'email' => 'email',
        ]);

        if ($validator->fails()) {
            return redirect($request->Url())
                ->withErrors($validator)
                ->withInput();
        } 

        $inputs = $request->all(); // retrieve all of the input data as an array             
        
        $source_id = DB::table('inbox_sources')->where('source', 'contact')->value('id');

        DB::table('inbox')->insert([
            'source_id' => $source_id, 
            'name' => $inputs['name'], 
            'email' => $inputs['email'], 
            'subject' => $inputs['subject'],
            'message' => $inputs['message'],
            'created_at' =>  now(),
            'ip' => $request->ip(),
            'is_read' => 0,
            'is_responded' => 0,
            'is_important' => 0,
        ]);
      
        return redirect($request->Url())->with('success', 'sent'); 
    }   

}
