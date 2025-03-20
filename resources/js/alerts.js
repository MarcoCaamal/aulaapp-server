import 'sweetalert2/dist/sweetalert2.min.css';
import Swal from "sweetalert2";


document.addEventListener('DOMContentLoaded', () => {
    iniciarApp();
});

function iniciarApp() {
    iniciarAlertas();
}

/**
 * Método para mostrar las alertas para la acción de eliminar registros.
 */
function iniciarAlertas() {
    const formsEliminar = document.querySelectorAll('.formEliminar');

    formsEliminar.forEach(form => {
        const urlAPI = form.querySelector('.urlAPI').value;

        form.addEventListener('submit', e => {
            e.preventDefault();

            Swal.fire({
                title: '¿Desea eliminar este registro?',
                text: 'Esta acción no se podra revertir',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '¡Si, eliminar!',
                cancelButtonText: '¡No, cancelar!',
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    const response = await fetch(urlAPI, {
                        method: 'delete',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    try {
                        const resultado = await response.json();

                        if (resultado.success) {
                            Swal.fire({
                                icon: 'success',
                                title: resultado.message,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                        else {
                            Swal.fire({
                                icon: 'info',
                                title: resultado.message,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            title: "¡Algo salió mal!",
                            text: "¡Ha ocurrido un error al eliminar el registro, si el problema continua comunicate con el administrador!",
                            icon: "error",
                            allowOutsideClick: false,
                            confirmButtonColor: '#0c7cd5',
                            allowEscapeKey: false
                        })
                        .then(() => {
                            location.reload();
                        });
                    }
                }
            });
        });
    });
}
