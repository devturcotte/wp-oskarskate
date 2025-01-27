const bannerBtn = document.querySelector(".banner-btn");
if (bannerBtn.textContent == "") {
  bannerBtn.classList.add("hidden");
} else {
  bannerBtn.classList.remove("hidden");
}

const bannerText = document.querySelector(".banner-texte");
if (bannerText.textContent == "") {
  bannerText.classList.add("hidden");
} else {
  bannerText.classList.remove("hidden");
}
