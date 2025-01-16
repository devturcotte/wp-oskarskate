<?php 
    define('WP_SCSS_ALWAYS_RECOMPILE', true);
?>

<?php

function create_posttype() {

    register_post_type( 'bannieres',
        array(
            'labels' => array(
                'name' => __( 'Bannières' ),
                'singular_name' => __( 'Banniere' )
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-format-image',
            'rewrite' => array('slug' => 'bannieres'),
            'show_in_rest' => true,
            'supports' => array('title', 'id'),   
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

    register_post_type( 'partenaires',
        array(
            'labels' => array(
                'name' => __( 'Partenaires' ),
                'singular_name' => __( 'Partenaires' )
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-groups',
            'rewrite' => array('slug' => 'partenaires'),
            'show_in_rest' => true,
            'supports' => array('title', 'id'),   
        )
    );
}

add_action('init', 'create_posttype');

?>