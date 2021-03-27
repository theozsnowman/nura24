<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('posts_images')) {

            Schema::create('posts_images', function (Blueprint $table) {
                $table->id();
                $table->integer('post_id');
                $table->string('file', 190)->nullable();
                $table->text('description')->nullable();
            });
        
        } else {

            Schema::table('posts_images', function (Blueprint $table) {

                if (! Schema::hasColumn('posts_images', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('posts_images', 'post_id')) 
                    $table->integer('post_id');
                
                if (! Schema::hasColumn('posts_images', 'file')) 
                    $table->string('file', 190)->nullable();
                
                if (! Schema::hasColumn('posts_images', 'description')) 
                    $table->text('description')->nullable();
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
        Schema::dropIfExists('posts_images');
    }
}
