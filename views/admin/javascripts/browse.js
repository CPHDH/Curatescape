document.addEventListener('DOMContentLoaded', function() {
	let btns = document.querySelectorAll('a.details-link');
	btns.forEach((b)=>{
		b.addEventListener('click',(a)=>{
			let details = a.target.parentElement.lastElementChild;
			details.classList.toggle('hidden')
		});
	});
});