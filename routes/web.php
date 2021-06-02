<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
  

// DEFAULT ROUTES FOR DEFAULT LANGUAGE
Auth::routes(['verify' => true]);

Route::get('/', 'Frontend\HomeController@index')->name('homepage');
Route::get('/{lang}', 'Frontend\HomeController@index')->name('homepage')->where(['lang' => '[a-zA-Z]{2}']);

Route::get('/login/admin', 'Admin\DashboardController@index')->name('admin')->middleware('verified');
Route::get('/login/user', 'User\UserController@profile')->name('user')->middleware('verified');


// Gateways
Route::get('/checkout-gateways/paypal', 'Gateways\PayPal@verifyIPN')->name('gateway.paypal'); 

 
// FAQ
Route::get('/'.config('permalinks.faq_permalink'), 'Frontend\FAQController@index')->name('faq');

// Docs
Route::get('/'.config('permalinks.docs_permalink'), 'Frontend\DocsController@index')->name('docs');
Route::get('/'.config('permalinks.docs_permalink').'/'.config('permalinks.docs_search_permalink'), 'Frontend\DocsController@search')->name('docs.search');
Route::get('/'.config('permalinks.docs_permalink').'/{slug}', 'Frontend\DocsController@categ')->where(['slug' => '[a-z0-9_-]+'])->name('docs.categ');    

// Downloads
Route::get('/'.config('permalinks.downloads_permalink'), 'Frontend\DownloadsController@index')->name('downloads');
Route::get('/'.config('permalinks.downloads_permalink').'/get/{hash}', 'Frontend\DownloadsController@get')->name('download.get')->where(['hash' => '[a-zA-Z0-9_-]+']);
Route::get('/'.config('permalinks.downloads_permalink').'/{slug}', 'Frontend\DownloadsController@show')->name('download')->where(['slug' => '[a-z0-9_-]+']);

// eCommerce
Route::get('/'.config('permalinks.cart_permalink').'/'.config('permalinks.cart_search_permalink'), 'Frontend\CartController@search')->name('cart.search');
Route::get('/'.config('permalinks.cart_permalink').'/{categ_slug}/{slug}', 'Frontend\CartController@product')->name('cart.product')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
Route::get('/'.config('permalinks.cart_permalink'), 'Frontend\CartController@index')->name('cart');
Route::get('/'.config('permalinks.cart_permalink').'/{slug}', 'Frontend\CartController@categ')->name('cart.categ')->where(['slug' => '[a-z0-9_-]+']);

// Profile
Route::get('/'.config('permalinks.profile_permalink').'/{id}/{slug}', 'Frontend\ProfileController@index')->name('profile')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);
        
// contact page
Route::get('/'.config('permalinks.contact_permalink'), 'Frontend\ContactController@index')->name('contact');
Route::post('/'.config('permalinks.contact_permalink'), 'Frontend\ContactController@send');  
Route::post('/', 'Frontend\ContactController@send')->name('homepage.contact');  

         
// Blog routes
Route::get('/'.config('permalinks.posts_permalink').'/'.config('permalinks.posts_search_permalink'), 'Frontend\PostsController@search')->name('posts.search');
Route::get('/'.config('permalinks.posts_permalink').'/'.config('permalinks.posts_tag_permalink').'/{slug}', 'Frontend\PostsController@tag')->name('posts.tag')->where(['slug' => '[a-z0-9_-]+']);
Route::get('/'.config('permalinks.post_permalink').'/{categ_slug}/{slug}', 'Frontend\PostsController@post')->name('post')->where(['categ_slug' => '[a-z0-9_-]{3,}+', 'slug' => '[a-z0-9_-]+']); // categ_slug - minimum length 3 (to avoid errors related to lang prefix)
Route::get('/'.config('permalinks.post_permalink').'/{categ_slug}/{slug}/like', 'Frontend\PostsController@like')->name('post.like')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
Route::post('/'.config('permalinks.post_permalink').'/{categ_slug}/{slug}/comment', 'Frontend\PostsController@comment')->name('post.comment')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);           
Route::get('/'.config('permalinks.posts_permalink'), 'Frontend\PostsController@index')->name('posts');
Route::get('/'.config('permalinks.posts_permalink').'/{slug}', 'Frontend\PostsController@categ')->name('posts.categ')->where(['slug' => '[a-z0-9_-]+']);

// Forum routes
Route::get('/'.config('permalinks.forum_permalink').'/create-topic', 'Frontend\ForumController@create_topic')->name('forum.topic.create');
Route::post('/'.config('permalinks.forum_permalink').'/create-topic', 'Frontend\ForumController@store_topic')->name('forum.topic.store');
Route::post('/'.config('permalinks.forum_permalink').'/{id}/{slug}', 'Frontend\ForumController@store_post')->name('forum.post.store')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);

Route::get('/'.config('permalinks.forum_permalink'), 'Frontend\ForumController@index')->name('forum');
Route::get('/'.config('permalinks.forum_permalink').'/{id}/{slug}', 'Frontend\ForumController@topic')->name('forum.topic')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);
Route::get('/'.config('permalinks.forum_permalink').'/{topic_id}/{slug}#{post_id}', 'Frontend\ForumController@post')->name('forum.post')->where(['topic_id' => '[0-9]+', 'slug' => '[a-z0-9_-]+', 'post_id' => '[0-9]+']);
Route::get('/'.config('permalinks.forum_permalink').'/{slug}', 'Frontend\ForumController@categ')->name('forum.categ')->where(['slug' => '[a-z0-9_-]+']);

Route::get('/'.config('permalinks.forum_permalink').'/report/{type}/{id}', 'Frontend\ForumController@report')->name('forum.report')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
Route::post('/'.config('permalinks.forum_permalink').'/report/{type}/{id}', 'Frontend\ForumController@create_report')->name('forum.report.create')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
Route::get('/'.config('permalinks.forum_permalink').'/like/{type}/{id}', 'Frontend\ForumController@like')->name('forum.like')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
Route::get('/'.config('permalinks.forum_permalink').'/best-answer/{id}', 'Frontend\ForumController@best_answer')->name('forum.best_answer')->where(['id' => '[0-9]+']);
Route::get('/'.config('permalinks.forum_permalink').'/quote/{type}/{id}', 'Frontend\ForumController@quote')->name('forum.quote')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);

// static page
Route::get('/{parent_slug}/{slug}', 'Frontend\PageController@index')->name('child_page')->where(['parent_slug' => '[a-z0-9_-]{3,}+', 'slug' => '[a-z0-9_-]+']); // if page is a child of a parent page
Route::get('/{slug}', 'Frontend\PageController@index')->name('page')->where(['slug' => '[a-z0-9_-]+']);


// ROUTES FOR ADDITIONAL LANGUAGES
Route::group([
    'prefix' => '{lang?}', 
    'where' => ['lang' => '[a-zA-Z]{2}']
    ], function($lang) {

    Auth::routes(['verify' => true, 'lang' => $lang]);
    
    Route::get('/', 'Frontend\HomeController@index')->name('homepage');
    Route::get('/login/user', 'User\UserController@profile')->name('user')->middleware('verified');
    
    // FAQ
    Route::get('/'.config('permalinks.faq_permalink'), 'Frontend\FAQController@index')->name('faq');

    // Docs
    Route::get('/'.config('permalinks.docs_permalink'), 'Frontend\DocsController@index')->name('docs');
    Route::get('/'.config('permalinks.docs_permalink').'/'.config('permalinks.docs_search_permalink'), 'Frontend\DocsController@search')->name('docs.search');
    Route::get('/'.config('permalinks.docs_permalink').'/{slug}', 'Frontend\DocsController@categ')->where(['slug' => '[a-z0-9_-]+'])->name('docs.categ');    
    
    // Downloads
    Route::get('/'.config('permalinks.downloads_permalink'), 'Frontend\DownloadsController@index')->name('downloads');
    Route::get('/'.config('permalinks.downloads_permalink').'/get/{hash}', 'Frontend\DownloadsController@get')->name('download.get')->where(['hash' => '[a-zA-Z0-9_-]+']);
    Route::get('/'.config('permalinks.downloads_permalink').'/{slug}', 'Frontend\DownloadsController@show')->name('download')->where(['slug' => '[a-z0-9_-]+']);

    // eCommerce
    Route::get('/'.config('permalinks.cart_permalink').'/'.config('permalinks.cart_search_permalink'), 'Frontend\CartController@search')->name('cart.search');
    Route::get('/'.config('permalinks.cart_permalink').'/{categ_slug}/{slug}', 'Frontend\CartController@product')->name('cart.product')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
    Route::get('/'.config('permalinks.cart_permalink'), 'Frontend\CartController@index')->name('cart');
    Route::get('/'.config('permalinks.cart_permalink').'/{slug}', 'Frontend\CartController@categ')->name('cart.categ')->where(['slug' => '[a-z0-9_-]+']);

    // Profile
    Route::get('/'.config('permalinks.profile_permalink').'/{id}/{slug}', 'Frontend\ProfileController@index')->name('profile')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);
            
    // contact page
    Route::get('/'.config('permalinks.contact_permalink'), 'Frontend\ContactController@index')->name('contact');
    Route::post('/'.config('permalinks.contact_permalink'), 'Frontend\ContactController@send');              
    Route::post('/', 'Frontend\ContactController@send')->name('homepage.contact');  
    
    // Blog routes
    Route::get('/'.config('permalinks.posts_permalink').'/'.config('permalinks.posts_search_permalink'), 'Frontend\PostsController@search')->name('posts.search');
    Route::get('/'.config('permalinks.posts_permalink').'/'.config('permalinks.posts_tag_permalink').'/{slug}', 'Frontend\PostsController@tag')->name('posts.tag')->where(['slug' => '[a-z0-9_-]+']);
    Route::get('/'.config('permalinks.post_permalink').'/{categ_slug}/{slug}', 'Frontend\PostsController@post')->name('post')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
    Route::get('/'.config('permalinks.post_permalink').'/{categ_slug}/{slug}/like', 'Frontend\PostsController@like')->name('post.like')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
    Route::post('/'.config('permalinks.post_permalink').'/{categ_slug}/{slug}/comment', 'Frontend\PostsController@comment')->name('post.comment')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);           
    Route::get('/'.config('permalinks.posts_permalink'), 'Frontend\PostsController@index')->name('posts');
    Route::get('/'.config('permalinks.posts_permalink').'/{slug}', 'Frontend\PostsController@categ')->name('posts.categ')->where(['slug' => '[a-z0-9_-]+']);
   
    // Forum routes
    Route::get('/'.config('permalinks.forum_permalink').'/create-topic', 'Frontend\ForumController@create_topic')->name('forum.topic.create');
    Route::post('/'.config('permalinks.forum_permalink').'/create-topic', 'Frontend\ForumController@store_topic')->name('forum.topic.store');
    Route::post('/'.config('permalinks.forum_permalink').'/{id}/{slug}', 'Frontend\ForumController@store_post')->name('forum.post.store')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);

    Route::get('/'.config('permalinks.forum_permalink'), 'Frontend\ForumController@index')->name('forum');
    Route::get('/'.config('permalinks.forum_permalink').'/{id}/{slug}', 'Frontend\ForumController@topic')->name('forum.topic')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);
    Route::get('/'.config('permalinks.forum__permalink').'/{topic_id}/{slug}#{post_id}', 'Frontend\ForumController@post')->name('forum.post')->where(['topic_id' => '[0-9]+', 'slug' => '[a-z0-9_-]+', 'post_id' => '[0-9]+']);
    Route::get('/'.config('permalinks.forum_permalink').'/{slug}', 'Frontend\ForumController@categ')->name('forum.categ')->where(['slug' => '[a-z0-9_-]+']);

    Route::get('/'.config('permalinks.forum_permalink').'/report/{type}/{id}', 'Frontend\ForumController@report')->name('forum.report')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
    Route::post('/'.config('permalinks.forum_permalink').'/report/{type}/{id}', 'Frontend\ForumController@create_report')->name('forum.report.create')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
    Route::get('/'.config('permalinks.forum_permalink').'/like/{type}/{id}', 'Frontend\ForumController@like')->name('forum.like')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
    Route::get('/'.config('permalinks.forum_permalink').'/best-answer/{id}', 'Frontend\ForumController@best_answer')->name('forum.best_answer')->where(['id' => '[0-9]+']);
    Route::get('/'.config('permalinks.forum_permalink').'/quote/{type}/{id}', 'Frontend\ForumController@quote')->name('forum.quote')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);

    // static page
    Route::get('/{parent_slug}/{slug}', 'Frontend\PageController@index')->name('child_page')->where(['parent_slug' => '[a-z0-9_-]{3,}+', 'slug' => '[a-z0-9_-]+']); // if page is a child of a parent page
    Route::get('/{slug}', 'Frontend\PageController@index')->name('page')->where(['slug' => '[a-z0-9_-]+']);

});
