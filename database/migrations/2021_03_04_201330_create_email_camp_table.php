<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailCampTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('email_camp')) {

            Schema::create('email_camp', function (Blueprint $table) {
                $table->id();
                $table->string('title', 250);
                $table->text('description')->nullable();
                $table->text('subject');
                $table->mediumText('content')->nullable();
                $table->dateTime('created_at');
                $table->dateTime('sent_at')->nullable();
            });
        
        } else {

            Schema::table('email_camp', function (Blueprint $table) {

                if (! Schema::hasColumn('email_camp', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('email_camp', 'title')) 
                    $table->string('title', 250);
                
                if (! Schema::hasColumn('email_camp', 'description')) 
                    $table->text('description')->nullable();
                
                if (! Schema::hasColumn('email_camp', 'subject')) 
                    $table->text('subject');
                
                if (! Schema::hasColumn('email_camp', 'content')) 
                    $table->mediumText('content')->nullable();
                
                if (! Schema::hasColumn('email_camp', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('email_camp', 'sent_at')) 
                    $table->dateTime('sent_at')->nullable();

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
        Schema::dropIfExists('email_camp');
    }
}
