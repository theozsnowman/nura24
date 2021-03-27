<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartShoppingCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_shopping_cart')) {

            Schema::create('cart_shopping_cart', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->integer('product_id');
                $table->integer('variant_id')->nullable();
                $table->integer('quantity')->nullable();
                $table->dateTime('created_at');
                $table->string('ip', 100)->nullable();
            });
       
        } else {

            Schema::table('cart_shopping_cart', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_shopping_cart', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_shopping_cart', 'user_id')) 
                    $table->integer('user_id')->nullable();
                
                if (! Schema::hasColumn('cart_shopping_cart', 'product_id')) 
                    $table->integer('product_id');
                
                if (! Schema::hasColumn('cart_shopping_cart', 'variant_id')) 
                    $table->integer('variant_id')->nullable();
                
                if (! Schema::hasColumn('cart_shopping_cart', 'quantity')) 
                    $table->integer('quantity')->nullable();
                
                if (! Schema::hasColumn('cart_shopping_cart', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('cart_shopping_cart', 'ip')) 
                    $table->string('ip', 100)->nullable();

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
        Schema::dropIfExists('cart_shopping_cart');
    }
}
