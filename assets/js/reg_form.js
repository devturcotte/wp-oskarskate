document.addEventListener("DOMContentLoaded", function () {
  /*******************************************
   * GESTION DU LOCAL STORAGE PAR ÉVÉNEMENT
   *******************************************/
  function getRegistrations() {
    const regData = localStorage.getItem("registrationData");
    return regData ? JSON.parse(regData) : {};
  }
  function getRegistrationForEvent(eventId) {
    const regs = getRegistrations();
    return regs[eventId] || null;
  }
  function saveRegistrationForEvent(eventId, data) {
    const regs = getRegistrations();
    regs[eventId] = data;
    localStorage.setItem("registrationData", JSON.stringify(regs));
  }
  function clearRegistrationForEvent(eventId) {
    const regs = getRegistrations();
    delete regs[eventId];
    localStorage.setItem("registrationData", JSON.stringify(regs));
  }

  /*******************************************
   * RENDERING THE “VOUS ÊTES INSCRIT” MESSAGE
   *******************************************/
  function renderRegistrationMessage(container, regData, eventId) {
    // Show a simple message + 2 buttons
    container.innerHTML = `
      <p>Vous êtes inscrit avec l'adresse : ${regData.email}</p>
      <button id="cancelParticipation">Annuler ma participation</button>
      <button id="registerOther">Inscrire un autre nom ?</button>
    `;

    // Handle “Annuler ma participation”
    container.querySelector("#cancelParticipation").addEventListener("click", function () {
      const formData = new FormData();
      formData.set("storyId", eventId);
      formData.set("email", regData.email);
      // Also pass the slug if needed
      formData.set("eventSlug", regData.eventSlug || "");

      fetch("/wp-oskarskate/wp-admin/admin-ajax.php?action=cancel_registration", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            clearRegistrationForEvent(eventId);
            // Option 1: Reset the modal form in place
            renderEmptyForm(container, eventId, regData.eventTitle);
          } else {
            console.error("Erreur lors de l'annulation :", data.message);
          }
        })
        .catch((error) => console.error("Erreur AJAX lors de l'annulation :", error));
    });

    // Handle “Inscrire un autre nom ?”
    container.querySelector("#registerOther").addEventListener("click", function () {
      // Just clear local storage for this event
      clearRegistrationForEvent(eventId);
      // Re-show an empty form (no page reload):
      renderEmptyForm(container, eventId, regData.eventTitle);
    });
  }

  /*******************************************
   * RENDERING AN EMPTY FORM (NO PAGE RELOAD)
   *******************************************/
  function renderEmptyForm(container, eventId, eventTitle) {
    // This is the original HTML for the registration form
    // Adjust as needed
    container.innerHTML = `
      <form class="registration-form">
        <h3>Inscription à l'événement</h3>
        <input type="hidden" name="eventTitle" value="${eventTitle}" />
        <label>Prénom : <input type="text" name="firstName" required></label>
        <label>Nom : <input type="text" name="lastName" required></label>
        <label>Email : <input type="email" name="email" required></label>
        <label>Confirmez votre email : <input type="email" name="confirmEmail" required></label>
        <div class="form-checkbox">
          <label>
            <p>Bénévole</p>
            <input type="checkbox" name="benevolat" value="1">
          </label>
        </div>
        <div class="ctrl-form-buttons">
          <button type="submit">Valider</button>
          <button type="button" class="close-registration">Annuler</button>
        </div>
      </form>
    `;
  }

  // A helper function to transform a string into a “slug”
  function sanitizeTitle(str) {
    return (str || "")
      .toLowerCase()
      .trim()
      .replace(/\s+/g, "-")
      .replace(/[^a-z0-9\-]/g, "");
  }

  /*******************************************
   * FORM SUBMISSION LOGIC
   *******************************************/
  document.addEventListener("submit", function (e) {
    if (!e.target.matches(".registration-form")) return;
    e.preventDefault();

    const form = e.target;
    const modalEl = form.closest(".story-modal");
    if (!modalEl) return;

    // Retrieve event info from the modal
    const eventId = modalEl.getAttribute("data-parent-story");
    const eventTitle = modalEl.getAttribute("data-event-title") || "Event_" + eventId;
    const eventSlug = sanitizeTitle(eventTitle);

    // Update the hidden eventTitle field if it exists
    const eventTitleInput = form.querySelector('input[name="eventTitle"]');
    if (eventTitleInput) {
      eventTitleInput.value = eventTitle;
    }

    // Build FormData
    const formData = new FormData(form);
    const formDataObj = Object.fromEntries(formData.entries());

    // Normalize the email
    const emailSub = (formDataObj.email || "").trim().toLowerCase();

    // Check if there's already a registration for this event
    const existingReg = getRegistrationForEvent(eventId);
    if (existingReg && (existingReg.email || "").trim().toLowerCase() === emailSub) {
      // Already registered
      const container = form.closest(".registration-form-overlay") || form.parentNode;
      renderRegistrationMessage(container, existingReg, eventId);
      return;
    }

    // Determine if user is “benevole”
    const isBenevole = formData.get("benevolat") === "1";
    formData.set("registrationType", isBenevole ? "benevole" : "participant");
    formData.set("storyId", eventId);
    formData.set("eventSlug", eventSlug);

    // Also store them in formDataObj for local storage
    formDataObj.eventSlug = eventSlug;
    formDataObj.email = emailSub; // overwrite with normalized

    // Send via AJAX
    fetch("/wp-oskarskate/wp-admin/admin-ajax.php?action=my_registration", {
      method: "POST",
      body: formData,
    })
      .then((resp) => resp.json())
      .then((data) => {
        if (data.success) {
          // Save to local storage
          saveRegistrationForEvent(eventId, {
            firstName: formDataObj.firstName,
            lastName: formDataObj.lastName,
            email: emailSub,
            eventSlug: eventSlug,
            eventTitle: eventTitle,
          });
          const container = form.closest(".registration-form-overlay") || form.parentNode;
          renderRegistrationMessage(container, formDataObj, eventId);
        } else {
          console.error("Erreur serveur :", data.message);
        }
      })
      .catch((error) => console.error("Erreur lors de l'inscription :", error));
  });

  // “Annuler” button inside the form
  document.addEventListener("click", function (e) {
    if (!e.target.matches(".close-registration")) return;
    e.preventDefault();
    const form = e.target.closest("form.registration-form");
    if (form) form.reset();
  });
});
