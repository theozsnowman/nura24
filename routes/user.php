<?php
/*
|--------------------------------------------------------------------------
| Registered Users Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your registerd users area. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


 Route::group(['prefix' => 'login/user'], function($lang) {     

    // profile
    Route::get('/profile', 'User\UserController@profile')->name('user.profile')->middleware('verified');
    Route::post('/profile', 'User\UserController@update_profile')->middleware('verified');
    Route::get('/profile/delete-avatar', 'User\UserController@delete_avatar')->middleware('verified')->name('user.profile.delete_avatar');

    // cart
    Route::get('/cart', 'User\CartController@cart')->name('cart.basket')->middleware('verified');
    Route::post('/cart/add/{id}', 'User\CartController@cart_add')->name('cart.add')->where(['id' => '[0-9]+'])->middleware('verified');
    Route::delete('/cart/delete/{id}', 'User\CartController@cart_delete')->where(['id' => '[0-9]+'])->name('shopping_cart.delete')->middleware('verified');
    Route::post('/store-order', 'User\CartController@store_order')->name('cart.store_order')->middleware('verified');
    Route::post('/checkout', 'User\CartController@checkout')->name('cart.checkout')->middleware('verified');
    
    Route::get('/orders', 'User\CartController@orders')->name('user.orders');
    Route::get('/order/{code}', 'User\CartController@order')->where(['code' => '[0-9a-zA-Z]+'])->name('user.orders.show');
    Route::delete('/order/{code}', 'User\CartController@destroy_order')->where(['code' => '[0-9a-zA-Z]+']);

    // downloads
    Route::get('/downloads', 'User\DownloadsController@index')->name('user.downloads')->middleware('verified');
    Route::get('/download/{id}', 'User\DownloadsController@download')->middleware('verified')->name('user.download')->where(['id' => '[0-9]+']);
      
    // Suport tickets routes
    Route::post('/tickets/reply/{code}', 'User\TicketsController@reply')->name('user.tickets.reply')->where('id', '[a-zA-Z0-9]+');

    Route::resource('/tickets', 'User\TicketsController')
        ->names(['index' => 'user.tickets', 'create' => 'user.tickets.create', 'show' => 'user.tickets.show'])
        ->parameters(['tickets' => 'code']);    
   
    Route::get('/tickets/{code}/mark_important_response/{response_id}', 'User\TicketsController@mark_important_response')
        ->name('user.tickets.mark_important_response')->where(['code'=>'[a-zA-Z0-9]+', 'response_id'=>'[0-9]+']);

    Route::get('/tickets/{code}/unmark_important_response/{response_id}', 'User\TicketsController@unmark_important_response')
        ->name('user.tickets.unmark_important_response')->where(['code'=>'[a-zA-Z0-9]+', 'response_id'=>'[0-9]+']); 

    Route::get('/tickets/{code}/close', 'User\TicketsController@close')->name('user.tickets.close')->where('code', '[a-zA-Z0-9]+');

    Route::get('/tickets/{code}/open', 'User\TicketsController@open')->name('user.tickets.open')->where('code', '[a-zA-Z0-9]+');     

    // forum
    Route::get('/forum/topics', 'User\ForumController@topics')->name('user.forum.topics')->middleware('verified');
    Route::get('/forum/posts', 'User\ForumController@posts')->name('user.forum.posts')->middleware('verified');
    Route::get('/forum/warnings', 'User\ForumController@warnings')->name('user.forum.warnings')->middleware('verified');
    Route::get('/forum/restrictions', 'User\ForumController@restrictions')->name('user.forum.restrictions')->middleware('verified');
    Route::get('/forum/config', 'User\ForumController@config')->name('user.forum.config')->middleware('verified');
    Route::post('/forum/config', 'User\ForumController@update_config')->name('user.forum.config')->middleware('verified');

});


// ROUTES FOR ADDITIONAL LANGUAGES
Route::group([
    'prefix' => '{lang?}/login/user', 
    'where' => ['lang' => '[a-zA-Z]{2}']
    ], function($lang) {      

    // profile
    Route::get('/profile', 'User\UserController@profile')->name('user.profile')->middleware('verified');
    Route::post('/profile', 'User\UserController@update_profile')->middleware('verified');
    Route::get('/profile/delete-avatar', 'User\UserController@delete_avatar')->middleware('verified')->name('user.profile.delete_avatar');

    // cart
    Route::get('/cart', 'User\CartController@cart')->name('cart.basket')->middleware('verified');
    Route::post('/cart/add/{id}', 'User\CartController@cart_add')->name('cart.add')->where(['id' => '[0-9]+'])->middleware('verified');
    Route::delete('/cart/delete/{id}', 'User\CartController@cart_delete')->where(['id' => '[0-9]+'])->name('shopping_cart.delete')->middleware('verified');
    Route::post('/store-order', 'User\CartController@store_order')->name('cart.store_order')->middleware('verified');
    Route::post('/checkout', 'User\CartController@checkout')->name('cart.checkout')->middleware('verified');
    
    Route::get('/orders', 'User\CartController@orders')->name('user.orders');
    Route::get('/order/{code}', 'User\CartController@order')->where(['code' => '[0-9a-zA-Z]+'])->name('user.orders.show');
    Route::delete('/order/{code}', 'User\CartController@destroy_order')->where(['code' => '[0-9a-zA-Z]+']);

    // downloads
    Route::get('/downloads', 'User\DownloadsController@index')->name('user.downloads')->middleware('verified');
    Route::get('/download/{id}', 'User\DownloadsController@download')->middleware('verified')->name('user.download')->where(['id' => '[0-9]+']);

    // Suport tickets routes
    Route::post('/tickets/reply/{code}', 'User\TicketsController@reply')->name('user.tickets.reply')->where('id', '[a-zA-Z0-9]+');

    Route::resource('/tickets', 'User\TicketsController')
        ->names(['index' => 'user.tickets', 'create' => 'user.tickets.create', 'show' => 'user.tickets.show'])
        ->parameters(['tickets' => 'code']);    
   
    Route::get('/tickets/{code}/mark_important_response/{response_id}', 'User\TicketsController@mark_important_response')
        ->name('user.tickets.mark_important_response')->where(['code'=>'[a-zA-Z0-9]+', 'response_id'=>'[0-9]+']);

    Route::get('/tickets/{code}/unmark_important_response/{response_id}', 'User\TicketsController@unmark_important_response')
        ->name('user.tickets.unmark_important_response')->where(['code'=>'[a-zA-Z0-9]+', 'response_id'=>'[0-9]+']); 

    Route::get('/tickets/{code}/close', 'User\TicketsController@close')->name('user.tickets.close')->where('code', '[a-zA-Z0-9]+');

    Route::get('/tickets/{code}/open', 'User\TicketsController@open')->name('user.tickets.open')->where('code', '[a-zA-Z0-9]+');     

    // forum
    Route::get('/forum/topics', 'User\ForumController@topics')->name('user.forum.topics')->middleware('verified');
    Route::get('/forum/posts', 'User\ForumController@posts')->name('user.forum.posts')->middleware('verified');
    Route::get('/forum/warnings', 'User\ForumController@warnings')->name('user.forum.warnings')->middleware('verified');
    Route::get('/forum/restrictions', 'User\ForumController@restrictions')->name('user.forum.restrictions')->middleware('verified');
    Route::get('/forum/config', 'User\ForumController@config')->name('user.forum.config')->middleware('verified');
    Route::post('/forum/config', 'User\ForumController@update_config')->name('user.forum.config')->middleware('verified');
    
});
