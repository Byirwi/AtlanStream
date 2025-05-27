document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner les éléments du menu mobile
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    const mobileNavItems = document.querySelectorAll('.mobile-nav ul li');
    
    // Ajouter des index aux éléments pour l'animation en cascade
    mobileNavItems.forEach((item, index) => {
        item.style.setProperty('--item-index', index);
    });
    
    if (mobileMenuToggle && mobileNav) {
        // Événement de clic sur le hamburger
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenuToggle.classList.toggle('active');
            mobileNav.classList.toggle('active');
            
            // Empêcher le défilement du body lorsque le menu est ouvert
            document.body.classList.toggle('menu-open');
        });
        
        // Fermer le menu au clic sur un lien (sauf lien admin dropdown)
        mobileNavItems.forEach(item => {
            const link = item.querySelector('a:not(.admin-dropdown-toggle)');
            if (link) {
                link.addEventListener('click', function() {
                    mobileMenuToggle.classList.remove('active');
                    mobileNav.classList.remove('active');
                    document.body.classList.remove('menu-open');
                });
            }
        });
        
        // Fermer le menu au clic en dehors
        document.addEventListener('click', function(e) {
            if (mobileNav.classList.contains('active') && 
                !e.target.closest('.mobile-nav') && 
                !e.target.closest('.mobile-menu-toggle')) {
                mobileMenuToggle.classList.remove('active');
                mobileNav.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
        
        // Fermer le menu avec la touche Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
                mobileMenuToggle.classList.remove('active');
                mobileNav.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
    }
    
    // Gérer le menu déroulant admin dans la navigation mobile
    const mobileAdminToggle = document.querySelector('.mobile-nav .admin-dropdown-toggle');
    const mobileAdminMenu = document.querySelector('.mobile-nav .admin-dropdown');
    
    if (mobileAdminToggle && mobileAdminMenu) {
        mobileAdminToggle.addEventListener('click', function(e) {
            e.preventDefault();
            mobileAdminMenu.classList.toggle('active');
            
            // Scroll vers l'élément pour s'assurer qu'il est visible
            if (mobileAdminMenu.classList.contains('active')) {
                setTimeout(() => {
                    mobileAdminMenu.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 300);
            }
        });
    }
    
    // Synchroniser le thème entre les toggles desktop et mobile
    const themeToggle = document.getElementById('theme-toggle');
    const mobileThemeToggle = document.getElementById('mobile-theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const mobileThemeIcon = document.getElementById('mobile-theme-icon');
    
    if (themeToggle && mobileThemeToggle) {
        // Synchroniser les états initiaux
        mobileThemeIcon.innerHTML = themeIcon.innerHTML;
        
        // Synchroniser le changement de thème depuis le toggle mobile
        mobileThemeToggle.addEventListener('click', function() {
            themeToggle.click(); // Déclencher le clic sur le toggle desktop
            mobileThemeIcon.innerHTML = themeIcon.innerHTML; // Synchroniser l'icône
        });
        
        // Mettre à jour l'icône mobile quand le thème change
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'innerHTML') {
                    mobileThemeIcon.innerHTML = themeIcon.innerHTML;
                }
            });
        });
        
        observer.observe(themeIcon, { attributes: true, childList: true });
    }
});
