const openBtn = document.querySelector(".open-btn");
const closeBtn = document.querySelector(".close-btn");
const navItems = document.querySelector(".nav-items");
const content = document.querySelector("body");

[openBtn, closeBtn].forEach((btn) => {
  btn.addEventListener("click", () => {
    openBtn.classList.toggle("hidden");
    closeBtn.classList.toggle("hidden");
    navItems.classList.toggle("hidden");
    content.classList.toggle("no-scroll");
  });
});
