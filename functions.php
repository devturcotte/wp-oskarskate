<?php 
    define('WP_SCSS_ALWAYS_RECOMPILE', true);
?>

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
            'menu_icon' => 'dashicons-admin-users',
            'rewrite' => array('slug' => 'infos-contact'),
            'show_in_rest' => true,
            'supports' => array('title', 'id'),   
        )
    );
}

add_action('init', 'create_posttype');

?>