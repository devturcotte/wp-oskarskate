<?php
/**
 * registration_handler.php
 *
 * This script receives the registration data from your form via POST,
 * then creates (or uses an existing) folder for the event in your theme’s
 * assets/forms directory. It writes (or appends) the registration data into a
 * CSV file within that event’s folder.
 */

header('Content-Type: application/json');

// -----------------------------------------------------------------
// 1. Load WordPress functions if needed
// -----------------------------------------------------------------
// If this file is accessed directly, you might need to load WP functions.
// Adjust the path if necessary.
// require_once( dirname(__FILE__, 3) . '/wp-load.php' );

// -----------------------------------------------------------------
// 2. Retrieve and sanitize the form data
// -----------------------------------------------------------------
$registrationData = $_POST;

if (!isset($registrationData['storyId']) || !isset($registrationData['registrationType'])) {
    echo json_encode(['success' => false, 'message' => 'Missing data.']);
    exit;
}

$storyId          = sanitize_text_field($registrationData['storyId']);
$registrationType = sanitize_text_field($registrationData['registrationType']); // "participant" or "benevole"
$firstName        = isset($registrationData['firstName']) ? sanitize_text_field($registrationData['firstName']) : '';
$lastName         = isset($registrationData['lastName'])  ? sanitize_text_field($registrationData['lastName'])  : '';
$email            = isset($registrationData['email'])     ? sanitize_email($registrationData['email']) : '';

// -----------------------------------------------------------------
// 3. Determine the event folder name using the event title if provided.
// -----------------------------------------------------------------
if (isset($registrationData['eventTitle']) && !empty($registrationData['eventTitle'])) {
    $eventName = $registrationData['eventTitle'];
} else {
    $eventName = (function_exists('get_event_name') && get_event_name($storyId)) ? get_event_name($storyId) : 'event_' . $storyId;
}
$folderName = sanitize_title($eventName);

// -----------------------------------------------------------------
// 4. Create the event folder inside your theme's assets/forms folder if it doesn't exist
// -----------------------------------------------------------------
$formsDir = get_template_directory() . '/assets/forms';
$eventDir = $formsDir . '/' . $folderName;

if (!file_exists($eventDir)) {
    if (!mkdir($eventDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'Could not create event folder.']);
        exit;
    }
}

// -----------------------------------------------------------------
// 5. Determine the CSV file name based on registration type.
// -----------------------------------------------------------------
$fileSuffix = ($registrationType === 'benevole') ? 'benevole' : 'participant';
$filePath   = $eventDir . '/registrations_' . $fileSuffix . '.csv';

// -----------------------------------------------------------------
// 6. Prepare the data to be recorded
// -----------------------------------------------------------------
$timestamp = date('Y-m-d H:i:s');
$header    = ['firstName', 'lastName', 'email', 'registrationType', 'timestamp'];
$data      = [$firstName, $lastName, $email, $registrationType, $timestamp];

// If the file does not exist or is empty, we will write the header first.
$writeHeader = !file_exists($filePath) || filesize($filePath) === 0;

// -----------------------------------------------------------------
// 7. Write the registration data into the CSV file
// -----------------------------------------------------------------
if (($fp = fopen($filePath, 'a')) !== false) {
    if ($writeHeader) {
        fputcsv($fp, $header);
    }
    fputcsv($fp, $data);
    fclose($fp);
    echo json_encode([
        'success' => true,
        'message' => 'Registration saved.',
        'file'    => $folderName . '/registrations_' . $fileSuffix . '.csv'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error writing registration file.'
    ]);
}
