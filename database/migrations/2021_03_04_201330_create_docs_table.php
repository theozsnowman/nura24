<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('docs')) {

            Schema::create('docs', function (Blueprint $table) {
                $table->id();
                $table->smallInteger('lang_id')->nullable();
                $table->integer('user_id')->nullable();
                $table->string('title', 250);
                $table->string('slug', 250);
                $table->smallInteger('categ_id')->nullable();
                $table->text('content')->nullable();
                $table->tinyInteger('active');
                $table->smallInteger('position')->nullable();
                $table->dateTime('created_at');
                $table->text('search_terms')->nullable();
                $table->tinyInteger('featured')->default(0);
            });
        
        } else {

            Schema::table('docs', function (Blueprint $table) {

                if (! Schema::hasColumn('docs', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('docs', 'lang_id')) 
                    $table->smallInteger('lang_id')->nullable();
                
                if (! Schema::hasColumn('docs', 'user_id')) 
                    $table->integer('user_id')->nullable();
                
                if (! Schema::hasColumn('docs', 'title')) 
                    $table->string('title', 250);
                
                if (! Schema::hasColumn('docs', 'slug')) 
                    $table->string('slug', 250);
                
                if (! Schema::hasColumn('docs', 'categ_id')) 
                    $table->smallInteger('categ_id')->nullable();
                
                if (! Schema::hasColumn('docs', 'content')) 
                    $table->text('content')->nullable();
                
                if (! Schema::hasColumn('docs', 'active')) 
                    $table->tinyInteger('active');
                
                if (! Schema::hasColumn('docs', 'position')) 
                    $table->smallInteger('position')->nullable();
                
                if (! Schema::hasColumn('docs', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('docs', 'search_terms')) 
                    $table->text('search_terms')->nullable();
                
                if (! Schema::hasColumn('docs', 'featured')) 
                    $table->tinyInteger('featured')->default(0);
         
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
        Schema::dropIfExists('docs');
    }
}
