<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadsLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('downloads_langs')) {

            Schema::create('downloads_langs', function (Blueprint $table) {
                $table->id();
                $table->integer('lang_id');
                $table->integer('download_id');
                $table->string('title', 250)->nullable();
                $table->text('summary')->nullable();
                $table->mediumText('content')->nullable();
                $table->string('meta_title', 250)->nullable();
                $table->text('meta_description')->nullable();
            });
        
        } else {

            Schema::table('downloads_langs', function (Blueprint $table) {

                if (! Schema::hasColumn('downloads_langs', 'id')) 
                    $table->id();
            
                if (! Schema::hasColumn('downloads_langs', 'lang_id')) 
                    $table->integer('lang_id');
                
                if (! Schema::hasColumn('downloads_langs', 'download_id')) 
                    $table->integer('download_id');
                
                if (! Schema::hasColumn('downloads_langs', 'title')) 
                    $table->string('title', 250)->nullable();
                
                if (! Schema::hasColumn('downloads_langs', 'summary')) 
                    $table->text('summary')->nullable();
                
                if (! Schema::hasColumn('downloads_langs', 'content')) 
                    $table->mediumText('content')->nullable();
                
                if (! Schema::hasColumn('downloads_langs', 'meta_title')) 
                    $table->string('meta_title', 250)->nullable();
                
                if (! Schema::hasColumn('downloads_langs', 'meta_description')) 
                    $table->text('meta_description')->nullable();

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
        Schema::dropIfExists('downloads_langs');
    }
}
