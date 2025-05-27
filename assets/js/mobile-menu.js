document.addEventListener('DOMContentLoaded', function() {
    // Loader de page
    const loadingScreen = document.querySelector('.loading-screen');
    if (loadingScreen) {
        window.addEventListener('load', function() {
            setTimeout(function() {
                loadingScreen.classList.add('hidden');
                setTimeout(function() {
                    loadingScreen.style.display = 'none';
                }, 500);
            }, 300);
        });
    }

    // Éléments du menu mobile
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    const mobileNavItems = document.querySelectorAll('.mobile-nav ul li');
    
    // Ajouter des index aux éléments pour l'animation en cascade
    mobileNavItems.forEach((item, index) => {
        item.style.setProperty('--item-index', index);
    });
    
    if (mobileMenuToggle && mobileNav) {
        // Événement de clic sur le hamburger
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            mobileMenuToggle.classList.toggle('active');
            mobileNav.classList.toggle('active');
            
            // Empêcher le défilement du body lorsque le menu est ouvert
            document.body.classList.toggle('menu-open');
        });
        
        // Fermer le menu au clic sur un lien (sauf lien admin dropdown)
        document.querySelectorAll('.mobile-nav a:not(.admin-dropdown-toggle)').forEach(link => {
            link.addEventListener('click', function() {
                mobileMenuToggle.classList.remove('active');
                mobileNav.classList.remove('active');
                document.body.classList.remove('menu-open');
            });
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
            e.stopPropagation();
            mobileAdminMenu.classList.toggle('active');
        });
    }
    
    // Synchroniser le thème entre les toggles desktop et mobile
    const themeToggle = document.getElementById('theme-toggle');
    const mobileThemeToggle = document.getElementById('mobile-theme-toggle');
    
    if (themeToggle && mobileThemeToggle) {
        // Synchroniser les changements du toggle mobile vers desktop
        mobileThemeToggle.addEventListener('click', function() {
            themeToggle.click();
        });
    }
    
    // Double-tap pour éviter les délais sur mobile
    const touchElements = document.querySelectorAll('.mobile-nav a, .mobile-nav button');
    
    touchElements.forEach(el => {
        el.addEventListener('touchstart', function() {
            this.classList.add('touch-active');
        }, {passive: true});
        
        el.addEventListener('touchend', function() {
            this.classList.remove('touch-active');
        }, {passive: true});
    });
});
