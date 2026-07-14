document.addEventListener('DOMContentLoaded', function() {
	let btns = document.querySelectorAll('a.details-link');
	btns.forEach((b)=>{
		b.addEventListener('click',(a)=>{
			a.preventDefault();
			let details = b.closest('tr').querySelector('.details');
			let hidden = details.classList.toggle('hidden');
			b.setAttribute('aria-expanded', hidden ? 'false' : 'true');
		});
	});
});
