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

class HomeController extends Controller
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
    * Homepage
    */
    public function index(Request $request)
    {                            
        return view('frontend/'.$this->config->template.'/index', [
         
        ]);
    }

}
