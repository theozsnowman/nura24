<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('users_permissions')) {

            Schema::create('users_permissions', function (Blueprint $table) {
                    $table->id();
                    $table->integer('module_id');
                    $table->smallInteger('permission_id');
                    $table->integer('user_id');
                });
        
            } else {

            Schema::table('users_permissions', function (Blueprint $table) {
                if (! Schema::hasColumn('users_permissions', 'id')) 
                    $table->id();

                if (! Schema::hasColumn('users_permissions', 'module_id')) 
                    $table->integer('module_id');
                    
                if (! Schema::hasColumn('users_permissions', 'permission_id')) 
                    $table->smallInteger('permission_id');
                    
                if (! Schema::hasColumn('users_permissions', 'user_id')) 
                    $table->integer('user_id');
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
        Schema::dropIfExists('users_permissions');
    }
}
