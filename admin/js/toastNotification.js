// toast notification script
function showToast(message, type = "success") {
  const toast = document.getElementById("toast");

  toast.className = "toast show " + type;

  toast.innerText = message;

  setTimeout(() => {
    toast.classList.remove("show");
  }, 3000);
}
