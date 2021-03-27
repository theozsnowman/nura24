<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailCampRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('email_camp_recipients')) {

            Schema::create('email_camp_recipients', function (Blueprint $table) {
                $table->id();
                $table->integer('campaign_id');
                $table->dateTime('sent_at')->nullable();
                $table->string('email', 150);
                $table->string('name', 100)->nullable();
                $table->dateTime('created_at');
            });
        
        } else {

            Schema::table('email_camp_recipients', function (Blueprint $table) {

                if (! Schema::hasColumn('email_camp_recipients', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('email_camp_recipients', 'campaign_id')) 
                    $table->integer('campaign_id');
            
                if (! Schema::hasColumn('email_camp_recipients', 'sent_at')) 
                    $table->dateTime('sent_at')->nullable();
            
                if (! Schema::hasColumn('email_camp_recipients', 'email')) 
                    $table->string('email', 150);
            
                if (! Schema::hasColumn('email_camp_recipients', 'name')) 
                    $table->string('name', 100)->nullable();
            
                if (! Schema::hasColumn('email_camp_recipients', 'created_at')) 
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
        Schema::dropIfExists('email_camp_recipients');
    }
}
