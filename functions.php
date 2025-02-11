<?php
/**
 * Theme Functions
 */

/**
 * Enqueue Cool Timeline plugin assets and main.js.
 */
function my_force_load_cool_timeline_css()
{
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
 * (Optionnel) Utiliser la librairie 'ics'
 */
function enqueue_ics_library()
{
    // Utiliser la version minifiée qui expose une variable globale 'ICS'
    wp_enqueue_script(
        'ics-lib',
        'https://unpkg.com/ics/dist/ics.deps.min.js',
        array(),
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_ics_library', 15);

function enqueue_registration_form_script()
{
    wp_enqueue_script(
        'reg-form',
        get_template_directory_uri() . '/assets/js/reg_form.js',
        array('jquery'), // or array() if no dependency is needed
        '1.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'enqueue_registration_form_script');

/**
 * Enqueue the calendar.js module.
 * This file defines CalendarUtils, including our ICS generation function.
 */
function enqueue_calendar_js()
{
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
 * Injecte dans la sortie HTML d’une story l’attribut data-type_dactivite
 * contenant la première valeur du champ ACF "type_dactivite" (le slug, par exemple "atelier").
 *
 * Si le champ ACF n’est pas renseigné, on peut éventuellement le récupérer via la taxonomie "type_dactivites".
 *
 * @param string $output Le HTML de la story.
 * @param array  $args   Les arguments de la story (contenant 'post_id').
 * @return string
 */
function add_type_dactivite_to_story_output($output, $args)
{
    if (isset($args['post_id'])) {
        $post_id = $args['post_id'];
        // Récupère le champ ACF "type_dactivite"
        $acf_types = get_field('type_dactivite', $post_id);
        if (!empty($acf_types) && is_array($acf_types)) {
            $first_type = current($acf_types);
            $attr = ' data-type_dactivite="' . esc_attr($first_type) . '"';
        } else {
            // Optionnel : récupération via la taxonomie "type_dactivites" (si le champ ACF n'est pas renseigné)
            $terms = get_the_terms($post_id, 'type_dactivites');
            if (!empty($terms) && !is_wp_error($terms)) {
                $term = current($terms);
                $attr = ' data-type_dactivite="' . esc_attr($term->term_id) . '"';
            } else {
                $attr = ''; // Aucun type trouvé
            }
        }
        // Injection dans la balise d'ouverture du conteneur principal de la story
        $output = preg_replace('/(<div\s+id="ctl-story-[^"]+")/i', '$1' . $attr, $output, 1);
    }
    return $output;
}
add_filter('cool_timeline_story_output', 'add_type_dactivite_to_story_output', 10, 2);

/**
 * Enqueue custom Cool Timeline JavaScript.
 * This script depends on calendar-js.
 */
function my_cool_timeline_custom_scripts()
{
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
add_action('init', 'create_posttype');

/**
 * (Optional) Enqueue main.js again if necessary.
 */
function oskar_enqueue_scripts()
{
    wp_enqueue_script(
        'main',
        get_template_directory_uri() . '/assets/js/main.js'
    );
}
add_action('wp_enqueue_scripts', 'oskar_enqueue_scripts');

function defer_script($tag, $handle)
{
    if ('main' === $handle) {
        return str_replace('type="text/javascript" src', 'type="module" defer src', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'defer_script', 10, 2);

/**
 * Disable the WYSIWYG editor for pages.
 */
function remove_wysiwyg()
{
    remove_post_type_support('page', 'editor');
}
add_action('init', 'remove_wysiwyg');


function my_handle_registration_ajax()
{
    $registrationData = $_POST;

    if (!isset($registrationData['storyId']) || !isset($registrationData['registrationType'])) {
        wp_send_json_error('Missing data.');
    }

    $storyId = sanitize_text_field($registrationData['storyId']);
    $registrationType = sanitize_text_field($registrationData['registrationType']);
    $firstName = isset($registrationData['firstName']) ? sanitize_text_field($registrationData['firstName']) : '';
    $lastName  = isset($registrationData['lastName'])  ? sanitize_text_field($registrationData['lastName'])  : '';
    $email     = isset($registrationData['email'])     ? sanitize_email($registrationData['email'])          : '';

    // If `eventTitle` provided, use it; else fallback to "event_{$storyId}"
    if (isset($registrationData['eventTitle']) && !empty($registrationData['eventTitle'])) {
        $eventName = $registrationData['eventTitle'];
    } else {
        $eventName = 'event_' . $storyId;
    }
    $folderName = sanitize_title($eventName);

    $formsDir = get_template_directory() . '/assets/forms';
    $eventDir = $formsDir . '/' . $folderName;
    if (!file_exists($eventDir) && !mkdir($eventDir, 0755, true)) {
        wp_send_json_error('Could not create event folder.');
    }

    // Use `registrationType` to determine suffix
    $fileSuffix = ($registrationType === 'benevole') ? 'benevole' : 'participant';
    $filePath = $eventDir . '/registrations_' . $fileSuffix . '.csv';

    $timestamp = date('Y-m-d H:i:s');
    $header = ['firstName','lastName','email','registrationType','timestamp'];
    $data   = [$firstName, $lastName, $email, $registrationType, $timestamp];

    $writeHeader = (!file_exists($filePath) || filesize($filePath) === 0);
    if (($fp = fopen($filePath, 'a')) !== false) {
        if ($writeHeader) {
            fputcsv($fp, $header);
        }
        fputcsv($fp, $data);
        fclose($fp);

        wp_send_json_success([
            'message' => 'Registration saved.',
            'file' => $folderName . '/registrations_' . $fileSuffix . '.csv'
        ]);
    } else {
        wp_send_json_error('Error writing registration file.');
    }
}
add_action('wp_ajax_nopriv_my_registration', 'my_handle_registration_ajax');
add_action('wp_ajax_my_registration', 'my_handle_registration_ajax');


/**
 * Injecte les champs ACF en tant qu’attributs data-* dans chaque story Cool Timeline.
 *
 * On injecte notamment :
 *  - data-preview-url
 *  - data-event-date
 *  - data-start-time / data-end-time
 *  - data-end-date
 *  - data-location-name, data-location-address, data-organizer
 *  - data-type_dactivite (le slug du premier élément du champ ACF "type_dactivite")
 *
 * Ce filtre est appliqué sur "the_content".
 */
add_filter('the_content', 'inject_acf_preview_url_in_cool_timeline', 20);
function inject_acf_preview_url_in_cool_timeline($content)
{
    $pattern = '/(<div\s[^>]*id="ctl-story-(\d+)"[^>]*>)/i';
    $callback = function ($matches) {
        $story_id = (int) $matches[2];

        // Retrieve the event date from 'ctl_story_date'
        $event_date_raw = get_post_meta($story_id, 'ctl_story_date', true);
        $formatted_date = '';
        $formatted_start_time = '';
        if (!empty($event_date_raw)) {
            $date_obj = DateTime::createFromFormat('m/d/Y h:i A', $event_date_raw);
            if ($date_obj) {
                $formatted_date = $date_obj->format('Y-m-d'); // ex. "2025-03-08"
                $formatted_start_time = $date_obj->format('H:i'); // ex. "10:09"
            }
        }

        // Retrieve endDate from ACF (optional)
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

        // Build the <div ...> opening tag with data attributes
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
        // Inject location, organizer, date and times
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

        // IMPORTANT: Inject the ACF field "type_dactivite" (expected to be an array)
        $acf_types = get_field('type_dactivite', $story_id);
        if (!empty($acf_types) && is_array($acf_types)) {
            $div_open_tag .= ' data-type_dactivite=\'' . esc_attr(json_encode($acf_types)) . '\'';
        }

        $evenement_facebook = get_field('evenement_facebook', $story_id);
        if ($evenement_facebook) {
            // Depending on your configuration, this may return an array or a simple value.
            // For simplicity, we assume a simple value here.
            $div_open_tag .= ' data-evenement-facebook="' . esc_attr($evenement_facebook) . '"';
        }
        $lien_facebook = get_field('lien_facebook', $story_id);
        if ($lien_facebook) {
            $div_open_tag .= ' data-lien-facebook="' . esc_attr($lien_facebook) . '"';
        }
        $evenement_instagram = get_field('evenement_instagram', $story_id);
        if ($evenement_instagram) {
            // Depending on your configuration, this may return an array or a simple value.
            // For simplicity, we assume a simple value here.
            $div_open_tag .= ' data-evenement-instagram="' . esc_attr($evenement_instagram) . '"';
        }
        $lien_instagram = get_field('lien_instagram', $story_id);
        if ($lien_instagram) {
            $div_open_tag .= ' data-lien-instagram="' . esc_attr($lien_instagram) . '"';
        }
        // ======================================================

        $div_open_tag .= '>';

        error_log("Injecting for story #{$story_id}: date={$formatted_date}, startTime={$formatted_start_time}, endDate={$formatted_end_date}, endTime={$formatted_end_time}");

        return $div_open_tag;
    };

    return preg_replace_callback($pattern, $callback, $content) ?: $content;
}
add_filter('the_content', 'inject_acf_preview_url_in_cool_timeline', 20);

function my_cancel_registration_ajax() {
    // Récupération et validation des paramètres.
    $storyId = sanitize_text_field($_POST['storyId'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    if (empty($storyId) || empty($email)) {
        wp_send_json_error("Données manquantes.");
    }
    
    // Définir le chemin du fichier CSV.
    // Adaptez la logique ici en fonction de la manière dont vous nommez vos dossiers.
    // Par exemple, si vos dossiers sont nommés avec un slug basé sur l'événement,
    // vous devrez récupérer ce slug. Pour l'exemple, on utilise "event_" + $storyId.
    $formsDir = get_template_directory() . '/assets/forms';
    $eventDir = $formsDir . '/' . sanitize_title('event_' . $storyId);
    // Supposons que l'inscription se trouve dans le fichier registrations_participant.csv.
    $filePath = $eventDir . '/registrations_participant.csv';
    
    if (removeRegistrationFromCSV($filePath, $email)) {
        wp_send_json_success("Inscription supprimée.");
    } else {
        wp_send_json_error("Erreur lors de la suppression de l'inscription.");
    }
}
add_action('wp_ajax_nopriv_cancel_registration', 'my_cancel_registration_ajax');
add_action('wp_ajax_cancel_registration', 'my_cancel_registration_ajax');

function removeRegistrationFromCSV($filePath, $email) {
    if (!file_exists($filePath)) {
        error_log("Le fichier n'existe pas: " . $filePath);
        return false;
    }

    $rows = [];
    $header = null;

    if (($handle = fopen($filePath, "r")) !== false) {
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            error_log("Impossible de lire l'en-tête du CSV");
            return false;
        }
        while (($data = fgetcsv($handle)) !== false) {
            // Ici, on suppose que l'email est dans la 3e colonne (index 2).
            if (isset($data[2]) && strtolower(trim($data[2])) === strtolower(trim($email))) {
                // On ne l'ajoute pas.
                continue;
            }
            $rows[] = $data;
        }
        fclose($handle);
    } else {
        error_log("Impossible d'ouvrir le fichier: " . $filePath);
        return false;
    }

    // Réécriture du fichier CSV.
    if (($handle = fopen($filePath, "w")) !== false) {
        fputcsv($handle, $header);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
        return true;
    } else {
        error_log("Impossible d'ouvrir le fichier en écriture: " . $filePath);
        return false;
    }
}

/**
 * Re-enqueue ACF scripts after Cool Timeline scripts.
 */
add_action('wp_loaded', 're_enqueue_acf_scripts_after_cooltimeline', 20);
function re_enqueue_acf_scripts_after_cooltimeline()
{
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
