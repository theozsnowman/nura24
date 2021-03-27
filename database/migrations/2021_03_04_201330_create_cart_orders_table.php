<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_orders')) {

            Schema::create('cart_orders', function (Blueprint $table) {
                $table->id();
                $table->string('code', 100)->unique('code');
                $table->integer('user_id')->nullable();
                $table->decimal('total', 10)->nullable();
                $table->smallInteger('currency_id');
                $table->tinyInteger('is_paid')->nullable();
                $table->dateTime('due_date')->nullable();
                $table->dateTime('created_at');
                $table->integer('created_by_user_id')->nullable();
                $table->dateTime('paid_at')->nullable();
                $table->smallInteger('gateway_id')->nullable();
                $table->string('gateway_code', 200)->nullable();
                $table->tinyInteger('priority')->default(0);
                $table->text('staff_notes')->nullable();
                $table->text('client_notes')->nullable();
                $table->text('gateway_data')->nullable();
                $table->text('billing_address')->nullable();
                $table->text('shopping_cart_data')->nullable();
            });

        } else {

            Schema::table('cart_orders', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_orders', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_orders', 'code'))                     
                    $table->string('code', 100)->unique('code');
                
                if (! Schema::hasColumn('cart_orders', 'user_id')) 
                    $table->integer('user_id')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'total')) 
                    $table->decimal('total', 10)->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'currency_id')) 
                    $table->smallInteger('currency_id');
                
                if (! Schema::hasColumn('cart_orders', 'is_paid')) 
                    $table->tinyInteger('is_paid')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'due_date')) 
                    $table->dateTime('due_date')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('cart_orders', 'created_by_user_id')) 
                    $table->integer('created_by_user_id')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'paid_at')) 
                    $table->dateTime('paid_at')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'gateway_id')) 
                    $table->smallInteger('gateway_id')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'gateway_code'))                 
                    $table->string('gateway_code', 200)->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'priority')) 
                    $table->tinyInteger('priority')->default(0);
                
                if (! Schema::hasColumn('cart_orders', 'staff_notes')) 
                    $table->text('staff_notes')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'client_notes')) 
                    $table->text('client_notes')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'gateway_data')) 
                    $table->text('gateway_data')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'billing_address')) 
                    $table->text('billing_address')->nullable();
                
                if (! Schema::hasColumn('cart_orders', 'shopping_cart_data')) 
                    $table->text('shopping_cart_data')->nullable();

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
        Schema::dropIfExists('cart_orders');
    }
}
