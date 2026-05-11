/* =========================================
    AJAX FORM SUBMIT + POPUP
    ========================================= */

const form = document.getElementById("admissionForm");

if (form) {
  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append("admission_button", true);

    try {
      const response = await fetch(window.location.href, {
        method: "POST",
        body: formData,
      });

      const text = await response.text();
      const data = JSON.parse(text);

      if (data.status === "success") {
        document.getElementById("admissionNo").innerText = data.admission_no;
        document.getElementById("popupModal").style.display = "flex";

        form.reset();
      } else {
        alert(data.message || "Error occurred");
      }
    } catch (error) {
      console.error("Submit Error:", error);
      alert("Something went wrong. Please try again.");
    }
  });
}

/* =========================================
    MODAL CLOSE
    ========================================= */

function closeModal() {
  document.getElementById("popupModal").style.display = "none";
}
