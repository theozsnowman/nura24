<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSliderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('slider')) {

            Schema::create('slider', function (Blueprint $table) {
                $table->id();
                $table->smallInteger('lang_id')->nullable();
                $table->string('title', 250)->nullable();
                $table->mediumText('content')->nullable();
                $table->string('image', 250)->nullable();
                $table->string('url', 250)->nullable();
                $table->string('target', 50)->nullable();
                $table->tinyInteger('position')->nullable();
                $table->tinyInteger('active');
            });
        
        } else {

            Schema::table('slider', function (Blueprint $table) {

                if (! Schema::hasColumn('slider', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('slider', 'lang_id')) 
                    $table->smallInteger('lang_id')->nullable();
                
                if (! Schema::hasColumn('slider', 'title')) 
                    $table->string('title', 250)->nullable();
                
                if (! Schema::hasColumn('slider', 'content')) 
                    $table->mediumText('content')->nullable();
                
                if (! Schema::hasColumn('slider', 'image')) 
                    $table->string('image', 250)->nullable();
                
                if (! Schema::hasColumn('slider', 'url')) 
                    $table->string('url', 250)->nullable();
                
                if (! Schema::hasColumn('slider', 'target')) 
                    $table->string('target', 50)->nullable();
                
                if (! Schema::hasColumn('slider', 'position')) 
                    $table->tinyInteger('position')->nullable();
                
                if (! Schema::hasColumn('slider', 'active')) 
                    $table->tinyInteger('active');
             
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
        Schema::dropIfExists('slider');
    }
}
