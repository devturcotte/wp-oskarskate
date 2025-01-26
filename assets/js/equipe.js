const btnMembre = document.querySelectorAll(".btn-membre");
btnMembre.forEach((btn) => {
  btn.addEventListener("click", () => {
    console.log(btn.id);
  });
});

const btnPrevious = document.querySelector(".btn-previous");
const btnNext = document.querySelector(".btn-next");
[btnPrevious, btnNext].forEach((btn) => {
  btn.addEventListener("click", () => {
    console.log(btn);
  });
});
