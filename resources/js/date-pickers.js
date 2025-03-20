import flatpickr from "flatpickr";
import 'flatpickr/dist/l10n/es';

flatpickr('#fecha_inicio', {
    locale: 'es',
    minDate: 'today'
});
flatpickr('#fecha_fin', {
    locale: 'es',
    minDate: 'today',
});
flatpickr('.hora-inicio', {
    locale: 'es',
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: false
});
flatpickr('.hora-fin', {
    locale: 'es',
    enableTime: true,
    noCalendar: true,
    dateFormat: "H:i",
    time_24hr: false
});
