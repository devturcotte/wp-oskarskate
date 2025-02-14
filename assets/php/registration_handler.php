<?php
/**
 * registration_handler.php
 *
 * Gère les actions AJAX :
 *  - get_registrations : Récupère la liste actuelle (participants, bénévoles) + compte + ACF.
 *  - my_registration   : Inscrit un nouvel utilisateur (participant ou bénévole) dans le CSV.
 *  - cancel_registration : Annule une inscription.
 *
 * Format CSV : 2 sections (Participants / Bénévoles), chaque ligne :
 *    firstName,lastName,email,registrationType,timestamp,regCount
 *  - regCount = ordre d'arrivée (1,2,3,...)
 */

// Si ce fichier est appelé directement (hors WP), décommentez la ligne suivante :
// require_once dirname(__FILE__, 3) . '/wp-load.php';

// ------------------------------------------------------------------------
// 1) ACTION : GET_REGISTRATIONS
// ------------------------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'get_registrations') {
    $storyId = isset($_POST['storyId']) ? sanitize_text_field($_POST['storyId']) : '';
    if (empty($storyId)) {
        wp_send_json_error('Aucun storyId fourni.');
        exit;
    }
    // Récupère le titre de l'événement (pour nommer le CSV) depuis WP
    $eventName = get_the_title($storyId);
    if (!$eventName) {
        $eventName = "event_{$storyId}";
    }
    $folderName = sanitize_title($eventName);

    // Emplacement du CSV
    $formsDir = get_template_directory() . '/assets/forms';
    $eventDir = $formsDir . '/' . $folderName;
    $filePath = $eventDir . '/event_registrations.csv';

    $participants = [];
    $benevoles = [];
    if (file_exists($filePath)) {
        list($existingTitle, $participants, $benevoles) = parseEventCSV($filePath);
    }

    // Lit les champs ACF (capacité max, etc.)
    $maxParticipants = get_field('nombre_de_places_participants', $storyId);
    $maxBenevoles    = get_field('nombre_de_places_benevoles', $storyId);

    // Calcule le nombre d'inscrits
    $countParticipants = count($participants);
    $countBenevoles    = count($benevoles);

    // Réponse JSON
    wp_send_json_success([
        'countParticipants' => $countParticipants,
        'countBenevoles'    => $countBenevoles,
        'maxParticipants'   => $maxParticipants,
        'maxBenevoles'      => $maxBenevoles,
    ]);
    exit;
}

// ------------------------------------------------------------------------
// 2) ACTION : MY_REGISTRATION (nouvelle inscription)
// ------------------------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'my_registration') {
    // Vérification
    if (empty($_POST['storyId']) || empty($_POST['registrationType'])) {
        wp_send_json_error('Paramètres insuffisants (storyId, registrationType manquants).');
        exit;
    }

    // Récupération & assainissement
    $storyId         = sanitize_text_field($_POST['storyId']);
    $registrationType= strtolower(sanitize_text_field($_POST['registrationType'])); // "participant" ou "benevole"
    $firstName       = sanitize_text_field($_POST['firstName'] ?? '');
    $lastName        = sanitize_text_field($_POST['lastName']  ?? '');
    $email           = strtolower(trim(sanitize_email($_POST['email'] ?? '')));
    $eventTitle      = sanitize_text_field($_POST['eventTitle'] ?? ("event_{$storyId}"));

    $folderName = sanitize_title($eventTitle);

    // Dossier/fichier CSV
    $formsDir = get_template_directory() . '/assets/forms';
    $eventDir = $formsDir . '/' . $folderName;
    if (!file_exists($eventDir) && !mkdir($eventDir, 0755, true)) {
        wp_send_json_error("Impossible de créer le dossier: $eventDir");
        exit;
    }
    $filePath = $eventDir . '/event_registrations.csv';

    // Vérifie si l'événement accepte les inscriptions (ACF "besoin_dinscriptions")
    $besoin_dinscriptions = get_field('besoin_dinscriptions', $storyId);
    $inscriptions_open = false;
    if (is_array($besoin_dinscriptions)) {
        // Gère le cas d'un tableau
        foreach ($besoin_dinscriptions as $val) {
            if (is_array($val)) {
                if (!empty($val['value']) && ($val['value'] === 'Oui' || $val['value'] === '1')) {
                    $inscriptions_open = true;
                    break;
                }
            } else {
                if (trim($val) === 'Oui' || trim($val) === '1') {
                    $inscriptions_open = true;
                    break;
                }
            }
        }
    } else {
        // Gère le cas d'une simple chaîne
        if (trim($besoin_dinscriptions) === 'Oui' || trim($besoin_dinscriptions) === '1') {
            $inscriptions_open = true;
        }
    }
    if (!$inscriptions_open) {
        wp_send_json_error('Les inscriptions ne sont pas ouvertes pour cet événement.');
        exit;
    }

    // Lecture du CSV existant
    $participants = [];
    $benevoles    = [];
    $existingEventName = $eventTitle; // on va le surcharger si on trouve un autre titre
    if (file_exists($filePath)) {
        list($existingEventName, $participants, $benevoles) = parseEventCSV($filePath);
        if (!$existingEventName) $existingEventName = $eventTitle;
    }

    // Retire toute occurrence pour cet email (évite doublons)
    removeByEmail($participants, $email);
    removeByEmail($benevoles, $email);

    // Récupère la capacité max (ACF)
    $maxParticipants = get_field('nombre_de_places_participants', $storyId);
    $maxBenevoles    = get_field('nombre_de_places_benevoles', $storyId);

    // Vérification du capacity "avant" d'ajouter
    if ($registrationType === 'participant') {
        if (!empty($maxParticipants) && count($participants) >= intval($maxParticipants)) {
            wp_send_json_error("Les places ont toutes été comblées, merci!");
            exit;
        }
    } else { // benevole
        if (!empty($maxBenevoles) && count($benevoles) >= intval($maxBenevoles)) {
            wp_send_json_error("Nous avons tous nos bénévoles.");
            exit;
        }
    }

    // On prépare la nouvelle ligne
    $entry = [
        'firstName'        => $firstName,
        'lastName'         => $lastName,
        'email'            => $email,
        'registrationType' => $registrationType,
        'timestamp'        => date('Y-m-d H:i:s'),
        'regCount'         => '', // on va définir ci-dessous
    ];

    // Ajoute au bon tableau en incrémentant regCount
    if ($registrationType === 'benevole') {
        $entry['regCount'] = count($benevoles) + 1;
        $benevoles[]       = $entry;
    } else {
        $entry['regCount'] = count($participants) + 1;
        $participants[]    = $entry;
    }

    // Réécrit le CSV
    if (!rewriteEventCSV($filePath, $existingEventName, $participants, $benevoles)) {
        wp_send_json_error("Erreur d'écriture du CSV.");
        exit;
    }

    // Réponse : on renvoie aussi le nouveau total
    wp_send_json_success([
        'message'            => 'Inscription enregistrée.',
        'registrationType'   => $registrationType,
        'countParticipants'  => count($participants),
        'countBenevoles'     => count($benevoles),
    ]);
    exit;
}

// ------------------------------------------------------------------------
// 3) ACTION : CANCEL_REGISTRATION (annulation)
// ------------------------------------------------------------------------
if (isset($_POST['action']) && $_POST['action'] === 'cancel_registration') {
    $storyId    = sanitize_text_field($_POST['storyId'] ?? '');
    $email      = strtolower(trim(sanitize_email($_POST['email'] ?? '')));
    $eventTitle = sanitize_text_field($_POST['eventTitle'] ?? ("event_{$storyId}"));
    if (empty($storyId) || empty($email)) {
        wp_send_json_error("Paramètres manquants pour annuler l'inscription.");
        exit;
    }

    $folderName = sanitize_title($eventTitle);
    $formsDir   = get_template_directory() . '/assets/forms';
    $eventDir   = $formsDir . '/' . $folderName;
    $filePath   = $eventDir . '/event_registrations.csv';
    if (!file_exists($filePath)) {
        wp_send_json_error("Fichier d'inscription introuvable pour cet événement.");
        exit;
    }

    // Lit le CSV, supprime l'entrée correspondant à l'email
    $participants = [];
    $benevoles    = [];
    $evtName      = '';
    list($evtName, $participants, $benevoles) = parseEventCSV($filePath);
    removeByEmail($participants, $email);
    removeByEmail($benevoles, $email);

    // Réécriture
    if (!rewriteEventCSV($filePath, $evtName, $participants, $benevoles)) {
        wp_send_json_error("Erreur lors de la suppression de l'inscription (rewrite CSV a échoué).");
        exit;
    }

    wp_send_json_success("Inscription annulée.");
    exit;
}

// ------------------------------------------------------------------------
// FONCTIONS UTILITAIRES : parseEventCSV, rewriteEventCSV, removeByEmail
// ------------------------------------------------------------------------

/**
 * parseEventCSV($filePath)
 * Lit le fichier CSV avec 2 sections (Participants / Bénévoles).
 * Format attendu par ligne de données:
 *    firstName,lastName,email,registrationType,timestamp,regCount
 * Retourne [ $eventTitle, $arrayParticipants, $arrayBenevoles ].
 */
function parseEventCSV($filePath) {
    if (!file_exists($filePath)) {
        return [null, [], []];
    }
    $fp = fopen($filePath, 'r');
    if (!$fp) {
        return [null, [], []];
    }

    $eventTitle   = null;
    $participants = [];
    $benevoles    = [];
    $currentSection = null;

    while (($line = fgets($fp)) !== false) {
        $line = trim($line);
        if ($line === '') continue;

        // Gère la détection de sections
        if (stripos($line, 'Événement:') === 0) {
            $eventTitle = trim(substr($line, strlen('Événement:')));
            continue;
        }
        if (stripos($line, 'Titre de la liste: Participants') === 0) {
            $currentSection = 'participants';
            continue;
        }
        if (stripos($line, 'Titre de la liste: Bénévoles') === 0) {
            $currentSection = 'benevoles';
            continue;
        }
        if (stripos($line, 'Description:') === 0) {
            // on ignore la ligne description
            continue;
        }

        // Sinon, on parse la ligne CSV
        $parts = str_getcsv($line); // sépare par virgule
        // On attend 6 colonnes
        if (count($parts) >= 5 && $currentSection) {
            $row = [
                'firstName'        => $parts[0],
                'lastName'         => $parts[1],
                'email'            => strtolower(trim($parts[2])),
                'registrationType' => $parts[3],
                'timestamp'        => $parts[4],
                'regCount'         => $parts[5] ?? '', // 6e col
            ];
            if ($currentSection === 'participants') {
                $participants[] = $row;
            } else {
                $benevoles[] = $row;
            }
        }
    }
    fclose($fp);

    return [$eventTitle, $participants, $benevoles];
}

/**
 * rewriteEventCSV($filePath, $eventName, $participants, $benevoles)
 * Réécrit le fichier CSV complet avec 2 sections.
 * Format:
 *    Événement: ...
 *
 *    Titre de la liste: Participants
 *    Description: firstName,lastName,email,registrationType,timestamp,regCount
 *    <lignes participants>
 *
 *    Titre de la liste: Bénévoles
 *    Description: firstName,lastName,email,registrationType,timestamp,regCount
 *    <lignes bénévoles>
 */
function rewriteEventCSV($filePath, $eventName, $participants, $benevoles) {
    $fp = fopen($filePath, 'w');
    if (!$fp) {
        return false;
    }
    fwrite($fp, "Événement: {$eventName}\n\n");

    // --- Participants ---
    fwrite($fp, "Titre de la liste: Participants\n");
    fwrite($fp, "Description: firstName,lastName,email,registrationType,timestamp,regCount\n");
    foreach ($participants as $p) {
        $line = implode(',', [
            $p['firstName'] ?? '',
            $p['lastName'] ?? '',
            $p['email'] ?? '',
            $p['registrationType'] ?? '',
            $p['timestamp'] ?? '',
            $p['regCount'] ?? '',
        ]);
        fwrite($fp, $line . "\n");
    }
    fwrite($fp, "\n");

    // --- Bénévoles ---
    fwrite($fp, "Titre de la liste: Bénévoles\n");
    fwrite($fp, "Description: firstName,lastName,email,registrationType,timestamp,regCount\n");
    foreach ($benevoles as $b) {
        $line = implode(',', [
            $b['firstName'] ?? '',
            $b['lastName'] ?? '',
            $b['email'] ?? '',
            $b['registrationType'] ?? '',
            $b['timestamp'] ?? '',
            $b['regCount'] ?? '',
        ]);
        fwrite($fp, $line . "\n");
    }
    fwrite($fp, "\n");

    fclose($fp);
    return true;
}

/**
 * removeByEmail(&$array, $email)
 * Retire toute entrée ayant un 'email' identique (insensible à la casse).
 */
function removeByEmail(&$array, $email) {
    $emailLower = strtolower(trim($email));
    foreach ($array as $i => $row) {
        if (strtolower(trim($row['email'] ?? '')) === $emailLower) {
            unset($array[$i]);
        }
    }
}
