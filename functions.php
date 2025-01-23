<?php

function create_posttype() {

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
}

function remove_wysiwyg() {
    remove_post_type_support( 'page', 'editor' );
}

add_action('init', 'create_posttype');
add_action('init', 'remove_wysiwyg');

?>