<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailCampDenyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('email_camp_deny')) {

            Schema::create('email_camp_deny', function (Blueprint $table) {
                $table->id();
                $table->string('email', 150);
                $table->dateTime('created_at');
                $table->string('reason', 100)->nullable();
            });
        
        } else {

            Schema::table('email_camp_deny', function (Blueprint $table) {

                if (! Schema::hasColumn('email_camp_deny', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('email_camp_deny', 'email')) 
                    $table->string('email', 150);
                
                if (! Schema::hasColumn('email_camp_deny', 'created_at')) 
                    $table->dateTime('created_at');

                if (! Schema::hasColumn('email_camp_deny', 'reason')) 
                    $table->string('reason', 100)->nullable();

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
        Schema::dropIfExists('email_camp_deny');
    }
}
