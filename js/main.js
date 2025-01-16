document.addEventListener("DOMContentLoaded", function () {
	const lightModeToggle = document.getElementById("light-mode-toggle");
	lightModeToggle.addEventListener("click", toggleLightMode);

	const lightModePreference = localStorage.getItem("lightMode");
	if (lightModePreference === "enabled") {
		document.body.classList.add("light-mode")
	} else {
		document.body.classList.remove("light-mode")
	}
});

function toggleLightMode() {
	document.body.classList.toggle("light-mode");
	const isLightModeEnabled = document.body.classList.contains('light-mode');
	if (isLightModeEnabled) {
		localStorage.setItem("lightMode", "enabled");
	} else {
		localStorage.setItem("lightMode", "disabled");
	}
}

const icon = document.getElementById('togglePassword');
let password = document.getElementById('password');

icon.addEventListener('click', function() {
  if(password.type === "password") {
    password.type = "text";
		icon.classList.add("fa-eye");
    icon.classList.remove("fa-eye-slash");
  }
  else {
    password.type = "password";
    icon.classList.add("fa-eye-slash");
    icon.classList.remove("fa-eye");
  }
});

