<?php

declare(strict_types=1);

return [
    // Success Messages
    'successful_create' => 'El Desafio se ha creado correctamente.',
    'successful_update' => 'El Desafio se ha actualizado correctamente.',
    'successful_delete' => 'El Desafio se ha eliminado correctamente.',

    // Error Messages
    'error_create' => 'Ha ocurrido un error al intentar crear el Desafio, verifique que sus datos sean correctos y vuelva a intentarlo. Si el problema persiste comuníqueselo a su administrador.',
    'error_update' => 'Ha ocurrido un error al intentar actualizar el Desafio, verifique que sus datos sean correctos y vuelva a intentarlo. Si el problema persiste comuníqueselo a su administrador.',
    'error_delete' => 'Ha ocurrido un error al intentar eliminar el Desafio, verifique que sus datos sean correctos y vuelva a intentarlo. Si el problema persiste comuníqueselo a su administrador.',
    'error_not_found' => 'No se ha encontrado el Desafio solicitado en nuestros registros.',

    'error_profesor_materia_mismatch' => 'Error, el profesor no cuenta con la materia ingresada, por favor verifique sus datos y vuelva a intentarlo.',
    'error_start_date_less_than_today' => 'Error, la fecha de inicio del Desafio no puede ser menor que la de hoy.',
    'error_desafio_finished' => 'Error, el Desafio ya ha terminado. No se puede realizar operaciones con un desafio terminado.',

];
