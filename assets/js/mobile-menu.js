document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner les éléments du menu mobile
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    
    if (mobileMenuToggle && mobileNav) {
        // Événement de clic sur le hamburger
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenuToggle.classList.toggle('active');
            mobileNav.classList.toggle('active');
            
            // Empêcher le défilement du body lorsque le menu est ouvert
            document.body.classList.toggle('menu-open');
        });
        
        // Fermer le menu au clic sur un lien
        const mobileNavLinks = mobileNav.querySelectorAll('a');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Ne pas fermer si c'est un lien déroulant admin
                if (!this.classList.contains('admin-dropdown-toggle')) {
                    mobileMenuToggle.classList.remove('active');
                    mobileNav.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            });
        });
    }
    
    // Gérer le menu déroulant admin dans la navigation mobile
    const mobileAdminToggle = document.querySelector('.mobile-nav .admin-dropdown-toggle');
    const mobileAdminMenu = document.querySelector('.mobile-nav .admin-dropdown');
    
    if (mobileAdminToggle && mobileAdminMenu) {
        mobileAdminToggle.addEventListener('click', function(e) {
            e.preventDefault();
            mobileAdminMenu.classList.toggle('active');
        });
    }
});
