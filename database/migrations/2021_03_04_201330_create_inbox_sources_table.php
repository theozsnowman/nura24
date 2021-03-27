<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInboxSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('inbox_sources')) {

            Schema::create('inbox_sources', function (Blueprint $table) {
                $table->id();
                $table->string('source', 50);
                $table->mediumText('details')->nullable();
                $table->tinyInteger('active')->default(0);
            });
        
        } else {

            Schema::table('inbox_sources', function (Blueprint $table) {

                if (! Schema::hasColumn('inbox_sources', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('inbox_sources', 'source')) 
                    $table->string('source', 50);
                
                if (! Schema::hasColumn('inbox_sources', 'details')) 
                    $table->mediumText('details')->nullable();
                
                if (! Schema::hasColumn('inbox_sources', 'active')) 
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
        Schema::dropIfExists('inbox_sources');
    }
}
