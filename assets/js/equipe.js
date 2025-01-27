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
    btnActif = navIndex;
    setActif(btn);
  });
  setActif(btn);
});

btnPrevious.addEventListener("click", () => {
  navIndex--;
  if (navIndex < 1) {
    navIndex = membres.length;
  }
  changerMembre(navIndex);
});

btnNext.addEventListener("click", () => {
  navIndex++;
  if (navIndex > membres.length) {
    navIndex = 1;
  }
  changerMembre(navIndex);
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

function setActif(btn) {
  if (btn.id == btnActif) {
    btn.classList.add("actif");
  }
}
