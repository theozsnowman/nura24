<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_gateways')) {

            Schema::create('cart_gateways', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 50);
                $table->string('title', 255);
                $table->text('client_info')->nullable();
                $table->string('logo', 255)->nullable();
                $table->string('vendor_email', 150)->nullable();
                $table->tinyInteger('active')->default(0);
                $table->tinyInteger('hidden')->default(0);
                $table->tinyInteger('instant')->default(0);
                $table->smallInteger('position')->nullable();
                $table->string('checkout_file', 100)->nullable();
                $table->tinyInteger('protected')->default(0);
            });

        } else {

            Schema::table('cart_gateways', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_gateways', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_gateways', 'slug'))                     
                    $table->string('slug', 50);
                
                if (! Schema::hasColumn('cart_gateways', 'title')) 
                    $table->string('title', 255);
                
                if (! Schema::hasColumn('cart_gateways', 'client_info')) 
                    $table->text('client_info')->nullable();
                
                if (! Schema::hasColumn('cart_gateways', 'logo')) 
                    $table->string('logo', 255)->nullable();
                
                if (! Schema::hasColumn('cart_gateways', 'vendor_email')) 
                    $table->string('vendor_email', 150)->nullable();
                
                if (! Schema::hasColumn('cart_gateways', 'active')) 
                    $table->tinyInteger('active')->default(0);
                
                if (! Schema::hasColumn('cart_gateways', 'hidden')) 
                    $table->tinyInteger('hidden')->default(0);
                
                if (! Schema::hasColumn('cart_gateways', 'instant')) 
                    $table->tinyInteger('instant')->default(0);
                
                if (! Schema::hasColumn('cart_gateways', 'position')) 
                    $table->smallInteger('position')->nullable();
                
                if (! Schema::hasColumn('cart_gateways', 'checkout_file')) 
                    $table->string('checkout_file', 100)->nullable();
                
                if (! Schema::hasColumn('cart_gateways', 'protected')) 
                    $table->tinyInteger('protected')->default(0);

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
        Schema::dropIfExists('cart_gateways');
    }
}
