<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocksGroupsContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('blocks_groups_content')) {

            Schema::create('blocks_groups_content', function (Blueprint $table) {
                $table->id();
                $table->integer('group_id');
                $table->string('file', 250);
                $table->text('content')->nullable();
                $table->tinyInteger('active')->default(0);
                $table->smallInteger('position')->default(0);
            });
        
        } else {

            Schema::table('blocks_groups_content', function (Blueprint $table) {

                if (! Schema::hasColumn('blocks_groups_content', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('blocks_groups_content', 'group_id')) 
                    $table->integer('group_id');
                
                if (! Schema::hasColumn('blocks_groups_content', 'file')) 
                    $table->string('file', 250);

                if (! Schema::hasColumn('blocks_groups_content', 'content')) 
                    $table->text('content')->nullable();

                if (! Schema::hasColumn('blocks_groups_content', 'active')) 
                    $table->tinyInteger('active')->default(0);

                if (! Schema::hasColumn('blocks_groups_content', 'position')) 
                    $table->smallInteger('position')->default(0);

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
        Schema::dropIfExists('blocks_groups_content');
    }
}
