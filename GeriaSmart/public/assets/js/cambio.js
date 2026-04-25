const enlaces = document.querySelectorAll('a');

enlaces.forEach(enlace => {
    enlace.addEventListener('click', (event) => {
        event.preventDefault();
        const url = enlace.href;

        document.body.classList.add('fade-out'); // clase que anima salida

        setTimeout(() => {
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.body.innerHTML = html;
                    document.body.classList.remove('fade-out');
                    document.body.classList.add('fade-in');  // clase que anima entrada
                });
        }, 500); // tiempo de animación de salida
    });
});
