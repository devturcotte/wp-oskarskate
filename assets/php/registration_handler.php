<?php
/**
 * registration_handler.php
 *
 * Ce fichier gère via AJAX l'inscription et l'annulation d'inscription à un événement.
 * Les inscriptions sont enregistrées dans un fichier CSV unique par événement, organisé en deux sections :
 * - La section "Participants" (en haut)
 * - La section "Bénévoles" (en bas)
 *
 * Chaque ligne a le format : firstName,lastName,email,registrationType,timestamp
 */

header('Content-Type: application/json');

// Si ce fichier est appelé directement (hors contexte WP), décommentez la ligne suivante
// require_once dirname(__FILE__, 3) . '/wp-load.php';

/* ---------------------- INSCRIPTION ------------------------- */
if (isset($_POST['action']) && $_POST['action'] == 'my_registration') {

    // Vérification de base
    if (empty($_POST['storyId']) || empty($_POST['registrationType'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Données manquantes (storyId, registrationType).'
        ]);
        exit;
    }

    // Sanitize des données
    $storyId = sanitize_text_field($_POST['storyId']);
    $registrationType = sanitize_text_field($_POST['registrationType']); // "participant" ou "benevole"
    $firstName = isset($_POST['firstName']) ? sanitize_text_field($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? sanitize_text_field($_POST['lastName']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

    // Détermination du nom de l'événement et du dossier associé
    if (!empty($_POST['eventTitle'])) {
        $eventName = $_POST['eventTitle'];
    } else {
        $eventName = "event_{$storyId}";
    }
    $folderName = sanitize_title($eventName);

    // Construction du chemin
    $formsDir = get_template_directory() . '/assets/forms';
    $eventDir = $formsDir . '/' . $folderName;
    if (!file_exists($eventDir) && !mkdir($eventDir, 0755, true)) {
        wp_send_json_error("Impossible de créer le dossier de l'événement à : $eventDir");
        exit;
    }
    $filePath = $eventDir . '/event_registrations.csv';

    // Vérification que l'événement accepte les inscriptions (champ ACF "besoin_dinscriptions")
    $besoin_dinscriptions = get_field('besoin_dinscriptions', $storyId);
    error_log("DEBUG besoin_dinscriptions for post $storyId: " . print_r($besoin_dinscriptions, true));
    
    // Determine if inscriptions are open.
    $inscriptions_open = false;
    if ( is_array($besoin_dinscriptions) ) {
        // If it returns an array, check if "Oui" or "1" is present.
        foreach ( $besoin_dinscriptions as $item ) {
            if ( is_array($item) ) {
                // In case of "Both" return type (an array with keys "value" and "label")
                if ( isset($item['value']) && ( $item['value'] === 'Oui' || $item['value'] === '1' ) ) {
                    $inscriptions_open = true;
                    break;
                }
            } else {
                if ( trim($item) === 'Oui' || trim($item) === '1' ) {
                    $inscriptions_open = true;
                    break;
                }
            }
        }
    } else {
        // If it's not an array, compare directly.
        $inscriptions_open = ( trim($besoin_dinscriptions) === 'Oui' || trim($besoin_dinscriptions) === '1' );
    }
    
    if ( ! $inscriptions_open ) {
        wp_send_json_error('Les inscriptions sont fermées pour cet événement.');
        exit;
    }

    // Évite les doublons en supprimant toute inscription existante pour cet email
    removeByEmail($participants, $email);
    removeByEmail($benevoles, $email);

    // Prépare la nouvelle inscription
    $entry = [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'email' => strtolower(trim($email)),
        'registrationType' => $registrationType,
        'timestamp' => date('Y-m-d H:i:s'),
    ];
    if ($registrationType === 'benevole') {
        $benevoles[] = $entry;
    } else {
        $participants[] = $entry;
    }

    // Réécrit le fichier CSV avec les deux sections
    if (rewriteEventCSV($filePath, $existingEventName, $participants, $benevoles)) {
        wp_send_json_success([
            'success' => true,
            'message' => 'Inscription enregistrée.',
            'file' => $folderName . '/event_registrations.csv'
        ]);
    } else {
        wp_send_json_error('Erreur lors de l\'écriture du fichier d\'inscription.');
    }
    exit;
}

/* ---------------------- ANNULATION ------------------------- */
if (isset($_POST['action']) && $_POST['action'] == 'cancel_registration') {
    $storyId = sanitize_text_field($_POST['storyId'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $eventSlug = sanitize_title($_POST['eventSlug'] ?? '');

    if (empty($storyId) || empty($email)) {
        wp_send_json_error("Données manquantes.");
        exit;
    }
    if (empty($eventSlug)) {
        $eventSlug = 'event_' . $storyId;
    }
    $formsDir = get_template_directory() . '/assets/forms';
    $eventDir = $formsDir . '/' . $eventSlug;
    $filePath = $eventDir . '/event_registrations.csv';
    if (!file_exists($filePath)) {
        wp_send_json_error("Fichier d’inscription introuvable pour cet événement.");
        exit;
    }
    list($eventName, $participants, $benevoles) = parseEventCSV($filePath);
    removeByEmail($participants, $email);
    removeByEmail($benevoles, $email);
    if (rewriteEventCSV($filePath, $eventName, $participants, $benevoles)) {
        wp_send_json_success("Inscription supprimée.");
    } else {
        wp_send_json_error("Erreur lors de la suppression de l'inscription (rewrite failed).");
    }
    exit;
}

/* ------------------ FONCTIONS UTILITAIRES ------------------ */

/**
 * parseEventCSV($filePath)
 *
 * Lit le fichier CSV organisé en deux sections :
 *   - Participants
 *   - Bénévoles
 *
 * Retourne : [ $eventTitle, $participantsArray, $benevolesArray ]
 */
function parseEventCSV($filePath)
{
    if (!file_exists($filePath)) {
        return [null, [], []];
    }
    $fp = fopen($filePath, 'r');
    if (!$fp) {
        return [null, [], []];
    }
    $eventTitle = null;
    $participants = [];
    $benevoles = [];
    $currentSection = null;
    while (($line = fgets($fp)) !== false) {
        $line = trim($line);
        if ($line === '')
            continue;
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
        if (stripos($line, 'Description:') === 0)
            continue;
        $parts = str_getcsv($line);
        if (count($parts) >= 5 && $currentSection) {
            $row = [
                'firstName' => $parts[0],
                'lastName' => $parts[1],
                'email' => strtolower(trim($parts[2])),
                'registrationType' => $parts[3],
                'timestamp' => $parts[4],
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
 *
 * Réécrit le fichier CSV avec la structure suivante :
 *
 *   Événement: $eventName
 *
 *   Titre de la liste: Participants
 *   Description: firstName,lastName,email,registrationType,timestamp
 *   <lignes pour les participants>
 *
 *   Titre de la liste: Bénévoles
 *   Description: firstName,lastName,email,registrationType,timestamp
 *   <lignes pour les bénévoles>
 */
function rewriteEventCSV($filePath, $eventName, $participants, $benevoles)
{
    $fp = fopen($filePath, 'w');
    if (!$fp) {
        return false;
    }
    fwrite($fp, "Événement: {$eventName}\n\n");
    fwrite($fp, "Titre de la liste: Participants\n");
    fwrite($fp, "Description: firstName,lastName,email,registrationType,timestamp\n");
    foreach ($participants as $p) {
        $line = implode(',', [
            $p['firstName'],
            $p['lastName'],
            $p['email'],
            $p['registrationType'],
            $p['timestamp']
        ]);
        fwrite($fp, $line . "\n");
    }
    fwrite($fp, "\n");
    fwrite($fp, "Titre de la liste: Bénévoles\n");
    fwrite($fp, "Description: firstName,lastName,email,registrationType,timestamp\n");
    foreach ($benevoles as $b) {
        $line = implode(',', [
            $b['firstName'],
            $b['lastName'],
            $b['email'],
            $b['registrationType'],
            $b['timestamp']
        ]);
        fwrite($fp, $line . "\n");
    }
    fwrite($fp, "\n");
    fclose($fp);
    return true;
}

/**
 * removeByEmail(&$array, $email)
 *
 * Supprime de l'array toute entrée dont l'email correspond (insensible à la casse).
 */
function removeByEmail(&$array, $email)
{
    $emailLower = strtolower(trim($email));
    foreach ($array as $i => $row) {
        if (strtolower(trim($row['email'])) === $emailLower) {
            unset($array[$i]);
        }
    }
}
?>