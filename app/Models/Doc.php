<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Doc extends Model
{

    protected $fillable = ['parent_id', 'title', 'slug', 'active', 'lang_id'];
    protected $table = 'docs_categ';    

    public function children() 
    {        
        return $this->hasMany('App\Models\Doc', 'parent_id')
            ->leftJoin('sys_lang', 'docs_categ.lang_id', '=', 'sys_lang.id')			
            ->select('docs_categ.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc');
    }


    public function childCategories()
    {
        return $this->hasMany('App\Models\Doc', 'parent_id')
            ->with('children')
            ->leftJoin('sys_lang', 'docs_categ.lang_id', '=', 'sys_lang.id')			
            ->select('docs_categ.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang')
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc');
    }


    public function active_children() 
    {        
        return $this->hasMany('App\Models\Doc', 'parent_id')
            ->leftJoin('sys_lang', 'docs_categ.lang_id', '=', 'sys_lang.id')			
            ->select('docs_categ.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang')
            ->where('active', 1)
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc');
    }


    public function active_childCategories()
    {
        return $this->hasMany('App\Models\Doc', 'parent_id')
            ->leftJoin('sys_lang', 'docs_categ.lang_id', '=', 'sys_lang.id')			
            ->select('docs_categ.*', 'sys_lang.name as lang_name', 'sys_lang.code as lang')
            ->where('active', 1)
            ->orderBy('position', 'asc')
            ->orderBy('title', 'asc')
            ->with('active_children');
    }
   

    public static function recount_categ_items($categ_id)
    {
        // count categ items
        $counter = DB::table('docs')
            ->where('categ_id', $categ_id)
            ->count();        

        // count categ items
        $q = DB::table('docs_categ')
            ->where('id', $categ_id)
            ->first();   
        if($q) {
            $tree_ids = $q->tree_ids;
            $categ_tree_counter = 0;

            $array_tree = explode(',', $tree_ids);
            foreach($array_tree as $tree_categ_id) {
                $tree_counter = DB::table('docs')
                    ->where('categ_id', $tree_categ_id)
                    ->count();  
                $categ_tree_counter = $categ_tree_counter + $tree_counter;    
            }            
        }

        DB::table('docs_categ')
            ->where('id', $categ_id)
            ->update([
            'count_items' => $counter ?? 0,
            'count_tree_items' => $categ_tree_counter ?? 0,           
        ]);    

        return;
    }
    

    public function regenerate_tree_ids()
    {        
        $root_categories = DB::table('docs_categ')->get();     
        foreach($root_categories as $root) {

            $id = $root->id;            

            $tree = array($id);
            
            $q = DB::table('docs_categ')->where('parent_id', $id)->first();                                                
                
            if($q) {                            
                $tree = array_unique(array_merge($tree, array($q->id)));      

                $q2 = DB::table('docs_categ')->where('parent_id', $q->id)->orWhere('parent_id', $q->parent_id)->get();                  

                foreach($q2 as $item)  {
                    $tree = array_unique(array_merge($tree, array($item->id)));    
                    
                    $q3 = DB::table('docs_categ')->where('parent_id', $item->id)->orWhere('parent_id', $item->parent_id)->get();   
                    foreach($q3 as $item2)  {      
                        $tree = array_unique(array_merge($tree, array($item2->id)));      

                        $q4 = DB::table('docs_categ')->where('parent_id', $item2->id)->orWhere('parent_id', $item2->parent_id)->get();   
                        foreach($q4 as $item3)  {           
                            $tree = array_unique(array_merge($tree, array($item3->id)));      

                            $q5 = DB::table('docs_categ')->where('parent_id', $item3->id)->orWhere('parent_id', $item3->parent_id)->get();   
                            foreach($q5 as $item4)  {          
                                $tree = array_unique(array_merge($tree, array($item4->id)));      

                                $q6 = DB::table('docs_categ')->where('parent_id', $item4->id)->orWhere('parent_id', $item4->parent_id)->get();   
                                foreach($q6 as $item5)  {          
                                    $tree = array_unique(array_merge($tree, array($item5->id)));      
                                }
                            }
                        }
                    } 
                }   
            }

            $values = implode (",", $tree);

            DB::table('docs_categ')
                ->where('id', $id)
                ->update([
                    'tree_ids' => $values ?? null,                
            ]);                                                                     

        } // end foreach        


        $inactive_categs = DB::table('docs_categ')->where('active', 0)->get();     
        foreach($inactive_categs as $categ) {
            $inactive_tree = DB::table('docs_categ')->where('id', $categ->id)->first();     
            $inactive_tree_ids = $inactive_tree->tree_ids;
            
            $myArray = explode(',', $inactive_tree_ids);
            
            foreach($myArray as $categ_id) {
                DB::table('docs_categ')->where('id', $categ_id)->update(['active' => 0]);      
            }
            
        }
           
        return;
    }
 

    public static function get_uncategorized_categ_id()
    {
        $categ = DB::table('docs_categ')
            ->where('slug', 'uncategorized')
            ->first();        

        return $categ->id ?? null;
    }
}