<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsInternalInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('tickets_internal_info')) {

            Schema::create('tickets_internal_info', function (Blueprint $table) {
                $table->id();
                $table->integer('ticket_id');
                $table->integer('user_id');
                $table->text('message')->nullable();
                $table->string('file', 250)->nullable();
                $table->dateTime('created_at');
            });
        
        } else {

            Schema::table('tickets_internal_info', function (Blueprint $table) {

                if (! Schema::hasColumn('tickets_internal_info', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('tickets_internal_info', 'ticket_id')) 
                    $table->integer('ticket_id');
                
                if (! Schema::hasColumn('tickets_internal_info', 'user_id')) 
                    $table->integer('user_id');
                
                if (! Schema::hasColumn('tickets_internal_info', 'message')) 
                    $table->text('message')->nullable();
                
                if (! Schema::hasColumn('tickets_internal_info', 'file')) 
                    $table->string('file', 250)->nullable();
                
                if (! Schema::hasColumn('tickets_internal_info', 'created_at')) 
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
        Schema::dropIfExists('tickets_internal_info');
    }
}
