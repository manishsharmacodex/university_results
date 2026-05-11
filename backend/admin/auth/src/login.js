// windows history prevent to back button
if (window.history && window.history.pushState) {
  window.history.pushState(null, document.title, window.location.href);

  window.onpopstate = function () {
    window.history.pushState(null, document.title, window.location.href);
  };
}

// refresh functions
function refreshCaptcha() {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  xhr.onload = function () {
    if (this.status === 200) {
      let data = this.responseText.trim().split("|");

      if (data.length === 3) {
        document.getElementById("num1").innerText = data[0];
        document.getElementById("operator").innerText = data[1];
        document.getElementById("num2").innerText = data[2];
      }
    }
  };

  xhr.send("refresh_captcha=1");
}
