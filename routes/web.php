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
Route::get('/'.config('nura.faq_slug'), 'Frontend\FAQController@index')->name('faq');

// Docs
Route::get('/'.config('nura.docs_slug'), 'Frontend\DocsController@index')->name('docs');
Route::get('/'.config('nura.docs_slug').'/'.config('nura.docs_search_slug'), 'Frontend\DocsController@search')->name('docs.search');
Route::get('/'.config('nura.docs_slug').'/{slug}', 'Frontend\DocsController@categ')->where(['slug' => '[a-z0-9_-]+'])->name('docs.categ');    

// Downloads
Route::get('/'.config('nura.downloads_slug'), 'Frontend\DownloadsController@index')->name('downloads');
Route::get('/'.config('nura.downloads_slug').'/get/{hash}', 'Frontend\DownloadsController@get')->name('download.get')->where(['hash' => '[a-zA-Z0-9_-]+']);
Route::get('/'.config('nura.downloads_slug').'/{slug}', 'Frontend\DownloadsController@show')->name('download')->where(['slug' => '[a-z0-9_-]+']);

// eCommerce
Route::get('/'.config('nura.cart_slug').'/'.config('nura.cart_search_slug'), 'Frontend\CartController@search')->name('cart.search');
Route::get('/'.config('nura.cart_slug').'/{categ_slug}/{slug}', 'Frontend\CartController@product')->name('cart.product')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
Route::get('/'.config('nura.cart_slug'), 'Frontend\CartController@index')->name('cart');
Route::get('/'.config('nura.cart_slug').'/{slug}', 'Frontend\CartController@categ')->name('cart.categ')->where(['slug' => '[a-z0-9_-]+']);

// Profile
Route::get('/'.config('nura.profile_slug').'/{id}/{slug}', 'Frontend\ProfileController@index')->name('profile')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);
        
// contact page
Route::get('/'.config('nura.contact_slug'), 'Frontend\ContactController@index')->name('contact');
Route::post('/'.config('nura.contact_slug'), 'Frontend\ContactController@send');  
Route::post('/', 'Frontend\ContactController@send')->name('homepage.contact');  

         
// Blog routes
Route::get('/'.config('nura.posts_slug').'/'.config('nura.posts_search_slug'), 'Frontend\PostsController@search')->name('posts.search');
Route::get('/'.config('nura.posts_slug').'/'.config('nura.posts_tag_slug').'/{slug}', 'Frontend\PostsController@tag')->name('posts.tag')->where(['slug' => '[a-z0-9_-]+']);
Route::get('/'.config('nura.post_slug').'/{categ_slug}/{slug}', 'Frontend\PostsController@post')->name('post')->where(['categ_slug' => '[a-z0-9_-]{3,}+', 'slug' => '[a-z0-9_-]+']); // categ_slug - minimum length 3 (to avoid errors related to lang prefix)
Route::get('/'.config('nura.post_slug').'/{categ_slug}/{slug}/like', 'Frontend\PostsController@like')->name('post.like')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
Route::post('/'.config('nura.post_slug').'/{categ_slug}/{slug}/comment', 'Frontend\PostsController@comment')->name('post.comment')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);           
Route::get('/'.config('nura.posts_slug'), 'Frontend\PostsController@index')->name('posts');
Route::get('/'.config('nura.posts_slug').'/{slug}', 'Frontend\PostsController@categ')->name('posts.categ')->where(['slug' => '[a-z0-9_-]+']);

// Forum routes
Route::get('/'.config('nura.forum_slug').'/create-topic', 'Frontend\ForumController@create_topic')->name('forum.topic.create');
Route::post('/'.config('nura.forum_slug').'/create-topic', 'Frontend\ForumController@store_topic')->name('forum.topic.store');
Route::post('/'.config('nura.forum_slug').'/{id}/{slug}', 'Frontend\ForumController@store_post')->name('forum.post.store')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);

Route::get('/'.config('nura.forum_slug'), 'Frontend\ForumController@index')->name('forum');
Route::get('/'.config('nura.forum_slug').'/{id}/{slug}', 'Frontend\ForumController@topic')->name('forum.topic')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);
Route::get('/'.config('nura.forum_slug').'/{topic_id}/{slug}#{post_id}', 'Frontend\ForumController@post')->name('forum.post')->where(['topic_id' => '[0-9]+', 'slug' => '[a-z0-9_-]+', 'post_id' => '[0-9]+']);
Route::get('/'.config('nura.forum_slug').'/{slug}', 'Frontend\ForumController@categ')->name('forum.categ')->where(['slug' => '[a-z0-9_-]+']);

Route::get('/'.config('nura.forum_slug').'/report/{type}/{id}', 'Frontend\ForumController@report')->name('forum.report')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
Route::post('/'.config('nura.forum_slug').'/report/{type}/{id}', 'Frontend\ForumController@create_report')->name('forum.report.create')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
Route::get('/'.config('nura.forum_slug').'/like/{type}/{id}', 'Frontend\ForumController@like')->name('forum.like')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
Route::get('/'.config('nura.forum_slug').'/best-answer/{id}', 'Frontend\ForumController@best_answer')->name('forum.best_answer')->where(['id' => '[0-9]+']);
Route::get('/'.config('nura.forum_slug').'/quote/{type}/{id}', 'Frontend\ForumController@quote')->name('forum.quote')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);

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
    Route::get('/'.config('nura.faq_slug'), 'Frontend\FAQController@index')->name('faq');

    // Docs
    Route::get('/'.config('nura.docs_slug'), 'Frontend\DocsController@index')->name('docs');
    Route::get('/'.config('nura.docs_slug').'/'.config('nura.docs_search_slug'), 'Frontend\DocsController@search')->name('docs.search');
    Route::get('/'.config('nura.docs_slug').'/{slug}', 'Frontend\DocsController@categ')->where(['slug' => '[a-z0-9_-]+'])->name('docs.categ');    
    
    // Downloads
    Route::get('/'.config('nura.downloads_slug'), 'Frontend\DownloadsController@index')->name('downloads');
    Route::get('/'.config('nura.downloads_slug').'/get/{hash}', 'Frontend\DownloadsController@get')->name('download.get')->where(['hash' => '[a-zA-Z0-9_-]+']);
    Route::get('/'.config('nura.downloads_slug').'/{slug}', 'Frontend\DownloadsController@show')->name('download')->where(['slug' => '[a-z0-9_-]+']);

    // eCommerce
    Route::get('/'.config('nura.cart_slug').'/'.config('nura.cart_search_slug'), 'Frontend\CartController@search')->name('cart.search');
    Route::get('/'.config('nura.cart_slug').'/{categ_slug}/{slug}', 'Frontend\CartController@product')->name('cart.product')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
    Route::get('/'.config('nura.cart_slug'), 'Frontend\CartController@index')->name('cart');
    Route::get('/'.config('nura.cart_slug').'/{slug}', 'Frontend\CartController@categ')->name('cart.categ')->where(['slug' => '[a-z0-9_-]+']);

    // Profile
    Route::get('/'.config('nura.profile_slug').'/{id}/{slug}', 'Frontend\ProfileController@index')->name('profile')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);
            
    // contact page
    Route::get('/'.config('nura.contact_slug'), 'Frontend\ContactController@index')->name('contact');
    Route::post('/'.config('nura.contact_slug'), 'Frontend\ContactController@send');              
    Route::post('/', 'Frontend\ContactController@send')->name('homepage.contact');  
    
    // Blog routes
    Route::get('/'.config('nura.posts_slug').'/'.config('nura.posts_search_slug'), 'Frontend\PostsController@search')->name('posts.search');
    Route::get('/'.config('nura.posts_slug').'/'.config('nura.posts_tag_slug').'/{slug}', 'Frontend\PostsController@tag')->name('posts.tag')->where(['slug' => '[a-z0-9_-]+']);
    Route::get('/'.config('nura.post_slug').'/{categ_slug}/{slug}', 'Frontend\PostsController@post')->name('post')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
    Route::get('/'.config('nura.post_slug').'/{categ_slug}/{slug}/like', 'Frontend\PostsController@like')->name('post.like')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);
    Route::post('/'.config('nura.post_slug').'/{categ_slug}/{slug}/comment', 'Frontend\PostsController@comment')->name('post.comment')->where(['categ_slug' => '[a-z0-9_-]+', 'slug' => '[a-z0-9_-]+']);           
    Route::get('/'.config('nura.posts_slug'), 'Frontend\PostsController@index')->name('posts');
    Route::get('/'.config('nura.posts_slug').'/{slug}', 'Frontend\PostsController@categ')->name('posts.categ')->where(['slug' => '[a-z0-9_-]+']);
   
    // Forum routes
    Route::get('/'.config('nura.forum_slug').'/create-topic', 'Frontend\ForumController@create_topic')->name('forum.topic.create');
    Route::post('/'.config('nura.forum_slug').'/create-topic', 'Frontend\ForumController@store_topic')->name('forum.topic.store');
    Route::post('/'.config('nura.forum_slug').'/{id}/{slug}', 'Frontend\ForumController@store_post')->name('forum.post.store')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);

    Route::get('/'.config('nura.forum_slug'), 'Frontend\ForumController@index')->name('forum');
    Route::get('/'.config('nura.forum_slug').'/{id}/{slug}', 'Frontend\ForumController@topic')->name('forum.topic')->where(['id' => '[0-9]+', 'slug' => '[a-z0-9_-]+']);
    Route::get('/'.config('nura.forum_slug').'/{topic_id}/{slug}#{post_id}', 'Frontend\ForumController@post')->name('forum.post')->where(['topic_id' => '[0-9]+', 'slug' => '[a-z0-9_-]+', 'post_id' => '[0-9]+']);
    Route::get('/'.config('nura.forum_slug').'/{slug}', 'Frontend\ForumController@categ')->name('forum.categ')->where(['slug' => '[a-z0-9_-]+']);

    Route::get('/'.config('nura.forum_slug').'/report/{type}/{id}', 'Frontend\ForumController@report')->name('forum.report')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
    Route::post('/'.config('nura.forum_slug').'/report/{type}/{id}', 'Frontend\ForumController@create_report')->name('forum.report.create')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
    Route::get('/'.config('nura.forum_slug').'/like/{type}/{id}', 'Frontend\ForumController@like')->name('forum.like')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);
    Route::get('/'.config('nura.forum_slug').'/best-answer/{id}', 'Frontend\ForumController@best_answer')->name('forum.best_answer')->where(['id' => '[0-9]+']);
    Route::get('/'.config('nura.forum_slug').'/quote/{type}/{id}', 'Frontend\ForumController@quote')->name('forum.quote')->where(['type' => '[a-z0-9_-]+', 'id' => '[0-9]+']);

    // static page
    Route::get('/{parent_slug}/{slug}', 'Frontend\PageController@index')->name('child_page')->where(['parent_slug' => '[a-z0-9_-]{3,}+', 'slug' => '[a-z0-9_-]+']); // if page is a child of a parent page
    Route::get('/{slug}', 'Frontend\PageController@index')->name('page')->where(['slug' => '[a-z0-9_-]+']);

});
