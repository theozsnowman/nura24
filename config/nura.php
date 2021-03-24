<?php
$reques_uri = $_SERVER['REQUEST_URI'];
preg_match_all('#/([^/]*)#', $reques_uri, $matches);
$lang = $matches[1][0];


/*
|--------------------------------------------------------------------------
| Slugs settings (URL structure for modules)    
| You can add custom clugs for each language
*/
    

switch($lang) {

    // 'es' language
    case 'es':       
        $posts_slug = 'blog';
        $posts_tag_slug = 'tag';
        $posts_search_slug = 'buscar';
        $post_slug = 'blog'; // this can be empty if you don't want to show posts section slug in url, when showing an post
        $docs_slug = 'documentacion';
        $docs_search_slug = 'buscar';
        $faq_slug = 'preguntas-y-respuestas';
        $contact_slug = 'contacto';
        $downloads_slug = 'descargas';
        $profile_slug = 'perfil';    
        $cart_slug = 'tienda';    
        $cart_search_slug = 'buscar';
        $forum_slug = 'foro';
        break;

    // default language        
    default:
        $posts_slug = 'blog';        
        $posts_tag_slug = 'tag';
        $posts_search_slug = 'search';
        $post_slug = 'blog'; // this can be empty if you don't want to show posts section slug in url, when showing an post
        $docs_slug = 'docs';
        $docs_search_slug = 'search';
        $faq_slug = 'faq';
        $contact_slug = 'contact';
        $downloads_slug = 'downloads';
        $profile_slug = 'profile';    
        $cart_slug = 'shop';    
        $cart_search_slug = 'search';
        $forum_slug = 'forum';
    }


// DO NOT CHANGE BELOW:
return [    
    'posts_slug' => $posts_slug,
    'posts_tag_slug' =>  $posts_tag_slug,
    'posts_search_slug' => $posts_search_slug,
    'post_slug' => $post_slug,
    'docs_slug' => $docs_slug,
    'docs_search_slug' => $docs_search_slug,
    'faq_slug' => $faq_slug,
    'contact_slug' => $contact_slug,
    'downloads_slug' => $downloads_slug,
    'profile_slug' => $profile_slug,
    'cart_slug' => $cart_slug,
    'cart_search_slug' => $cart_search_slug,
    'forum_slug' => $forum_slug,

];
