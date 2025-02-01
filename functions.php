<?php

/**
 * Forcer le chargement du CSS de Cool Timeline.
 */ function my_force_load_cool_timeline_css()
{
    // 1) Charger le fichier styles.css (pour l’horizontal roadmap, etc.)
    wp_enqueue_style(
        'cooltimeline-styles-forced',
        // Ici, on vise : wp-content/plugins/cool-timeline/assets/css/styles.css
        plugins_url('assets/css/styles.css', WP_PLUGIN_DIR . '/cool-timeline/cooltimeline.php'),
        [],
        '1.0'
    );

    // 2) Charger le fichier ctl-vertical-timeline.css (pour le layout vertical)
    wp_enqueue_style(
        'cooltimeline-vertical-styles-forced',
        // D’après votre arborescence, 
        //   wp-content/plugins/cool-timeline/includes/shortcodes/assets/css/ctl-vertical-timeline.css
        plugins_url(
            'includes/shortcodes/assets/css/ctl-vertical-timeline.css',
            WP_PLUGIN_DIR . '/cool-timeline/cooltimeline.php'
        ),
        [],
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'my_force_load_cool_timeline_css', 20);

/**
 * Enegistrer les custom post types.
 */
function create_posttype()
{
    register_post_type(
        'infos-contact',
        array(
            'labels' => array(
                'name' => __('Infos-Contact'),
                'singular_name' => __('Infos-Contact')
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-list-view',
            'rewrite' => array('slug' => 'infos-contact'),
            'show_in_rest' => true,
            'supports' => array('title', 'id'),
        )
    );

    register_post_type(
        'campagne-dons',
        array(
            'labels' => array(
                'name' => __('Campagne-Dons'),
                'singular_name' => __('Campagne-Dons')
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-feedback',
            'rewrite' => array('slug' => 'campagne-dons'),
            'show_in_rest' => true,
            'supports' => array('title', 'id', 'thumbnail', 'custom-fields'),
        )
    );

    register_post_type(
        'equipe',
        array(
            'labels' => array(
                'name' => __('Équipe'),
                'singular_name' => __('Équipe')
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-groups',
            'rewrite' => array('slug' => 'equipe'),
            'show_in_rest' => true,
            'supports' => array('title', 'id', 'thumbnail', 'custom-fields'),
        )
    );

    register_post_type(
        'faq',
        array(
            'labels' => array(
                'name' => __('FAQ'),
                'singular_name' => __('FAQ')
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
function oskar_enqueue_scripts() {
    wp_enqueue_script(
        'equipe',
        get_template_directory_uri() . '/assets/js/equipe.js'
    );

    wp_enqueue_script(
        'faq',
        get_template_directory_uri() . '/assets/js/faq.js'
    );

    wp_enqueue_script(
        'banners',
        get_template_directory_uri() . '/assets/js/banners.js'
    );
}

function defer_script($tag, $handle) {
    if ('equipe' === $handle) {
        return str_replace('src', 'defer="defer" src', $tag);
    }
    if ('faq' === $handle) {
        return str_replace('src', 'defer="defer" src', $tag);
    }
    if ('banners' === $handle) {
        return str_replace('src', 'defer="defer" src', $tag);
    }
    return $tag;
}

add_action('wp_enqueue_scripts', 'oskar_enqueue_scripts');
add_filter('script_loader_tag', 'defer_script', 10, 2);
add_action('init', 'create_posttype');

/**
 * Remove WYSIWYG editor from pages.
 */
function remove_wysiwyg()
{
    remove_post_type_support('page', 'editor');
}
add_action('init', 'remove_wysiwyg');

/**
 * Inject ACF fields (preview image, location name, address) as data-attributes
 * into each Cool Timeline story div.
 */
add_filter('the_content', 'inject_acf_preview_url_in_cool_timeline', 20);
function inject_acf_preview_url_in_cool_timeline($content)
{
    // Pattern pour trouver chaque div de story par ID : ctl-story-###
    $pattern = '/(<div\s[^>]*id="ctl-story-(\d+)"[^>]*>)/i';

    // Callback pour injecter les attributs
    $callback = function ($matches) {
        $story_id = (int) $matches[2];

        // Récupérer les champs ACF et meta
        $apercu = get_field('apercue_vignette', $story_id);
        $location_name = get_field('location_name', $story_id);
        $location_address = get_field('location_address', $story_id);
        $organizer = get_field('organisateurs', $story_id);
        $event_date = get_post_meta($story_id, 'ctl_story_date', true);
        $start_time = get_field('startTime', $story_id);
        $end_time = get_field('endTime', $story_id);
        $time_zone = 'America/New_York'; 

        // Préparer l'URL de l'image
        $image_url = '';
        if ($apercu) {
            if (is_string($apercu)) {
                // Le champ ACF est une URL sous forme de chaîne
                $image_url = $apercu;
            } elseif (is_array($apercu) && isset($apercu['url'])) {
                // Le champ ACF est un tableau avec une clé 'url'
                $image_url = $apercu['url'];
            } elseif (is_numeric($apercu)) {
                // Le champ ACF est un ID d'attachement
                $image_url = wp_get_attachment_image_url($apercu, 'full');
            }
        }

        // Formatage de la date (doit rester au format "YYYY-MM-DD")
        if ($event_date && is_string($event_date)) {
            $formatted_date = date_i18n('Y-m-d', strtotime($event_date));
        } else {
            $formatted_date = '';
        }

        // Formatage des heures pour forcer le format "HH:mm"
        if ($start_time) {
            $formatted_start_time = date('H:i', strtotime($start_time));
        } else {
            $formatted_start_time = ''; // ou définir une valeur par défaut
        }
        if ($end_time) {
            $formatted_end_time = date('H:i', strtotime($end_time));
        } else {
            $formatted_end_time = ''; // ou définir une valeur par défaut
        }

        // Supprimer le '>' pour ajouter des attributs
        $div_open_tag = rtrim($matches[1], '>');

        // Injection des attributs data-* si disponibles
        if ($image_url) {
            $div_open_tag .= ' data-preview-url="' . esc_attr($image_url) . '"';
        }
        if ($location_name) {
            $div_open_tag .= ' data-location-name="' . esc_attr($location_name) . '"';
        }
        if ($location_address) {
            $div_open_tag .= ' data-location-address="' . esc_attr($location_address) . '"';
        }
        if ($organizer) {
            $div_open_tag .= ' data-organizer="' . esc_attr($organizer) . '"';
        }
        if ($formatted_date) {
            $div_open_tag .= ' data-event-date="' . esc_attr($formatted_date) . '"';
        }
        if ($formatted_start_time) {
            $div_open_tag .= ' data-start-time="' . esc_attr($formatted_start_time) . '"';
        }
        if ($formatted_end_time) {
            $div_open_tag .= ' data-end-time="' . esc_attr($formatted_end_time) . '"';
        }
        if ($time_zone) {
            $div_open_tag .= ' data-timezone="' . esc_attr($time_zone) . '"';
        }

        // Fermer la balise div
        $div_open_tag .= '>';

        // Log pour le débogage
        error_log("Injecting data attributes for story #$story_id");

        return $div_open_tag;
    };

    // Effectuer le remplacement dans le contenu
    return preg_replace_callback($pattern, $callback, $content) ?: $content;
}

/**
 * Re-enqueue ACF scripts after Cool Timeline scripts (example from your snippet).
 */
add_action('wp_loaded', 're_enqueue_acf_scripts_after_cooltimeline', 20);
function re_enqueue_acf_scripts_after_cooltimeline()
{
    // Example code to remove a Cool Timeline action if it exists.
    if (class_exists('CoolTimeline')) {
        $ctl = CoolTimeline::get_instance();
        if (has_action('wp_print_scripts', [$ctl, 'ctl_deregister_javascript'])) {
            remove_action('wp_print_scripts', [$ctl, 'ctl_deregister_javascript'], 100);
            error_log("Action 'ctl_deregister_javascript' supprimée.");
        } else {
            error_log("Action 'ctl_deregister_javascript' introuvable.");
        }
    }
}

/**
 * Enqueue custom JS for the Cool Timeline interactions.
 */
function my_cool_timeline_custom_scripts()
{
    wp_enqueue_script(
        'cool-timeline-custom',
        get_stylesheet_directory_uri() . '/assets/js/custom-cool-timeline.js',
        ['jquery'],
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'my_cool_timeline_custom_scripts');