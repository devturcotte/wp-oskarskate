/**
 * Removes all HTML tags from a string.
 * @param {string} html
 * @returns {string}
 */
function stripHTML(html) {
  const tmp = document.createElement("DIV");
  tmp.innerHTML = html;
  return tmp.textContent || tmp.innerText || "";
}

/**
 * Parses a date (in "YYYY-MM-DD" or ISO format) and a time ("HH:mm")
 * to create a local Date object.
 * @param {string} dateStr - The date string (e.g., "2025-02-04").
 * @param {string} timeStr - The time string (e.g., "14:09").
 * @returns {Date}
 */
function parseEventDate(dateStr, timeStr) {
  const datePart = dateStr.indexOf("T") !== -1 ? dateStr.split("T")[0] : dateStr;
  return new Date(datePart + "T" + timeStr);
}

/**
 * Formats a Date into the ICS UTC format (e.g., 20250305T101500Z).
 * @param {Date} date
 * @returns {string}
 */
function gmdate(date) {
  if (!(date instanceof Date) || isNaN(date.getTime())) {
    console.error("gmdate error: invalid date", date);
    return "";
  }
  const iso = date.toISOString(); // e.g., "2025-03-05T10:15:00.000Z"
  return iso.replace(/[-:]/g, "").split(".")[0] + "Z";
}

/**
 * Constructs a date string for Yahoo Calendar in the format YYYYMMDDTHHMM (no seconds).
 * @param {Date} date
 * @returns {string}
 */
function formatYahooDateTime(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  return `${year}${month}${day}T${hours}${minutes}`;
}

/**
 * Formats a Date for Outlook.com in the format YYYY-MM-DDTHH:mm:ss (local time).
 * @param {Date} date
 * @returns {string}
 */
function formatOutlookDateTime(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  const seconds = String(date.getSeconds()).padStart(2, "0");
  return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
}

/**
 * --------------------------------------------------------------------------------
 * 1) Build a dynamic event location string
 * --------------------------------------------------------------------------------
 *
 * Concatenates available information (venue name and address)
 * to form the full location string.
 *
 * @param {Object} eventData - The event data.
 * @returns {string} The complete location.
 */
function buildEventLocation(eventData) {
  let location = "";
  if (eventData.locationName) {
    location += eventData.locationName;
  }
  if (eventData.locationAddress) {
    if (location !== "") {
      location += ", ";
    }
    location += eventData.locationAddress;
  }
  return location;
}

/**
 * --------------------------------------------------------------------------------
 * 2) Generation of Calendar URLs (Google, Yahoo, Outlook)
 * --------------------------------------------------------------------------------
 */
function generateCalendarURLs(eventData) {
  // Fallback: if endDate is missing, use startDate.
  const finalStartDate = eventData.startDate || "";
  const finalEndDate = eventData.endDate || finalStartDate;
  const finalStartTime = eventData.startTime || "10:15";
  const finalEndTime = eventData.endTime || "23:30";

  const startDateTime = parseEventDate(finalStartDate, finalStartTime);
  const endDateTime = parseEventDate(finalEndDate, finalEndTime);

  if (isNaN(startDateTime.getTime()) || isNaN(endDateTime.getTime())) {
    console.error("Invalid date/time for event:", eventData);
    return {
      google: "#",
      outlook: "#",
      yahoo: "#"
    };
  }

  const googleFormat = gmdate(startDateTime) + "/" + gmdate(endDateTime);
  const outlookStartFormat = formatOutlookDateTime(startDateTime);
  const outlookEndFormat = formatOutlookDateTime(endDateTime);
  const yahooStartFormat = formatYahooDateTime(startDateTime);
  const yahooEndFormat = formatYahooDateTime(endDateTime);

  const googleURL =
    "https://www.google.com/calendar/render?action=TEMPLATE" +
    "&text=" + encodeURIComponent(eventData.title) +
    "&dates=" + encodeURIComponent(googleFormat) +
    "&details=" + encodeURIComponent(stripHTML(eventData.description)) +
    "&location=" + encodeURIComponent(buildEventLocation(eventData));

  const outlookURL =
    "https://outlook.live.com/owa/?path=/calendar/action/compose&rru=addevent" +
    "&subject=" + encodeURIComponent(eventData.title) +
    "&startdt=" + encodeURIComponent(outlookStartFormat) +
    "&enddt=" + encodeURIComponent(outlookEndFormat) +
    "&body=" + encodeURIComponent(stripHTML(eventData.description)) +
    "&location=" + encodeURIComponent(buildEventLocation(eventData));

  const yahooURL =
    "https://calendar.yahoo.com/?v=60&view=d&type=20" +
    "&title=" + encodeURIComponent(eventData.title) +
    "&st=" + encodeURIComponent(yahooStartFormat) +
    "&et=" + encodeURIComponent(yahooEndFormat) +
    "&desc=" + encodeURIComponent(stripHTML(eventData.description)) +
    "&in_loc=" + encodeURIComponent(buildEventLocation(eventData));

  return {
    google: googleURL,
    outlook: outlookURL,
    yahoo: yahooURL
  };
}

/**
 * --------------------------------------------------------------------------------
 * 3) Build the HTML dropdown for calendar options.
 * --------------------------------------------------------------------------------
 */
function buildCalendarDropdown(calURLs) {
  return `
    <ul class="calendar-options">
      <!-- External links for Google, Outlook, Yahoo -->
      <li><a href="${calURLs.google}" target="_blank">Google Calendar</a></li>
      <li><a href="${calURLs.outlook}" target="_blank">Outlook.com</a></li>
      <li><a href="${calURLs.yahoo}" target="_blank">Yahoo</a></li>
      <!-- Apple Calendar and iCal use EventKit (via the ics library) -->
      <li><a href="javascript:void(0)" class="eventkit-download" data-caltype="apple">Apple Calendar</a></li>
      <li><a href="javascript:void(0)" class="eventkit-download" data-caltype="ical">iCal</a></li>
    </ul>
  `;
}

/**
 * --------------------------------------------------------------------------------
 * 4) Apple Calendar / iCal using the ics library (via EventKit)
 * --------------------------------------------------------------------------------
 *
 * Uses the ics library (which you already include in functions.php) to generate
 * an ICS file for Apple Calendar/iCal. This file is then downloaded.
 *
 * Note: Ensure the ics library is loaded on the frontend (for example, via Browserify/Webpack or by enqueuing a bundled script).
 */
function generateEventKitFile(eventData) {
  // Convert eventData.startDate and eventData.startTime into an array: [YYYY, M, D, H, M]
  const startDateParts = eventData.startDate.split("-").map(Number);
  const startTimeParts = eventData.startTime.split(":").map(Number);
  const eventStart = [
    startDateParts[0],
    startDateParts[1],
    startDateParts[2],
    startTimeParts[0],
    startTimeParts[1]
  ];

  // Calculate the event duration (in minutes)
  const startDateTime = parseEventDate(eventData.startDate, eventData.startTime);
  const endDateTime = parseEventDate(eventData.endDate, eventData.endTime);
  const diffMs = endDateTime - startDateTime;
  const diffMinutes = Math.floor(diffMs / (1000 * 60));
  const hours = Math.floor(diffMinutes / 60);
  const minutes = diffMinutes % 60;

  // Build the event object for the ics library.
  const event = {
    start: eventStart,
    duration: { hours: hours, minutes: minutes },
    title: eventData.title,
    description: stripHTML(eventData.description),
    location: buildEventLocation(eventData)
    // Additional fields can be added if needed.
  };

  // Call the ics library to create the event.
  ics.createEvent(event, (error, value) => {
    if (error) {
      console.error("Error generating event ICS:", error);
      return;
    }
    // Trigger a download of the ICS file.
    const blob = new Blob([value], { type: 'text/calendar;charset=utf-8' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    const fileName = (eventData.title || "event").replace(/\s+/g, "_") + ".ics";
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(link.href);
  });
}

/**
 * --------------------------------------------------------------------------------
 * 5) Final Export
 * --------------------------------------------------------------------------------
 */
window.CalendarUtils = {
  generateCalendarURLs,
  buildCalendarDropdown,
  generateEventKitFile
};

/* ----------------------------------------------------------------------------
   Initialization:
   - Example event data (replace with your actual event data)
   - Attach click listeners to the Apple/iCal options.
---------------------------------------------------------------------------- */
window.currentEventData = {
  id: "1738697425244",
  title: "Événement N01",
  description: "<p>This is a sample event description with <strong>HTML</strong> content.</p>",
  startDate: "2025-02-04", // Format: YYYY-MM-DD
  startTime: "14:09",      // Format: HH:mm (24-hour clock)
  endDate: "2025-03-05",    // Format: YYYY-MM-DD
  endTime: "17:00",        // Format: HH:mm
  locationName: "Empire Sherbrooke",
  locationAddress: "2905 Boul de Portland"
};

document.addEventListener("DOMContentLoaded", function () {
  // Generate calendar URLs for Google, Outlook, Yahoo.
  const calURLs = CalendarUtils.generateCalendarURLs(window.currentEventData);
  
  // Insert the dropdown HTML into an element with the ID "calendar-dropdown"
  const container = document.getElementById("calendar-dropdown");
  if (container) {
    container.innerHTML = buildCalendarDropdown(calURLs);
  }
  
  // Attach click listeners to elements with the "eventkit-download" class.
  document.querySelectorAll(".eventkit-download").forEach(function (element) {
    element.addEventListener("click", function (e) {
      e.preventDefault();
      console.log("EventKit button clicked, caltype:", element.getAttribute("data-caltype"));
      // Use the ics library to generate and download the ICS file.
      CalendarUtils.generateEventKitFile(window.currentEventData);
    });
  });
});
