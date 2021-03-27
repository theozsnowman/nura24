<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadsFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('downloads_files')) {

            Schema::create('downloads_files', function (Blueprint $table) {
                $table->id();
                $table->string('hash', 100)->nullable();
                $table->integer('download_id');
                $table->string('title', 250);
                $table->mediumText('file')->nullable();
                $table->string('version', 50)->nullable();
                $table->date('release_date')->nullable();
                $table->dateTime('created_at');
                $table->tinyInteger('active');
                $table->tinyInteger('featured');
                $table->integer('count_downloads')->default(0);
            });
        
        } else {

            Schema::table('downloads_files', function (Blueprint $table) {

                if (! Schema::hasColumn('downloads_files', 'id')) 
                    $table->id();
            
                if (! Schema::hasColumn('downloads_files', 'hash')) 
                    $table->string('hash', 100)->nullable();
                
                if (! Schema::hasColumn('downloads_files', 'download_id')) 
                    $table->integer('download_id');
                
                if (! Schema::hasColumn('downloads_files', 'title')) 
                    $table->string('title', 250);
                
                if (! Schema::hasColumn('downloads_files', 'file')) 
                    $table->mediumText('file')->nullable();
                
                if (! Schema::hasColumn('downloads_files', 'version')) 
                    $table->string('version', 50)->nullable();
                
                if (! Schema::hasColumn('downloads_files', 'release_date')) 
                    $table->date('release_date')->nullable();
                
                if (! Schema::hasColumn('downloads_files', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('downloads_files', 'active')) 
                    $table->tinyInteger('active');
                
                if (! Schema::hasColumn('downloads_files', 'featured')) 
                    $table->tinyInteger('featured');
                
                if (! Schema::hasColumn('downloads_files', 'count_downloads')) 
                    $table->integer('count_downloads')->default(0);

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
        Schema::dropIfExists('downloads_files');
    }
}
