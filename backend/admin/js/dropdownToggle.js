// Dropdown Toggle
const dropdownBtn = document.querySelector(".dropdown-btn");

dropdownBtn.addEventListener("click", function () {
  this.parentElement.classList.toggle("active");
});
