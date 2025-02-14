<?php
/**
 * Theme Functions
 */

/**
 * Enqueue Cool Timeline plugin assets and main.js.
 */
function my_force_load_cool_timeline_css() {
    // 1) Enqueue plugin styles (horizontal timeline, etc.)
    wp_enqueue_style(
        'cooltimeline-styles-forced',
        plugins_url('assets/css/styles.css', WP_PLUGIN_DIR . '/cool-timeline/cooltimeline.php'),
        array(),
        '1.0'
    );
    // 2) Enqueue vertical timeline styles.
    wp_enqueue_style(
        'cooltimeline-vertical-styles-forced',
        plugins_url('includes/shortcodes/assets/css/ctl-vertical-timeline.css', WP_PLUGIN_DIR . '/cool-timeline/cooltimeline.php'),
        array(),
        '1.0'
    );
    // 3) Enqueue the main.js file.
    wp_enqueue_script(
        'main',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'my_force_load_cool_timeline_css', 20);

/**
 * (Optional) Enqueue the ics library.
 */
function enqueue_ics_library() {
    wp_enqueue_script(
        'ics-lib',
        'https://unpkg.com/ics/dist/ics.deps.min.js',
        array(),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_ics_library', 15);

/**
 * Enqueue the registration form script.
 */
function enqueue_registration_form_script() {
    wp_enqueue_script(
        'reg-form',
        get_template_directory_uri() . '/assets/js/reg_form.js',
        array('jquery'),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_registration_form_script');

/**
 * Enqueue the calendar.js module.
 */
function enqueue_calendar_js() {
    wp_enqueue_script(
        'calendar-js',
        get_template_directory_uri() . '/assets/js/calendar.js',
        array(),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_calendar_js');

/**
 * Inject ACF fields into each story's HTML.
 *
 * This function injects several data-* attributes into the story container.
 * In addition to the preview URL, dates, etc., we now also add:
 *  - data-nb-places-participants : maximum number of participant places (ACF field nombre_de_places_participants)
 *  - data-nb-places-benevoles    : maximum number of bénévoles (ACF field nombre_de_places_benevoles)
 */
function inject_acf_preview_url_in_cool_timeline($content) {
    $pattern = '/(<div\s[^>]*id="ctl-story-(\d+)"[^>]*>)/i';
    $callback = function ($matches) {
        $story_id = (int) $matches[2];

        // Retrieve the event date from meta.
        $event_date_raw = get_post_meta($story_id, 'ctl_story_date', true);
        $formatted_date = '';
        $formatted_start_time = '';
        if (!empty($event_date_raw)) {
            $date_obj = DateTime::createFromFormat('m/d/Y h:i A', $event_date_raw);
            if ($date_obj) {
                $formatted_date = $date_obj->format('Y-m-d'); // e.g., "2025-03-08"
                $formatted_start_time = $date_obj->format('H:i'); // e.g., "10:09"
            }
        }

        // Retrieve end date (optional)
        $acf_end_date = get_field('end_date', $story_id);
        $formatted_end_date = !empty($acf_end_date) ? $acf_end_date : '';

        // Retrieve other fields
        $apercu = get_field('apercue_vignette', $story_id);
        $location_name = get_field('location_name', $story_id);
        $location_address = get_field('location_address', $story_id);
        $organizer = get_field('organisateurs', $story_id);
        $end_time = get_field('endTime', $story_id) ?: '';
        $time_zone = 'America/New_York';
        $formatted_end_time = ($end_time) ? date('H:i', strtotime($end_time)) : '';

        // Build the div opening tag
        $div_open_tag = rtrim($matches[1], '>');

        // Inject preview URL
        if ($apercu) {
            if (is_string($apercu)) {
                $div_open_tag .= ' data-preview-url="' . esc_attr($apercu) . '"';
            } elseif (is_array($apercu) && isset($apercu['url'])) {
                $div_open_tag .= ' data-preview-url="' . esc_attr($apercu['url']) . '"';
            } elseif (is_numeric($apercu)) {
                $img_url = wp_get_attachment_image_url($apercu, 'full');
                if ($img_url) {
                    $div_open_tag .= ' data-preview-url="' . esc_attr($img_url) . '"';
                }
            }
        }
        // Inject other standard fields
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
        if ($formatted_end_date) {
            $div_open_tag .= ' data-end-date="' . esc_attr($formatted_end_date) . '"';
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

        // Inject ACF "type_dactivite" field
        $acf_types = get_field('type_dactivite', $story_id);
        if (!empty($acf_types) && is_array($acf_types)) {
            $div_open_tag .= ' data-type_dactivite=\'' . esc_attr(json_encode($acf_types)) . '\'';
        }
        // Optionally, inject social fields
        $evenement_facebook = get_field('evenement_facebook', $story_id);
        if ($evenement_facebook) {
            $div_open_tag .= ' data-evenement-facebook="' . esc_attr($evenement_facebook) . '"';
        }
        $lien_facebook = get_field('lien_facebook', $story_id);
        if ($lien_facebook) {
            $div_open_tag .= ' data-lien-facebook="' . esc_attr($lien_facebook) . '"';
        }
        $evenement_instagram = get_field('evenement_instagram', $story_id);
        if ($evenement_instagram) {
            $div_open_tag .= ' data-evenement-instagram="' . esc_attr($evenement_instagram) . '"';
        }
        $lien_instagram = get_field('lien_instagram', $story_id);
        if ($lien_instagram) {
            $div_open_tag .= ' data-lien-instagram="' . esc_attr($lien_instagram) . '"';
        }

        $besoin_inscriptions = get_field('besoin_dinscriptions', $story_id);
if (is_array($besoin_inscriptions)) {
    // In case it's returned as an array, we check if "Oui" is in it.
    $inscriptions_needed = in_array('Oui', $besoin_inscriptions) ? "Oui" : "Non";
} else {
    $inscriptions_needed = trim($besoin_inscriptions);
}
$div_open_tag .= ' data-besoin-inscriptions="' . esc_attr($inscriptions_needed) . '"';

        // *** NEW FIELDS: Available places ***
        $nb_places_participants = get_field('nombre_de_places_participants', $story_id);
        if ($nb_places_participants) {
            $div_open_tag .= ' data-nb-places-participants="' . esc_attr($nb_places_participants) . '"';
        }
        $nb_places_benevoles = get_field('nombre_de_places_benevoles', $story_id);
        if ($nb_places_benevoles) {
            $div_open_tag .= ' data-nb-places-benevoles="' . esc_attr($nb_places_benevoles) . '"';
        }
        // *****************************************

        $div_open_tag .= '>';
        error_log("Injecting for story #{$story_id}: date={$formatted_date}");
        return $div_open_tag;
    };

    return preg_replace_callback($pattern, $callback, $content) ?: $content;
}
add_filter('the_content', 'inject_acf_preview_url_in_cool_timeline', 20);

/**

/**
 * Enqueue custom Cool Timeline JavaScript.
 */
function my_cool_timeline_custom_scripts() {
    wp_enqueue_script(
        'cool-timeline-custom',
        get_template_directory_uri() . '/assets/js/custom-cool-timeline.js',
        array('jquery', 'calendar-js'),
        '1.0',
        true
    );
    wp_localize_script('cool-timeline-custom', 'myThemePaths', array(
        'cssMain' => get_template_directory_uri() . '/assets/styles/css/main.css'
    ));
}
add_action('wp_enqueue_scripts', 'my_cool_timeline_custom_scripts');

/**
 * Register Custom Post Types.
 */
function create_posttype() {
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
add_action('init', 'create_posttype');

/**
 * (Optional) Re-enqueue main.js if necessary.
 */
function oskar_enqueue_scripts() {
    wp_enqueue_script(
        'main',
        get_template_directory_uri() . '/assets/js/main.js'
    );
}
add_action('wp_enqueue_scripts', 'oskar_enqueue_scripts');

function defer_script($tag, $handle) {
    if ('main' === $handle) {
        return str_replace('type="text/javascript" src', 'type="module" defer src', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'defer_script', 10, 2);

/**
 * Disable the WYSIWYG editor for pages.
 */
function remove_wysiwyg() {
    remove_post_type_support('page', 'editor');
}
add_action('init', 'remove_wysiwyg');

/**
 * Re-enqueue ACF scripts after Cool Timeline scripts.
 */
add_action('wp_loaded', 're_enqueue_acf_scripts_after_cooltimeline', 20);
function re_enqueue_acf_scripts_after_cooltimeline() {
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
 * Inclus le registration handler (AJAX actions).
 */
function include_registration_handler() {
    require_once get_template_directory() . '/assets/php/registration_handler.php';
}
add_action('init', 'include_registration_handler');