/**
 * reg_form.js
 *
 * 1) Gère l'inscription (envoi AJAX) pour un événement (eventSlug).
 * 2) Évite les doublons en vérifiant le localStorage ET en demandant au serveur d'écraser l'entrée précédente.
 * 3) Permet d'annuler l'inscription (annule sur le serveur ET dans le localStorage).
 *
 * NOTE: On suppose que cct.js (ou custom-cool-timeline.js) appelle window.afficherFormulaireVide(...)
 *       pour insérer le <form> dans un overlay. Aussi, en cliquant sur "Participer", on appelle la fonction
 *       toggleRegistrationOverlay(...) qui appelle window.afficherFormulaireVide().
 */
document.addEventListener("DOMContentLoaded", function () {
  /*******************************************
   * FONCTIONS LOCAL STORAGE
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
   * MESSAGE "VOUS ÊTES DÉJÀ INSCRIT" + BOUTONS
   *******************************************/
  function afficherMessageDejaInscrit(container, regData, eventId) {
    container.innerHTML = `
      <p>Vous êtes déjà inscrit avec l'adresse : ${regData.email}</p>
      <button id="cancelParticipation">Annuler ma participation</button>
      <button id="registerOther">Inscrire un autre nom ?</button>
    `;

    const btnCancel = container.querySelector("#cancelParticipation");
    btnCancel.addEventListener("click", function () {
      const formData = new FormData();
      formData.set("storyId", eventId);
      formData.set("email", regData.email);
      formData.set("eventSlug", regData.eventSlug || "");
      // IMPORTANT: on ajoute l'action dans le POST
      formData.set("action", "cancel_registration");

      fetch("/wp-oskarskate/wp-admin/admin-ajax.php", {
        method: "POST",
        body: formData,
        credentials: "same-origin"
      })
        .then((r) => r.json())
        .then((resp) => {
          if (resp.success) {
            clearRegistrationForEvent(eventId);
            container.innerHTML = `<p>Inscription annulée.</p>`;
          } else {
            console.error("Erreur lors de l'annulation :", resp.data);
          }
        })
        .catch((err) => {
          console.error("Erreur AJAX lors de l'annulation :", err);
        });
    });

    const btnOther = container.querySelector("#registerOther");
    btnOther.addEventListener("click", function () {
      clearRegistrationForEvent(eventId);
      afficherFormulaireVide(container, eventId, regData.eventTitle);
    });
  }

  /*******************************************
   * AFFICHAGE D'UN FORMULAIRE VIDE
   *******************************************/
  function afficherFormulaireVide(container, eventId, eventTitle) {
    container.innerHTML = `
      <form class="registration-form">
        <header class="form-header">
          <h3>Inscription à l'événement</h3>
        </header>
        <div class="form-inputs-labels">
          <input type="hidden" name="eventTitle" value="${eventTitle || ""}" />
          <label>Prénom : <input type="text" name="firstName" required></label>
          <label>Nom : <input type="text" name="lastName" required></label>
          <label>Email : <input type="email" name="email" required></label>
          <label>Confirmez votre email : <input type="email" name="confirmEmail" required></label>
        </div>
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
  window.afficherFormulaireVide = afficherFormulaireVide;

  function assainirTitre(str) {
    return (str || "")
      .toLowerCase()
      .trim()
      .replace(/\s+/g, "-")
      .replace(/[^a-z0-9\-]/g, "");
  }

  /*******************************************
   * SOUMISSION DU FORMULAIRE
   *******************************************/
  document.addEventListener("submit", function (e) {
    if (!e.target.matches(".registration-form")) return;
    e.preventDefault();

    const form = e.target;
    const modalEl = form.closest(".story-modal");
    if (!modalEl) {
      console.warn("Impossible de trouver le modal parent pour l'inscription.");
      return;
    }

    const eventId = modalEl.getAttribute("data-parent-story") || "";
    const rawTitle = modalEl.getAttribute("data-event-title") || `Event_${eventId}`;
    const eventSlug = assainirTitre(rawTitle);

    const hiddenTitle = form.querySelector('input[name="eventTitle"]');
    if (hiddenTitle) {
      hiddenTitle.value = rawTitle;
    }

    const formData = new FormData(form);
    const formObj = Object.fromEntries(formData.entries());
    const emailLower = (formObj.email || "").trim().toLowerCase();

    const existingReg = getRegistrationForEvent(eventId);
    if (existingReg && (existingReg.email || "").toLowerCase() === emailLower) {
      const container = form.closest(".registration-form-overlay") || form.parentNode;
      afficherMessageDejaInscrit(container, existingReg, eventId);
      return;
    }

    const isBenevole = formData.get("benevolat") === "1";
    formData.set("registrationType", isBenevole ? "benevole" : "participant");
    formData.set("storyId", eventId);
    formData.set("eventSlug", eventSlug);
    // IMPORTANT: ajouter l'action dans le POST pour que le handler la détecte
    formData.set("action", "my_registration");

    formObj.email = emailLower;
    formObj.eventSlug = eventSlug;
    formObj.eventTitle = rawTitle;

    fetch("/wp-oskarskate/wp-admin/admin-ajax.php", {
      method: "POST",
      body: formData,
      credentials: "same-origin"
    })
      .then((r) => r.json())
      .then((resp) => {
        if (resp.success) {
          saveRegistrationForEvent(eventId, {
            firstName: formObj.firstName,
            lastName: formObj.lastName,
            email: emailLower,
            eventSlug: eventSlug,
            eventTitle: rawTitle,
          });
          const overlay = form.closest(".registration-form-overlay") || form.parentNode;
          afficherMessageDejaInscrit(overlay, formObj, eventId);
        } else {
          console.error("Erreur serveur :", resp.data);
        }
      })
      .catch((err) => {
        console.error("Erreur lors de l'inscription :", err);
      });
  });

  /*******************************************
   * BOUTON "ANNULER" DANS LE FORMULAIRE
   *******************************************/
  document.addEventListener("click", function (ev) {
    if (!ev.target.matches(".close-registration")) return;
    ev.preventDefault();
    const form = ev.target.closest("form.registration-form");
    if (form) form.reset();
  });
});
