
    function applyToMainContainers(fn) {
    document.querySelectorAll('.container:not(.navbar-container)').forEach(fn);
}

    function resetAnimationClasses() {
    applyToMainContainers(container => {
        container.classList.remove('fade-out');
        // Por si necesitas reiniciar entrada
        container.classList.remove('fade-in');
    });
}

    // Animaciones al navegar normalmente
    document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function(e) {
        if (
            link.host === location.host &&
            link.target !== "_blank" &&
            !link.classList.contains("btn-login") &&
            !link.classList.contains("btn-register")
        ) {
            e.preventDefault();
            applyToMainContainers(container => {
                container.classList.add("fade-out");
            });
            setTimeout(() => {
                window.location = link.href;
            }, 400);
        }
    });
});

    // Animación de entrada NORMAL al cargar el DOM por primera vez
    window.addEventListener("DOMContentLoaded", function() {
    applyToMainContainers(container => {
        container.classList.add("fade-in");
        setTimeout(function() {
            container.classList.remove("fade-in");
        }, 400);
    });
});

    // Solución para cargar correctamente con el botón volver del navegador
    window.addEventListener('pageshow', function(event){
    // Si viene del historial, reinicia clases y animación para mostrar el contenido
    resetAnimationClasses();
    applyToMainContainers(container => {
    container.style.opacity = "1"; // Para mostrar inmediatamente
});
});
