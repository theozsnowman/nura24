<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_currencies')) {

            Schema::create('cart_currencies', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50);
                $table->string('symbol', 50);
                $table->string('label', 100)->nullable();
                $table->string('style', 50);
                $table->string('t_separator', 10);
                $table->string('d_separator', 10);
                $table->tinyInteger('condensed');
                $table->tinyInteger('hidden');
                $table->tinyInteger('active');
                $table->tinyInteger('is_default')->nullable();
                $table->decimal('conversion_rate', 10, 3)->nullable();
            });
        }

        else {

            Schema::table('cart_currencies', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_currencies', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_currencies', 'code'))                     
                    $table->string('code', 50);
                
                if (! Schema::hasColumn('cart_currencies', 'symbol')) 
                    $table->string('symbol', 50);
                
                if (! Schema::hasColumn('cart_currencies', 'label')) 
                    $table->string('label', 100)->nullable();
                
                if (! Schema::hasColumn('cart_currencies', 'style')) 
                    $table->string('style', 50);
                
                if (! Schema::hasColumn('cart_currencies', 't_separator')) 
                    $table->string('t_separator', 10);
                
                if (! Schema::hasColumn('cart_currencies', 'd_separator')) 
                    $table->string('d_separator', 10);
                
                if (! Schema::hasColumn('cart_currencies', 'condensed')) 
                    $table->tinyInteger('condensed');
                
                if (! Schema::hasColumn('cart_currencies', 'hidden')) 
                    $table->tinyInteger('hidden');
                
                if (! Schema::hasColumn('cart_currencies', 'active')) 
                    $table->tinyInteger('active');
                
                if (! Schema::hasColumn('cart_currencies', 'is_default')) 
                    $table->tinyInteger('is_default')->nullable();
                
                if (! Schema::hasColumn('cart_currencies', 'conversion_rate')) 
                    $table->decimal('conversion_rate', 10, 3)->nullable();
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
        Schema::dropIfExists('cart_currencies');
    }
}
