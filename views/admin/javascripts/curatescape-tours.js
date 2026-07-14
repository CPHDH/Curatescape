document.addEventListener('DOMContentLoaded', function() {
	let btns = document.querySelectorAll('a.details-link');
	btns.forEach((b)=>{
		b.addEventListener('click',(a)=>{
			a.preventDefault();
			let details = a.target.closest('tr').querySelector('.details');
			details.classList.toggle('hidden')
		});
	});
});
