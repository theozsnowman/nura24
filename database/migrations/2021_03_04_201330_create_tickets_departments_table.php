<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('tickets_departments')) {

            Schema::create('tickets_departments', function (Blueprint $table) {
                $table->id();
                $table->string('title', 150);
                $table->text('description')->nullable();
                $table->tinyInteger('active')->default(0);
                $table->tinyInteger('hidden')->default(0);
            });
        
        } else {

            Schema::table('tickets_departments', function (Blueprint $table) {

                if (! Schema::hasColumn('tickets_departments', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('tickets_departments', 'title')) 
                    $table->string('title', 150);
                
                if (! Schema::hasColumn('tickets_departments', 'description')) 
                    $table->text('description')->nullable();
                
                if (! Schema::hasColumn('tickets_departments', 'active')) 
                    $table->tinyInteger('active')->default(0);
                
                if (! Schema::hasColumn('tickets_departments', 'hidden')) 
                    $table->tinyInteger('hidden')->default(0);

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
        Schema::dropIfExists('tickets_departments');
    }
}
