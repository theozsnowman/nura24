<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('blocks')) {

            Schema::create('blocks', function (Blueprint $table) {
                $table->id();
                $table->string('label', 100);
                $table->text('description')->nullable();
                $table->tinyInteger('active')->default(0);
                $table->string('test');
            });

        } else {

            Schema::table('blocks', function (Blueprint $table) {

                if (! Schema::hasColumn('blocks', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('blocks', 'label'))                     
                    $table->string('label', 100);

                if (! Schema::hasColumn('blocks', 'description'))                     
                    $table->text('description')->nullable();

                if (! Schema::hasColumn('blocks', 'active'))                     
                    $table->tinyInteger('active')->default(0);

                if (! Schema::hasColumn('blocks', 'test'))                     
                    $table->string('test');

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
        Schema::dropIfExists('blocks');
    }
}
