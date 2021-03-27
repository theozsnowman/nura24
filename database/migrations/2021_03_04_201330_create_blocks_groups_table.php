<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocksGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        if (! Schema::hasTable('blocks_groups')) {

            Schema::create('blocks_groups', function (Blueprint $table) {
                $table->id();
                $table->string('label', 100);
                $table->text('description')->nullable();
                $table->tinyInteger('active')->default(0);
            });
        
        } else {

            Schema::table('blocks_groups', function (Blueprint $table) {

                if (! Schema::hasColumn('blocks_groups', 'id')) 
                    $table->id();
                     
                if (! Schema::hasColumn('blocks_groups', 'label'))                      
                    $table->string('label', 100);
            
                if (! Schema::hasColumn('blocks_groups', 'description')) 
                    $table->text('description')->nullable();

                if (! Schema::hasColumn('blocks_groups', 'active')) 
                    $table->tinyInteger('active')->default(0);

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
        Schema::dropIfExists('blocks_groups');
    }
}
