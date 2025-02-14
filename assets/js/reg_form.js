/**
 * reg_form.js
 *
 * Gère le formulaire d'inscription (front-end) :
 *   1) Inscription via AJAX (my_registration)
 *   2) Annulation via AJAX (cancel_registration)
 *   3) Stockage dans localStorage (optionnel) pour éviter doublons
 *   4) Switch (participant/benevole) si la même adresse email est déjà inscrite avec un autre type
 *   5) Mise à jour des compteurs d'inscrits (participants, benevoles)
 *
 *  Utilisé par custom-cool-timeline.js (ou cct.js) via la fonction globale window.afficherFormulaireVide(...)
 */

// Wrap in DOMContentLoaded or IIFE
document.addEventListener("DOMContentLoaded", function () {
  /*************************************************
   * FONCTIONS LOCALSTORAGE
   *************************************************/
  function getRegistrationsFromLS() {
    const regData = localStorage.getItem("registrationData");
    return regData ? JSON.parse(regData) : {};
  }
  function saveRegistrationInLS(eventId, data) {
    const regs = getRegistrationsFromLS();
    regs[eventId] = data;
    localStorage.setItem("registrationData", JSON.stringify(regs));
  }
  function getRegistrationForEvent(eventId) {
    const regs = getRegistrationsFromLS();
    return regs[eventId] || null;
  }
  function clearRegistrationForEvent(eventId) {
    const regs = getRegistrationsFromLS();
    delete regs[eventId];
    localStorage.setItem("registrationData", JSON.stringify(regs));
  }

  /*************************************************
   * FONCTION UTILE : Slugifier
   *************************************************/
  function assainirTitre(str) {
    return (str || "")
      .toLowerCase()
      .trim()
      .replace(/\s+/g, "-")
      .replace(/[^a-z0-9-]/g, "");
  }

  /*************************************************
   * OPTIONNEL : Fetch existants
   *************************************************/
  function fetchExistingRegistrations(eventId) {
    return new Promise((resolve, reject) => {
      const formData = new FormData();
      formData.append("action", "get_registrations");
      formData.append("storyId", eventId);

      fetch("/wp-oskarskate/wp-admin/admin-ajax.php", {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      })
        .then((r) => r.json())
        .then((resp) => {
          if (resp.success) {
            resolve(resp.data);
          } else {
            reject(resp.data || "Erreur get_registrations");
          }
        })
        .catch(reject);
    });
  }

  /*************************************************
   * FONCTION GLOBALE : afficherFormulaireVide
   * Insère un <form> d'inscription dans le container donné.
   *************************************************/
  function afficherFormulaireVide(container, eventId, eventTitle) {
    // On va lire data-nb-places-participants et data-nb-places-benevoles
    const storyEl = document.getElementById("ctl-story-" + eventId);
    const maxParticipants =
      parseInt(
        storyEl ? storyEl.getAttribute("data-nb-places-participants") : "0",
        10
      ) || 0;
    const maxBenevoles =
      parseInt(
        storyEl ? storyEl.getAttribute("data-nb-places-benevoles") : "0",
        10
      ) || 0;

    // Détermine si on affiche la checkbox "benevole"
    let volunteerOptionHtml = "";
    if (maxBenevoles === 0 && maxParticipants > 0) {
      // Que participants
      volunteerOptionHtml = "";
    } else if (maxParticipants === 0 && maxBenevoles > 0) {
      // Que bénévoles
      volunteerOptionHtml = `
        <p style="color:#555; font-style:italic;">Inscriptions pour bénévoles seulement</p>
        <input type="hidden" name="benevolat" value="1">
      `;
    } else {
      // Les deux
      volunteerOptionHtml = `
        <label>
          <p>S'inscrire comme bénévole ?</p>
          <input type="checkbox" name="benevolat" value="1">
        </label>
      `;
    }

    // Construit le HTML du formulaire
    container.innerHTML = `
      <form class="registration-form">
        <header class="form-header">
          <h3>Inscription à l'événement</h3>
        </header>
        <div class="form-inputs-labels">
          <input type="hidden" name="eventTitle" value="${eventTitle || ""}">
          <label>Prénom : <input type="text" name="firstName" required></label>
          <label>Nom : <input type="text" name="lastName" required></label>
          <label>Email : <input type="email" name="email" required></label>
          <label>Confirmez votre email : <input type="email" name="confirmEmail" required></label>
        </div>
        ${
          volunteerOptionHtml
            ? `<div class="form-checkbox">${volunteerOptionHtml}</div>`
            : ""
        }
        <div class="ctrl-form-buttons">
          <button type="submit">Valider</button>
          <button type="button" class="close-registration">Annuler</button>
        </div>
      </form>
    `;
  }
  window.afficherFormulaireVide = afficherFormulaireVide;

  /*************************************************
   * AFFICHER MESSAGE "DÉJA INSCRIT" + SWITCH POSSIBLE
   *************************************************/
  function afficherMessageDejaInscrit(container, regData, eventId, newType) {
    const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);
    if (newType && regData.registrationType !== newType) {
      container.innerHTML = `
        <p>Vous êtes déjà inscrit(e) en tant que <strong>${capitalize(
          regData.registrationType
        )}</strong>
        avec l'adresse : ${regData.email}.<br>
        Voulez-vous changer pour <strong>${capitalize(newType)}</strong> ?</p>
        <button id="switchYes">Oui</button>
        <button id="switchNo">Non</button>
      `;
      document.getElementById("switchYes").addEventListener("click", () => {
        const formData = new FormData();
        formData.set("firstName", regData.firstName);
        formData.set("lastName", regData.lastName);
        formData.set("email", regData.email);
        formData.set("registrationType", newType);
        formData.set("storyId", eventId);
        formData.set("eventSlug", regData.eventSlug || "");
        formData.set("eventTitle", regData.eventTitle || "");
        formData.set("action", "my_registration");

        fetch("/wp-oskarskate/wp-admin/admin-ajax.php", {
          method: "POST",
          body: formData,
          credentials: "same-origin",
        })
          .then((r) => r.json())
          .then((resp) => {
            if (resp.success) {
              // Met à jour localStorage
              saveRegistrationInLS(
                eventId,
                Object.assign({}, regData, { registrationType: newType })
              );
              container.innerHTML = `
                <p>Vous êtes maintenant inscrit(e) en tant que <strong>${capitalize(
                  newType
                )}</strong> à l'adresse : ${regData.email}</p>
                <button id="cancelParticipation">Annuler l'inscription</button>
              `;
              attacherBoutonCancel(container, getRegistrationForEvent(eventId), eventId);
            } else {
              container.innerHTML = `<p style="color:red;">${resp.data}</p>`;
            }
          })
          .catch((err) => {
            console.error("Erreur AJAX switch:", err);
          });
      });
      document.getElementById("switchNo").addEventListener("click", () => {
        container.innerHTML = `
          <p>Vous restez inscrit(e) en tant que <strong>${capitalize(
            regData.registrationType
          )}</strong> à l'adresse : ${regData.email}</p>
          <button id="cancelParticipation">Annuler l'inscription</button>
        `;
        attacherBoutonCancel(container, regData, eventId);
      });
    } else {
      container.innerHTML = `
        <p>Vous êtes déjà inscrit(e) en tant que <strong>${capitalize(
          regData.registrationType
        )}</strong> avec l'adresse : ${regData.email}</p>
        <button id="cancelParticipation">Annuler l'inscription</button>
      `;
      attacherBoutonCancel(container, regData, eventId);
    }
  }

  /*************************************************
   * ATTACHER LE BOUTON "ANNULER L'INSCRIPTION"
   *************************************************/
  function attacherBoutonCancel(container, regData, eventId) {
    const btnCancel = container.querySelector("#cancelParticipation");
    if (btnCancel) {
      btnCancel.addEventListener("click", () => {
        const formData = new FormData();
        formData.set("storyId", eventId);
        formData.set("email", regData.email);
        formData.set("eventTitle", regData.eventTitle || "");
        formData.set("action", "cancel_registration");

        fetch("/wp-oskarskate/wp-admin/admin-ajax.php", {
          method: "POST",
          body: formData,
          credentials: "same-origin",
        })
          .then((r) => r.json())
          .then((resp) => {
            if (resp.success) {
              clearRegistrationForEvent(eventId);
              container.innerHTML = `<p>Inscription annulée.</p>`;
              setTimeout(() => {
                if (typeof window.closeStoryModal === "function") {
                  const storyEl = document.getElementById("ctl-story-" + eventId);
                  window.closeStoryModal(storyEl);
                }
              }, 2000);
            } else {
              container.innerHTML = `<p style="color:red;">${resp.data}</p>`;
            }
          })
          .catch((err) => {
            console.error("Erreur annulation:", err);
          });
      });
    }
  }

  /*************************************************
   * GESTION SUBMIT FORM
   *************************************************/
  document.addEventListener("submit", function (e) {
    if (!e.target.matches(".registration-form")) return;
    e.preventDefault();

    const form = e.target;
    const modalEl = form.closest(".story-modal");
    if (!modalEl) {
      console.warn("Impossible de trouver le modal parent pour ce formulaire.");
      return;
    }

    const rawId = modalEl.getAttribute("data-parent-story") || "";
    const eventId = rawId.replace("ctl-story-", "");
    const rawTitle = modalEl.getAttribute("data-event-title") || `Event_${eventId}`;

    const formData = new FormData(form);
    const formObj = Object.fromEntries(formData.entries());
    const emailLower = (formObj.email || "").trim().toLowerCase();

    // Check email confirm
    if (formObj.email !== formObj.confirmEmail) {
      alert("Les adresses e-mail ne correspondent pas.");
      return;
    }

    // Détermine le type d'inscription
    let newType = "participant";
    if (form.querySelector("input[name='benevolat']")) {
      newType = formData.get("benevolat") === "1" ? "benevole" : "participant";
    }

    // Vérifie si on a déjà localStorage
    const existingReg = getRegistrationForEvent(eventId);
    if (
      existingReg &&
      (existingReg.email || "").toLowerCase() === emailLower
    ) {
      // Soit c'est un switch, soit c'est la même chose
      const overlay = form.closest(".registration-form-overlay") || form.parentNode;
      if (existingReg.registrationType !== newType) {
        afficherMessageDejaInscrit(overlay, existingReg, eventId, newType);
      } else {
        afficherMessageDejaInscrit(overlay, existingReg, eventId);
      }
      return;
    }

    // On prépare le formData AJAX
    formData.set("registrationType", newType);
    formData.set("storyId", eventId);
    formData.set("email", emailLower);
    formData.set("eventTitle", rawTitle);
    formData.set("action", "my_registration");

    fetch("/wp-oskarskate/wp-admin/admin-ajax.php", {
      method: "POST",
      body: formData,
      credentials: "same-origin",
    })
      .then((r) => r.json())
      .then((resp) => {
        if (resp.success) {
          // Succès => on stocke dans localStorage
          saveRegistrationInLS(eventId, {
            firstName: formObj.firstName,
            lastName: formObj.lastName,
            email: emailLower,
            eventSlug: assainirTitre(rawTitle),
            eventTitle: rawTitle,
            registrationType: newType,
          });
          // On affiche message "Déjà inscrit"
          const overlay = form.closest(".registration-form-overlay") || form.parentNode;
          afficherMessageDejaInscrit(
            overlay,
            getRegistrationForEvent(eventId),
            eventId
          );
          // Met à jour les compteurs si renvoyés par le serveur
          if (
            resp.countParticipants !== undefined &&
            resp.countBenevoles !== undefined
          ) {
            const regInfo = overlay.querySelector(".registration-info");
            if (regInfo) {
              const partEl = regInfo.querySelector("#current-participants");
              const beneEl = regInfo.querySelector("#current-benevoles");
              if (partEl) partEl.textContent = resp.countParticipants;
              if (beneEl) beneEl.textContent = resp.countBenevoles;
            }
          }
        } else {
          // Erreur => on l'affiche
          const overlay = form.closest(".registration-form-overlay") || form.parentNode;
          overlay.innerHTML = `<p style="color:red;">${resp.data}</p>`;
        }
      })
      .catch((err) => {
        console.error("Erreur AJAX inscription:", err);
      });
  });

  /*************************************************
   * BOUTON ANNULER FORM (pas annuler inscription)
   *************************************************/
  document.addEventListener("click", function (ev) {
    if (!ev.target.matches(".close-registration")) return;
    ev.preventDefault();
    const form = ev.target.closest("form.registration-form");
    if (form) form.reset();
  });

  /*************************************************
   * FALLBACK closeStoryModal si non défini
   *************************************************/
  if (typeof window.closeStoryModal !== "function") {
    window.closeStoryModal = function (storyEl) {
      if (!storyEl) return;
      const modalEl = storyEl.querySelector(".story-modal");
      if (modalEl) {
        modalEl.classList.remove("open");
        modalEl.classList.add("hidden");
        modalEl.style.display = "none";
        const storyCard = storyEl.querySelector(".my-story-rebuilt");
        if (storyCard) storyCard.style.visibility = "visible";
        storyEl.appendChild(modalEl);
        console.log("Modal fermé (fallback).");
      }
    };
  }
});
