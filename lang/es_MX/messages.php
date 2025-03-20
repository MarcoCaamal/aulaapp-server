<?php

declare(strict_types=1);

return [
    // Success messages
    'successful_creation' => 'Se ha creado correctamente :name.',
    'successful_update' => 'Se ha actualizado correctamente :name.',
    'successful_deletion' => 'Se ha eliminado correctamente :name.',

    // Error messages
    "failed_creation" => 'Ha ocurrido un error al intentar crear :name, por favor revise sus datos y vuelva intentarlo más tarde.',
    'failed_update' => 'Ha ocurrido un error al intentar actualizar :name, por favor revise sus datos y vuelva intentarlo más tarde.',
    'failed_deletion' => 'Ha ocurrido un error al intentar eliminar :name, por favor revise sus datos y vuelva intentarlo más tarde.',
    'date_less_than_today' => 'El campo :attribute no puede ser menor a la fecha actual.',

    // Warning messages
    'not_found' => 'No se ha encontrado :name en nuestros registros, por favor revise sus datos.',

    /** Ciclos messages **/
    'ciclos' => [
        'activate' => 'El ciclo se ha activado con exito.',
        'desactivate' => 'El ciclo se ha desactivado con exito.',

        'error_activate' => 'Ha ocurrido un error inesperado al intentar activar el ciclo.',
        'error_desactivate' => 'Ha ocurrido un error inesperado al intentar desactivar el ciclo.',
        'error_already_exists_active_ciclo' => 'Ha ocurrido un error, ya existe un ciclo activo, desactive el ciclo activo y vuelva a intentarlo.',

        'not_active_ciclo' => 'No hay un ciclo activo actualmente, por favor active un ciclo y vuelva a intentarlo.',
        'not_delete_active_ciclo' => 'No se puede eliminar un ciclo que este activo.',
    ],

    /** User messages **/
    'users' => [
        'grupo_already_used' => 'El alumno ya ha estado en ese grupo antes, por favor elija otro y vuelva a intentarlo.',
        'already_exists' => 'El :user con el nombre :name ya existe, por favor elija otro nombre y vuelva a intentarlo.',
        'profesor_materias_empty' => 'El profesor no tiene materias asignadas, por favor configure las materias del profesor y vuela a intentarlo.',
        'alumno_asesoria_already_confirmed' => 'El alumno ya tiene confirmada esa Asesoría.',
        'alumno_without_group' => 'El alumno no tiene un grupo asignado, por favor asegurese que el alumno tenga un grupo asignado y vuelva a intentarlo.',
    ],

    'alumnos' => [
        'no_asesoria_confirmed' => 'No se encontro su asistencia a la asesoria, por favor confirme que los datos sean correctos y vuelva a intentarlo.'
    ],

    /** Horarios messages */
    'horarios' => [
        'conflic_horario' => 'El horario que intento ingresar tiene conflictos con otro horario ya existente.',
    ],

    /** Asesorias messages **/
    'asesorias' => [
        'no_weekends' => 'El campo :attribute no acepta fechas que sus días sean fines de semana.',
        'mismatched_days' => 'El día de la semana de la asesoria no coincide con la fecha ingresada',
        'already_confirmed' => 'Ya tiene confirmada la Asesoria seleccionada.',
        'asistencia_cancelled_successful' => 'La asistencia a la asesoria se ha cancelado correctamente.',
        'asistencia_cancelled_failed' => 'Ha ocurrido un error al cancelar la asistencia a la asesoria, revise que los datos sean correctos y vuelva a intentarlo.',
        'already_cancelled' => 'La asesoria ya estaba cancelada anteriormente.',
        'successful_cancelled' => 'La asesoria se cancelo correctamente.',
        'error_cancelled' => 'Ha ocurrido un error al intentar cancelar la asesoria, revise sus datos y vuelva a intentarlo.',
    ],
];
