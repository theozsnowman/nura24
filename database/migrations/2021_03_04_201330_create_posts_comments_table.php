<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('posts_comments')) {

            Schema::create('posts_comments', function (Blueprint $table) {
                $table->id();
                $table->integer('post_id');
                $table->integer('user_id')->nullable();
                $table->string('name', 250)->nullable();
                $table->string('email', 250)->nullable();
                $table->mediumText('comment');
                $table->dateTime('created_at');
                $table->string('ip', 50);
            });
        
        } else {

            Schema::table('posts_comments', function (Blueprint $table) {

                if (! Schema::hasColumn('posts_comments', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('posts_comments', 'post_id')) 
                    $table->integer('post_id');
                
                if (! Schema::hasColumn('posts_comments', 'user_id')) 
                    $table->integer('user_id')->nullable();
                
                if (! Schema::hasColumn('posts_comments', 'name')) 
                    $table->string('name', 250)->nullable();
                
                if (! Schema::hasColumn('posts_comments', 'email')) 
                    $table->string('email', 250)->nullable();
                
                if (! Schema::hasColumn('posts_comments', 'comment')) 
                    $table->mediumText('comment');
                
                if (! Schema::hasColumn('posts_comments', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('posts_comments', 'ip')) 
                    $table->string('ip', 50);
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
        Schema::dropIfExists('posts_comments');
    }
}
