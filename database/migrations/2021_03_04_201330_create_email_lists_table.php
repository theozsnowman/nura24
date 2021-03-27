<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('email_lists')) {

            Schema::create('email_lists', function (Blueprint $table) {
                $table->id();
                $table->string('title', 250);
                $table->text('description')->nullable();
                $table->dateTime('created_at');
            });
        
        } else {

            Schema::table('email_lists', function (Blueprint $table) {

                if (! Schema::hasColumn('email_lists', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('email_lists', 'title')) 
                    $table->string('title', 250);
                
                if (! Schema::hasColumn('email_lists', 'description')) 
                    $table->text('description')->nullable();
                
                if (! Schema::hasColumn('email_lists', 'created_at')) 
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
        Schema::dropIfExists('email_lists');
    }
}
