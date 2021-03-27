<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_images')) {

            Schema::create('cart_images', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id');
                $table->string('file', 190)->nullable();
                $table->text('description')->nullable();
            });
        
        } else {

            Schema::table('cart_images', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_images', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_images', 'product_id'))                     
                    $table->integer('product_id');
                
                if (! Schema::hasColumn('cart_images', 'file')) 
                    $table->string('file', 190)->nullable();
                
                if (! Schema::hasColumn('cart_images', 'description')) 
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
        Schema::dropIfExists('cart_images');
    }
}
