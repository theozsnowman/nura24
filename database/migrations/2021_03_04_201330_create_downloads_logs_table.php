<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('downloads_logs')) {

            Schema::create('downloads_logs', function (Blueprint $table) {
                $table->id();
                $table->integer('download_id');
                $table->integer('file_id');
                $table->integer('user_id')->nullable();
                $table->dateTime('created_at');
                $table->string('ip', 50)->nullable();
            });
        
        } else {

            Schema::table('downloads_logs', function (Blueprint $table) {

                if (! Schema::hasColumn('downloads_logs', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('downloads_logs', 'download_id')) 
                    $table->integer('download_id');
                
                if (! Schema::hasColumn('downloads_logs', 'file_id')) 
                    $table->integer('file_id');
                
                if (! Schema::hasColumn('downloads_logs', 'user_id')) 
                    $table->integer('user_id')->nullable();
                
                if (! Schema::hasColumn('downloads_logs', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('downloads_logs', 'ip')) 
                    $table->string('ip', 50)->nullable();

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
        Schema::dropIfExists('downloads_logs');
    }
}
