/**
 * Pide la posición actual del navegador, con límite de tiempo. Nunca lanza ni
 * bloquea: si se deniega el permiso, no hay GPS o tarda demasiado, resuelve a
 * null y quien la llame sigue adelante sin la ubicación (es una prueba extra,
 * no un requisito para poder fichar).
 */
export function obtenerUbicacion() {
    return new Promise((resolve) => {
        if (! ('geolocation' in navigator)) {
            resolve(null);
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (posicion) => resolve({ lat: posicion.coords.latitude, lng: posicion.coords.longitude }),
            () => resolve(null),
            { enableHighAccuracy: true, timeout: 6000, maximumAge: 30000 }
        );
    });
}
