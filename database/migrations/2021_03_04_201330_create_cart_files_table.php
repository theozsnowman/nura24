<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_files')) {

            Schema::create('cart_files', function (Blueprint $table) {
                $table->id();
                $table->string('hash', 100)->nullable();
                $table->integer('product_id');
                $table->string('title', 250);
                $table->mediumText('description')->nullable();
                $table->mediumText('file')->nullable();
                $table->string('version', 50)->nullable();
                $table->date('release_date')->nullable();
                $table->dateTime('created_at');
                $table->tinyInteger('active');
                $table->tinyInteger('featured');
                $table->integer('count_downloads')->default(0);
            });

        } else {

            Schema::table('cart_files', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_files', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_files', 'hash'))                     
                    $table->string('hash', 100)->nullable();
                
                if (! Schema::hasColumn('cart_files', 'product_id')) 
                    $table->integer('product_id');
                
                if (! Schema::hasColumn('cart_files', 'title')) 
                    $table->string('title', 250);
                
                if (! Schema::hasColumn('cart_files', 'description')) 
                    $table->mediumText('description')->nullable();
                
                if (! Schema::hasColumn('cart_files', 'file')) 
                    $table->mediumText('file')->nullable();
                
                if (! Schema::hasColumn('cart_files', 'version')) 
                    $table->string('version', 50)->nullable();
                
                if (! Schema::hasColumn('cart_files', 'release_date')) 
                    $table->date('release_date')->nullable();
                
                if (! Schema::hasColumn('cart_files', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('cart_files', 'active')) 
                    $table->tinyInteger('active');
                
                if (! Schema::hasColumn('cart_files', 'featured')) 
                    $table->tinyInteger('featured');
                
                if (! Schema::hasColumn('cart_files', 'count_downloads')) 
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
        Schema::dropIfExists('cart_files');
    }
}
