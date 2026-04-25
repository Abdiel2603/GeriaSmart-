document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-desvincular-action');
    const idCuidador = new URLSearchParams(window.location.search).get('id_usr');
    const modalElement = document.getElementById('desvincularModal');
    const confirmButton = document.getElementById('confirmarDesvinculacionBtn');
    let idPacienteSeleccionado = null;

    if (!modalElement || !confirmButton) {
        return;
    }

    const desvincularModal = new bootstrap.Modal(modalElement);

    buttons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            idPacienteSeleccionado = this.getAttribute('data-id');
            desvincularModal.show();
        });
    });

    confirmButton.addEventListener('click', function () {
        if (!idPacienteSeleccionado || !idCuidador) {
            return;
        }

        fetch('../../lib/desvincular_paciente.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_paciente=${encodeURIComponent(idPacienteSeleccionado)}&id_cuidador=${encodeURIComponent(idCuidador)}`
        })
            .then(response => response.json())
            .then(data => {
                desvincularModal.hide();
                if (data.success) {
                    window.location.href = `pacientes_vinculados.php?id_usr=${encodeURIComponent(idCuidador)}&msg=${encodeURIComponent('Paciente desvinculado exitosamente')}`;
                } else {
                    window.location.href = `pacientes_vinculados.php?id_usr=${encodeURIComponent(idCuidador)}&error=${encodeURIComponent(data.message || 'No se pudo desvincular al paciente.')}`;
                }
            })
            .catch(() => {
                desvincularModal.hide();
                window.location.href = `pacientes_vinculados.php?id_usr=${encodeURIComponent(idCuidador)}&error=${encodeURIComponent('Error de red al desvincular al paciente.')}`;
            });
    });
});
