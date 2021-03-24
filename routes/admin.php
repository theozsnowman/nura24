<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your admin application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
        'prefix' => 'login/admin', 
        ], function()
    {
          
    Route::get('/change-lang/{code}', 'Admin\ConfigController@update_backend_lang')->name('admin.change-lang')->where('code', '[0-9A-Za-z]+');

    /*
    |--------------------------------------------------------------------------
    | Accounts routes
    |--------------------------------------------------------------------------
    */                         

    Route::get('/accounts/permissions', 'Admin\AccountsController@permissions')->name('admin.accounts.permissions');
    Route::post('/accounts/permissions', 'Admin\AccountsController@update_permissions')->name('admin.accounts.permissions.update');
                  
    Route::resource('/accounts/tags', 'Admin\AccountsTagsController')
        ->names(['index' => 'admin.accounts.tags', 'create' => 'admin.accounts.tags.create', 'show' => 'admin.accounts.tags.show'])
        ->parameters(['tags' => 'id']);           

    Route::get('/accounts/{id}/tags', 'Admin\AccountsController@tags')->name('admin.account.tags')->where('id', '[0-9]+');
    Route::post('/accounts/{id}/tags', 'Admin\AccountsController@create_tag')->name('admin.account.tags.create')->where('id', '[0-9]+');
    Route::delete('/accounts/{id}/tags', 'Admin\AccountsController@delete_tag')->where('id', '[0-9]+');

    Route::get('/accounts/{id}/notes', 'Admin\AccountsController@notes')->name('admin.account.notes')->where('id', '[0-9]+');
    Route::post('/accounts/{id}/notes', 'Admin\AccountsController@create_note')->name('admin.account.notes.create')->where('id', '[0-9]+');
    Route::delete('/accounts/{id}/notes', 'Admin\AccountsController@delete_note')->where('id', '[0-9]+');

    Route::get('/accounts/{id}/orders', 'Admin\AccountsController@orders')->name('admin.account.orders')->where('id', '[0-9]+');    
    Route::get('/accounts/{id}/tickets', 'Admin\AccountsController@tickets')->name('admin.account.tickets')->where('id', '[0-9]+');

    Route::resource('/accounts', 'Admin\AccountsController')
        ->names(['index' => 'admin.accounts', 'create' => 'admin.accounts.create', 'show' => 'admin.accounts.show'])
        ->parameters(['accounts' => 'id']);           


    /*
    |--------------------------------------------------------------------------
    | Posts routes
    |--------------------------------------------------------------------------
    */       
    Route::resource('/posts/categ', 'Admin\PostsCategoriesController')
        ->names(['index' => 'admin.posts.categ', 'create' => 'admin.posts.categ.create', 'show' => 'admin.posts.categ.show'])
        ->parameters(['categ' => 'id']);

    Route::resource('/posts/likes', 'Admin\PostsLikesController')
        ->names(['index' => 'admin.posts.likes', 'show' => 'admin.posts.likes.show'])
        ->parameters(['likes' => 'id']);

    Route::resource('/posts/comments', 'Admin\PostsCommentsController')
        ->names(['index' => 'admin.posts.comments', 'show' => 'admin.posts.comments.show'])
        ->parameters(['comments' => 'id']);

    Route::get('/posts/{id}/delete-main-image', 'Admin\PostsController@delete_main_image')->name('admin.posts.delete_main_image')->where('id', '[0-9]+');

    Route::resource('/posts/images', 'Admin\PostsImagesController')
        ->names(['index' => 'admin.posts.images', 'show' => 'admin.posts.images.show'])
        ->parameters(['images' => 'id']);

    Route::resource('/posts', 'Admin\PostsController')
        ->names(['index' => 'admin.posts', 'create' => 'admin.posts.create', 'show' => 'admin.posts.show'])
        ->parameters(['posts' => 'id']);

    Route::get('/posts-config', 'Admin\PostsConfigController@index')->name('admin.posts.config');
    Route::post('/posts-config', 'Admin\PostsConfigController@update')->name('admin.posts.config.update');
   

    /*
    |--------------------------------------------------------------------------
    | Static pages routes
    |--------------------------------------------------------------------------
    */
    Route::resource('/pages', 'Admin\PagesController')
        ->names(['index' => 'admin.pages', 'create' => 'admin.pages.create', 'show' => 'admin.pages.show'])
        ->parameters(['pages' => 'id']);
    Route::get('/pages/{id}/images', 'Admin\PagesController@images')->name('admin.pages.images')->where('id', '[0-9]+');
    Route::post('/pages/{id}/images', 'Admin\PagesController@create_image')->name('admin.pages.images.create')->where('id', '[0-9]+');
    Route::delete('/pages/{id}/images', 'Admin\PagesController@delete_image')->name('admin.pages.images.delete')->where('id', '[0-9]+');
    Route::get('/pages/{id}/delete-main-image', 'Admin\PagesController@delete_main_image')->name('admin.pages.delete_main_image')->where('id', '[0-9]+');


    /*
    |--------------------------------------------------------------------------
    | Blocks routes
    |--------------------------------------------------------------------------
    */
    Route::resource('/blocks', 'Admin\BlocksController')
        ->names(['index' => 'admin.blocks', 'create' => 'admin.blocks.create', 'show' => 'admin.blocks.show'])
        ->parameters(['blocks' => 'id']);    
    Route::get('/blocks/{id}/delete-image', 'Admin\BlocksController@delete_image')->name('admin.blocks.delete_image')->where('id', '[0-9]+');


    /*
    |--------------------------------------------------------------------------
    | Galleries routes
    |--------------------------------------------------------------------------
    */
    Route::get('/blocks-groups/{id}/content', 'Admin\BlocksGroupsController@blocks')->name('admin.blocks.groups.content')->where('id', '[0-9]+');
    Route::get('/blocks-groups/{id}/content/create', 'Admin\BlocksGroupsController@create_block')->name('admin.blocks.groups.content.create')->where('id', '[0-9]+');
    Route::get('/blocks-groups/{id}/content/{block_id}/update', 'Admin\BlocksGroupsController@show_block')->name('admin.blocks.groups.content.show')->where('id', '[0-9]+')->where('block_id', '[0-9]+');

    Route::post('/blocks-groups/{id}/content', 'Admin\BlocksGroupsController@store_block')->where('id', '[0-9]+');
    Route::post('/blocks-groups/{id}/content/{block_id}/', 'Admin\BlocksGroupsController@update_block')->name('admin.blocks.groups.content.update')->where('id', '[0-9]+')->where('block_id', '[0-9]+');
    Route::get('/blocks-groups/{id}/content/{block_id}/delete-image', 'Admin\BlocksGroupsController@block_delete_image')->name('admin.blocks.groups.content.delete_image')->where('id', '[0-9]+')->where('block_id', '[0-9]+');
    Route::delete('/blocks-groups/{id}/content', 'Admin\BlocksGroupsController@destroy_block')->where('id', '[0-9]+');
    
    Route::get('/blocks-groups', 'Admin\BlocksGroupsController@index')->name('admin.blocks.groups');
    Route::post('/blocks-groups', 'Admin\BlocksGroupsController@store');
    Route::put('/blocks-groups', 'Admin\BlocksGroupsController@update');
    Route::delete('/blocks-groups', 'Admin\BlocksGroupsController@destroy');


    /*
    |--------------------------------------------------------------------------
    | Slider routes
    |--------------------------------------------------------------------------
    */
    Route::post('/slider/config', 'Admin\SliderController@config')->name('admin.slider.config');

    Route::resource('/slider', 'Admin\SliderController')
        ->names(['index' => 'admin.slider', 'create' => 'admin.slider.create', 'show' => 'admin.slider.show'])
        ->parameters(['slider' => 'id']);

  
    /*
    |--------------------------------------------------------------------------
    | FAQ routes
    |--------------------------------------------------------------------------
    */
    Route::resource('/faq', 'Admin\FaqController')
        ->names(['index' => 'admin.faq', 'create' => 'admin.faq.create', 'show' => 'admin.faq.show'])
        ->parameters(['faq' => 'id']);


    /*
    |--------------------------------------------------------------------------
    | Downloads routes
    |--------------------------------------------------------------------------
    */    
    Route::get('/downloads/logs', 'Admin\DownloadsController@logs')->name('admin.downloads.logs');

    Route::resource('/downloads', 'Admin\DownloadsController')
        ->names(['index' => 'admin.downloads', 'create' => 'admin.downloads.create', 'show' => 'admin.downloads.show'])
        ->parameters(['downloads' => 'id']);

    Route::get('/download/{id}/translate', 'Admin\DownloadsController@translate')->name('admin.download.translate')->where('id', '[0-9]+');
    Route::post('/download/{id}/translate', 'Admin\DownloadsController@update_translate');
        
    Route::get('/download/{id}/files', 'Admin\DownloadsController@files')->name('admin.download.files')->where('id', '[0-9]+');    
    Route::post('/download/{id}/files', 'Admin\DownloadsController@create_file')->name('admin.download.files.create')->where('id', '[0-9]+');    
    Route::put('/download/{id}/files', 'Admin\DownloadsController@update_file')->name('admin.download.files.update')->where('id', '[0-9]+');    
    Route::delete('/download/{id}/files', 'Admin\DownloadsController@delete_file')->name('admin.download.files.delete')->where('id', '[0-9]+');

    Route::get('/download/{id}/images', 'Admin\DownloadsController@images')->name('admin.download.images')->where('id', '[0-9]+');
    Route::post('/download/{id}/images', 'Admin\DownloadsController@store_image')->where('id', '[0-9]+');
    Route::delete('/download/{id}/images', 'Admin\DownloadsController@destroy_image')->name('admin.download.images.delete')->where('id', '[0-9]+');

        

    /*
    |--------------------------------------------------------------------------
    | Docs routes
    |--------------------------------------------------------------------------
    */
    Route::resource('/docs/categ', 'Admin\DocsCategoriesController')
        ->names(['index' => 'admin.docs.categ', 'create' => 'admin.docs.categ.create', 'show' => 'admin.docs.categ.show'])
        ->parameters(['categ' => 'id']);        

    Route::get('/docs/{id}/images', 'Admin\DocsController@images')->name('admin.docs.images')->where('id', '[0-9]+');
    Route::post('/docs/{id}/images', 'Admin\DocsController@create_image')->name('admin.docs.images.create')->where('id', '[0-9]+');
    Route::delete('/docs/{id}/images', 'Admin\DocsController@delete_image')->name('admin.docs.images.delete')->where('id', '[0-9]+');

    Route::resource('/docs', 'Admin\DocsController')
        ->names(['index' => 'admin.docs', 'create' => 'admin.docs.create', 'show' => 'admin.docs.show'])
        ->parameters(['docs' => 'id']);
               
        

    /*
    |--------------------------------------------------------------------------
    | Support tickets routes
    |--------------------------------------------------------------------------
    */
    Route::post('/tickets/reply/{id}', 'Admin\TicketsController@reply')->name('admin.tickets.reply')->where('id', '[0-9]+');
    
    Route::get('/tickets/config', 'Admin\TicketsController@config')->name('admin.tickets.config');
    Route::post('/tickets/config', 'Admin\TicketsController@update_config');
    
    Route::resource('/tickets/departments', 'Admin\TicketsDepartmentsController')
        ->names(['index' => 'admin.tickets.departments', 'create' => 'admin.tickets.departments.create', 'show' => 'admin.tickets.departments.show'])
        ->parameters(['departments' => 'id']);    

    Route::resource('/tickets', 'Admin\TicketsController')
        ->names(['index' => 'admin.tickets', 'create' => 'admin.tickets.create', 'show' => 'admin.tickets.show'])
        ->parameters(['tickets' => 'id']);    

    Route::get('/tickets/{id}/mark_important_response/{response_id}', 'Admin\TicketsController@mark_important_response')
        ->name('admin.tickets.mark_important_response')->where(['id'=>'[0-9]+', 'response_id'=>'[0-9]+']);
    
    Route::get('/tickets/{id}/unmark_important_response/{response_id}', 'Admin\TicketsController@unmark_important_response')
        ->name('admin.tickets.unmark_important_response')->where(['code'=>'[a-zA-Z0-9]+', 'response_id'=>'[0-9]+']); 
    
    Route::get('/tickets/{id}/close', 'Admin\TicketsController@close')->name('admin.tickets.close')->where('id', '[0-9]+');
    
    Route::get('/tickets/{id}/open', 'Admin\TicketsController@open')->name('admin.tickets.open')->where('id', '[0-9]+');       

    Route::get('/tickets/{id}/internal-info', 'Admin\TicketsController@internal_info')->name('admin.ticket.internal_info')->where('id', '[0-9]+');
    Route::post('/tickets/{id}/internal-info', 'Admin\TicketsController@store_internal_info')->name('admin.ticket.internal_info.create')->where('id', '[0-9]+');        
    Route::delete('/tickets/{id}/internal-info', 'Admin\TicketsController@destroy_internal_info')->name('admin.ticket.internal_info.delete')->where('id', '[0-9]+');        

    Route::post('/tickets/{id}/delete-response/{response_id}', 'Admin\TicketsController@delete_response')
        ->name('admin.tickets.responses.delete')->where(['id'=>'[0-9]+', 'response_id'=>'[0-9]+']);


    /*
    |--------------------------------------------------------------------------
    | Forum routes
    |--------------------------------------------------------------------------
    */
    Route::get('/forum/config', 'Admin\ForumConfigController@index')->name('admin.forum.config');        
    Route::post('/forum/config', 'Admin\ForumConfigController@update');        

    Route::get('/forum/reports/topics', 'Admin\ForumModerationController@reports_topics')->name('admin.forum.reports.topics');        
    Route::post('/forum/reports/topics/delete/{id}', 'Admin\ForumModerationController@reports_topics_delete')->name('admin.forum.reports.topics.delete');        
    Route::post('/forum/reports/topics', 'Admin\ForumModerationController@reports_topics_update')->name('admin.forum.reports.topic.update');        

    Route::get('/forum/reports/posts', 'Admin\ForumModerationController@reports_posts')->name('admin.forum.reports.posts');        
    Route::post('/forum/reports/posts/delete/{id}', 'Admin\ForumModerationController@reports_posts_delete')->name('admin.forum.reports.posts.delete');        
    Route::post('/forum/reports/posts', 'Admin\ForumModerationController@reports_posts_update')->name('admin.forum.reports.post.update');        

    Route::get('/forum/restrictions', 'Admin\ForumModerationController@restrictions')->name('admin.forum.restrictions');        
    Route::post('/forum/restrictions', 'Admin\ForumModerationController@restrictions_update');        

    Route::get('/forum/moderators', 'Admin\ForumModerationController@moderators')->name('admin.forum.moderators');        
    Route::post('/forum/moderators', 'Admin\ForumModerationController@moderators_update');        

    Route::resource('/forum/categories', 'Admin\ForumCategoriesController')
        ->names(['index' => 'admin.forum.categ', 'create' => 'admin.forum.categ.create', 'show' => 'admin.forum.categ.show'])
        ->parameters(['categories' => 'id']);           
        
    Route::get('/forum/topics', 'Admin\ForumActivityController@topics')->name('admin.forum.topics');
    Route::post('/forum/topics/delete/{id}', 'Admin\ForumActivityController@delete_topic')->name('admin.forum.topics.delete')->where('id', '[0-9]+');
    Route::post('/forum/topics/update/{id}', 'Admin\ForumActivityController@update_topic')->name('admin.forum.topics.update')->where('id', '[0-9]+');
    Route::get('/forum/posts', 'Admin\ForumActivityController@posts')->name('admin.forum.posts');
    Route::post('/forum/posts/delete/{id}', 'Admin\ForumActivityController@delete_post')->name('admin.forum.posts.delete')->where('id', '[0-9]+');


    /*
    |--------------------------------------------------------------------------
    | Languages config routes
    |--------------------------------------------------------------------------
    */
    Route::resource('/config/langs', 'Admin\LangsController')
        ->names(['index' => 'admin.config.langs', 'create' => 'admin.config.langs.create', 'show' => 'admin.config.langs.show'])
        ->parameters(['langs' => 'id']);


    /*
    |--------------------------------------------------------------------------
    | Translates routes
    |--------------------------------------------------------------------------
    */
    Route::get('/translates', 'Admin\TranslatesController@index')->name('admin.translates');
    Route::get('/translate-lang', 'Admin\TranslatesController@translate_lang')->name('admin.translate_lang');
    Route::post('/translates/create_key', 'Admin\TranslatesController@create_key')->name('admin.translates.create_key');
    Route::post('/translates/update_key', 'Admin\TranslatesController@update_key')->name('admin.translates.update_key');
    Route::post('/translates/delete_key', 'Admin\TranslatesController@delete_key')->name('admin.translates.delete_key');
    Route::post('/translates/update_translate', 'Admin\TranslatesController@update_translate')->name('admin.translates.update_translate');
    Route::get('/translates/regenerate_lang_file', 'Admin\TranslatesController@regenerate_lang_file')->name('admin.translates.regenerate_lang_file');
    Route::post('/translates/scan_template', 'Admin\TranslatesController@scan_template')->name('admin.translates.scan_template');


    /*
    |--------------------------------------------------------------------------
    | General config routes
    |--------------------------------------------------------------------------
    */    

    Route::get('/config/general', 'Admin\ConfigController@general')->name('admin.config.general');
    Route::post('/config/general', 'Admin\ConfigController@update_general');

    Route::get('/config/modules', 'Admin\ConfigController@modules')->name('admin.config.modules');
    Route::post('/config/modules', 'Admin\ConfigController@update_module');

    Route::get('/config/registration', 'Admin\ConfigController@registration')->name('admin.config.registration');
    Route::post('/config/registration', 'Admin\ConfigController@update_registration');

    Route::get('/config/antispam', 'Admin\ConfigController@antispam')->name('admin.config.antispam');
    Route::post('/config/antispam', 'Admin\ConfigController@update_antispam');

    Route::get('/config/email', 'Admin\ConfigController@email')->name('admin.config.email');
    Route::post('/config/email', 'Admin\ConfigController@update_email');
    Route::post('/config/email/test', 'Admin\ConfigController@send_test_email')->name('admin.send_test_email');

    Route::get('/config/locale', 'Admin\ConfigController@locale')->name('admin.config.locale');
    Route::post('/config/locale', 'Admin\ConfigController@update_locale');

    Route::get('/config/contact', 'Admin\ConfigController@contact')->name('admin.config.contact');
    Route::post('/config/contact', 'Admin\ConfigController@update_contact');

    Route::get('/config/template/activate', 'Admin\ConfigController@activate_template')->name('admin.config.template.activate');
    Route::get('/config/template', 'Admin\ConfigController@template')->name('admin.config.template');

    Route::get('/config/logo', 'Admin\ConfigController@logo')->name('admin.config.logo');
    Route::post('/config/logo', 'Admin\ConfigController@update_logo');

    Route::get('/config/template/tools', 'Admin\ConfigController@template_tools')->name('admin.config.template.tools');
    Route::post('/config/template/tools', 'Admin\ConfigController@update_template_tools');

    Route::get('/config/site-offline', 'Admin\ConfigController@site_offline')->name('admin.config.site_offline');
    Route::post('/config/site-offline', 'Admin\ConfigController@update_site_offline');

    Route::get('/config/license', 'Admin\ConfigController@license')->name('admin.config.license');
    Route::post('/config/license', 'Admin\ConfigController@update_license')->name('admin.config.license.update');

    Route::get('/config/tools', 'Admin\ConfigController@tools')->name('admin.tools.server');    
    Route::get('/config/tools/backup', 'Admin\ConfigController@backup')->name('admin.tools.backup');
    Route::get('/config/tools/sitemap', 'Admin\ConfigController@sitemap')->name('admin.tools.sitemap');    
    Route::post('/config/tools/backup', 'Admin\ConfigController@process_backup');
    Route::post('/config/tools/sitemap', 'Admin\ConfigController@process_sitemap');
    Route::get('/config/tools/system', 'Admin\ConfigController@system')->name('admin.tools.system');
    Route::get('/config/tools/clear-cache/{section}', 'Admin\ConfigController@clear_cache')->where(['section' => '[a-z0-9_-]+'])->name('admin.tools.clear_cache');
    Route::get('/config/tools/clear-logs/{section}', 'Admin\ConfigController@clear_logs')->where(['section' => '[a-z0-9_-]+'])->name('admin.tools.clear_logs');

    // update routes
    Route::get('/config/tools/update', 'Admin\UpdateController@index')->name('admin.tools.update');
    Route::post('/config/tools/update/check', 'Admin\UpdateController@check_update')->name('admin.tools.update.check');
    Route::post('/config/tools/update/process', 'Admin\UpdateController@update')->name('admin.tools.update.process');


    Route::resource('/config/variables', 'Admin\ConfigVariablesController')
        ->names(['index' => 'admin.config.variables', 'create' => 'admin.config.variables.create', 'show' => 'admin.config.variables.show'])
        ->parameters(['variables' => 'id']);

   

    /*
    |--------------------------------------------------------------------------
    | Inbox routes
    |--------------------------------------------------------------------------
    */
    Route::resource('/inbox', 'Admin\InboxController')
        ->names(['index' => 'admin.inbox', 'show' => 'admin.inbox.show'])
        ->parameters(['inbox' => 'id']);

    Route::get('/inbox/delete/{id}', 'Admin\InboxController@delete')->where('id', '[0-9]+');
    Route::post('/inbox/{id}', 'Admin\InboxController@reply')->where('id', '[0-9]+')->name('admin.inbox.reply');
    Route::get('/inbox/{id}/important', 'Admin\InboxController@important')->where('id', '[0-9]+')->name('admin.inbox.important');
    Route::get('/inbox/{id}/spam', 'Admin\InboxController@spam')->where('id', '[0-9]+')->name('admin.inbox.spam');


    /*
    |--------------------------------------------------------------------------
    | Profile routes
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', 'Admin\ProfileController@index')->name('admin.profile');
    Route::post('/profile', 'Admin\ProfileController@update');
    Route::get('/profile/delete-avatar', 'Admin\ProfileController@delete_avatar')->name('admin.profile.delete_avatar');
    

    /*
    |--------------------------------------------------------------------------
    | eCommerce routes
    |--------------------------------------------------------------------------
    */
    Route::resource('/orders', 'Admin\CartOrdersController')
        ->names(['index' => 'admin.cart.orders', 'create' => 'admin.cart.orders.create', 'show' => 'admin.cart.orders.show'])
        ->parameters(['orders' => 'id']);

    Route::post('/orders/{id}/update-payment', 'Admin\CartOrdersController@update_order_payment')->name('admin.cart.orders.update_payment')->where('id', '[0-9]+');
    Route::post('/orders/{id}/update-notes', 'Admin\CartOrdersController@update_order_notes')->name('admin.cart.orders.update_notes')->where('id', '[0-9]+');
    Route::post('/orders/{id}/update-items', 'Admin\CartOrdersController@update_order_items')->name('admin.cart.orders.update_items')->where('id', '[0-9]+');

    Route::resource('/cart/products', 'Admin\CartProductsController')
        ->names(['index' => 'admin.cart.products', 'create' => 'admin.cart.products.create', 'show' => 'admin.cart.products.show'])
        ->parameters(['products' => 'id']);  
    Route::get('/cart/products/{id}/translate', 'Admin\CartProductsController@translate')->name('admin.cart.product.translate')->where('id', '[0-9]+');
    Route::post('/cart/products/{id}/translate', 'Admin\CartProductsController@update_translate');

    Route::get('/cart/products/{id}/images', 'Admin\CartProductsController@images')->name('admin.cart.product.images')->where('id', '[0-9]+');
    Route::post('/cart/products/{id}/images', 'Admin\CartProductsController@store_image')->where('id', '[0-9]+');
    Route::delete('/cart/products/{id}/images', 'Admin\CartProductsController@destroy_image')->name('admin.cart.product.images.delete')->where('id', '[0-9]+');

    Route::get('/cart/products/{id}/files', 'Admin\CartProductsController@files')->name('admin.cart.product.files')->where('id', '[0-9]+');
    Route::post('/cart/products/{id}/files', 'Admin\CartProductsController@store_file')->where('id', '[0-9]+');
    Route::put('/cart/products/{id}/files', 'Admin\CartProductsController@update_file')->where('id', '[0-9]+');
    Route::delete('/cart/products/{id}/files', 'Admin\CartProductsController@destroy_file')->name('admin.cart.product.files.delete')->where('id', '[0-9]+');       

    Route::resource('/cart/categories', 'Admin\CartCategoriesController')
        ->names(['index' => 'admin.cart.categ', 'create' => 'admin.cart.categ.create', 'show' => 'admin.cart.categ.show'])
        ->parameters(['categories' => 'id']);                                             
    Route::get('/cart/categ/{id}/translate', 'Admin\CartCategoriesController@translate')->name('admin.cart.categ.translate')->where('id', '[0-9]+');
    Route::post('/cart/categ/{id}/translate', 'Admin\CartCategoriesController@update_translate');

    Route::get('/cart/config/general', 'Admin\CartConfigController@general')->name('admin.cart.config.general');
    Route::post('/cart/config/general', 'Admin\CartConfigController@update_general');

    Route::get('/cart/config/gateways', 'Admin\CartConfigController@gateways')->name('admin.cart.config.gateways');
    Route::post('/cart/config/gateways', 'Admin\CartConfigController@store_gateway');
    Route::put('/cart/config/gateways', 'Admin\CartConfigController@update_gateway');
    Route::delete('/cart/config/gateways', 'Admin\CartConfigController@destroy_gateway');

    Route::get('/cart/config/currencies', 'Admin\CartConfigController@currencies')->name('admin.cart.config.currencies');    
    Route::put('/cart/config/currencies', 'Admin\CartConfigController@update_currency');
       
    
    /*
    |--------------------------------------------------------------------------
    | Email marketing routes
    |--------------------------------------------------------------------------
    */                          
    Route::resource('/email-marketing/campaigns', 'Admin\EmailCampaignsController')
        ->names(['index' => 'admin.email.campaigns', 'create' => 'admin.email.campaigns.create', 'show' => 'admin.email.campaigns.show'])
        ->parameters(['campaigns' => 'id']);           

    Route::get('/email-marketing/campaigns/{id}/recipients', 'Admin\EmailCampaignsController@recipients')->name('admin.email.campaigns.recipients')->where('id', '[0-9]+');
    Route::post('/email-marketing/campaigns/{id}/recipients', 'Admin\EmailCampaignsController@store_recipient')->where('id', '[0-9]+');
    Route::delete('/email-marketing/campaigns/{id}/recipients', 'Admin\EmailCampaignsController@destroy_recipient')->name('admin.email.campaigns.recipients.delete')->where('id', '[0-9]+');     

    Route::get('/email-marketing/campaigns/{id}/send', 'Admin\EmailCampaignsController@send')->name('admin.email.campaigns.send')->where('id', '[0-9]+');

    Route::get('/email-marketing/config', 'Admin\EmailCampaignsController@config')->name('admin.email.campaigns.config');
    Route::post('/email-marketing/config', 'Admin\EmailCampaignsController@update_config');


    Route::get('/email-marketing/black-list', 'Admin\EmailCampaignsController@black_list')->name('admin.email.black-list');
    Route::post('/email-marketing/black-list', 'Admin\EmailCampaignsController@store_black_list')->where('id', '[0-9]+');
    Route::delete('/email-marketing/black-list', 'Admin\EmailCampaignsController@destroy_black_list')->name('admin.email.black-list.delete');     

    Route::resource('/email-marketing/lists', 'Admin\EmailListsController')
        ->names(['index' => 'admin.email.lists', 'create' => 'admin.email.lists.create', 'show' => 'admin.email.lists.show'])
        ->parameters(['lists' => 'id']);   
    
    Route::get('/email-marketing/lists/{id}/recipients', 'Admin\EmailListsController@recipients')->name('admin.email.lists.recipients')->where('id', '[0-9]+');
    Route::post('/email-marketing/lists/{id}/recipients', 'Admin\EmailListsController@store_recipient')->where('id', '[0-9]+');
    Route::delete('/email-marketing/lists/{id}/recipients', 'Admin\EmailListsController@destroy_recipient')->name('admin.email.lists.recipients.delete')->where('id', '[0-9]+');     


    /*
    |--------------------------------------------------------------------------
    | AJAX routes
    |--------------------------------------------------------------------------
    */
    Route::get('/ajax/users', 'Admin\AjaxController@users')->name('admin.ajax.users');
    Route::get('/ajax/internals', 'Admin\AjaxController@internals')->name('admin.ajax.internals');
    Route::get('/ajax/accounts', 'Admin\AjaxController@accounts')->name('admin.ajax.accounts');
    Route::get('/ajax/staff', 'Admin\AjaxController@staff')->name('admin.ajax.staff');
    Route::get('/ajax/tags', 'Admin\AjaxController@tags')->name('admin.ajax.tags');
});