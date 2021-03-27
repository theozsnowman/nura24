<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('downloads')) {

            Schema::create('downloads', function (Blueprint $table) {
                $table->id();
                $table->string('title', 250);
                $table->string('slug', 250);
                $table->text('summary')->nullable();
                $table->text('content')->nullable();
                $table->string('image', 250)->nullable();
                $table->string('meta_title', 250)->nullable();
                $table->mediumText('meta_description')->nullable();
                $table->tinyInteger('login_required')->default(0);
                $table->dateTime('created_at');
                $table->tinyInteger('active');
                $table->string('custom_tpl', 100)->nullable();
                $table->mediumText('badges')->nullable();
                $table->tinyInteger('featured')->default(0);
            });
        
        } else {

            Schema::table('downloads', function (Blueprint $table) {

                if (! Schema::hasColumn('downloads', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('downloads', 'title')) 
                    $table->string('title', 250);
                
                if (! Schema::hasColumn('downloads', 'slug')) 
                    $table->string('slug', 250);
                
                if (! Schema::hasColumn('downloads', 'summary')) 
                    $table->text('summary')->nullable();
                
                if (! Schema::hasColumn('downloads', 'content')) 
                    $table->text('content')->nullable();
                
                if (! Schema::hasColumn('downloads', 'image')) 
                    $table->string('image', 250)->nullable();
                
                if (! Schema::hasColumn('downloads', 'meta_title')) 
                    $table->string('meta_title', 250)->nullable();
                
                if (! Schema::hasColumn('downloads', 'meta_description')) 
                    $table->mediumText('meta_description')->nullable();
                
                if (! Schema::hasColumn('downloads', 'login_required')) 
                    $table->tinyInteger('login_required')->default(0);
                
                if (! Schema::hasColumn('downloads', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('downloads', 'active')) 
                    $table->tinyInteger('active');
                
                if (! Schema::hasColumn('downloads', 'custom_tpl')) 
                    $table->string('custom_tpl', 100)->nullable();
                
                if (! Schema::hasColumn('downloads', 'badges')) 
                    $table->mediumText('badges')->nullable();
                
                if (! Schema::hasColumn('downloads', 'featured')) 
                    $table->tinyInteger('featured')->default(0);

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
        Schema::dropIfExists('downloads');
    }
}
