<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartCategLangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_categ_langs')) {

            Schema::create('cart_categ_langs', function (Blueprint $table) {
                $table->id();
                $table->integer('categ_id');
                $table->smallInteger('lang_id');
                $table->string('title', 250)->nullable();
                $table->text('description')->nullable();
                $table->string('meta_title', 250)->nullable();
                $table->text('meta_description')->nullable();
            });
        
        } else {

            Schema::table('cart_categ_langs', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_categ_langs', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_categ_langs', 'categ_id')) 
                    $table->integer('categ_id');

                if (! Schema::hasColumn('cart_categ_langs', 'lang_id'))                     
                    $table->smallInteger('lang_id');

                if (! Schema::hasColumn('cart_categ_langs', 'title'))                     
                    $table->string('title', 250)->nullable();

                if (! Schema::hasColumn('cart_categ_langs', 'description'))                     
                    $table->text('description')->nullable();

                if (! Schema::hasColumn('cart_categ_langs', 'meta_title'))                     
                    $table->string('meta_title', 250)->nullable();

                if (! Schema::hasColumn('cart_categ_langs', 'meta_description'))                     
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
        Schema::dropIfExists('cart_categ_langs');
    }
}
