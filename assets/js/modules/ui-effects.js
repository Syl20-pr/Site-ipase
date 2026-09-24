// ===== ui-effects.js — Effets d'interface : survol, accessibilité clavier, animations au scroll =====
function initHoverAndAccessibilityEvents() {

    // A. Événement 'keydown' (Accessibilité : touche Échap)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('quick-modal');
            if (modal && modal.classList.contains('active')) {
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            const nav = document.querySelector('.nav');
            if (nav && nav.classList.contains('open')) {
                nav.classList.remove('open');
            }
        }
    });

    // B. CSS :hover gère les animations des cartes (pas besoin de JS supplémentaire)
    // Les mouseenter/mouseleave sont gérés uniquement via CSS pour éviter les conflits de transform.

    // C. Événement 'resize' (Réinitialisation responsive)
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            const nav = document.querySelector('.nav');
            if (nav) nav.classList.remove('open');
        }
    });
}

/* ── 7. Animation des Éléments au Défilement (IntersectionObserver API) ── */
function initScrollAnimations() {
    if (!('IntersectionObserver' in window)) return;

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.15
    };

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('scroll-animated');
                obs.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const animatedElements = document.querySelectorAll('.feature-card, .filiere-card-preview, .news-item-card, .testimonial-card, .mv-card');
    animatedElements.forEach(el => observer.observe(el));
}
