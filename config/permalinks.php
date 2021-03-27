<?php
/*
* IMPORTANT!!! Do NOT change this file. 
* If you want to edit permalinks, you MUST edit them in .env file
* If you have multiple languages you mmust add permalinks in .env for each language. Example: "permalink_posts_es = blog" (add _LANGUAGE-CODE for each permalink)
*/

$reques_uri = $_SERVER['REQUEST_URI'];
preg_match_all('#/([^/]*)#', $reques_uri, $matches);
$lang = $matches[1][0];

if($lang && strlen($lang) == 2) {
    $posts_permalink = env('SLUG_POSTS_'.strtoupper($lang)) ?? 'blog';
    $posts_tag_permalink = env('SLUG_POSTS_TAG_'.strtoupper($lang)) ?? 'tag';
    $posts_search_permalink = env('SLUG_POSTS_SEARCH_'.strtoupper($lang)) ?? 'search';
    $post_permalink = env('SLUG_POST_'.strtoupper($lang)) ?? 'blog'; 
    $docs_permalink = env('SLUG_DOCS_'.strtoupper($lang)) ?? 'docs';
    $docs_search_permalink = env('SLUG_DOCS_SEARCH_'.strtoupper($lang)) ?? 'search';
    $faq_permalink = env('SLUG_FAQ_'.strtoupper($lang)) ?? 'faq';
    $contact_permalink = env('SLUG_CONTACT_'.strtoupper($lang)) ?? 'contact';
    $downloads_permalink = env('SLUG_DOWNLOADS_'.strtoupper($lang)) ?? 'downloads';
    $profile_permalink = env('SLUG_PROFILE_'.strtoupper($lang)) ?? 'profile';    
    $cart_permalink = env('SLUG_CART_'.strtoupper($lang)) ?? 'shop';    
    $cart_search_permalink = env('SLUG_CART_SEARCH_'.strtoupper($lang)) ?? 'search';
    $forum_permalink = env('SLUG_FORUM_'.strtoupper($lang)) ?? 'forum';
}        

else {
    $posts_permalink = env('SLUG_POSTS') ?? 'blog';
    $posts_tag_permalink = env('SLUG_POSTS_TAG') ?? 'tag';
    $posts_search_permalink = env('SLUG_POSTS_SEARCH') ?? 'search';
    $post_permalink = env('SLUG_POST') ?? 'blog';
    $docs_permalink = env('SLUG_DOCS') ?? 'docs';
    $docs_search_permalink = env('SLUG_DOCS_SEARCH') ?? 'search';
    $faq_permalink = env('SLUG_FAQ') ?? 'faq';
    $contact_permalink = env('SLUG_CONTACT') ?? 'contact';
    $downloads_permalink = env('SLUG_DOWNLOADS') ?? 'downloads';
    $profile_permalink = env('SLUG_PROFILE') ?? 'profile';    
    $cart_permalink = env('SLUG_CART') ?? 'shop';    
    $cart_search_permalink = env('SLUG_CART_SEARCH') ?? 'search';
    $forum_permalink = env('SLUG_FORUM') ?? 'forum';
}


// DO NOT CHANGE BELOW:
return [    
    'posts_permalink' => $posts_permalink,
    'posts_tag_permalink' =>  $posts_tag_permalink,
    'posts_search_permalink' => $posts_search_permalink,
    'post_permalink' => $post_permalink,
    'docs_permalink' => $docs_permalink,
    'docs_search_permalink' => $docs_search_permalink,
    'faq_permalink' => $faq_permalink,
    'contact_permalink' => $contact_permalink,
    'downloads_permalink' => $downloads_permalink,
    'profile_permalink' => $profile_permalink,
    'cart_permalink' => $cart_permalink,
    'cart_search_permalink' => $cart_search_permalink,
    'forum_permalink' => $forum_permalink,
];
