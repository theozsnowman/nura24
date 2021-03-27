<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartProductsLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_products_langs')) {

            Schema::create('cart_products_langs', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id');
                $table->smallInteger('lang_id');
                $table->string('title', 250)->nullable();
                $table->text('content')->nullable();
                $table->text('help_info')->nullable();
                $table->string('meta_title', 250)->nullable();
                $table->text('meta_description')->nullable();
            });
            
        } else {

            Schema::table('cart_products_langs', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_products_langs', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_products_langs', 'product_id')) 
                    $table->integer('product_id');

                if (! Schema::hasColumn('cart_products_langs', 'lang_id'))                     
                    $table->smallInteger('lang_id');

                if (! Schema::hasColumn('cart_products_langs', 'title')) 
                    $table->string('title', 250)->nullable();

                if (! Schema::hasColumn('cart_products_langs', 'content')) 
                    $table->text('content')->nullable();

                if (! Schema::hasColumn('cart_products_langs', 'help_info')) 
                    $table->text('help_info')->nullable();

                if (! Schema::hasColumn('cart_products_langs', 'meta_title')) 
                    $table->string('meta_title', 250)->nullable();

                if (! Schema::hasColumn('cart_products_langs', 'meta_description')) 
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
        Schema::dropIfExists('cart_products_langs');
    }
}
