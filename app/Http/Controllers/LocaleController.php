<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use DB;

class LocaleController extends Controller
{
    
   
    /**
    * Change language
    */
    public function change_lang(Request $request)
    {
        $code = $request->code;         

        $check = DB::table('sys_lang')->where('active', 1)->where('code', $code)->first();                   
        if(!$check) return redirect()->back();        

        //$request->session()->put('locale', $lang_code); 
        if($check->is_default==1) return redirect(route('homepage'))->cookie('active_lang', $code, 60*24*30, '/'); 
        else return redirect(route('homepage', ['locale'=>$code]))->cookie('active_lang', $code, 60*24*30, '/'); 
    }


     /**
    * Change currency
    */
    public function change_currency(Request $request)
    {        
        $code = $request->code;         

        $check = DB::table('sys_currencies')->where('active', 1)->where('hidden', 0)->where('code', $code)->first();                   
        if(!$check) return redirect(back());        

        //$request->session()->put('locale', $lang_code);        
        return redirect()->back()->cookie('active_currency_code', $code, 60*24*30, '/'); 
    }

}
