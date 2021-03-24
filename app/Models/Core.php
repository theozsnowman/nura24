<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use App;

class Core extends Model
{
    protected $table = 'sys_config';
     

    public function __construct()
    {        
        
    }


     /**
     * Get configs from database
     *
     * @return array
     */
    public static function config()
    {        
        $results = DB::table('sys_config')->pluck('value','name')->toArray();
        return (object)$results;
    }
    
    
    /**
     * Get default lang
     */
    public static function get_default_lang() {

        $lang = DB::table('sys_lang')
            ->where('is_default', 1)
            ->first();    
             
        return $lang;
    }


    /**
     * Get active lang ID from active lang code
     */
    public static function get_active_lang_id($code) {

        $q = DB::table('sys_lang')
            ->select('id')
            ->where('code', $code)
            ->first();    
             
        return $q->id ?? null;
    }


    /**
     * Get custom template for module
     */
    public static function get_module_template($slug) {

        $q = DB::table('sys_modules')
            ->select('template')
            ->where('slug', $slug)
            ->first();    
             
        return $q->template ?? null;
    }

}