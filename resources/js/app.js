import './bootstrap';

const revealElements = document.querySelectorAll('[data-reveal]');
if (revealElements.length > 0) {
	const revealObserver = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (!entry.isIntersecting) {
				return;
			}

			const delay = entry.target.getAttribute('data-reveal-delay');

			if (delay) {
				entry.target.style.setProperty('--reveal-delay', `${delay}ms`);
			}

			entry.target.classList.add('is-visible');
			revealObserver.unobserve(entry.target);
		});
	}, {
		threshold: 0.18,
	});

	revealElements.forEach((element) => {
		revealObserver.observe(element);
	});
}

const counterElements = document.querySelectorAll('.counter[data-target]');
if (counterElements.length > 0) {
	const counterObserver = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (!entry.isIntersecting) {
				return;
			}

			const counter = entry.target;
			const target = Number(counter.getAttribute('data-target'));
			const duration = 1400;
			const start = performance.now();

			const updateCounter = (timestamp) => {
				const progress = Math.min((timestamp - start) / duration, 1);
				counter.textContent = Math.floor(progress * target).toLocaleString('en-US');

				if (progress < 1) {
					requestAnimationFrame(updateCounter);
				}
			};

			requestAnimationFrame(updateCounter);
			counterObserver.unobserve(counter);
		});
	}, {
		threshold: 0.5,
	});

	counterElements.forEach((counter) => {
		counterObserver.observe(counter);
	});
}

const videoModal = document.querySelector('#videoModal');
const videoFrame = document.querySelector('#videoFrame');
const closeVideoButton = document.querySelector('#closeVideo');
const playButtons = document.querySelectorAll('[data-video]');

if (videoModal && videoFrame && playButtons.length > 0) {
	const closeVideoModal = () => {
		videoModal.classList.remove('is-open');
		videoModal.setAttribute('aria-hidden', 'true');
		videoFrame.src = '';
		document.body.style.overflow = '';
	};

	playButtons.forEach((button) => {
		button.addEventListener('click', () => {
			const videoUrl = button.getAttribute('data-video');

			if (!videoUrl) {
				return;
			}

			videoFrame.src = `${videoUrl}?autoplay=1&rel=0`;
			videoModal.classList.add('is-open');
			videoModal.setAttribute('aria-hidden', 'false');
			document.body.style.overflow = 'hidden';
		});
	});

	closeVideoButton?.addEventListener('click', closeVideoModal);
	videoModal.addEventListener('click', (event) => {
		if (event.target === videoModal) {
			closeVideoModal();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape' && videoModal.classList.contains('is-open')) {
			closeVideoModal();
		}
	});
}
