const sliderContainer = document.querySelector(".slider-container");
if (sliderContainer) {
  slider();
}

function slider() {
  document.addEventListener("DOMContentLoaded", () => {
    const slider = document.querySelector(".slider_slides");
    const slides = document.querySelectorAll(".slider_slide");
    const btnPrev = document.querySelector(".control--previous");
    const btnNext = document.querySelector(".control--next");

    let currentIndex = 0;
    let isAnimating = false; 

    function updateSlideClasses() {

      slides.forEach((slide, index) => {
        slide.classList.remove("active");
        if (index === currentIndex) {
          slide.classList.add("active");
        }
      });
    }

    function moveSlider(index) {
      currentIndex = (index + slides.length) % slides.length;

      const activeSlide = slides[currentIndex];
      slider.scrollTo({
        left:
          activeSlide.offsetLeft -
          (slider.clientWidth - activeSlide.clientWidth) / 2,
        behavior: "smooth",
      });
      updateSlideClasses();
      
        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentIndex);
        });
    }

    function moveSlider(newIndex) {
        if (isAnimating) return; 
        isAnimating = true;

        currentIndex = (newIndex + slides.length) % slides.length;
        const activeSlide = slides[currentIndex];

        slider.style.scrollBehavior = "smooth"; 
        slider.scrollTo({
            left: activeSlide.offsetLeft - (slider.clientWidth - activeSlide.clientWidth) / 2,
        });

        updateSlideClasses();

        setTimeout(() => {
            isAnimating = false;
        }, 500);
    }

    updateSlideClasses();

    btnPrev.addEventListener("click", () => moveSlider(currentIndex - 1));
    btnNext.addEventListener("click", () => moveSlider(currentIndex + 1));
  });
}
