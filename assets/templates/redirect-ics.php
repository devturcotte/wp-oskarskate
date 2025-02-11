<?php
// redirect-ics.php
// Ce fichier fait la redirection HTTP 303 vers le vrai .ics

// Si vous avez besoin d'identifier un événement particulier :
$event_id = isset($_GET['eventid']) ? (int) $_GET['eventid'] : 0;

// Imaginons que vous sachiez construire l'URL du .ics via l'ID
// ou alors vous mettez en dur l'URL d'un .ics sur votre serveur / S3
$ics_url = 'https://exemple.com/events/monEvenement.ics';

// Réponse HTTP : 303 => iOS comprendra qu'il faut juste ouvrir le fichier .ics
header('HTTP/1.1 303 See Other');
header('Location: ' . $ics_url);
exit;
