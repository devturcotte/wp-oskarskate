const equipeContainer = document.querySelector(".equipe-main-container");
if (equipeContainer) {
  equipe();
}

function equipe() {
  const btnPrevious = document.querySelector(".btn-previous");
  const btnNext = document.querySelector(".btn-next");
  const btnMembre = document.querySelectorAll(".btn-membre");
  const membres = document.querySelectorAll(".membre-container");
  let navIndex = membres.length;
  let btnActif = navIndex;

  btnMembre.forEach((btn) => {
    btn.addEventListener("click", () => {
      changerMembre(parseInt(btn.id), btn);
      navIndex = btn.id;
      setActif();
    });
    setActif();
  });

  btnPrevious.addEventListener("click", () => {
    navIndex++;
    if (navIndex > membres.length) {
      navIndex = 1;
    }
    changerMembre(navIndex);
    setActif();
  });

  btnNext.addEventListener("click", () => {
    navIndex--;
    if (navIndex < 1) {
      navIndex = membres.length;
    }
    changerMembre(navIndex);
    setActif();
  });

  function changerMembre(index, btnIndex) {
    if (btnIndex || index) {
      membres.forEach((membre) => {
        if (membre.id == btnIndex || membre.id == index) {
          membre.classList.remove("hidden");
        } else {
          membre.classList.add("hidden");
        }
      });
    }
  }
  changerMembre(navIndex, btnActif);

  function setActif() {
    btnActif = navIndex;
    btnMembre.forEach((btn) => {
      if (btn.id == btnActif) {
        btn.classList.add("actif");
      } else {
        btn.classList.remove("actif");
      }
    });
  }

  function setStats() {
    const quantites = document.querySelectorAll(".quantite");
    const checkedMapping = {
      _1: 1,
      _2: 2,
      _3: 3,
      _4: 4,
    };

    quantites.forEach((quantite) => {
      let checkedCount = 5;
      for (const className in checkedMapping) {
        if (quantite.classList.contains(className)) {
          checkedCount = checkedMapping[className];
          break;
        }
      }

      quantite.innerHTML = `
        ${'<div class="circle checked"></div>'.repeat(checkedCount)}
        ${'<div class="circle"></div>'.repeat(5 - checkedCount)}
      `;
    });
  }
  setStats();
}
