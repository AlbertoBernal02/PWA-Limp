const CLAVE_DESCARTADO = 'nitido-instalar-descartado';

function yaInstalada() {
    return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
}

function esIos() {
    return /iphone|ipad|ipod/i.test(window.navigator.userAgent);
}

function crearBanner(mensaje, onInstalar) {
    const banner = document.createElement('div');
    banner.className = 'install-banner';

    const texto = document.createElement('p');
    texto.className = 'install-banner-text';
    texto.textContent = mensaje;
    banner.appendChild(texto);

    const acciones = document.createElement('div');
    acciones.className = 'install-banner-actions';

    if (onInstalar) {
        const btnInstalar = document.createElement('button');
        btnInstalar.type = 'button';
        btnInstalar.className = 'btn-primary';
        btnInstalar.textContent = 'Instalar';
        btnInstalar.addEventListener('click', onInstalar);
        acciones.appendChild(btnInstalar);
    }

    const btnCerrar = document.createElement('button');
    btnCerrar.type = 'button';
    btnCerrar.className = 'install-banner-close';
    btnCerrar.setAttribute('aria-label', 'Cerrar aviso de instalación');
    btnCerrar.textContent = '✕';
    btnCerrar.addEventListener('click', () => {
        localStorage.setItem(CLAVE_DESCARTADO, 'true');
        banner.remove();
    });
    acciones.appendChild(btnCerrar);

    banner.appendChild(acciones);
    document.body.appendChild(banner);

    return banner;
}

export function initInstallPrompt() {
    if (yaInstalada() || localStorage.getItem(CLAVE_DESCARTADO) === 'true') return;

    let eventoDiferido = null;

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        eventoDiferido = event;

        const banner = crearBanner(
            'Instala Nítido en tu móvil para acceder más rápido, incluso sin cobertura.',
            async () => {
                eventoDiferido.prompt();
                const resultado = await eventoDiferido.userChoice;
                if (resultado.outcome === 'accepted') {
                    localStorage.setItem(CLAVE_DESCARTADO, 'true');
                }
                banner.remove();
            }
        );
    });

    if (esIos()) {
        crearBanner('Para instalar Nítido: pulsa el botón Compartir y luego "Añadir a pantalla de inicio".', null);
    }

    window.addEventListener('appinstalled', () => {
        localStorage.setItem(CLAVE_DESCARTADO, 'true');
        document.querySelector('.install-banner')?.remove();
    });
}
