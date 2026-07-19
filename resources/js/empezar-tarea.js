import { obtenerUbicacion } from './geolocation';

export function initEmpezarTarea() {
    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (! form.matches?.('[data-empezar-tarea]')) return;

        event.preventDefault();

        const ubicacion = await obtenerUbicacion();
        const datos = new FormData(form);
        if (ubicacion) {
            datos.set('lat', ubicacion.lat);
            datos.set('lng', ubicacion.lng);
        }

        try {
            await fetch(form.action, {
                method: 'POST',
                body: datos,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
        } finally {
            window.Turbo ? window.Turbo.visit(location.href, { action: 'replace' }) : location.reload();
        }
    });
}
