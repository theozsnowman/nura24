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

class FAQController extends Controller
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
    * Display faq page
    */
    public function index()
    {                      
        if(! check_module('faq')) return redirect('/');        

        $faqs = DB::table('faq')
            ->where('active', 1)    
            ->where(function ($query) { $query->where('lang_id', active_lang()->id)->orWhereNull('lang_id'); }) 
            ->orderBy('position', 'asc')   
            ->orderBy('title', 'asc')   
            ->get();            

        return view('frontend/'.$this->config->template.'/faq', [           
            'faqs' => $faqs,            
        ]);
    }

}
