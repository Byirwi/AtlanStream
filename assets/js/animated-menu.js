/**
 * Script pour animer le menu desktop d'AtlanStream
 */
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du menu admin déroulant
    const adminToggle = document.querySelector('.desktop-menu .admin-dropdown-toggle');
    const adminMenu = document.querySelector('.desktop-menu .admin-dropdown');
    
    if (adminToggle && adminMenu) {
        // Effet au survol
        adminToggle.addEventListener('mouseenter', function() {
            adminMenu.classList.add('active');
        });
        
        // Gestion du menu déroulant pour admin
        const adminDropdown = document.querySelector('.desktop-menu .admin-dropdown');
        
        // Fermer le menu quand on quitte la zone
        const handleMouseLeave = function() {
            adminMenu.classList.remove('active');
        };
        
        adminToggle.addEventListener('mouseleave', function(e) {
            // Ne pas fermer si on va vers le menu déroulant
            const toElement = e.relatedTarget;
            if (!adminDropdown.contains(toElement)) {
                setTimeout(handleMouseLeave, 100);
            }
        });
        
        adminDropdown.addEventListener('mouseleave', handleMouseLeave);
        
        // Empêcher la fermeture si on survole le menu déroulant
        adminDropdown.addEventListener('mouseenter', function() {
            clearTimeout(handleMouseLeave);
        });
        
        // Clic alternatif pour les mobiles
        adminToggle.addEventListener('click', function(e) {
            e.preventDefault();
            adminMenu.classList.toggle('active');
        });
    }
    
    // Ajouter la classe active au lien courant
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.desktop-menu li a');
    
    navLinks.forEach(link => {
        const linkHref = link.getAttribute('href');
        if (linkHref && linkHref.includes(currentPage)) {
            link.classList.add('active');
        }
    });
    
    // Effet de particules pour le logo (facultatif, peut être lourd)
    const logo = document.querySelector('.logo');
    if (logo) {
        logo.addEventListener('mouseenter', function() {
            const logoRect = logo.getBoundingClientRect();
            const centerX = logoRect.left + logoRect.width / 2;
            const centerY = logoRect.top + logoRect.height / 2;
            
            // Créer et animer des particules (version simple)
            for (let i = 0; i < 5; i++) {
                const particle = document.createElement('div');
                particle.classList.add('logo-particle');
                particle.style.position = 'absolute';
                particle.style.width = '8px';
                particle.style.height = '8px';
                particle.style.backgroundColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color');
                particle.style.borderRadius = '50%';
                particle.style.zIndex = '100';
                particle.style.opacity = '0.7';
                particle.style.left = `${centerX}px`;
                particle.style.top = `${centerY}px`;
                particle.style.pointerEvents = 'none';
                
                document.body.appendChild(particle);
                
                // Animer la particule
                const angle = Math.random() * Math.PI * 2;
                const distance = 20 + Math.random() * 30;
                const destX = centerX + Math.cos(angle) * distance;
                const destY = centerY + Math.sin(angle) * distance;
                
                particle.animate([
                    { transform: 'scale(1)', opacity: 0.7 },
                    { transform: 'scale(0)', opacity: 0, left: `${destX}px`, top: `${destY}px` }
                ], {
                    duration: 800 + Math.random() * 600,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                    fill: 'forwards'
                }).onfinish = function() {
                    particle.remove();
                };
            }
        });
    }
});
