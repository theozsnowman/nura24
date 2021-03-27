<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('tickets_responses')) {

            Schema::create('tickets_responses', function (Blueprint $table) {
                $table->id();
                $table->integer('ticket_id');
                $table->dateTime('created_at');
                $table->integer('user_id');
                $table->text('message');
                $table->string('file', 250)->nullable();
                $table->tinyInteger('important_for_operator')->default(0);
                $table->tinyInteger('important_for_client')->default(0);
            });
        
        } else {

            Schema::table('tickets_responses', function (Blueprint $table) {

                if (! Schema::hasColumn('tickets_responses', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('tickets_responses', 'ticket_id')) 
                    $table->integer('ticket_id');
                
                if (! Schema::hasColumn('tickets_responses', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('tickets_responses', 'user_id')) 
                    $table->integer('user_id');
                
                if (! Schema::hasColumn('tickets_responses', 'message')) 
                    $table->text('message');
                
                if (! Schema::hasColumn('tickets_responses', 'file')) 
                    $table->string('file', 250)->nullable();
                
                if (! Schema::hasColumn('tickets_responses', 'important_for_operator')) 
                    $table->tinyInteger('important_for_operator')->default(0);
                
                if (! Schema::hasColumn('tickets_responses', 'important_for_client')) 
                    $table->tinyInteger('important_for_client')->default(0);                    
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
        Schema::dropIfExists('tickets_responses');
    }
}
