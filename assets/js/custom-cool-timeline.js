document.addEventListener("DOMContentLoaded", function () {
  if (typeof AddToCalendar !== "undefined" && typeof AddToCalendar.init === "function") {
      AddToCalendar.init();
  }

  // Chaîne de date canonique au format "YYYY-MM-DD" (pour des calculs internes, par exemple)
  var myDate = "2025-01-31";

  /**
   * Formate une date pour l'affichage en "jour mois année" (ex: "31 janvier 2025").
   * @param {string|Date} dateStr - La date à formater.
   * @returns {string} - La date formatée pour l'affichage.
   */
  function formatDateReadable(dateStr) {
    const date = new Date(dateStr);
    const options = { year: "numeric", month: "long", day: "numeric" };
    return date.toLocaleDateString("fr-FR", options);
  }

  /**
   * Formate une date au format "YYYY-MM-DD" pour le plugin AddToCalendar.
   * @param {string|Date} dateInput - La date à formater.
   * @returns {string} - La date formatée en "YYYY-MM-DD".
   */
  function formatDateForCalendar(dateInput) {
    const date = new Date(dateInput);
    const year = date.getFullYear();
    // Les mois commencent à 0, donc on ajoute 1 et on formate avec deux chiffres
    const month = String(date.getMonth() + 1).padStart(2, "0");
    // On formate le jour avec deux chiffres
    const day = String(date.getDate()).padStart(2, "0");
    return `${year}-${month}-${day}`;
  }

  /**
   * Effectue un calcul d'offset sur la date.
   * La fonction attend une chaîne au format "YYYY-MM-DD" et renvoie un résultat fictif.
   * @param {string} dateStr - La date à utiliser pour le calcul.
   * @returns {string} - Offset calculé (pour l'exemple, une valeur fictive).
   */
  function calculateOffset(dateStr) {
    // Vérifier que la date est au format "YYYY-MM-DD"
    if (!dateStr.match(/^\d{4}-\d{2}-\d{2}$/)) {
      console.error(
        "offset calculation failed: date misspelled [-> YYYY-MM-DD]"
      );
      return "";
    }
    // ... effectuer ici les calculs d'offset nécessaires
    return "offset calculé"; // Remplacer par le calcul réel si besoin
  }

  // Exemple de calcul d'offset et affichage de la date
  var offset = calculateOffset(myDate);
  var displayDate = formatDateReadable(myDate);
  console.log("Date affichée :", displayDate); // Affichera : "31 janvier 2025"

  /**
   * Ouvre la modale spécifique à l'événement (story).
   * @param {HTMLElement} storyEl - L'élément story cliqué.
   * @param {Object} storyData - Les données associées à la story.
   */
  function openStoryModal(storyEl, storyData) {
    const modalEl = storyEl.querySelector(".story-modal");
    if (!modalEl) {
      console.warn("Pas de .story-modal dans la story.");
      return;
    }

    // Préparer les dates pour l'affichage et pour le plugin
    const displayStartDate =
      formatDateReadable(storyData.startDate) || formatDateReadable(new Date());
    const displayEndDate =
      formatDateReadable(storyData.endDate) || displayStartDate;

    const calendarStartDate = formatDateForCalendar(storyData.startDate);
    const calendarEndDate = formatDateForCalendar(storyData.endDate);

    // Injecter le contenu du modal avec les dates correctement formatées
    modalEl.querySelector(".modal-body").innerHTML = `
      <img
        src="${storyData.imageUrl || ""}"
        alt="Story ${storyData.id}"
        class="modal-image"
      />
      <div class="modal-infos">
        <div class="modal-header">
          <h2>${storyData.title || "Titre Inconnu"}</h2>
          <!-- Bouton Add to Calendar avec les dates au format requis -->
          <add-to-calendar-button
            name="${storyData.title || "Titre Inconnu"}"
            startDate="${calendarStartDate}"
            endDate="${calendarEndDate}"
            startTime="${storyData.startTime || "10:15"}"
            endTime="${storyData.endTime || "23:30"}"
            timeZone="${storyData.timeZone || "America/New_York"}"
            location="${
              storyData.locationAddress || "https://add-to-calendar-button.com"
            }"
            description="${storyData.descriptionHtml || ""}"
            buttonStyle="custom"
            options="'Apple','Google','iCal','Outlook.com','Yahoo'"
            lightMode="bodyScheme"
            customCss="/wp-content/themes/wp-oskarskate/assets/styles/css/main.css"
            displayMode="button"
            text="Ajouter à mon calendrier">
          </add-to-calendar-button>
          <div class="modal-date">${displayStartDate || "Date à venir"}</div>
        </div>
        <!-- Informations complémentaires sur l'événement -->
        <div class="modal-infos-content">
          <div class="modal-times">
            <p><strong>De :</strong> ${storyData.startTime || "N/A"}</p>
            <p><strong>À :</strong> ${storyData.endTime || "N/A"}</p>
          </div>
          <p class="modal-organizer"><strong>Organisateur :</strong> ${
            storyData.organizer || "N/A"
          }</p>
          <p><strong>Nom du lieu :</strong> ${storyData.locationName || "?"}</p>
          <div class="modal-ctrl-adresse">
            <p><strong>Adresse :</strong> ${
              storyData.locationAddress || "?"
            }</p>
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <div class="modal-description">
            ${storyData.descriptionHtml || ""}
          </div>
        </div>
        <!-- Footer du modal -->
        <div class="modal-footer">
          <div class="modal-footer-social">
            <button class="modal-btn-social fb"><i class="fa-brands fa-facebook"></i></button>
            <button class="modal-btn-social insta"><i class="fa-brands fa-instagram"></i></button>
          </div>
          <div class="footer-btn-participer">
            <a href="#" class="registrer-link">Participer</a>
          </div>
        </div>
      </div>
    `;

    // Afficher le modal
    modalEl.classList.remove("hidden");
    modalEl.classList.add("open");

    console.log(`Modal ouverte pour la story #${storyData.id}.`);

    // Initialiser le plugin AddToCalendar si nécessaire
    if (
      typeof AddToCalendar !== "undefined" &&
      typeof AddToCalendar.init === "function"
    ) {
      AddToCalendar.init();
    }

    // Ajuster la hauteur du modal pour correspondre à celle de la story
    adjustModalHeight(storyEl, modalEl);

    // Ajout du gestionnaire pour le bouton Participer
    const registerBtn = modalEl.querySelector(".registrer-link");
    if (registerBtn) {
      registerBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        e.preventDefault();
        showRegistrationForm(modalEl);
      });
    }
  }

  /**
   * Affiche le formulaire d'inscription en overlay par-dessus la modal-body.
   * @param {HTMLElement} modalEl - L'élément modal.
   */
  function showRegistrationForm(modalEl) {
    const formHtml = `
      <div class="registration-form-overlay">
        <form class="registration-form">
          <h3>Inscription à l'événement</h3>
          <label>Prénom: <input type="text" name="firstName" required></label>
          <label>Nom: <input type="text" name="lastName" required></label>
          <label>Email: <input type="email" name="email" required></label>
          <label>Confirmez votre email: <input type="email" name="confirmEmail" required></label>
          <button type="submit">Valider</button>
          <button type="button" class="close-registration">Annuler</button>
        </form>
      </div>
    `;
    const modalBody = modalEl.querySelector(".modal-body");
    modalBody.insertAdjacentHTML("beforeend", formHtml);

    const formOverlay = modalEl.querySelector(".registration-form-overlay");
    formOverlay.classList.add("drawer-up");

    const closeFormBtn = formOverlay.querySelector(".close-registration");
    closeFormBtn.addEventListener("click", function (e) {
      e.preventDefault();
      formOverlay.classList.remove("drawer-up");
      setTimeout(() => {
        formOverlay.remove();
      }, 300);
    });

    const registrationForm = formOverlay.querySelector(".registration-form");
    registrationForm.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(registrationForm);
      console.log(
        "Données du formulaire d'inscription :",
        Object.fromEntries(formData.entries())
      );
    });
  }

  /**
   * Ajuste la hauteur du modal pour qu'elle corresponde à celle de la story.
   * @param {HTMLElement} storyEl - L'élément story.
   * @param {HTMLElement} modalEl - L'élément modal.
   */
  function adjustModalHeight(storyEl, modalEl) {
    const storyHeight = storyEl.getBoundingClientRect().height;
    modalEl.querySelector(".modal-content").style.height = `${storyHeight}px`;

    const resizeObserver = new ResizeObserver(() => {
      const updatedHeight = storyEl.getBoundingClientRect().height;
      modalEl.querySelector(
        ".modal-content"
      ).style.height = `${updatedHeight}px`;
    });
    resizeObserver.observe(storyEl);
  }

  // Récupérer toutes les stories du plugin
  const stories = document.querySelectorAll(".ctl-story");
  console.log("Nombre de stories trouvées :", stories.length);

  // Intersection Observer pour animer les éléments au scroll
  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const theStory = entry.target;
          console.log("Story visible :", theStory.id);

          const dateLabels = theStory.querySelector(".story-date");
          const storyContent = theStory.querySelector(".my-story-content");
          const footerBtn = theStory.querySelector(".footer-btn-savoir");

          const position = theStory.classList.contains("ctl-story-right")
            ? "right"
            : "left";

          if (dateLabels) {
            dateLabels.classList.remove("hiddenAtStart");
            const ctlLabelBig = dateLabels.querySelector(
              `.ctl-label-big.story-date.ctl-label-big-${position}`
            );
            if (ctlLabelBig) {
              ctlLabelBig.classList.add(`scale-in-hor-${position}`);
            }
          }
          if (storyContent) {
            storyContent.classList.remove("hiddenAtStart");
            if (position === "left") {
              storyContent.classList.add("swing-in-right-fwd");
            } else {
              storyContent.classList.add("swing-in-left-fwd");
            }
            storyContent.classList.add(position);
          }
          if (footerBtn) {
            footerBtn.classList.remove("hiddenAtStart");
            setTimeout(() => {
              footerBtn.classList.add("show");
            }, 500);
          }
          obs.unobserve(theStory);
        }
      });
    },
    {
      rootMargin: "0px 0px -10% 0px",
      threshold: 0.1,
    }
  );

  // Boucle sur chaque story pour reconstruire le HTML et ajouter les écouteurs
  stories.forEach(function (storyEl) {
    const storyIdFull = storyEl.id;
    const storyId = storyIdFull.replace("ctl-story-", "");

    const dateLabelsEl = storyEl.querySelector(".ctl-labels");
    const dateLabelsHtml = dateLabelsEl ? dateLabelsEl.outerHTML : "";
    const iconEl = storyEl.querySelector(".ctl-icon");
    const iconHtml = iconEl ? iconEl.outerHTML : "";

    const storyDate = storyEl.getAttribute("data-event-date") || "";
    const startDate =
      storyEl.getAttribute("data-start-date") || new Date().toISOString();
    const endDate = storyEl.getAttribute("data-end-date") || startDate;
    const startTime = storyEl.getAttribute("data-start-time") || "10:15";
    const endTime = storyEl.getAttribute("data-end-time") || "23:30";
    const timeZone =
      storyEl.getAttribute("data-timezone") || "America/New_York";
    const organizer = storyEl.getAttribute("data-organizer") || "N/A";

    let titleText = "";
    let titleHref = "#";
    const titleLink = storyEl.querySelector(".ctl-title a");
    if (titleLink) {
      titleText = titleLink.textContent.trim();
      titleHref = titleLink.getAttribute("href");
    } else {
      const titleEl = storyEl.querySelector(".ctl-title");
      if (titleEl) {
        titleText = titleEl.textContent.trim();
      }
    }

    const descEl = storyEl.querySelector(".ctl-description");
    const descHtml = descEl ? descEl.innerHTML.trim() : "";

    const previewUrl = storyEl.getAttribute("data-preview-url") || "";
    const locationName = storyEl.getAttribute("data-location-name") || "";
    const locationAddress = storyEl.getAttribute("data-location-address") || "";

    const googleMapsUrl = locationAddress
      ? "https://www.google.com/maps/search/?api=1&query=" +
        encodeURIComponent(locationAddress)
      : "#";

    let position = "left";
    if (storyEl.classList.contains("ctl-story-right")) {
      position = "right";
    }

    // Modification du HTML des labels pour ajouter les classes et le triangle
    let modifiedDateLabelsHtml = dateLabelsHtml;
    if (position === "left") {
      modifiedDateLabelsHtml = dateLabelsHtml
        .replace(
          '<div class="ctl-labels">',
          '<div class="ctl-labels ctl-labels-left">'
        )
        .replace(
          '<div class="ctl-label-big story-date">',
          '<div class="ctl-label-big story-date ctl-label-big-left">'
        )
        .replace(
          '<div class="ctl-label-big story-date ctl-label-big-left">',
          '<div class="triangle-left"></div><div class="ctl-label-big story-date ctl-label-big-left">'
        );
    } else if (position === "right") {
      modifiedDateLabelsHtml = dateLabelsHtml
        .replace(
          '<div class="ctl-labels">',
          '<div class="ctl-labels ctl-labels-right">'
        )
        .replace(
          '<div class="ctl-label-big story-date">',
          '<div class="ctl-label-big story-date ctl-label-big-right">'
        )
        .replace("</div>", '</div><div class="triangle-right"></div>');
    }

    const newHtml = `
      <div class="my-story-rebuilt ${
        position === "left" ? "swing-in-right-fwd" : "swing-in-left-fwd"
      }">
        <!-- Zone de la date avec animation -->
        <div class="story-date hiddenAtStart ${
          position === "left" ? "story-date-left" : "story-date-right"
        }">
          ${modifiedDateLabelsHtml}
        </div>

        <!-- Icône centrée -->
        <div class="my-icon-centered">
          ${iconHtml}
        </div>

        <!-- Contenu de la story -->
        <div class="my-story-content hiddenAtStart ${position}">
          ${
            previewUrl
              ? `<div class="my-story-img" style="position: relative;">
                    <img src="${previewUrl}" alt="Story #${storyId}" class="story-image" />
                    <div class="img-overlay hidden">
                      <button class="overlay-close">✕</button>
                      <p>Voir l’adresse sur Google Maps ?</p>
                      <a href="${googleMapsUrl}" class="btn-aller-voir" target="_blank">Aller voir</a>
                    </div>
                  </div>`
              : ""
          }
          <h3 class="my-story-title">${titleText || "Événement sans titre"}</h3>
          ${
            locationName
              ? `<p class="my-location-name">${locationName}</p>`
              : ""
          }
          ${
            locationAddress
              ? `<div class="story-ctrl-adresse">
                    <p class="my-location-address">${locationAddress}</p>
                    <i class="fa-solid fa-location-dot"></i>
                  </div>`
              : ""
          }
          ${
            descHtml
              ? `<div class="my-story-description">${descHtml}</div>`
              : `<p>(Aucune description)</p>`
          }
          <div class="my-story-footer">
            <div class="footer-social">
              <button class="btn-social fb"><i class="fa-brands fa-facebook"></i></button>
              <button class="btn-social insta"><i class="fa-brands fa-instagram"></i></button>
            </div>
            <div class="footer-btn-savoir hiddenAtStart">
              <a href="${titleHref}" class="readmore-link">En savoir plus ▸</a>
            </div>
          </div>
        </div>

        <!-- Modal spécifique à la story -->
        <div class="story-modal my-modal-overlay hidden">
          <div class="modal-content">
            <button class="modal-close">✕</button>
            <div class="modal-body">
              <!-- Contenu dynamique -->
            </div>
          </div>
        </div>
      </div><!-- .my-story-rebuilt -->
    `;

    // Remplacer le contenu de la story
    storyEl.innerHTML = newHtml;

    // Rendre la story cliquable pour ouvrir le modal
    storyEl.style.cursor = "pointer";
    storyEl.addEventListener("click", (evt) => {
      evt.preventDefault();
      const storyData = {
        id: storyId,
        title: titleText,
        imageUrl: previewUrl,
        locationName: locationName,
        locationAddress: locationAddress,
        descriptionHtml: descHtml,
        date: storyDate,
        startDate: startDate,
        endDate: endDate,
        startTime: startTime,
        endTime: endTime,
        timeZone: timeZone,
        organizer: organizer,
      };
      console.log("Données de la story pour le modal :", storyData);
      openStoryModal(storyEl, storyData);
    });

    // Gestion de l'overlay Google Maps
    const newOverlay = storyEl.querySelector(".img-overlay");
    const closeOverlayBtn = newOverlay?.querySelector(".overlay-close");
    const addressLink = newOverlay?.querySelector(".btn-aller-voir");

    const locNameEl = storyEl.querySelector(".my-location-name");
    const locAddrEl = storyEl.querySelector(".my-location-address");

    function toggleOverlay(e) {
      e.stopPropagation();
      if (!newOverlay) return;
      newOverlay.classList.toggle("hidden");
    }
    if (locNameEl) locNameEl.addEventListener("click", toggleOverlay);
    if (locAddrEl) locAddrEl.addEventListener("click", toggleOverlay);

    if (closeOverlayBtn) {
      closeOverlayBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        newOverlay.classList.add("hidden");
      });
    }
    if (addressLink) {
      addressLink.addEventListener("click", (e) => {
        e.stopPropagation();
        console.log("Lien 'Aller voir' :", addressLink.href);
      });
    }

    // Gestion de la fermeture du modal via le bouton X ou le clic en dehors
    const modalCloseBtn = storyEl.querySelector(".modal-close");
    const modalEl = storyEl.querySelector(".story-modal");
    if (modalCloseBtn && modalEl) {
      modalCloseBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        modalEl.classList.remove("open");
        modalEl.classList.add("hidden");
        console.log("Modal fermée via le bouton X.");
      });
    }
    if (modalEl) {
      modalEl.addEventListener("click", function (e) {
        if (e.target === modalEl) {
          modalEl.classList.remove("open");
          modalEl.classList.add("hidden");
          console.log("Modal fermée en cliquant en dehors.");
        }
      });
    }
    observer.observe(storyEl);
  });
});
