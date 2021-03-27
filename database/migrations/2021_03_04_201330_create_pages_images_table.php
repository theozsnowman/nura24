<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('pages_images')) {

            Schema::create('pages_images', function (Blueprint $table) {
                $table->id();
                $table->integer('page_id');
                $table->string('file', 190)->nullable();
                $table->text('description')->nullable();
            });
        
        } else {

            Schema::table('pages_images', function (Blueprint $table) {

                if (! Schema::hasColumn('pages_images', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('pages_images', 'page_id')) 
                    $table->integer('page_id');
                
                if (! Schema::hasColumn('pages_images', 'file')) 
                    $table->string('file', 190)->nullable();
                
                if (! Schema::hasColumn('pages_images', 'description')) 
                    $table->text('description')->nullable();
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
        Schema::dropIfExists('pages_images');
    }
}
