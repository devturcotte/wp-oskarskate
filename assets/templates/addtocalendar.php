<?php
/**
 * Template Part: Custom Add To Calendar
 *
 * This template outputs a custom "Add to Calendar" button and modal.
 *
 * You can include this file in your event template using:
 * <?php get_template_part('templates/addtocalendar'); ?>
 */

// For demonstration, we'll use placeholders for event details.
$event_title = "Événement N01";
$event_start_date = "2025-02-01";
$event_start_time = "00:00";
$event_end_date = "2025-02-01";
$event_end_time = "17:00";
$event_description = "<p>Skate ipsum dolor sit amet, frontside Rune Glifberg crail slide gnar bucket kingpin. Tuna-flip frontside air Kevin Jarvis boardslide axle. Vernon Courtland Johnson switch melancholy flypaper nose blunt. No comply rail Willy Santos acid drop regular footed.</p>";
$event_location = "2905 Boul de Portland";

// Format dates for calendar services.
$start_datetime = gmdate("Ymd\THis\Z", strtotime($event_start_date . " " . $event_start_time));
$end_datetime = gmdate("Ymd\THis\Z", strtotime($event_end_date . " " . $event_end_time));

// Generate URLs for each calendar service:

// Google Calendar.
$google_url = "https://www.google.com/calendar/render?action=TEMPLATE&text=" . urlencode($event_title) .
    "&dates=" . $start_datetime . "/" . $end_datetime .
    "&details=" . urlencode(strip_tags($event_description)) .
    "&location=" . urlencode($event_location);

// Apple Calendar & iCal: For simplicity, these could point to an ICS file.
// (You'll need to implement ICS file generation or link to a static file.)
$apple_url = "#";  // Replace with your ICS URL if available.
$ical_url = "#";  // Replace with your ICS URL if available.

// Outlook.com Calendar.
$outlook_url = "https://outlook.live.com/owa/?path=/calendar/action/compose&rru=addevent" .
    "&subject=" . urlencode($event_title) .
    "&body=" . urlencode(strip_tags($event_description)) .
    "&startdt=" . urlencode($event_start_date . "T" . $event_start_time) .
    "&enddt=" . urlencode($event_end_date . "T" . $event_end_time);

// Yahoo Calendar.
$yahoo_url = "https://calendar.yahoo.com/?v=60&view=d&type=20" .
    "&title=" . urlencode($event_title) .
    "&st=" . $start_datetime .
    "&et=" . $end_datetime .
    "&desc=" . urlencode(strip_tags($event_description)) .
    "&in_loc=" . urlencode($event_location);
?>

<div class="custom-add-to-calendar">
    <button id="open-atcb-modal">
        <i class="fa-regular fa-calendar-plus"></i> Ajouter à mon calendrier
    </button>
    <div id="atcb-modal" class="atcb-modal">
        <h3>Ajouter l'événement à votre calendrier</h3>
        <ul>
            <li><a href="<?php echo esc_url($apple_url); ?>" class="calendar-link" data-service="apple"
                    target="_blank">Apple Calendar</a></li>
            <li><a href="<?php echo esc_url($google_url); ?>" class="calendar-link" data-service="google"
                    target="_blank">Google Calendar</a></li>
            <li><a href="<?php echo esc_url($ical_url); ?>" class="calendar-link" data-service="ical"
                    target="_blank">iCal</a></li>
            <li><a href="<?php echo esc_url($outlook_url); ?>" class="calendar-link" data-service="outlook"
                    target="_blank">Outlook.com</a></li>
            <li><a href="<?php echo esc_url($yahoo_url); ?>" class="calendar-link" data-service="yahoo"
                    target="_blank">Yahoo</a></li>
        </ul>
        <button id="close-atcb-modal">Fermer</button>
    </div>
</div>