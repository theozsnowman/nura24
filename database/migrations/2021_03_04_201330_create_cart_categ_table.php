<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartCategTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('cart_categ')) {

            Schema::create('cart_categ', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->nullable();
                $table->string('tree_ids', 250)->nullable();
                $table->string('product_type', 50);
                $table->string('title', 150);
                $table->string('slug', 150);
                $table->text('description')->nullable();
                $table->tinyInteger('active');
                $table->smallInteger('position')->nullable();
                $table->text('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->text('badges')->nullable();
                $table->string('custom_tpl', 200)->nullable();
                $table->string('icon', 250)->nullable();
                $table->integer('count_items')->nullable();
                $table->integer('count_tree_items')->nullable();
            });
        
        } else {

            Schema::table('cart_categ', function (Blueprint $table) {

                if (! Schema::hasColumn('cart_categ', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('cart_categ', 'parent_id'))                     
                    $table->integer('parent_id')->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'tree_ids'))                 
                    $table->string('tree_ids', 250)->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'product_type')) 
                    $table->string('product_type', 50);
                
                if (! Schema::hasColumn('cart_categ', 'title')) 
                    $table->string('title', 150);
                
                if (! Schema::hasColumn('cart_categ', 'slug')) 
                    $table->string('slug', 150);
                
                if (! Schema::hasColumn('cart_categ', 'description')) 
                    $table->text('description')->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'active')) 
                    $table->tinyInteger('active');
                
                if (! Schema::hasColumn('cart_categ', 'position')) 
                    $table->smallInteger('position')->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'meta_title')) 
                    $table->text('meta_title')->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'meta_description')) 
                    $table->text('meta_description')->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'badges')) 
                    $table->text('badges')->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'custom_tpl')) 
                    $table->string('custom_tpl', 200)->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'icon')) 
                    $table->string('icon', 250)->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'count_items')) 
                    $table->integer('count_items')->nullable();
                
                if (! Schema::hasColumn('cart_categ', 'count_tree_items'))                 
                    $table->integer('count_tree_items')->nullable();

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
        Schema::dropIfExists('cart_categ');
    }
}
