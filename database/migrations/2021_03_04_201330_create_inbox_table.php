<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInboxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('inbox')) {

            Schema::create('inbox', function (Blueprint $table) {
                $table->id();
                $table->integer('source_id')->nullable();
                $table->string('name', 250);
                $table->string('email', 250);
                $table->string('subject', 250)->nullable();
                $table->mediumText('message')->nullable();
                $table->dateTime('created_at');
                $table->string('ip', 50);
                $table->tinyInteger('is_read')->default(0);
                $table->tinyInteger('is_responded')->default(0);
                $table->tinyInteger('is_important')->default(0);
                $table->integer('task_id')->nullable();
            });
        
        } else {

            Schema::table('inbox', function (Blueprint $table) {

                if (! Schema::hasColumn('inbox', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('inbox', 'source_id')) 
                    $table->integer('source_id')->nullable();
                
                if (! Schema::hasColumn('inbox', 'name')) 
                    $table->string('name', 250);
                
                if (! Schema::hasColumn('inbox', 'email')) 
                    $table->string('email', 250);
                
                if (! Schema::hasColumn('inbox', 'subject')) 
                    $table->string('subject', 250)->nullable();
                
                if (! Schema::hasColumn('inbox', 'message')) 
                    $table->mediumText('message')->nullable();
                
                if (! Schema::hasColumn('inbox', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('inbox', 'ip')) 
                    $table->string('ip', 50);
                
                if (! Schema::hasColumn('inbox', 'is_read')) 
                    $table->tinyInteger('is_read')->default(0);
                
                if (! Schema::hasColumn('inbox', 'is_responded')) 
                    $table->tinyInteger('is_responded')->default(0);
                
                if (! Schema::hasColumn('inbox', 'is_important')) 
                    $table->tinyInteger('is_important')->default(0);
                
                if (! Schema::hasColumn('inbox', 'task_id')) 
                    $table->integer('task_id')->nullable();

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
        Schema::dropIfExists('inbox');
    }
}
