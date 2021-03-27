<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartOrdersItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_orders_items')) {

            Schema::create('cart_orders_items', function (Blueprint $table) {
                $table->id();
                $table->integer('order_id');
                $table->integer('product_id')->nullable();
                $table->tinyInteger('is_paid')->default(0);
                $table->tinyInteger('is_delivered')->default(0);
                $table->integer('ticket_id')->nullable();
                $table->text('item_name')->nullable();
                $table->text('item_description')->nullable();
                $table->decimal('price', 10)->nullable();
                $table->integer('quantity');
            });

        } else {

            Schema::table('cart_orders_items', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_orders_items', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_orders_items', 'order_id')) 
                    $table->integer('order_id');
                
                if (! Schema::hasColumn('cart_orders_items', 'product_id')) 
                    $table->integer('product_id')->nullable();
                
                if (! Schema::hasColumn('cart_orders_items', 'is_paid')) 
                    $table->tinyInteger('is_paid')->default(0);
                
                if (! Schema::hasColumn('cart_orders_items', 'is_delivered')) 
                    $table->tinyInteger('is_delivered')->default(0);
                
                if (! Schema::hasColumn('cart_orders_items', 'ticket_id')) 
                    $table->integer('ticket_id')->nullable();
                
                if (! Schema::hasColumn('cart_orders_items', 'item_name')) 
                    $table->text('item_name')->nullable();
                
                if (! Schema::hasColumn('cart_orders_items', 'item_description')) 
                    $table->text('item_description')->nullable();
                
                if (! Schema::hasColumn('cart_orders_items', 'price')) 
                    $table->decimal('price', 10)->nullable();
                
                if (! Schema::hasColumn('cart_orders_items', 'quantity')) 
                    $table->integer('quantity');

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
        Schema::dropIfExists('cart_orders_items');
    }
}
