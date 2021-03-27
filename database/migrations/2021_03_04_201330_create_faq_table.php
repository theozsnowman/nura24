<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('faq')) {

            Schema::create('faq', function (Blueprint $table) {
                $table->id();
                $table->smallInteger('lang_id')->nullable();
                $table->string('title', 250)->nullable();
                $table->mediumText('content')->nullable();
                $table->tinyInteger('position')->nullable();
                $table->tinyInteger('active');
            });
        
        } else {

            Schema::table('faq', function (Blueprint $table) {

                if (! Schema::hasColumn('faq', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('faq', 'lang_id')) 
                    $table->smallInteger('lang_id')->nullable();
                
                if (! Schema::hasColumn('faq', 'title')) 
                    $table->string('title', 250)->nullable();
                
                if (! Schema::hasColumn('faq', 'content')) 
                    $table->mediumText('content')->nullable();
                
                if (! Schema::hasColumn('faq', 'position')) 
                    $table->tinyInteger('position')->nullable();
                
                if (! Schema::hasColumn('faq', 'active')) 
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
        Schema::dropIfExists('faq');
    }
}
