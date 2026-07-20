function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

async function agregarAHueco(huecoId, tipo, id) {
    const endpoint = tipo === 'trabajo'
        ? `/admin/almacen/huecos/${huecoId}/trabajos`
        : `/admin/almacen/huecos/${huecoId}/herramientas`;

    const cuerpo = tipo === 'trabajo' ? { trabajo_id: id } : { herramienta_id: id };

    await fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify(cuerpo),
    });

    window.Turbo ? window.Turbo.visit(location.href, { action: 'replace' }) : location.reload();
}

// Arrastrar y soltar de tarjeta de herramienta o trabajo a hueco: solo tiene sentido
// en escritorio (los paneles de tarjetas ya están ocultos en móvil vía CSS), pero
// los listeners son inofensivos si no hay tarjetas que arrastrar.
export function initAlmacenDrag() {
    document.addEventListener('dragstart', (event) => {
        const tarjeta = event.target.closest('[data-herramienta-id], [data-trabajo-id]');
        if (! tarjeta) return;

        const tipo = tarjeta.dataset.trabajoId ? 'trabajo' : 'herramienta';
        const id = tarjeta.dataset.trabajoId || tarjeta.dataset.herramientaId;

        event.dataTransfer.setData('text/plain', `${tipo}:${id}`);
        event.dataTransfer.effectAllowed = 'copy';
    });

    document.addEventListener('dragover', (event) => {
        const hueco = event.target.closest('[data-hueco-drop]');
        if (! hueco) return;

        event.preventDefault();
        hueco.classList.add('ring-2', 'ring-brand-500');
    });

    document.addEventListener('dragleave', (event) => {
        const hueco = event.target.closest('[data-hueco-drop]');
        if (! hueco) return;

        hueco.classList.remove('ring-2', 'ring-brand-500');
    });

    document.addEventListener('drop', (event) => {
        const hueco = event.target.closest('[data-hueco-drop]');
        if (! hueco) return;

        event.preventDefault();
        hueco.classList.remove('ring-2', 'ring-brand-500');

        const [tipo, id] = (event.dataTransfer.getData('text/plain') || '').split(':');
        if (tipo && id) agregarAHueco(hueco.dataset.huecoDrop, tipo, id);
    });
}
