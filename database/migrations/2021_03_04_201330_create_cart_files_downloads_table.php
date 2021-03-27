<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartFilesDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_files_downloads')) {

            Schema::create('cart_files_downloads', function (Blueprint $table) {
                $table->id();
                $table->integer('file_id');
                $table->integer('product_id');
                $table->integer('user_id');
                $table->dateTime('downloaded_at');
                $table->string('ip', 50)->nullable();
            });
        
        } else {

            Schema::table('cart_files_downloads', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_files_downloads', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_files_downloads', 'file_id'))                     
                    $table->integer('file_id');
    
                if (! Schema::hasColumn('cart_files_downloads', 'product_id')) 
                    $table->integer('product_id');
                
                if (! Schema::hasColumn('cart_files_downloads', 'user_id')) 
                    $table->integer('user_id');
                
                if (! Schema::hasColumn('cart_files_downloads', 'downloaded_at')) 
                    $table->dateTime('downloaded_at');
                
                if (! Schema::hasColumn('cart_files_downloads', 'ip')) 
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
        Schema::dropIfExists('cart_files_downloads');
    }
}
