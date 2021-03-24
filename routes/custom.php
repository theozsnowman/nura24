<?php
/*
|--------------------------------------------------------------------------
| Custom Routes - Global
|--------------------------------------------------------------------------
|
*/



/*
|--------------------------------------------------------------------------
| Custom Routes - Admin area
|--------------------------------------------------------------------------
|
| Here is where you can register custom routes for your application admin area
|
*/

Route::group(['prefix' => 'login/admin'], function() {
      
    Route::get('/nura24/cpanel', 'Admin\Custom\CPanelController@index')->name('admin.custom.cpanel'); 

    Route::resource('/nura24/licenses', 'Admin\Custom\LicensesController')
        ->names(['index' => 'admin.custom.licenses', 'create' => 'admin.custom.licenses.create', 'show' => 'admin.custom.licenses.show'])
        ->parameters(['licenses' => 'id']);      

});


/*
|--------------------------------------------------------------------------
| Custom Routes - Users area
|--------------------------------------------------------------------------
|
| Here is where you can register custom routes for your application users area
|
*/

Route::group(['prefix' => 'login/user'], function() {

    Route::resource('/domains', 'User\Custom\DomainsController')
        ->names(['index' => 'user.custom.domains', 'create' => 'user.custom.domains.create', 'show' => 'user.custom.domains.show'])
        ->parameters(['domains' => 'dom']);   

    Route::get('/{dom}/licenses', 'User\Custom\LicensesController@index')->name('user.custom.licenses')->where(['dom' => '[0-9a-zA-Z.]+']);
    Route::get('/{dom}/licenses/new', 'User\Custom\LicensesController@create')->name('user.custom.licenses.new')->where(['dom' => '[0-9a-zA-Z.]+']);
    Route::post('/{dom}/licenses/new', 'User\Custom\LicensesController@store')->where(['dom' => '[0-9a-zA-Z.]+']);

    Route::get('/{dom}/licenses/new/{plan_id}', 'User\Custom\LicensesController@create_step2')->where(['plan_id' => '[0-9]+', 'dom' => '[0-9a-zA-Z.]+']);
    Route::post('/{dom}/licenses/new/{plan_id}', 'User\Custom\LicensesController@store_step2')->where(['plan_id' => '[0-9]+', 'dom' => '[0-9a-zA-Z.]+'])->name('user.custom.licenses.new.step2');


});



/*
|--------------------------------------------------------------------------
| Custom Routes - Frontend area
|--------------------------------------------------------------------------
|
| Here is where you can register custom routes for your application frontend area
|
*/
