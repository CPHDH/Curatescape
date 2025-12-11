document.addEventListener('DOMContentLoaded', function() { // deferred
	// FIX DOUBLE ACTIVE CLASS ON SECONDARY NAVIGATION
	let secondary_nav_actives = document.querySelectorAll(".active .curatescape_secondary-nav-fix-js");
	if (secondary_nav_actives.length) {
		if (secondary_nav_actives.length > 1) {
			secondary_nav_actives[0].parentElement.classList.remove("active");
		}
	}
});