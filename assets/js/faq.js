const questions = document.querySelectorAll(".question");
questions.forEach((question) => {
  question.addEventListener("click", () => {
    const reponse = question.nextElementSibling;
    reponse.classList.toggle("hidden");

    const i = question.lastElementChild;
    if (i.classList.contains("fa-plus")) {
      i.classList.remove("fa-plus");
      i.classList.add("fa-minus");
    } else {
      i.classList.remove("fa-minus");
      i.classList.add("fa-plus");
    }
  });
});
