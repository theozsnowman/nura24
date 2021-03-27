<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_products')) {

            Schema::create('cart_products', function (Blueprint $table) {
                $table->id();
                $table->smallInteger('categ_id')->nullable();
                $table->integer('brand_id')->nullable();
                $table->decimal('price', 10)->nullable();
                $table->decimal('old_price', 10)->nullable();
                $table->decimal('discount_percent', 10)->nullable();
                $table->string('sku', 250);
                $table->string('title', 255);
                $table->string('slug', 255);
                $table->text('summary')->nullable();
                $table->mediumText('content')->nullable();
                $table->text('help_info')->nullable();
                $table->string('image', 255)->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->nullable();
                $table->integer('created_by_user_id')->nullable();
                $table->string('status', 50)->nullable();
                $table->tinyInteger('hidden')->nullable();
                $table->tinyInteger('featured')->nullable();
                $table->string('meta_title', 255)->nullable();
                $table->text('meta_description')->nullable();
                $table->text('search_terms')->nullable();
                $table->string('custom_tpl', 100)->nullable();
                $table->tinyInteger('disable_orders')->default(0);
                $table->text('disable_orders_notes')->nullable();
                $table->tinyInteger('disable_reviews')->default(0);
                $table->decimal('rating', 10)->nullable();
                $table->integer('rating_votes')->nullable();
            });
        
        } else {

            Schema::table('cart_products', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_products', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_products', 'categ_id')) 
                    $table->smallInteger('categ_id')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'brand_id')) 
                    $table->integer('brand_id')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'price')) 
                    $table->decimal('price', 10)->nullable();
                
                if (! Schema::hasColumn('cart_products', 'old_price')) 
                    $table->decimal('old_price', 10)->nullable();
                
                if (! Schema::hasColumn('cart_products', 'discount_percent')) 
                    $table->decimal('discount_percent', 10)->nullable();
                
                if (! Schema::hasColumn('cart_products', 'sku')) 
                    $table->string('sku', 250);
                
                if (! Schema::hasColumn('cart_products', 'title')) 
                    $table->string('title', 255);
                
                if (! Schema::hasColumn('cart_products', 'slug')) 
                    $table->string('slug', 255);
                
                if (! Schema::hasColumn('cart_products', 'summary')) 
                    $table->text('summary')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'content'))                 
                    $table->mediumText('content')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'help_info')) 
                    $table->text('help_info')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'image')) 
                    $table->string('image', 255)->nullable();
                
                if (! Schema::hasColumn('cart_products', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('cart_products', 'updated_at')) 
                    $table->dateTime('updated_at')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'created_by_user_id')) 
                    $table->integer('created_by_user_id')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'status')) 
                    $table->string('status', 50)->nullable();
                
                if (! Schema::hasColumn('cart_products', 'hidden')) 
                    $table->tinyInteger('hidden')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'featured')) 
                    $table->tinyInteger('featured')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'meta_title')) 
                    $table->string('meta_title', 255)->nullable();
                
                if (! Schema::hasColumn('cart_products', 'meta_description')) 
                    $table->text('meta_description')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'search_terms')) 
                    $table->text('search_terms')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'custom_tpl')) 
                    $table->string('custom_tpl', 100)->nullable();
                
                if (! Schema::hasColumn('cart_products', 'disable_orders')) 
                    $table->tinyInteger('disable_orders')->default(0);
                
                if (! Schema::hasColumn('cart_products', 'disable_orders_notes')) 
                    $table->text('disable_orders_notes')->nullable();
                
                if (! Schema::hasColumn('cart_products', 'disable_reviews')) 
                    $table->tinyInteger('disable_reviews')->default(0);
                
                if (! Schema::hasColumn('cart_products', 'rating')) 
                    $table->decimal('rating', 10)->nullable();
                
                if (! Schema::hasColumn('cart_products', 'rating_votes')) 
                    $table->integer('rating_votes')->nullable();

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
        Schema::dropIfExists('cart_products');
    }
}
