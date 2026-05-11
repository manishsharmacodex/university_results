// Uppercase input
document.querySelectorAll("input[type='text'], textarea").forEach((field) => {
  field.addEventListener("input", function () {
    this.value = this.value.toUpperCase();
  });
});
