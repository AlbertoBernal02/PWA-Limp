import { encolarMarcarHecha, hayPendientePara } from './offline-queue';
import { obtenerUbicacion } from './geolocation';

function marcarComoPendienteEnPantalla(form) {
    const contenedor = form.parentElement;
    const aviso = document.createElement('p');
    aviso.className = 'text-xs text-amber-600 dark:text-amber-400 text-right';
    aviso.dataset.tareaPendiente = form.dataset.tareaId;
    aviso.textContent = 'Guardada sin conexión. Se enviará sola cuando vuelva la cobertura.';

    form.replaceWith(aviso);
}

export function initMarcarHechaOffline() {
    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (! form.matches?.('[data-marcar-hecha]')) return;

        event.preventDefault();

        const ubicacion = await obtenerUbicacion();

        if (navigator.onLine) {
            try {
                const datosOnline = new FormData(form);
                if (ubicacion) {
                    datosOnline.set('lat', ubicacion.lat);
                    datosOnline.set('lng', ubicacion.lng);
                }

                // Sin "ocurrido_en": confiamos en la hora del servidor, más fiable que
                // el reloj del dispositivo cuando la petición llega al instante.
                const respuesta = await fetch(form.action, {
                    method: 'POST',
                    body: datosOnline,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                // Éxito o error de validación: en ambos casos reflejamos el estado real del servidor.
                window.Turbo ? window.Turbo.visit(location.href, { action: 'replace' }) : location.reload();
                return;
            } catch (error) {
                // Fallo de red real aunque el navegador se creía "online": seguimos abajo y encolamos.
            }
        }

        // Solo cuando se encola (sin conexión) mandamos la hora del dispositivo,
        // para no perder de vista cuándo ocurrió realmente aunque se sincronice más tarde.
        const ocurridoEn = new Date().toISOString();

        const datos = new FormData(form);
        const foto = datos.get('foto_evidencia');

        // Los inputs de cantidad se llaman "cantidades[<id_herramienta>]"; los
        // guardamos tal cual para reenviarlos igual cuando se sincronice.
        const cantidades = {};
        for (const [clave, valor] of datos.entries()) {
            if (clave.startsWith('cantidades[')) {
                cantidades[clave] = valor;
            }
        }

        await encolarMarcarHecha({
            tareaId: form.dataset.tareaId,
            url: form.action,
            token: datos.get('_token'),
            ocurridoEn,
            nota: datos.get('nota') || null,
            foto: foto && foto.size ? foto : null,
            fotoNombre: foto?.name,
            cantidades,
            lat: ubicacion?.lat ?? null,
            lng: ubicacion?.lng ?? null,
        });

        marcarComoPendienteEnPantalla(form);
    });

    document.addEventListener('turbo:load', () => {
        document.querySelectorAll('[data-marcar-hecha]').forEach(async (form) => {
            if (await hayPendientePara(form.dataset.tareaId)) {
                marcarComoPendienteEnPantalla(form);
            }
        });
    });
}
