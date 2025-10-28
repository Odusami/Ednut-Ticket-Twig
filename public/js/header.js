document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const nav = document.getElementById('main-nav');
    let isMobileMenuOpen = false;

    // Toggle mobile menu
    mobileMenuToggle.addEventListener('click', function() {
        isMobileMenuOpen = !isMobileMenuOpen;
        if (isMobileMenuOpen) {
            nav.classList.add('nav--open');
        } else {
            nav.classList.remove('nav--open');
        }
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!nav.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
            nav.classList.remove('nav--open');
            isMobileMenuOpen = false;
        }
    });

    // Close menu when a nav link is clicked
    const navLinks = nav.querySelectorAll('.nav-link, .btn');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            nav.classList.remove('nav--open');
            isMobileMenuOpen = false;
        });
    });
});