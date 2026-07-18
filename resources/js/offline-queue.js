const DB_NAME = 'nitido-offline';
const DB_VERSION = 1;
const STORE = 'tareas-pendientes';

function abrirDb() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        request.onupgradeneeded = () => {
            request.result.createObjectStore(STORE, { keyPath: 'id' });
        };
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

async function encolar(registro) {
    const db = await abrirDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE, 'readwrite');
        tx.objectStore(STORE).put(registro);
        tx.oncomplete = () => resolve();
        tx.onerror = () => reject(tx.error);
    });
}

async function obtenerPendientes() {
    const db = await abrirDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE, 'readonly');
        const peticion = tx.objectStore(STORE).getAll();
        peticion.onsuccess = () => resolve(peticion.result);
        peticion.onerror = () => reject(peticion.error);
    });
}

async function eliminar(id) {
    const db = await abrirDb();
    return new Promise((resolve, reject) => {
        const tx = db.transaction(STORE, 'readwrite');
        tx.objectStore(STORE).delete(id);
        tx.oncomplete = () => resolve();
        tx.onerror = () => reject(tx.error);
    });
}

async function enviar(registro) {
    const formData = new FormData();
    formData.append('_token', registro.token);
    formData.append('_method', 'PATCH');
    if (registro.ocurridoEn) formData.append('ocurrido_en', registro.ocurridoEn);
    if (registro.nota) formData.append('nota', registro.nota);
    if (registro.foto) formData.append('foto_evidencia', registro.foto, registro.fotoNombre || 'evidencia.jpg');
    if (registro.cantidades) {
        Object.entries(registro.cantidades).forEach(([clave, valor]) => formData.append(clave, valor));
    }

    const respuesta = await fetch(registro.url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    });

    if (! respuesta.ok) {
        throw new Error(`Fallo al sincronizar tarea ${registro.tareaId}: ${respuesta.status}`);
    }
}

export async function encolarMarcarHecha(datos) {
    await encolar({ id: `${datos.tareaId}-${Date.now()}`, creadoEn: Date.now(), ...datos });
}

export async function hayPendientePara(tareaId) {
    const pendientes = await obtenerPendientes();
    return pendientes.some((p) => String(p.tareaId) === String(tareaId));
}

export async function sincronizarPendientes() {
    if (! navigator.onLine) return;

    const pendientes = await obtenerPendientes();
    if (! pendientes.length) return;

    let sincronizadaAlguna = false;

    for (const registro of pendientes) {
        try {
            await enviar(registro);
            await eliminar(registro.id);
            sincronizadaAlguna = true;
        } catch (error) {
            break; // seguimos sin buena conexión; se reintentará en el próximo evento "online"
        }
    }

    if (sincronizadaAlguna && document.querySelector('[data-marcar-hecha], [data-tarea-pendiente]')) {
        window.Turbo ? window.Turbo.visit(location.href, { action: 'replace' }) : location.reload();
    }
}

export function initOfflineSync() {
    sincronizarPendientes();
    window.addEventListener('online', sincronizarPendientes);
}
