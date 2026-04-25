document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.section-politica');
    const navLinks = document.querySelectorAll('.sidebar-nav a');

    window.addEventListener('scroll', function() {
        let current = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;

            if (pageYOffset >= (sectionTop - 200)) {
                current = '#' + section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === current) {
                link.classList.add('active');
            }
        });
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                navLinks.forEach(link => link.classList.remove('active'));
                this.classList.add('active');

                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });

                const sidebarCheckbox = document.getElementById('sidebar-toggle');
                if (window.innerWidth <= 992) {
                    sidebarCheckbox.checked = false;
                }
            }
        });
    });

    window.dispatchEvent(new Event('scroll'));
});