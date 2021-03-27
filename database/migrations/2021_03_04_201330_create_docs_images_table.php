<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('docs_images')) {

            Schema::create('docs_images', function (Blueprint $table) {
                $table->id();
                $table->integer('doc_id');
                $table->string('file', 190)->nullable();
                $table->text('description')->nullable();
            });
        
        } else {

            Schema::table('docs_images', function (Blueprint $table) {

                if (! Schema::hasColumn('docs_images', 'id')) 
                    $table->id();
             
                if (! Schema::hasColumn('docs_images', 'doc_id'))                     
                    $table->integer('doc_id');
                
                if (! Schema::hasColumn('docs_images', 'file')) 
                    $table->string('file', 190)->nullable();
                
                if (! Schema::hasColumn('docs_images', 'description')) 
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
        Schema::dropIfExists('docs_images');
    }
}
