<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsCategTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('docs_categ')) {

            Schema::create('docs_categ', function (Blueprint $table) {
                $table->id();
                $table->smallInteger('lang_id')->nullable();
                $table->integer('parent_id')->nullable();
                $table->string('tree_ids', 250)->nullable();
                $table->string('title', 150);
                $table->string('slug', 150);
                $table->text('description')->nullable();
                $table->tinyInteger('active');
                $table->smallInteger('position')->nullable();
                $table->text('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('badges')->nullable();
                $table->string('icon', 250)->nullable();
                $table->string('redirect_url', 250)->nullable();
                $table->integer('count_items')->nullable();
                $table->integer('count_tree_items')->nullable();
            });
        
        } else {

            Schema::table('docs_categ', function (Blueprint $table) {

                if (! Schema::hasColumn('docs_categ', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('docs_categ', 'lang_id')) 
                    $table->smallInteger('lang_id')->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'parent_id')) 
                    $table->integer('parent_id')->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'tree_ids')) 
                    $table->string('tree_ids', 250)->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'title')) 
                    $table->string('title', 150);
                
                if (! Schema::hasColumn('docs_categ', 'slug')) 
                    $table->string('slug', 150);
                
                if (! Schema::hasColumn('docs_categ', 'description')) 
                    $table->text('description')->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'active')) 
                    $table->tinyInteger('active');
                
                if (! Schema::hasColumn('docs_categ', 'position')) 
                    $table->smallInteger('position')->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'meta_title')) 
                    $table->text('meta_title')->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'meta_description')) 
                    $table->text('meta_description')->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'badges')) 
                    $table->text('badges')->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'icon')) 
                    $table->string('icon', 250)->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'redirect_url'))                     
                    $table->string('redirect_url', 250)->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'count_items')) 
                    $table->integer('count_items')->nullable();
                
                if (! Schema::hasColumn('docs_categ', 'count_tree_items')) 
                    $table->integer('count_tree_items')->nullable();

            });
        
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docs_categ');
    }
}
