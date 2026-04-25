document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmpassword');
    const roleSelect = document.querySelector('select[name="tipo_usuario"]');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Función para validar el correo electrónico
    function validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    // Función para validar el teléfono (mínimo 10 dígitos)
    function validarTelefono(telefono) {
        const re = /^[0-9]{10,}$/;
        return re.test(telefono);
    }

    // Función para validar la contraseña (mínimo 8 caracteres, 1 mayúscula, 1 número)
    function validarPassword(password) {
        const re = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
        return re.test(password);
    }

    // Función para mostrar mensajes de error
    function mostrarError(input, mensaje) {
        const formField = input.closest('.form-field');
        let error = formField.querySelector('.error-message');
        
        if (!error) {
            error = document.createElement('div');
            error.className = 'error-message text-danger mt-1';
            formField.appendChild(error);
        }
        
        error.textContent = mensaje;
        input.classList.add('is-invalid');
    }

    // Función para limpiar errores
    function limpiarError(input) {
        const formField = input.closest('.form-field');
        const error = formField.querySelector('.error-message');
        if (error) {
            error.remove();
        }
        input.classList.remove('is-invalid');
    }

    // Validación en tiempo real
    nameInput.addEventListener('input', () => {
        if (nameInput.value.trim() === '') {
            mostrarError(nameInput, 'El nombre es requerido');
        } else {
            limpiarError(nameInput);
        }
    });

    emailInput.addEventListener('input', () => {
        if (!validarEmail(emailInput.value)) {
            mostrarError(emailInput, 'Ingresa un correo electrónico válido');
        } else {
            limpiarError(emailInput);
        }
    });

    phoneInput.addEventListener('input', () => {
        if (!validarTelefono(phoneInput.value)) {
            mostrarError(phoneInput, 'Ingresa un número de teléfono válido (mínimo 10 dígitos)');
        } else {
            limpiarError(phoneInput);
        }
    });

    passwordInput.addEventListener('input', () => {
        if (!validarPassword(passwordInput.value)) {
            mostrarError(passwordInput, 'La contraseña debe tener al menos 8 caracteres, una mayúscula y un número');
        } else {
            limpiarError(passwordInput);
        }
    });

    confirmPasswordInput.addEventListener('input', () => {
        if (confirmPasswordInput.value !== passwordInput.value) {
            mostrarError(confirmPasswordInput, 'Las contraseñas no coinciden');
        } else {
            limpiarError(confirmPasswordInput);
        }
    });

    // Manejo del envío del formulario
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Validar todos los campos
        let esValido = true;
        
        if (nameInput.value.trim() === '') {
            mostrarError(nameInput, 'El nombre es requerido');
            esValido = false;
        }
        
        if (!validarEmail(emailInput.value)) {
            mostrarError(emailInput, 'Ingresa un correo electrónico válido');
            esValido = false;
        }
        
        if (!validarTelefono(phoneInput.value)) {
            mostrarError(phoneInput, 'Ingresa un número de teléfono válido (mínimo 10 dígitos)');
            esValido = false;
        }
        
        if (!validarPassword(passwordInput.value)) {
            mostrarError(passwordInput, 'La contraseña debe tener al menos 8 caracteres, una mayúscula y un número');
            esValido = false;
        }
        
        if (confirmPasswordInput.value !== passwordInput.value) {
            mostrarError(confirmPasswordInput, 'Las contraseñas no coinciden');
            esValido = false;
        }
        
        if (!esValido) {
            return;
        }
        
        // Si todo es válido, guardar el usuario
        const usuario = {
            nombre: nameInput.value.trim(),
            email: emailInput.value.trim(),
            telefono: phoneInput.value.trim(),
            password: passwordInput.value, // En una aplicación real, esto debería estar hasheado
            rol: roleSelect.value,
            fechaRegistro: new Date().toISOString()
        };
        
        // Obtener usuarios existentes o inicializar array vacío
        const usuarios = JSON.parse(localStorage.getItem('usuarios') || '[]');
        
        // Verificar si el correo ya está registrado
        const existeUsuario = usuarios.some(u => u.email === usuario.email);
        if (existeUsuario) {
            mostrarError(emailInput, 'Este correo ya está registrado');
            return;
        }
        
        // Agregar nuevo usuario
        usuarios.push(usuario);
        localStorage.setItem('usuarios', JSON.stringify(usuarios));
        
        // Mostrar mensaje de éxito
        alert('¡Registro exitoso! Serás redirigido al inicio de sesión.');
        
        // Redirigir al inicio de sesión después de 1 segundo
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 1000);
    });
});