<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('users_messages')) {

            Schema::create('users_messages', function (Blueprint $table) {
                $table->id();
                $table->integer('created_by_user_id');
                $table->integer('to_user_id');
                $table->dateTime('created_at');
                $table->string('subject', 250);
                $table->text('message')->nullable();
                $table->string('file', 250)->nullable();
                $table->tinyInteger('priority')->default(0);
                $table->tinyInteger('is_read')->default(0);
                $table->dateTime('readed_at')->nullable();
            });
        
        } else {

            Schema::table('users_messages', function (Blueprint $table) {

                if (! Schema::hasColumn('users_messages', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('users_messages', 'created_by_user_id')) 
                    $table->integer('created_by_user_id');
                
                if (! Schema::hasColumn('users_messages', 'to_user_id')) 
                    $table->integer('to_user_id');
                
                if (! Schema::hasColumn('users_messages', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('users_messages', 'subject')) 
                    $table->string('subject', 250);
                
                if (! Schema::hasColumn('users_messages', 'message')) 
                    $table->text('message')->nullable();
                
                if (! Schema::hasColumn('users_messages', 'file')) 
                    $table->string('file', 250)->nullable();
                
                if (! Schema::hasColumn('users_messages', 'priority')) 
                    $table->tinyInteger('priority')->default(0);
                
                if (! Schema::hasColumn('users_messages', 'is_read')) 
                    $table->tinyInteger('is_read')->default(0);
                
                if (! Schema::hasColumn('users_messages', 'readed_at'))                     
                    $table->dateTime('readed_at')->nullable();
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
        Schema::dropIfExists('users_messages');
    }
}
