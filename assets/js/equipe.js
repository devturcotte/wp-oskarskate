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
