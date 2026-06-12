// Raven Travel - Enhanced Modern JavaScript

// ===== MOBILE MENU TOGGLE =====
const menuBtn = document.getElementById("menu-btn");
const navLinks = document.getElementById("nav-links");

if (menuBtn && navLinks) {
  menuBtn.addEventListener("click", () => {
    navLinks.classList.toggle("open");
    const icon = menuBtn.querySelector("i");
    icon.className = navLinks.classList.contains("open") 
      ? "ri-close-line" 
      : "ri-menu-line";
  });

  // Close menu when clicking on a link
  navLinks.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", () => {
      navLinks.classList.remove("open");
      menuBtn.querySelector("i").className = "ri-menu-line";
    });
  });
}

// ===== NAVBAR SCROLL EFFECT =====
const navbar = document.querySelector(".navbar-elegant");
let lastScroll = 0;

window.addEventListener("scroll", () => {
  const currentScroll = window.pageYOffset;
  
  if (currentScroll > 100) {
    navbar?.classList.add("scrolled");
  } else {
    navbar?.classList.remove("scrolled");
  }
  
  // Hide/show navbar on scroll
  if (currentScroll > lastScroll && currentScroll > 500) {
    if (navbar) navbar.style.transform = "translateY(-100%)";
  } else {
    if (navbar) navbar.style.transform = "translateY(0)";
  }
  
  lastScroll = currentScroll;
});

// ===== ACTIVE NAV LINK =====
const sections = document.querySelectorAll("section[id]");
const navLinksItems = document.querySelectorAll(".nav__links a");

function setActiveNavLink() {
  const scrollY = window.pageYOffset;
  
  sections.forEach(section => {
    const sectionHeight = section.offsetHeight;
    const sectionTop = section.offsetTop - 100;
    const sectionId = section.getAttribute("id");
    
    if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
      navLinksItems.forEach(link => {
        link.classList.remove("active");
        if (link.getAttribute("href")?.includes(`#${sectionId}`)) {
          link.classList.add("active");
        }
      });
    }
  });
}

window.addEventListener("scroll", setActiveNavLink);

// ===== SMOOTH SCROLL =====
document.querySelectorAll('a[href*="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    const href = this.getAttribute('href');
    const hashIndex = href.indexOf('#');
    if (hashIndex === -1) return;
    
    const hash = href.substring(hashIndex);
    if (hash === '#' || hash === '') return;
    
    const target = document.querySelector(hash);
    if (target) {
      e.preventDefault();
      const navHeight = navbar?.offsetHeight || 80;
      const targetPosition = target.offsetTop - navHeight;
      
      window.scrollTo({
        top: targetPosition,
        behavior: 'smooth'
      });
    }
  });
});

// ===== COUNTER ANIMATION =====
function animateCounter(element, target, duration) {
  duration = duration || 2000;
  let current = 0;
  const increment = target / (duration / 16);
  const suffix = element.textContent.replace(/[0-9.]/g, '');
  
  const updateCounter = () => {
    current += increment;
    if (current < target) {
      element.textContent = Math.floor(current).toLocaleString('id-ID') + suffix;
      requestAnimationFrame(updateCounter);
    } else {
      element.textContent = target.toLocaleString('id-ID') + suffix;
    }
  };
  
  updateCounter();
}

// Intersection Observer for Counter Animation
const statsSection = document.querySelector('.hero__stats');
if (statsSection) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const counters = entry.target.querySelectorAll('.counter');
        counters.forEach(counter => {
          const target = parseInt(counter.getAttribute('data-target'));
          if (!isNaN(target)) {
            animateCounter(counter, target);
          }
        });
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });
  
  observer.observe(statsSection);
}

// ===== USER MENU DROPDOWN =====
const userMenuBtn = document.getElementById('userMenuBtn');
const userDropdown = document.getElementById('userDropdown');

if (userMenuBtn && userDropdown) {
  userMenuBtn.addEventListener('click', function(e) {
    e.stopPropagation();
    userDropdown.classList.toggle('show');
  });
  
  document.addEventListener('click', function(e) {
    if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
      userDropdown.classList.remove('show');
    }
  });
}

// ===== VIDEO MODAL =====
const playBtns = document.querySelectorAll('.btn-play');
const videoModal = document.getElementById('videoModal');
const videoFrame = document.getElementById('videoFrame');
const closeVideo = document.getElementById('closeVideo');

playBtns.forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const videoUrl = this.dataset.video || this.getAttribute('data-video');
    if (videoUrl && videoFrame && videoModal) {
      videoFrame.src = videoUrl + '?autoplay=1';
      videoModal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  });
});

if (closeVideo) {
  closeVideo.addEventListener('click', function() {
    closeVideoModal();
  });
}

if (videoModal) {
  videoModal.addEventListener('click', function(e) {
    if (e.target === videoModal) {
      closeVideoModal();
    }
  });
}

function closeVideoModal() {
  if (!videoModal || !videoFrame) return;
  videoModal.classList.remove('show');
  videoFrame.src = '';
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && videoModal && videoModal.classList.contains('show')) {
    closeVideoModal();
  }
});

// ===== NEWSLETTER FORM =====
const newsletterForm = document.getElementById('newsletterForm');
if (newsletterForm) {
  newsletterForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = this.querySelector('input[type="email"]').value;
    const btn = this.querySelector('button');
    const originalHTML = btn.innerHTML;
    const msgEl = document.getElementById('newsletterMessage');
    
    if (!isValidEmail(email)) {
      showNotification('Email tidak valid!', 'error');
      return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line spin"></i> Subscribing...';
    if (msgEl) { msgEl.style.display = 'none'; }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content 
      || this.querySelector('input[name="_token"]')?.value;

    fetch(this.action, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      body: JSON.stringify({ email: email }),
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
      if (ok) {
        showNotification(data.message, 'success');
        newsletterForm.reset();
      } else {
        const msg = data.message || data.errors?.email?.[0] || 'Terjadi kesalahan.';
        showNotification(msg, 'error');
      }
    })
    .catch(() => {
      showNotification('Tidak dapat terhubung ke server.', 'error');
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = originalHTML;
    });
  });
}

// ===== BACK TO TOP =====
const backToTop = document.getElementById('backToTop');
window.addEventListener('scroll', () => {
  if (window.scrollY > 500) {
    backToTop?.classList.add('show');
  } else {
    backToTop?.classList.remove('show');
  }
});

if (backToTop) {
  backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

// ===== SCROLL PROGRESS =====
function updateScrollProgress() {
  const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
  const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
  const scrolled = (winScroll / height) * 100;
  
  let progressBar = document.querySelector('.scroll-progress');
  if (!progressBar) {
    progressBar = document.createElement('div');
    progressBar.className = 'scroll-progress';
    document.body.appendChild(progressBar);
  }
  progressBar.style.width = scrolled + '%';
}

window.addEventListener('scroll', updateScrollProgress);

// ===== LAZY LOAD IMAGES =====
const images = document.querySelectorAll('img[data-src]');
const imageObserver = new IntersectionObserver((entries, observer) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      const img = entry.target;
      img.src = img.dataset.src;
      img.removeAttribute('data-src');
      observer.unobserve(img);
    }
  });
});

images.forEach(img => imageObserver.observe(img));

// ===== UTILITY FUNCTIONS =====
function showNotification(message, type) {
  type = type || 'info';
  const existing = document.querySelector('.custom-notification');
  if (existing) existing.remove();
  
  const notification = document.createElement('div');
  notification.className = 'custom-notification ' + type;
  
  const icons = {
    success: 'ri-checkbox-circle-line',
    error: 'ri-error-warning-line',
    warning: 'ri-alert-line',
    info: 'ri-information-line'
  };
  
  notification.innerHTML = 
    '<i class="' + (icons[type] || icons.info) + '"></i>' +
    '<span>' + message + '</span>' +
    '<button onclick="this.parentElement.remove()"><i class="ri-close-line"></i></button>';
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.classList.add('fade-out');
    setTimeout(() => notification.remove(), 300);
  }, 5000);
}

function showLoading() {
  const loader = document.createElement('div');
  loader.className = 'loading-overlay';
  loader.innerHTML = '<div class="spinner"><div></div><div></div><div></div><div></div></div>';
  document.body.appendChild(loader);
  document.body.style.overflow = 'hidden';
}

function hideLoading() {
  const loader = document.querySelector('.loading-overlay');
  if (loader) {
    loader.remove();
    document.body.style.overflow = '';
  }
}

function isValidEmail(email) {
  var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount);
}

function formatDate(dateString) {
  var options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
}

// ===== PARALLAX EFFECT =====
window.addEventListener('scroll', () => {
  const scrolled = window.pageYOffset;
  const parallaxElements = document.querySelectorAll('[data-parallax]');
  
  parallaxElements.forEach(element => {
    const speed = element.dataset.parallax || 0.5;
    element.style.transform = 'translateY(' + (scrolled * speed) + 'px)';
  });
});

// ===== DATA-REVEAL ANIMATION =====
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('revealed');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.15 });

document.querySelectorAll('[data-reveal]').forEach(el => {
  revealObserver.observe(el);
});

// ===== EXPORT FOR GLOBAL USE =====
window.RavenTravel = {
  showNotification: showNotification,
  showLoading: showLoading,
  hideLoading: hideLoading,
  formatCurrency: formatCurrency,
  formatDate: formatDate,
  isValidEmail: isValidEmail,
  animateCounter: animateCounter
};

// ===== DYNAMIC STYLES =====
var customStyles = document.createElement('style');
customStyles.textContent = [
  '.custom-notification {',
  '  position: fixed; top: 100px; right: 20px; background: white;',
  '  padding: 1rem 1.5rem; border-radius: 10px;',
  '  box-shadow: 0 10px 30px rgba(0,0,0,0.2);',
  '  display: flex; align-items: center; gap: 1rem;',
  '  z-index: 9999; animation: slideInRight 0.3s ease;',
  '  max-width: 400px; border-left: 4px solid;',
  '}',
  '.custom-notification.success { border-color: #10b981; }',
  '.custom-notification.error { border-color: #ef4444; }',
  '.custom-notification.warning { border-color: #f59e0b; }',
  '.custom-notification.info { border-color: #3b82f6; }',
  '.custom-notification i:first-child { font-size: 1.5rem; }',
  '.custom-notification.success i:first-child { color: #10b981; }',
  '.custom-notification.error i:first-child { color: #ef4444; }',
  '.custom-notification.warning i:first-child { color: #f59e0b; }',
  '.custom-notification.info i:first-child { color: #3b82f6; }',
  '.custom-notification button {',
  '  background: none; border: none; cursor: pointer;',
  '  padding: 0; font-size: 1.2rem; color: #666; margin-left: auto;',
  '}',
  '.custom-notification button:hover { color: #000; }',
  '.custom-notification.fade-out { animation: slideOutRight 0.3s ease forwards; }',
  '@keyframes slideInRight {',
  '  from { transform: translateX(400px); opacity: 0; }',
  '  to { transform: translateX(0); opacity: 1; }',
  '}',
  '@keyframes slideOutRight {',
  '  from { transform: translateX(0); opacity: 1; }',
  '  to { transform: translateX(400px); opacity: 0; }',
  '}',
  '.loading-overlay {',
  '  position: fixed; inset: 0; background: rgba(0,0,0,0.8);',
  '  display: flex; align-items: center; justify-content: center; z-index: 99999;',
  '}',
  '.spinner { display: inline-block; position: relative; width: 80px; height: 80px; }',
  '.spinner div {',
  '  position: absolute; width: 16px; height: 16px; border-radius: 50%;',
  '  background: #d4af37; animation: spinner 1.2s linear infinite;',
  '}',
  '.spinner div:nth-child(1) { top: 8px; left: 8px; animation-delay: 0s; }',
  '.spinner div:nth-child(2) { top: 8px; left: 32px; animation-delay: -0.4s; }',
  '.spinner div:nth-child(3) { top: 8px; left: 56px; animation-delay: -0.8s; }',
  '.spinner div:nth-child(4) { top: 32px; left: 8px; animation-delay: -0.4s; }',
  '@keyframes spinner { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }',
  '.spin { animation: spin 1s linear infinite; }',
  '@keyframes spin { to { transform: rotate(360deg); } }',
].join('\n');
document.head.appendChild(customStyles);

// ===== CONSOLE LOG =====
console.log('%c🚌 Raven Travel ', 'color: #d4af37; font-size: 24px; font-weight: bold;');
console.log('%cYour Journey, Our Priority', 'color: #1a1a2e; font-size: 14px; font-style: italic;');
console.log('%cWebsite loaded successfully ✓', 'color: #10b981; font-size: 12px;');

// ===== DATA-REVEAL SCROLL ANIMATIONS =====
(function() {
  var revealElements = document.querySelectorAll('[data-reveal]');
  if (revealElements.length > 0) {
    var revealObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (!entry.isIntersecting) return;
        var delay = entry.target.getAttribute('data-reveal-delay');
        if (delay) {
          entry.target.style.setProperty('--reveal-delay', delay + 'ms');
        }
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
      });
    }, { threshold: 0.18 });

    revealElements.forEach(function(element) {
      revealObserver.observe(element);
    });
  }
})();

// ===== AOS (Animate On Scroll) INITIALIZATION =====
document.addEventListener('DOMContentLoaded', function() {
  // Initialize AOS
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 1000,
      once: true,
      offset: 100
    });
  }
  
  // Initialize Swiper for testimonials
  if (typeof Swiper !== 'undefined') {
    const swiperElement = document.querySelector('.testimonials-swiper');
    if (swiperElement) {
      new Swiper('.testimonials-swiper', {
        loop: false,
        centeredSlides: true,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false,
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        breakpoints: {
          640: { slidesPerView: 1, spaceBetween: 20 },
          768: { slidesPerView: 2, spaceBetween: 30 },
          1024: { slidesPerView: 3, spaceBetween: 30 },
        }
      });
    }
  }
});
