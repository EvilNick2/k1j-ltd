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