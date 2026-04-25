document.addEventListener("DOMContentLoaded", function() {
    // Mostrar el contenido inmediatamente
    document.body.style.visibility = 'visible';

    // Verificar si la página tiene la clase 'no-scroll-reveal' en el body
    const noScrollReveal = document.body.classList.contains('no-scroll-reveal');

    if (noScrollReveal) {
        // Si la página tiene la clase 'no-scroll-reveal', añadir la clase 'visible' para activar las animaciones de entrada
        const elements = document.querySelectorAll('.reveal, .reveal-down, .reveal-static');
        elements.forEach(el => {
            el.classList.add('visible');
        });
    } else {
        // Configurar el observador para las animaciones de scroll
        const revealers = document.querySelectorAll('.reveal, .reveal-down');
        const options = {
            threshold: 0.1,
            rootMargin: '0px 0px -10% 0px'
        };

        const observer = new IntersectionObserver(function(entries, observer) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, options);

        // Iniciar observación de elementos
        revealers.forEach(el => observer.observe(el));
    }

    // Mostrar elementos con retraso para permitir que se apliquen los estilos
    setTimeout(() => {
        document.body.style.opacity = '1';
        
        // Si es una página sin scroll reveal, asegurarse de que todo esté visible
        if (noScrollReveal) {
            document.body.classList.add('all-visible');
        }
    }, 100);
});