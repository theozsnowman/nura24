<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('posts')) {

            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->smallInteger('lang_id')->nullable();
                $table->string('title', 250)->index('title');
                $table->string('slug', 250);
                $table->string('image', 250)->nullable();
                $table->integer('user_id')->nullable();
                $table->integer('categ_id')->nullable();
                $table->string('status', 20);
                $table->mediumText('summary')->nullable();
                $table->longText('content')->nullable();
                $table->tinyInteger('featured')->nullable();
                $table->tinyInteger('disable_comments')->nullable();
                $table->tinyInteger('disable_likes')->nullable();
                $table->string('meta_title', 250)->nullable();
                $table->string('meta_description', 250)->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at')->nullable();
                $table->integer('hits')->default(0);
                $table->string('custom_tpl', 100)->nullable();
                $table->smallInteger('minutes_to_read')->nullable();
                $table->integer('likes')->default(0);
                $table->text('search_terms')->nullable();
            });
        
        } else {

            Schema::table('posts', function (Blueprint $table) {

                if (! Schema::hasColumn('posts', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('posts', 'lang_id')) 
                    $table->smallInteger('lang_id')->nullable();
                
                if (! Schema::hasColumn('posts', 'title')) 
                    $table->string('title', 250);
                
                if (! Schema::hasColumn('posts', 'slug')) 
                    $table->string('slug', 250);
                
                if (! Schema::hasColumn('posts', 'image')) 
                    $table->string('image', 250)->nullable();
                
                if (! Schema::hasColumn('posts', 'user_id')) 
                    $table->integer('user_id')->nullable();
                
                if (! Schema::hasColumn('posts', 'categ_id')) 
                    $table->integer('categ_id')->nullable();
                
                if (! Schema::hasColumn('posts', 'status')) 
                    $table->string('status', 20);
                
                if (! Schema::hasColumn('posts', 'summary')) 
                    $table->mediumText('summary')->nullable();
                
                if (! Schema::hasColumn('posts', 'content'))                     
                    $table->longText('content')->nullable();
                
                if (! Schema::hasColumn('posts', 'featured')) 
                    $table->tinyInteger('featured')->nullable();
                
                if (! Schema::hasColumn('posts', 'disable_comments')) 
                    $table->tinyInteger('disable_comments')->nullable();
                
                if (! Schema::hasColumn('posts', 'disable_likes')) 
                    $table->tinyInteger('disable_likes')->nullable();
                
                if (! Schema::hasColumn('posts', 'meta_title')) 
                    $table->string('meta_title', 250)->nullable();
                
                if (! Schema::hasColumn('posts', 'meta_description')) 
                    $table->string('meta_description', 250)->nullable();
                
                if (! Schema::hasColumn('posts', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('posts', 'updated_at')) 
                    $table->dateTime('updated_at')->nullable();
                
                if (! Schema::hasColumn('posts', 'hits')) 
                    $table->integer('hits')->default(0);
                
                if (! Schema::hasColumn('posts', 'custom_tpl')) 
                    $table->string('custom_tpl', 100)->nullable();
                
                if (! Schema::hasColumn('posts', 'minutes_to_read')) 
                    $table->smallInteger('minutes_to_read')->nullable();
                
                if (! Schema::hasColumn('posts', 'likes')) 
                    $table->integer('likes')->default(0);
                
                if (! Schema::hasColumn('posts', 'search_terms')) 
                    $table->text('search_terms')->nullable();
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
        Schema::dropIfExists('posts');
    }
}
