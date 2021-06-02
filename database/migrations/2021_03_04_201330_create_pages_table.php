<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('pages')) {

            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->integer('parent_id')->nullable();
                $table->smallInteger('lang_id')->nullable();
                $table->integer('user_id')->nullable();
                $table->string('title', 250);
                $table->string('slug', 250);
                $table->text('summary')->nullable();
                $table->longText('content')->nullable();
                $table->string('image', 255)->nullable();
                $table->tinyInteger('active');
                $table->mediumText('meta_title')->nullable();
                $table->mediumText('meta_description')->nullable();
                $table->string('redirect_url', 250)->nullable();
                $table->string('custom_tpl_file', 250)->nullable();
                $table->text('badges')->nullable();
                $table->string('label', 250)->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable();
            });
        
        } else {

            Schema::table('pages', function (Blueprint $table) {

                if (! Schema::hasColumn('pages', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('pages', 'parent_id')) 
                    $table->integer('parent_id')->nullable();
                
                if (! Schema::hasColumn('pages', 'lang_id')) 
                    $table->smallInteger('lang_id')->nullable();
                
                if (! Schema::hasColumn('pages', 'user_id')) 
                    $table->integer('user_id')->nullable();
                
                if (! Schema::hasColumn('pages', 'title')) 
                    $table->string('title', 250);
                
                if (! Schema::hasColumn('pages', 'slug')) 
                    $table->string('slug', 250);
                
                if (! Schema::hasColumn('pages', 'content')) 
                    $table->longText('content')->nullable();
                
                if (! Schema::hasColumn('pages', 'image')) 
                    $table->string('image', 255)->nullable();
                
                if (! Schema::hasColumn('pages', 'active')) 
                    $table->tinyInteger('active');
                
                if (! Schema::hasColumn('pages', 'meta_title')) 
                    $table->mediumText('meta_title')->nullable();
                
                if (! Schema::hasColumn('pages', 'meta_description')) 
                    $table->mediumText('meta_description')->nullable();
                
                if (! Schema::hasColumn('pages', 'redirect_url')) 
                    $table->string('redirect_url', 250)->nullable();
                
                if (! Schema::hasColumn('pages', 'custom_tpl_file')) 
                    $table->string('custom_tpl_file', 250)->nullable();
                
                if (! Schema::hasColumn('pages', 'badges')) 
                    $table->text('badges')->nullable();
                
                if (! Schema::hasColumn('pages', 'label'))                     
                    $table->string('label', 250)->nullable();
                
                if (! Schema::hasColumn('pages', 'created_at')) 
                    $table->timestamp('created_at')->nullable()->useCurrent();
                
                if (! Schema::hasColumn('pages', 'updated_at')) 
                    $table->timestamp('updated_at')->nullable();
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
        Schema::dropIfExists('pages');
    }
}
