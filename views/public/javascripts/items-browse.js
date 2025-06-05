document.addEventListener('DOMContentLoaded', function() {
	// FIX DOUBLE ACTIVE CLASS ON ITEM NAVIGATION
	let secondary_nav_actives = document.querySelectorAll(".active .curatescape_js_fix");
	if (secondary_nav_actives.length) {
		if (secondary_nav_actives.length > 1) {
			secondary_nav_actives[0].parentElement.classList.remove("active");
		}
	}
});