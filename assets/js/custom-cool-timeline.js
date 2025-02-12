document.addEventListener("DOMContentLoaded", function () {
  /*******************************************
   * VARIABLES GLOBALES
   *******************************************/
  // Contrôle l'affichage des événements passés
  let showPassed = false;
  // Ensemble des types d'activités sélectionnés pour le filtre
  let selectedTypes = new Set();

  /*******************************************
   * FONCTIONS UTILES
   *******************************************/
  // Formate une date ISO en format lisible (ex. : "2 octobre 2024")
  function formatDateReadable(dateStr) {
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return "Date inconnue";
    return d.toLocaleDateString("fr-FR", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  }

  // Ajoute ou enlève la classe indiquant qu'un événement est passé
  function markPassedEvents() {
    const today = new Date();
    document.querySelectorAll(".ctl-story").forEach((story) => {
      const eventDateStr = story.getAttribute("data-event-date");
      if (eventDateStr) {
        const eventDate = new Date(eventDateStr);
        const contentEl = story.querySelector(".my-story-content");
        if (contentEl) {
          if (eventDate < today) contentEl.classList.add("my-story-content-passed");
          else contentEl.classList.remove("my-story-content-passed");
        }
      }
    });
  }

  /*******************************************
   * FONCTIONS DE GESTION DES MODALS
   *******************************************/
  // Ferme tous les modals ouverts
  function closeAllModals() {
    document.querySelectorAll(".story-modal.open").forEach((modalEl) => {
      const parentStoryId = modalEl.getAttribute("data-parent-story");
      if (parentStoryId) {
        const storyEl = document.getElementById(parentStoryId);
        if (storyEl) closeStoryModal(storyEl);
      }
    });
  }

  // Crée un placeholder pour conserver la hauteur de la story pendant l'affichage du modal
  function createPlaceholder(storyEl) {
    const storyCard = storyEl.querySelector(".my-story-rebuilt");
    if (!storyCard) return null;
    const placeholder = document.createElement("div");
    placeholder.className = "story-placeholder";
    placeholder.style.height = storyCard.offsetHeight + "px";
    storyCard.parentNode.insertBefore(placeholder, storyCard);
    return placeholder;
  }

  // Ajuste la hauteur du placeholder pour qu'elle corresponde à celle du modal
  function adjustPlaceholderHeight(modalEl, placeholder) {
    if (placeholder && modalEl) {
      placeholder.style.height = modalEl.offsetHeight + "px";
    }
  }

  /*******************************************
   * FONCTION PRINCIPALE : OUVERTURE DU MODAL
   *******************************************/
  function openStoryModal(storyEl, storyData) {
    // Ferme tous les autres modals
    closeAllModals();

    const modalEl = storyEl.querySelector(".story-modal");
    if (!modalEl) {
      console.warn("Aucun .story-modal trouvé pour cette story.");
      return null;
    }

    // Définir les attributs essentiels sur le modal
    modalEl.setAttribute("data-parent-story", storyEl.id);
    modalEl.setAttribute("data-event-title", storyData.title);

    // Construction de l'en-tête du modal
    const displayStartDate = storyData.date ? formatDateReadable(storyData.date) : "Date à venir";
    const calURLs = CalendarUtils.generateCalendarURLs({
      title: storyData.title || "Titre Inconnu",
      startDate: storyData.date,
      startTime: storyData.startTime || "10:15",
      endDate: storyData.endDate,
      endTime: storyData.endTime || "23:30",
      description: storyData.descriptionHtml || "",
      location: storyData.locationAddress || ""
    });
    const calendarOptionsHTML = CalendarUtils.buildCalendarDropdown(calURLs);

    // Construction des liens sociaux (si disponibles)
    let socialHTMLModal = "";
    if (storyData.evenementFacebook && storyData.lienFacebook) {
      socialHTMLModal += `
        <a href="${storyData.lienFacebook}" target="_blank">
          <button class="modal-btn-social fb">
            <i class="fa-brands fa-facebook"></i>
          </button>
        </a>`;
    }
    if (storyData.evenementInstagram && storyData.lienInstagram) {
      socialHTMLModal += `
        <a href="${storyData.lienInstagram}" target="_blank">
          <button class="modal-btn-social insta">
            <i class="fa-brands fa-instagram"></i>
          </button>
        </a>`;
    }

    /*****************************************************
     * CONSTRUCTION DU CONTENU PRINCIPAL DU MODAL
     * Vous pouvez modifier ici l'HTML pour changer le style.
     *****************************************************/
    let modalContentHTML = `
      <div class="ctrl-modal-content">
        <img src="${storyData.imageUrl || ""}" alt="Story ${storyData.id}" class="modal-image" />
        <div class="modal-infos">
          <div class="modal-header">
            <h2>${storyData.title || "Titre Inconnu"}</h2>
            <div class="ctrl-header-date">
              <div class="modal-date">${displayStartDate}</div>
              <div class="ctrl-atcb">
                <button class="dynamic-atcb-button">
                  <i class="fa-regular fa-calendar-plus"></i>
                </button>
                <div class="calendar-dropdown" style="display: none;">
                  ${calendarOptionsHTML}
                </div>
              </div>
            </div>
          </div>
          <div class="modal-infos-content">
            <div class="modal-times">
              <p><strong>De :</strong> ${storyData.startTime || "N/A"}</p>
              <p><strong>À :</strong> ${storyData.endTime || "N/A"}</p>
            </div>
            <p class="modal-organizer">${storyData.organizer || "N/A"}</p>
            <p>${storyData.locationName || "?"}</p>
            <div class="modal-ctrl-adresse">
              <p>${storyData.locationAddress || "?"}</p>
              <i class="fa-solid fa-location-dot"></i>
            </div>
            <div class="modal-description">
              ${storyData.descriptionHtml || ""}
            </div>
          </div>
          <div class="modal-footer">
            <div class="modal-footer-social">
              ${socialHTMLModal}
            </div>
    `;

    /*****************************************************
     * DÉTERMINATION DE L'AFFICHAGE DU BOUTON "PARTICIPER"
     * Le bouton s'affichera uniquement si :
     *   - L'attribut data-besoin-inscriptions vaut "Oui"
     *   - Et qu'au moins une des capacités (participants ou bénévoles) est > 0
     *****************************************************/
    const besoinInscriptions = (storyEl.getAttribute("data-besoin-inscriptions") || "Non").trim();
    const maxParticipants = parseInt(storyEl.getAttribute("data-nb-places-participants") || "0", 10);
    const maxBenevoles = parseInt(storyEl.getAttribute("data-nb-places-benevoles") || "0", 10);
    let showRegistrationButton = false;
    if (besoinInscriptions === "Oui" && (maxParticipants > 0 || maxBenevoles > 0)) {
      showRegistrationButton = true;
    }
    if (showRegistrationButton) {
      modalContentHTML += `
            <div class="footer-btn-participer">
              <a href="#" class="registrer-link">Participer</a>
            </div>
      `;
    }
    // Fin du pied de page
    modalContentHTML += `
          </div>
        </div>
      </div>
    `;
    // Insertion du contenu complet dans le modal
    modalEl.querySelector(".modal-body").innerHTML = modalContentHTML;

    /*****************************************************
     * FIN DE LA CONSTRUCTION DU MODAL
     *****************************************************/
  
    // Configuration du clic sur le fond pour fermer le modal.
    modalEl.addEventListener("click", function modalBackdropHandler(e) {
      if (e.target === modalEl) {
        closeStoryModal(storyEl);
        console.log("Modal fermé en cliquant sur le fond.");
      }
    }, { once: true });
  
    // Création d'un placeholder pour préserver la mise en page.
    const placeholder = createPlaceholder(storyEl);
    const storyCard = storyEl.querySelector(".my-story-rebuilt");
    if (storyCard) storyCard.style.visibility = "hidden";
  
    // Positionnement du modal.
    const rect = storyEl.getBoundingClientRect();
    const absoluteTop = rect.top + window.scrollY;
    const modalWidth = window.innerWidth * 0.8;
    const absoluteLeft = (window.innerWidth - modalWidth) / 2;
    modalEl.style.position = "absolute";
    modalEl.style.top = absoluteTop + "px";
    modalEl.style.left = absoluteLeft + "px";
    modalEl.style.width = modalWidth + "px";
    modalEl.style.zIndex = "1000";
  
    // Ajout du modal au body et affichage.
    document.body.appendChild(modalEl);
    modalEl.style.display = "block";
    modalEl.classList.remove("hidden");
    modalEl.classList.add("open");
    console.log(`Modal ouvert pour story #${storyData.id}, Titre: ${storyData.title}`);
  
    setTimeout(() => {
      adjustPlaceholderHeight(modalEl, placeholder);
    }, 50);
  
    // Configuration du dropdown du calendrier.
    const calendarButton = modalEl.querySelector(".dynamic-atcb-button");
    const dropdown = modalEl.querySelector(".calendar-dropdown");
    if (calendarButton && dropdown) {
      calendarButton.addEventListener("click", (e) => {
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === "flex" ? "none" : "flex";
      });
    }
  
    // Mise en place de l'overlay d'inscription si le bouton "Participer" est affiché.
    if (showRegistrationButton) {
      const registerBtn = modalEl.querySelector(".registrer-link");
      if (registerBtn) {
        registerBtn.addEventListener("click", function (e) {
          e.preventDefault();
          e.stopPropagation();
          // L'overlay d'inscription ne s'affichera que lorsque l'utilisateur cliquera sur "Participer"
          toggleRegistrationOverlay(modalEl, storyData);
        });
      }
    }
  
    return modalEl;
  }

  /*******************************************
   * FONCTION : FERMETURE DU MODAL
   *******************************************/
  function closeStoryModal(storyEl) {
    const modalEl = document.querySelector(`.story-modal[data-parent-story="${storyEl.id}"]`);
    if (modalEl) {
      modalEl.classList.remove("open");
      modalEl.classList.add("hidden");
      modalEl.style.display = "none";
      const storyCard = storyEl.querySelector(".my-story-rebuilt");
      if (storyCard) storyCard.style.visibility = "visible";
      const placeholder = storyEl.querySelector(".story-placeholder");
      if (placeholder) placeholder.remove();
      storyEl.appendChild(modalEl);
      console.log("Modal fermé et story restaurée.");
    }
  }

  /*******************************************
   * FONCTION : TOGGLE DE L'OVERLAY D'INSCRIPTION
   *******************************************/
  // Cette fonction crée ou affiche l'overlay qui contient le formulaire d'inscription.
  // Dans cet overlay, on insère également le bloc d'informations d'inscription,
  // qui affiche uniquement les lignes dont la capacité est non nulle.
  function toggleRegistrationOverlay(modalEl, storyData) {
    let overlay = modalEl.querySelector(".registration-form-overlay");
    if (overlay) {
      // Si l'overlay existe déjà, on le bascule
      overlay.style.display = (overlay.style.display === "flex") ? "none" : "flex";
      return;
    }
    overlay = document.createElement("div");
    overlay.className = "registration-form-overlay";
    overlay.style.display = "flex";
    // Insère l'overlay dans le modal (vous pouvez changer l'emplacement si nécessaire)
    modalEl.querySelector(".modal-body").appendChild(overlay);
  
    // Insertion du formulaire d'inscription via la fonction globale définie dans reg_form.js
    if (window.afficherFormulaireVide) {
      window.afficherFormulaireVide(overlay, storyData.id, storyData.title);
    } else {
      console.log("afficherFormulaireVide n'est pas défini dans window.");
    }
  
    // Insère le bloc d'informations d'inscription dans l'overlay.
    // On affiche une ligne pour "Places disponibles" uniquement si maxParticipants > 0,
    // et une ligne pour "Bénévoles demandés" uniquement si maxBenevoles > 0.
    const storyEl = document.getElementById(modalEl.getAttribute("data-parent-story"));
    const maxParticipants = parseInt(storyEl.getAttribute("data-nb-places-participants") || "0", 10);
    const maxBenevoles = parseInt(storyEl.getAttribute("data-nb-places-benevoles") || "0", 10);
    let registrationInfoHTML = "";
    if (maxParticipants > 0) {
      registrationInfoHTML += `<p>Places disponibles: <span id="current-participants">0</span> / ${maxParticipants}</p>`;
    }
    if (maxBenevoles > 0) {
      registrationInfoHTML += `<p>Bénévoles demandés: <span id="current-benevoles">0</span> / ${maxBenevoles}</p>`;
    }
    if (registrationInfoHTML) {
      registrationInfoHTML = `<div class="registration-info" style="padding:10px; background:#f9f9f9; border:1px solid #ddd; margin-bottom:10px;">${registrationInfoHTML}</div>`;
      // Insère le bloc d'infos au-dessus du formulaire
      overlay.insertAdjacentHTML("afterbegin", registrationInfoHTML);
    }
  }

  /*******************************************
   * FONCTIONS DE FILTRAGE & NAVIGATION ET RESPONSIVE
   *******************************************/
  // Récupère les années uniques à partir des stories.
  const yearsSet = new Set();
  document.querySelectorAll(".ctl-story").forEach((story) => {
    const eventDateStr = story.getAttribute("data-event-date");
    if (eventDateStr) {
      yearsSet.add(new Date(eventDateStr).getFullYear());
    }
  });
  let years = Array.from(yearsSet).sort((a, b) => a - b);
  const currentYear = new Date().getFullYear();
  let selectedYear = years.includes(currentYear)
    ? currentYear
    : years[0] || currentYear;

  // Met à jour la navigation par années.
  function updateYearNavigation(container) {
    container.innerHTML = "";
    const selectedIndex = years.indexOf(selectedYear);
    const prevYear = selectedIndex > 0 ? years[selectedIndex - 1] : null;
    const nextYear =
      selectedIndex < years.length - 1 ? years[selectedIndex + 1] : null;
    const btnLeft = document.createElement("button");
    btnLeft.className = "year-button";
    if (prevYear === null) {
      btnLeft.textContent = "••••";
      btnLeft.disabled = true;
    } else {
      btnLeft.textContent = prevYear;
      btnLeft.addEventListener("click", () => {
        selectedYear = prevYear;
        updateYearNavigation(container);
        filterStoriesByYear(selectedYear);
      });
    }
    container.appendChild(btnLeft);
    const btnCenter = document.createElement("button");
    btnCenter.className = "year-button selected";
    btnCenter.textContent = selectedYear;
    container.appendChild(btnCenter);
    const btnRight = document.createElement("button");
    btnRight.className = "year-button";
    if (nextYear === null) {
      btnRight.textContent = "••••";
      btnRight.disabled = true;
    } else {
      btnRight.textContent = nextYear;
      btnRight.addEventListener("click", () => {
        selectedYear = nextYear;
        updateYearNavigation(container);
        filterStoriesByYear(selectedYear);
      });
    }
    container.appendChild(btnRight);
  }

  // Fonction principale de filtrage des stories selon l'année et les types sélectionnés.
  function filterStoriesByYear(year) {
    const today = new Date();
    document.querySelectorAll(".ctl-story").forEach((story) => {
      const eventDateStr = story.getAttribute("data-event-date");
      if (eventDateStr) {
        const eventYear = new Date(eventDateStr).getFullYear();
        // Récupère le tableau des types depuis data-type_dactivite (format JSON)
        const typeData = story.getAttribute("data-type_dactivite") || "";
        let storyTypes = [];
        try {
          storyTypes = JSON.parse(typeData);
          if (!Array.isArray(storyTypes)) {
            storyTypes = [storyTypes];
          }
        } catch (e) {
          storyTypes = typeData
            .split(",")
            .map((t) => t.trim())
            .filter((t) => t.length > 0);
        }
        // Affiche la story si aucun filtre n'est appliqué ou si au moins un de ses types est sélectionné.
        const showByType =
          selectedTypes.size === 0 ||
          storyTypes.some((t) => selectedTypes.has(t));
        if (eventYear === year && showByType) {
          if (
            year === currentYear &&
            new Date(eventDateStr) < today &&
            !showPassed
          ) {
            story.style.display = "none";
          } else {
            story.style.display = "";
            const mobileDate = story.querySelector(".story-date-mobile");
            if (mobileDate) mobileDate.classList.remove("hidden");
          }
        } else {
          story.style.display = "none";
        }
      }
    });
    markPassedEvents();
    // Affiche le toggle uniquement pour l'année en cours.
    const toggleBtn = document.getElementById("togglePassed");
    if (toggleBtn) {
      toggleBtn.style.display =
        selectedYear === currentYear ? "inline-block" : "none";
    }
    if (window.innerWidth >= 1024) {
      resetAlternance();
    } else {
      updateStoriesForSmallScreen();
    }
  }

  /*******************************************
   * FONCTIONS RESPONSIVES
   *******************************************/
  function resetAlternance() {
    if (window.innerWidth < 1024) return;
    const visibleStories = Array.from(
      document.querySelectorAll(".ctl-story")
    ).filter((story) => story.style.display !== "none");
    visibleStories.forEach((story, index) => {
      story.classList.remove("ctl-story-left", "ctl-story-right");
      const contentEl = story.querySelector(".my-story-content");
      const dateEl = story.querySelector(".story-date");
      const labelsEl = dateEl ? dateEl.querySelector(".ctl-labels") : null;
      if (index % 2 === 0) {
        story.classList.add("ctl-story-left");
        if (contentEl) {
          contentEl.classList.remove("left", "right");
          contentEl.classList.add("left");
        }
        if (dateEl) {
          dateEl.classList.remove("story-date-right", "story-date-left");
          dateEl.classList.add("story-date-left");
        }
        if (labelsEl) {
          labelsEl.classList.remove("ctl-labels-right");
          labelsEl.classList.add("ctl-labels-left");
          labelsEl
            .querySelectorAll(".triangle-left, .triangle-right")
            .forEach((el) => el.remove());
          const ctlLabelBig = labelsEl.querySelector(".ctl-label-big");
          const triangle = document.createElement("div");
          triangle.className = "triangle-left";
          if (ctlLabelBig) {
            labelsEl.insertBefore(triangle, ctlLabelBig);
          } else {
            labelsEl.insertBefore(triangle, labelsEl.firstChild);
          }
        }
      } else {
        story.classList.add("ctl-story-right");
        if (contentEl) {
          contentEl.classList.remove("left", "right");
          contentEl.classList.add("right");
        }
        if (dateEl) {
          dateEl.classList.remove("story-date-right", "story-date-left");
          dateEl.classList.add("story-date-right");
        }
        if (labelsEl) {
          labelsEl.classList.remove("ctl-labels-left");
          labelsEl.classList.add("ctl-labels-right");
          labelsEl
            .querySelectorAll(".triangle-left, .triangle-right")
            .forEach((el) => el.remove());
          const triangle = document.createElement("div");
          triangle.className = "triangle-right";
          labelsEl.appendChild(triangle);
        }
      }
    });
  }

  function updateStoriesForSmallScreen() {
    Array.from(document.querySelectorAll(".ctl-story"))
      .filter((story) => story.style.display !== "none")
      .forEach((story) => {
        story.classList.remove("ctl-story-left");
        story.classList.add("ctl-story-right");
        const contentEl = story.querySelector(".my-story-content");
        if (contentEl) {
          contentEl.classList.remove("left");
          contentEl.classList.add("right");
        }
        const dateEl = story.querySelector(".story-date");
        if (dateEl) {
          dateEl.classList.remove("story-date-left");
          dateEl.classList.add("story-date-right");
        }
        const labelsEl = dateEl ? dateEl.querySelector(".ctl-labels") : null;
        if (labelsEl) {
          labelsEl.classList.remove("ctl-labels-left");
          labelsEl.classList.add("ctl-labels-right");
          labelsEl
            .querySelectorAll(".triangle-left, .triangle-right")
            .forEach((el) => el.remove());
          const triangle = document.createElement("div");
          triangle.className = "triangle-right";
          labelsEl.appendChild(triangle);
        }
      });
  }

  function overrideOddLeftStories() {
    const w = window.innerWidth;
    if (w <= 1024) {
      document.querySelectorAll(".ctl-story").forEach((storyEl) => {
        if (
          storyEl.dataset.originalPosition === "left" &&
          storyEl.dataset.originalParity === "odd"
        ) {
          storyEl.classList.remove("ctl-story-left", "odd");
          storyEl.classList.add("ctl-story-right", "even");
          const labelsEl = storyEl.querySelector(".ctl-labels");
          if (labelsEl) {
            labelsEl.classList.remove("ctl-labels-left");
            labelsEl.classList.add("ctl-labels-right");
          }
        }
      });
    }
  }
  overrideOddLeftStories();
  window.addEventListener("resize", function () {
    overrideOddLeftStories();
    if (window.innerWidth < 1024) {
      updateStoriesForSmallScreen();
    } else {
      resetAlternance();
    }
  });
  setTimeout(overrideOddLeftStories, 200);
  markPassedEvents();
  setTimeout(resetAlternance, 100);

  /*******************************************
   * Fermeture des modals en cliquant en dehors
   *******************************************/
  document.addEventListener("click", function (e) {
    // Si le clic n'est pas effectué dans un descendant d'un modal ouvert, ferme tous les modals.
    if (
      !e.target.closest(".story-modal") &&
      document.querySelector(".story-modal.open")
    ) {
      closeAllModals();
    }
  });

  /*******************************************
   * INITIALISATION DES CONTROLES
   *******************************************/
  function initControls() {
    const timelineTitleDiv = document.querySelector(".timeline-main-title");
    if (!timelineTitleDiv) return;
    const controlsContainer = document.createElement("div");
    controlsContainer.className = "ctrl-btn-container";

    // Bouton toggle pour les événements passés (seulement pour l'année en cours)
    const togglePassed = document.createElement("button");
    togglePassed.id = "togglePassed";
    togglePassed.className = "toggle-passed-button";
    togglePassed.innerHTML =
      '<i class="fa-solid fa-toggle-off"></i> Voir les événements passés';
    togglePassed.dataset.active = "false";
    togglePassed.addEventListener("click", function () {
      if (this.dataset.active === "false") {
        this.dataset.active = "true";
        this.classList.add("active");
        this.innerHTML =
          '<i class="fa-solid fa-toggle-on"></i> Voir les événements passés';
        showPassed = true;
      } else {
        this.dataset.active = "false";
        this.classList.remove("active");
        this.innerHTML =
          '<i class="fa-solid fa-toggle-off"></i> Voir les événements passés';
        showPassed = false;
      }
      filterStoriesByYear(selectedYear);
    });

    const filterContainer = initTypeFilter();
    const yearNavContainer = document.createElement("div");
    yearNavContainer.className = "year-navigation";
    updateYearNavigation(yearNavContainer);

    controlsContainer.appendChild(togglePassed);
    controlsContainer.appendChild(filterContainer);
    controlsContainer.appendChild(yearNavContainer);

    const h2 = timelineTitleDiv.querySelector("h2");
    if (h2 && h2.nextSibling) {
      timelineTitleDiv.insertBefore(controlsContainer, h2.nextSibling);
    } else {
      timelineTitleDiv.appendChild(controlsContainer);
    }
  }

  // Initialise le dropdown de filtres par type.
  function initTypeFilter() {
    const typeMapping = {
      atelier: "Atelier",
      mentorat: "Mentorat",
      activite: "Activité",
      animation: "Animation",
      cours: "Cours",
      demonstration: "Démonstration",
      film: "Film",
      "pique-nique": "Pique-nique",
    };
    const typeList = Object.keys(typeMapping);
    const filterContainer = document.createElement("div");
    filterContainer.className = "ctrl-btn-filters";

    const filterButton = document.createElement("button");
    filterButton.id = "filterButton";
    filterButton.textContent = "Filtres";
    filterButton.className = "filter-button";
    filterContainer.appendChild(filterButton);

    const filterDropdown = document.createElement("div");
    filterDropdown.id = "filterDropdown";
    filterDropdown.className = "filter-dropdown";
    filterDropdown.style.display = "none";

    // Crée une case à cocher pour chaque type.
    typeList.forEach((id) => {
      const itemLabel = document.createElement("label");
      itemLabel.className = "filter-item";
      const checkbox = document.createElement("input");
      checkbox.type = "checkbox";
      checkbox.value = id;
      checkbox.addEventListener("change", function (e) {
        if (this.checked) {
          selectedTypes.add(this.value);
        } else {
          selectedTypes.delete(this.value);
        }
        filterStoriesByYear(selectedYear);
      });
      itemLabel.appendChild(checkbox);
      const span = document.createElement("span");
      span.textContent = " " + typeMapping[id];
      itemLabel.appendChild(span);
      filterDropdown.appendChild(itemLabel);
    });

    // Bouton "Réinitialiser" pour vider la sélection.
    const resetButton = document.createElement("button");
    resetButton.textContent = "Réinitialiser";
    resetButton.className = "reset-button";
    resetButton.addEventListener("click", function (e) {
      e.stopPropagation();
      const inputs = filterDropdown.querySelectorAll("input");
      inputs.forEach((inp) => {
        inp.checked = false;
      });
      selectedTypes.clear();
      filterStoriesByYear(selectedYear);
    });
    filterDropdown.appendChild(resetButton);

    // Bouton "Fermer" (✕) en haut à droite du dropdown.
    const closeButton = document.createElement("button");
    closeButton.textContent = "✕";
    closeButton.className = "close-dropdown";
    closeButton.addEventListener("click", function (e) {
      e.stopPropagation();
      filterDropdown.style.display = "none";
    });
    filterDropdown.insertBefore(closeButton, filterDropdown.firstChild);

    filterContainer.appendChild(filterDropdown);

    filterButton.addEventListener("click", function (e) {
      e.stopPropagation();
      filterDropdown.style.display =
        filterDropdown.style.display === "none" ? "block" : "none";
    });
    document.addEventListener("click", function (e) {
      if (!filterContainer.contains(e.target)) {
        filterDropdown.style.display = "none";
      }
    });

    return filterContainer;
  }

  /*******************************************
   * OBSERVATION D'INTERSECTION (ANIMATIONS)
   *******************************************/
  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const theStory = entry.target;
          const isRight = theStory.classList.contains("ctl-story-right");
          const position = isRight ? "right" : "left";
          const dateLabels = theStory.querySelector(".story-date");
          if (dateLabels) {
            dateLabels.classList.remove("hiddenAtStart");
            const ctlLabelBig = dateLabels.querySelector(
              `.ctl-label-big.story-date.ctl-label-big-${position}`
            );
            if (ctlLabelBig)
              ctlLabelBig.classList.add(`scale-in-hor-${position}`);
          }
          const mobileDate = theStory.querySelector(".story-date-mobile");
          if (mobileDate) {
            mobileDate.classList.remove("hidden");
            mobileDate.classList.add("scale-in-ver-bottom");
          }
          const storyContent = theStory.querySelector(".my-story-content");
          if (storyContent) {
            storyContent.classList.remove("hiddenAtStart");
            storyContent.classList.add(
              position === "left" ? "swing-in-right-fwd" : "swing-in-left-fwd"
            );
            storyContent.classList.add(position);
          }
          const footerBtn = theStory.querySelector(".footer-btn-savoir");
          if (footerBtn) {
            footerBtn.classList.remove("hiddenAtStart");
            setTimeout(() => {
              footerBtn.classList.add("show");
            }, 200);
          }
          obs.unobserve(theStory);
        }
      });
    },
    { rootMargin: "0px 0px -10% 0px", threshold: 0.1 }
  );

  /*******************************************
   * CONSTRUCTION DU HTML POUR CHAQUE STORY
   *******************************************/
  document.querySelectorAll(".ctl-story").forEach(function (storyEl) {
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
      if (titleEl) titleText = titleEl.textContent.trim();
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
    // Récupère le tableau des types d'activité depuis data-type_dactivite (format JSON)
    const typeData = storyEl.getAttribute("data-type_dactivite") || "";
    let storyTypes = [];
    try {
      storyTypes = JSON.parse(typeData);
      if (!Array.isArray(storyTypes)) {
        storyTypes = [storyTypes];
      }
    } catch (e) {
      storyTypes = typeData
        .split(",")
        .map((t) => t.trim())
        .filter((t) => t.length > 0);
    }
    const isRight = storyEl.classList.contains("ctl-story-right");
    const position = isRight ? "right" : "left";

    let modifiedDateLabelsHtml = dateLabelsHtml;
    if (position === "left") {
      modifiedDateLabelsHtml = dateLabelsHtml
        .replace(
          '<div class="ctl-labels">',
          '<div class="ctl-labels ctl-labels-left">'
        )
        .replace(
          '<div class="ctl-label-big story-date">',
          '<div class="triangle-left"></div><div class="ctl-label-big story-date ctl-label-big-left">'
        );
    } else {
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

    const mobileDateHtml = `<div class="story-date-mobile hidden"><p>${formatDateReadable(
      storyDate
    )}</p></div>`;
    const desktopDateHtml = `<div class="story-date hiddenAtStart ${
      position === "left" ? "story-date-left" : "story-date-right"
    }">
                                ${modifiedDateLabelsHtml}
                              </div>`;

    let socialHTMLCard = "";
    if (
      storyEl.getAttribute("data-evenement-facebook") &&
      storyEl.getAttribute("data-lien-facebook")
    ) {
      socialHTMLCard += `<a href="${storyEl.getAttribute(
        "data-lien-facebook"
      )}" target="_blank">
                           <button class="btn-social fb">
                             <i class="fa-brands fa-facebook"></i>
                           </button>
                         </a>`;
    }
    if (
      storyEl.getAttribute("data-evenement-instagram") &&
      storyEl.getAttribute("data-lien-instagram")
    ) {
      socialHTMLCard += `<a href="${storyEl.getAttribute(
        "data-lien-instagram"
      )}" target="_blank">
                           <button class="btn-social insta">
                             <i class="fa-brands fa-instagram"></i>
                           </button>
                         </a>`;
    }

    const newHtml = `
      <div class="my-story-rebuilt ${
        position === "left" ? "swing-in-right-fwd" : "swing-in-left-fwd"
      }">
        ${mobileDateHtml}
        ${desktopDateHtml}
        <div class="my-icon-centered">
          ${iconHtml}
        </div>
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
          <div class="my-story-header">
            <h3 class="my-story-title">${
              titleText || "Événement sans titre"
            }</h3>
            <p class="my-story-completed">Complété</p>
          </div>
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
              ${socialHTMLCard}
            </div>
            <div class="footer-btn-savoir hiddenAtStart">
              <a href="${titleHref}" class="readmore-link">En savoir plus ▸</a>
            </div>
          </div>
        </div>
        <div class="story-modal my-modal-overlay hidden">
          <div class="modal-content">
            <button class="modal-close">✕</button>
            <div class="modal-body">
              <!-- Contenu dynamique du modal -->
            </div>
          </div>
        </div>
      </div>
    `;
    storyEl.innerHTML = newHtml;
    storyEl.style.cursor = "pointer";

    const newLabelsEl = storyEl.querySelector(".ctl-labels");
    if (newLabelsEl) {
      newLabelsEl.dataset.originalLabels = storyEl.classList.contains(
        "ctl-story-right"
      )
        ? "right"
        : "left";
    }

    storyEl.addEventListener("click", (evt) => {
      evt.preventDefault();
      evt.stopPropagation();
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
        evenementFacebook:
          storyEl.getAttribute("data-evenement-facebook") || "",
        lienFacebook: storyEl.getAttribute("data-lien-facebook") || "",
        evenementInstagram:
          storyEl.getAttribute("data-evenement-instagram") || "",
        lienInstagram: storyEl.getAttribute("data-lien-instagram") || "",
      };
      console.log("Données de la story pour le modal :", storyData);
      const modalElement = openStoryModal(storyEl, storyData);
      if (modalElement) {
        modalElement.setAttribute("data-parent-story", storyEl.id);
        modalElement.setAttribute("data-event-title", storyData.title);
      }
    });

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
        console.log("Lien Google Maps :", addressLink.href);
      });
    }

    const modalCloseBtn = storyEl.querySelector(".modal-close");
    const modalDiv = storyEl.querySelector(".story-modal");
    if (modalCloseBtn && modalDiv) {
      modalCloseBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        closeStoryModal(storyEl);
        console.log("Modal fermé via le bouton de fermeture.");
      });
    }
    if (modalDiv) {
      modalDiv.addEventListener("click", function (e) {
        if (e.target === modalDiv) {
          closeStoryModal(storyEl);
          console.log("Modal fermé en cliquant sur le fond.");
        }
      });
    }
    observer.observe(storyEl);
  });

  /*******************************************
   * FONCTIONS RESPONSIVES
   *******************************************/
  function resetAlternance() {
    if (window.innerWidth < 1024) return;
    const visibleStories = Array.from(
      document.querySelectorAll(".ctl-story")
    ).filter((story) => story.style.display !== "none");
    visibleStories.forEach((story, index) => {
      story.classList.remove("ctl-story-left", "ctl-story-right");
      const contentEl = story.querySelector(".my-story-content");
      const dateEl = story.querySelector(".story-date");
      const labelsEl = dateEl ? dateEl.querySelector(".ctl-labels") : null;
      if (index % 2 === 0) {
        story.classList.add("ctl-story-left");
        if (contentEl) {
          contentEl.classList.remove("left", "right");
          contentEl.classList.add("left");
        }
        if (dateEl) {
          dateEl.classList.remove("story-date-right", "story-date-left");
          dateEl.classList.add("story-date-left");
        }
        if (labelsEl) {
          labelsEl.classList.remove("ctl-labels-right");
          labelsEl.classList.add("ctl-labels-left");
          labelsEl
            .querySelectorAll(".triangle-left, .triangle-right")
            .forEach((el) => el.remove());
          const ctlLabelBig = labelsEl.querySelector(".ctl-label-big");
          const triangle = document.createElement("div");
          triangle.className = "triangle-left";
          if (ctlLabelBig) {
            labelsEl.insertBefore(triangle, ctlLabelBig);
          } else {
            labelsEl.insertBefore(triangle, labelsEl.firstChild);
          }
        }
      } else {
        story.classList.add("ctl-story-right");
        if (contentEl) {
          contentEl.classList.remove("left", "right");
          contentEl.classList.add("right");
        }
        if (dateEl) {
          dateEl.classList.remove("story-date-right", "story-date-left");
          dateEl.classList.add("story-date-right");
        }
        if (labelsEl) {
          labelsEl.classList.remove("ctl-labels-left");
          labelsEl.classList.add("ctl-labels-right");
          labelsEl
            .querySelectorAll(".triangle-left, .triangle-right")
            .forEach((el) => el.remove());
          const triangle = document.createElement("div");
          triangle.className = "triangle-right";
          labelsEl.appendChild(triangle);
        }
      }
    });
  }

  function updateStoriesForSmallScreen() {
    Array.from(document.querySelectorAll(".ctl-story"))
      .filter((story) => story.style.display !== "none")
      .forEach((story) => {
        story.classList.remove("ctl-story-left");
        story.classList.add("ctl-story-right");
        const contentEl = story.querySelector(".my-story-content");
        if (contentEl) {
          contentEl.classList.remove("left");
          contentEl.classList.add("right");
        }
        const dateEl = story.querySelector(".story-date");
        if (dateEl) {
          dateEl.classList.remove("story-date-left");
          dateEl.classList.add("story-date-right");
        }
        const labelsEl = dateEl ? dateEl.querySelector(".ctl-labels") : null;
        if (labelsEl) {
          labelsEl.classList.remove("ctl-labels-left");
          labelsEl.classList.add("ctl-labels-right");
          labelsEl
            .querySelectorAll(".triangle-left, .triangle-right")
            .forEach((el) => el.remove());
          const triangle = document.createElement("div");
          triangle.className = "triangle-right";
          labelsEl.appendChild(triangle);
        }
      });
  }

  function overrideOddLeftStories() {
    const w = window.innerWidth;
    if (w <= 1024) {
      document.querySelectorAll(".ctl-story").forEach((storyEl) => {
        if (
          storyEl.dataset.originalPosition === "left" &&
          storyEl.dataset.originalParity === "odd"
        ) {
          storyEl.classList.remove("ctl-story-left", "odd");
          storyEl.classList.add("ctl-story-right", "even");
          const labelsEl = storyEl.querySelector(".ctl-labels");
          if (labelsEl) {
            labelsEl.classList.remove("ctl-labels-left");
            labelsEl.classList.add("ctl-labels-right");
          }
        }
      });
    }
  }
  overrideOddLeftStories();
  window.addEventListener("resize", function () {
    overrideOddLeftStories();
    if (window.innerWidth < 1024) {
      updateStoriesForSmallScreen();
    } else {
      resetAlternance();
    }
  });
  setTimeout(overrideOddLeftStories, 200);
  markPassedEvents();
  setTimeout(resetAlternance, 100);

  /*******************************************
   * Fermeture des modals en cliquant en dehors
   *******************************************/
  document.querySelectorAll(".btn-social").forEach(function (button) {
    button.addEventListener("click", function (e) {
      e.stopPropagation();
      // Le navigateur va suivre le lien en raison du target="_blank"
    });
  });

  document.addEventListener("click", function (e) {
    // Si le clic n'est pas effectué dans un descendant d'un modal ouvert, ferme tous les modals
    if (
      !e.target.closest(".story-modal") &&
      document.querySelector(".story-modal.open")
    ) {
      closeAllModals();
    }
  });

  /*******************************************
   * INITIALISATION DES CONTROLES
   *******************************************/
  function initControls() {
    const timelineTitleDiv = document.querySelector(".timeline-main-title");
    if (!timelineTitleDiv) return;
    const controlsContainer = document.createElement("div");
    controlsContainer.className = "ctrl-btn-container";

    // Bouton toggle pour les événements passés (seulement pour l'année en cours)
    const togglePassed = document.createElement("button");
    togglePassed.id = "togglePassed";
    togglePassed.className = "toggle-passed-button";
    togglePassed.innerHTML =
      '<i class="fa-solid fa-toggle-off"></i> Voir les événements passés';
    togglePassed.dataset.active = "false";
    togglePassed.addEventListener("click", function () {
      if (this.dataset.active === "false") {
        this.dataset.active = "true";
        this.classList.add("active");
        this.innerHTML =
          '<i class="fa-solid fa-toggle-on"></i> Voir les événements passés';
        showPassed = true;
      } else {
        this.dataset.active = "false";
        this.classList.remove("active");
        this.innerHTML =
          '<i class="fa-solid fa-toggle-off"></i> Voir les événements passés';
        showPassed = false;
      }
      filterStoriesByYear(selectedYear);
    });

    const filterContainer = initTypeFilter();
    const yearNavContainer = document.createElement("div");
    yearNavContainer.className = "year-navigation";
    updateYearNavigation(yearNavContainer);

    controlsContainer.appendChild(togglePassed);
    controlsContainer.appendChild(filterContainer);
    controlsContainer.appendChild(yearNavContainer);

    const h2 = timelineTitleDiv.querySelector("h2");
    if (h2 && h2.nextSibling) {
      timelineTitleDiv.insertBefore(controlsContainer, h2.nextSibling);
    } else {
      timelineTitleDiv.appendChild(controlsContainer);
    }
  }

  initControls();
  filterStoriesByYear(selectedYear);
});
