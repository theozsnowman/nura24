<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadsImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('downloads_images')) {

            Schema::create('downloads_images', function (Blueprint $table) {
                $table->id();
                $table->integer('download_id');
                $table->string('file', 190)->nullable();
                $table->text('description')->nullable();
            });
        
        } else {

            Schema::table('downloads_images', function (Blueprint $table) {

                if (! Schema::hasColumn('downloads_images', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('downloads_images', 'download_id')) 
                    $table->integer('download_id');
                
                if (! Schema::hasColumn('downloads_images', 'file')) 
                    $table->string('file', 190)->nullable();
                
                if (! Schema::hasColumn('downloads_images', 'description')) 
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
        Schema::dropIfExists('downloads_images');
    }
}
