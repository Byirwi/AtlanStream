document.addEventListener('DOMContentLoaded', function() {
    // Récupérer le bouton de changement de thème
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    
    // Vérifier si une préférence de thème est déjà stockée
    const currentTheme = localStorage.getItem('theme') || 'dark';
    
    // Appliquer le thème au chargement de la page
    document.body.classList.add(currentTheme);
    updateThemeIcon(currentTheme);
    
    // Ajouter un événement de clic au bouton
    themeToggle.addEventListener('click', function() {
        // Vérifier le thème actuel et basculer
        if (document.body.classList.contains('dark')) {
            document.body.classList.replace('dark', 'light');
            localStorage.setItem('theme', 'light');
            updateThemeIcon('light');
        } else {
            document.body.classList.replace('light', 'dark');
            localStorage.setItem('theme', 'dark');
            updateThemeIcon('dark');
        }
    });
    
    // Mettre à jour l'icône du bouton en fonction du thème
    function updateThemeIcon(theme) {
        if (theme === 'light') {
            themeIcon.innerHTML = '🌙'; // Icône de lune pour basculer vers le thème sombre
            themeToggle.setAttribute('title', 'Passer au thème sombre');
        } else {
            themeIcon.innerHTML = '☀️'; // Icône de soleil pour basculer vers le thème clair
            themeToggle.setAttribute('title', 'Passer au thème clair');
        }
    }
});
