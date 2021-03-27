<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('tickets')) {

            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->integer('department_id')->nullable();
                $table->string('code', 50)->unique('code');
                $table->integer('product_id')->nullable();
                $table->integer('order_id')->nullable();
                $table->string('subject', 250);
                $table->text('message')->nullable();
                $table->dateTime('created_at');
                $table->integer('user_id')->nullable();
                $table->integer('locked_operator_user_id')->nullable();
                $table->tinyInteger('priority');
                $table->string('last_response', 25)->nullable();
                $table->string('file', 250)->nullable();
                $table->dateTime('closed_at')->nullable();
                $table->integer('closed_by_user_id')->nullable();
                $table->text('internal_info')->nullable();
            });
        
        } else {

            Schema::table('tickets', function (Blueprint $table) {

                if (! Schema::hasColumn('tickets', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('tickets', 'department_id')) 
                    $table->integer('department_id')->nullable();
                
                if (! Schema::hasColumn('tickets', 'code')) 
                    $table->string('code', 50)->unique('code');
                
                if (! Schema::hasColumn('tickets', 'product_id')) 
                    $table->integer('product_id')->nullable();
                
                if (! Schema::hasColumn('tickets', 'order_id')) 
                    $table->integer('order_id')->nullable();
                
                if (! Schema::hasColumn('tickets', 'subject'))                     
                    $table->string('subject', 250);
                
                if (! Schema::hasColumn('tickets', 'message')) 
                    $table->text('message')->nullable();
                
                if (! Schema::hasColumn('tickets', 'created_at')) 
                    $table->dateTime('created_at');
                
                if (! Schema::hasColumn('tickets', 'user_id')) 
                    $table->integer('user_id')->nullable();
                
                if (! Schema::hasColumn('tickets', 'locked_operator_user_id')) 
                    $table->integer('locked_operator_user_id')->nullable();
                
                if (! Schema::hasColumn('tickets', 'priority')) 
                    $table->tinyInteger('priority');
                
                if (! Schema::hasColumn('tickets', 'last_response'))                     
                    $table->string('last_response', 25)->nullable();
                
                if (! Schema::hasColumn('tickets', 'file')) 
                    $table->string('file', 250)->nullable();
                
                if (! Schema::hasColumn('tickets', 'closed_at')) 
                    $table->dateTime('closed_at')->nullable();
                
                if (! Schema::hasColumn('tickets', 'closed_by_user_id')) 
                    $table->integer('closed_by_user_id')->nullable();
                
                if (! Schema::hasColumn('tickets', 'internal_info')) 
                    $table->text('internal_info')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
