<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersInternalNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('users_internal_notes')) {

            Schema::create('users_internal_notes', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('created_by_user_id');
                $table->dateTime('created_at');
                $table->text('note')->nullable();
                $table->tinyInteger('sticky')->default(0);
                $table->string('file', 250)->nullable();
            });
        
        } else {

            Schema::table('users_internal_notes', function (Blueprint $table) {

                if (! Schema::hasColumn('users_internal_notes', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('users_internal_notes', 'user_id')) 
                    $table->integer('user_id');
                
                if (! Schema::hasColumn('users_internal_notes', 'created_by_user_id')) 
                    $table->integer('created_by_user_id');
                
                if (! Schema::hasColumn('users_internal_notes', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('users_internal_notes', 'note')) 
                    $table->text('note')->nullable();
                
                if (! Schema::hasColumn('users_internal_notes', 'sticky')) 
                    $table->tinyInteger('sticky')->default(0);
                
                if (! Schema::hasColumn('users_internal_notes', 'file')) 
                    $table->string('file', 250)->nullable();
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
        Schema::dropIfExists('users_internal_notes');
    }
}
