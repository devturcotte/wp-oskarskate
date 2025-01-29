<?php

function create_posttype() {

    register_post_type( 'infos-contact',
        array(
            'labels' => array(
                'name' => __( 'Infos-Contact' ),
                'singular_name' => __( 'Infos-Contact' )
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-list-view',
            'rewrite' => array('slug' => 'infos-contact'),
            'show_in_rest' => true,
            'supports' => array('title', 'id'),   
        )
    );

    register_post_type( 'campagne-dons',
        array(
            'labels' => array(
                'name' => __( 'Campagne-Dons' ),
                'singular_name' => __( 'Campagne-Dons' )
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-feedback',
            'rewrite' => array('slug' => 'campagne-dons'),
            'show_in_rest' => true,
            'supports' => array('title', 'id', 'thumbnail', 'custom-fields'),
        )
    );

    register_post_type( 'equipe',
        array(
            'labels' => array(
                'name' => __( 'Équipe' ),
                'singular_name' => __( 'Équipe' )
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-groups',
            'rewrite' => array('slug' => 'equipe'),
            'show_in_rest' => true,
            'supports' => array('title', 'id', 'thumbnail', 'custom-fields'),
        )
    );

    register_post_type( 'faq',
        array(
            'labels' => array(
                'name' => __( 'FAQ' ),
                'singular_name' => __( 'FAQ' )
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-info',
            'rewrite' => array('slug' => 'faq'),
            'show_in_rest' => true,
            'supports' => array('title', 'id'),   
        )
    );
}

function remove_wysiwyg() {
    remove_post_type_support( 'page', 'editor' );
}

function oskar_enqueue_scripts() {
    wp_enqueue_script(
        'main',
        get_template_directory_uri() . '/assets/js/main.js'
    );
}

function defer_script($tag, $handle) {
    if ('main' === $handle) {
        return str_replace('type="text/javascript" src', 'type="module" defer="defer" src', $tag);
    }
    return $tag;
}

add_action('wp_enqueue_scripts', 'oskar_enqueue_scripts');
add_filter('script_loader_tag', 'defer_script', 10, 2);
add_action('init', 'create_posttype');
add_action('init', 'remove_wysiwyg');
?>