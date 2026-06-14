/**
 * Paw Heaven - Main JavaScript
 * Handles all interactivity for the adoption platform
 */

document.addEventListener('DOMContentLoaded', function() {
  // Check login status for favorites
  const isLoggedIn = document.body.classList.contains('logged-in');

  // ===== Navbar Scroll Effect =====
  const navbar = document.getElementById('navbar');
  
  function handleScroll() {
    if (window.scrollY > 20) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  }
  
  window.addEventListener('scroll', handleScroll);
  handleScroll(); // Initial check

  // ===== Mobile Menu Toggle =====
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const navLinks = document.getElementById('navLinks');
  
  if (mobileMenuBtn && navLinks) {
    mobileMenuBtn.addEventListener('click', function() {
      navLinks.classList.toggle('mobile-open');
      
      // Toggle icon between menu and X
      const icon = mobileMenuBtn.querySelector('svg');
      if (navLinks.classList.contains('mobile-open')) {
        icon.innerHTML = `
          <line x1="18" x2="6" y1="6" y2="18"/>
          <line x1="6" x2="18" y1="6" y2="18"/>
        `;
      } else {
        icon.innerHTML = `
          <line x1="4" x2="20" y1="12" y2="12"/>
          <line x1="4" x2="20" y1="6" y2="6"/>
          <line x1="4" x2="20" y1="18" y2="18"/>
        `;
      }
    });
  }

  // ===== Favorite Button Toggle (AJAX) =====
  const favoriteButtons = document.querySelectorAll('.favorite-btn');
  
  favoriteButtons.forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      if (!isLoggedIn) {
        showToast('Please login to save favorites', 'error');
        return;
      }
      
      const petId = this.dataset.petId;
      const svg = this.querySelector('svg');
      
      // Optimistic UI update
      const currentlyActive = this.classList.contains('active');
      
      // Make AJAX call
      fetch('favorite-handler.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=toggle&pet_id=${petId}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          if (data.favorited) {
            this.classList.add('active');
            svg.setAttribute('fill', 'currentColor');
            showToast('Added to favorites!');
          } else {
            this.classList.remove('active');
            svg.setAttribute('fill', 'none');
            showToast('Removed from favorites');
          }
        } else {
          showToast(data.error || 'Failed to update favorites', 'error');
          // Revert UI if failed
          if (currentlyActive) {
            this.classList.add('active');
          } else {
            this.classList.remove('active');
          }
        }
      })
      .catch(() => {
        showToast('Failed to update favorites', 'error');
      });
    });
  });

  // ===== Toast Notification System =====
  function showToast(message, type = 'success') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.toast');
    existingToasts.forEach(t => t.remove());
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 0.875rem 1.25rem;
      border-radius: var(--radius);
      font-weight: 500;
      font-size: 0.875rem;
      z-index: 1000;
      transform: translateX(400px);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      max-width: 350px;
      backdrop-filter: blur(10px);
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
      toast.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto-remove after 4 seconds
    setTimeout(() => {
      toast.style.transform = 'translateX(400px)';
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  }

  // ===== Smooth Scroll for Anchor Links =====
  const anchorLinks = document.querySelectorAll('a[href^="#"]');
  
  anchorLinks.forEach(function(link) {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href !== '#') {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      }
    });
  });

  // ===== Intersection Observer for Animations =====
  const animatedElements = document.querySelectorAll('[data-animate]');
  
  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-in');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });
  
  animatedElements.forEach(function(el, index) {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    el.style.transitionDelay = (index * 0.1) + 's';
    observer.observe(el);
  });

  // ===== Form Validation & Loading States =====
  const forms = document.querySelectorAll('form');
  
  forms.forEach(function(form) {
    form.addEventListener('submit', function(e) {
      const submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn && !form.classList.contains('no-loader')) {
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        setTimeout(() => {
          submitBtn.classList.remove('loading');
          submitBtn.disabled = false;
        }, 3000);
      }
    });
  });

  // ===== Pet Card Click Handler =====
  const petCards = document.querySelectorAll('.pet-card');
  
  petCards.forEach(function(card) {
    card.addEventListener('click', function(e) {
      if (e.target.closest('.favorite-btn')) {
        return;
      }
      
      this.style.transform = 'scale(0.98)';
      setTimeout(() => {
        this.style.transform = '';
      }, 150);
      
      const link = card.querySelector('a.btn');
      if (link) {
        window.location.href = link.href;
      }
    });
    
    card.style.cursor = 'pointer';
  });

  // ===== Dynamic Stats Counter =====
  const counters = document.querySelectorAll('.counter');
  
  counters.forEach(counter => {
    const target = parseInt(counter.textContent);
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        counter.textContent = target;
        clearInterval(timer);
      } else {
        counter.textContent = Math.floor(current);
      }
    }, 30);
  });

  // ===== Auto-hide server-generated toasts =====
  setTimeout(() => {
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toast => {
      toast.style.transform = 'translateX(400px)';
      setTimeout(() => toast.remove(), 300);
    });
  }, 5000);

  console.log('🐾 Paw Heaven loaded successfully!');
});
