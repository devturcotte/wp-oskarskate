document.addEventListener("DOMContentLoaded", () => {
  const slider = document.querySelector(".slider_slides");
  const slides = document.querySelectorAll(".slider_slide");
  const btnPrev = document.querySelector(".control--previous");
  const btnNext = document.querySelector(".control--next");

  let isAnimating = false;
  let currentIndex = getRandomIndex();

  function getRandomIndex() {
      return Math.floor(Math.random() * slides.length);
  }

  function updateSlideClasses() {
      slides.forEach((slide, index) => {
          slide.classList.toggle("active", index === currentIndex);
      });
  }

  function moveSlider(newIndex) {
      if (isAnimating) return;
      isAnimating = true;

      currentIndex = (newIndex + slides.length) % slides.length;
      const activeSlide = slides[currentIndex];

      slider.style.scrollBehavior = "smooth";
      slider.scrollLeft = activeSlide.offsetLeft;
      updateSlideClasses();

      setTimeout(() => {
          isAnimating = false;
      }, 500);
  }

  moveSlider(currentIndex);

  btnPrev.addEventListener("click", () => moveSlider(currentIndex - 1));
  btnNext.addEventListener("click", () => moveSlider(currentIndex + 1));
});
