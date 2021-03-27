<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInboxReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('inbox_reply')) {

            Schema::create('inbox_reply', function (Blueprint $table) {
                $table->id();
                $table->integer('msg_id');
                $table->integer('sender_user_id');
                $table->mediumText('message');
                $table->dateTime('created_at');
            });
        
        } else {

            Schema::table('inbox_reply', function (Blueprint $table) {

                if (! Schema::hasColumn('inbox_reply', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('inbox_reply', 'msg_id')) 
                    $table->integer('msg_id');
                
                if (! Schema::hasColumn('inbox_reply', 'sender_user_id')) 
                    $table->integer('sender_user_id');
                
                if (! Schema::hasColumn('inbox_reply', 'message')) 
                    $table->mediumText('message');
                
                if (! Schema::hasColumn('inbox_reply', 'created_at')) 
                    $table->dateTime('created_at');            
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
        Schema::dropIfExists('inbox_reply');
    }
}
