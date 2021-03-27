<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailListsRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('email_lists_recipients')) {

            Schema::create('email_lists_recipients', function (Blueprint $table) {
                $table->id();
                $table->integer('list_id');
                $table->string('email', 150);
                $table->string('name', 100)->nullable();
                $table->dateTime('created_at');
            });
        
        } else {

            Schema::table('email_lists_recipients', function (Blueprint $table) {

                if (! Schema::hasColumn('email_lists_recipients', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('email_lists_recipients', 'list_id')) 
                    $table->integer('list_id');
                
                if (! Schema::hasColumn('email_lists_recipients', 'email')) 
                    $table->string('email', 150);

                if (! Schema::hasColumn('email_lists_recipients', 'name')) 
                    $table->string('name', 100)->nullable();

                if (! Schema::hasColumn('email_lists_recipients', 'created_at')) 
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
        Schema::dropIfExists('email_lists_recipients');
    }
}
