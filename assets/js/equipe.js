const btnMembre = document.querySelectorAll(".btn-membre");
const membres = document.querySelectorAll(".membre-container");
btnMembre.forEach((btn) => {
  btn.addEventListener("click", () => {
    membres.forEach((membre) => {
      if (btn.id === membre.id) {
        console.log(membre, btn);
      }
    });
  });
});

const btnPrevious = document.querySelector(".btn-previous");
const btnNext = document.querySelector(".btn-next");
[btnPrevious, btnNext].forEach((btn) => {
  btn.addEventListener("click", () => {
    console.log(btn);
  });
});
