// Al elegir un tipo de trabajo en el formulario de tarea, marca (sin desmarcar
// las que ya estuvieran) las herramientas típicas de ese trabajo. Es solo una
// sugerencia: el admin puede añadir o quitar libremente después.
export function initTrabajoSugerencias() {
    document.addEventListener('change', (event) => {
        const select = event.target.closest('[data-trabajo-select]');
        if (! select || ! select.value) return;

        let mapa;
        try {
            mapa = JSON.parse(select.dataset.trabajoSelect);
        } catch (error) {
            return;
        }

        const herramientaIds = mapa[select.value] || [];
        const form = select.closest('form');

        herramientaIds.forEach((id) => {
            const checkbox = form?.querySelector(`input[name="herramienta_ids[]"][value="${id}"]`);
            if (checkbox) checkbox.checked = true;
        });
    });
}
