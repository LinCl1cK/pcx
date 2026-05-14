document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");
  const showRegister = document.getElementById("showRegister");
  const showLogin = document.getElementById("showLogin");

  if (showRegister) {
    showRegister.addEventListener("click", function (e) {
      e.preventDefault();
      if (loginForm) loginForm.style.display = "none";
      if (registerForm) registerForm.style.display = "block";
    });
  }

  if (showLogin) {
    showLogin.addEventListener("click", function (e) {
      e.preventDefault();
      if (registerForm) registerForm.style.display = "none";
      if (loginForm) loginForm.style.display = "block";
    });
  }
});
