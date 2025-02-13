document.addEventListener("DOMContentLoaded", () => {
  const slider = document.querySelector(".slider_slides");
  const slides = document.querySelectorAll(".slider_slide");
  const btnPrev = document.querySelector(".control--previous");
  const btnNext = document.querySelector(".control--next");

  let currentIndex = 0;

  function updateSlideClasses() {
      slides.forEach((slide, index) => {
          slide.classList.toggle("active", index === currentIndex);
      });
  }

  function moveSlider(newIndex) {

      currentIndex = (newIndex + slides.length) % slides.length;
      const activeSlide = slides[currentIndex];

      slider.scrollTo({
        left: activeSlide.offsetLeft - (slider.offsetWidth - activeSlide.offsetWidth) 
    });

      updateSlideClasses();
  }

  moveSlider(currentIndex);

  btnPrev.addEventListener("click", () => moveSlider(currentIndex - 1));
  btnNext.addEventListener("click", () => moveSlider(currentIndex + 1));
});
