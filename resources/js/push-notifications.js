function base64UrlToUint8Array(base64Url) {
    const padding = '='.repeat((4 - (base64Url.length % 4)) % 4);
    const base64 = (base64Url + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = window.atob(base64);

    return Uint8Array.from([...raw].map((char) => char.charCodeAt(0)));
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

async function enviarSuscripcion(suscripcion) {
    const clave = suscripcion.getKey('p256dh');
    const auth = suscripcion.getKey('auth');

    await fetch('/push-subscriptions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        body: JSON.stringify({
            endpoint: suscripcion.endpoint,
            public_key: clave ? btoa(String.fromCharCode(...new Uint8Array(clave))) : null,
            auth_token: auth ? btoa(String.fromCharCode(...new Uint8Array(auth))) : null,
            content_encoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0],
        }),
    });
}

async function borrarSuscripcion(endpoint) {
    await fetch('/push-subscriptions', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        body: JSON.stringify({ endpoint }),
    });
}

function actualizarBoton(boton, activo) {
    boton.dataset.activo = activo ? '1' : '0';
    boton.title = activo ? 'Avisos activados (clic para desactivar)' : 'Activar avisos de tareas del día';
    boton.classList.toggle('text-brand-600', activo);
    boton.classList.toggle('dark:text-brand-400', activo);
}

async function refrescarEstadoBoton() {
    const botones = document.querySelectorAll('[data-push-toggle]');
    if (! botones.length) return;

    if (! ('serviceWorker' in navigator) || ! ('PushManager' in window)) {
        botones.forEach((boton) => boton.remove());
        return;
    }

    const registro = await navigator.serviceWorker.ready;
    const suscripcion = await registro.pushManager.getSubscription();
    botones.forEach((boton) => actualizarBoton(boton, !! suscripcion));
}

async function alternarSuscripcion() {
    const registro = await navigator.serviceWorker.ready;
    const suscripcion = await registro.pushManager.getSubscription();

    if (suscripcion) {
        await suscripcion.unsubscribe();
        await borrarSuscripcion(suscripcion.endpoint);
        await refrescarEstadoBoton();
        return;
    }

    const permiso = await Notification.requestPermission();
    if (permiso !== 'granted') {
        return;
    }

    const clavePublica = document.querySelector('meta[name="vapid-public-key"]')?.content;
    const nuevaSuscripcion = await registro.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: base64UrlToUint8Array(clavePublica),
    });

    await enviarSuscripcion(nuevaSuscripcion);
    await refrescarEstadoBoton();
}

// Delegado en document (no en el botón): tras cada navegación Turbo el nav se
// reemplaza y el botón es un nodo nuevo, así que un listener atado directamente
// a él se perdería.
export function initPushNotifications() {
    if (! ('serviceWorker' in navigator) || ! ('PushManager' in window)) return;

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-push-toggle]')) alternarSuscripcion();
    });

    document.addEventListener('turbo:load', refrescarEstadoBoton);
    refrescarEstadoBoton();
}
