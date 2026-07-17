const LONG_PRESS_MS = 550;

export function initContextMenus() {
    let menuEl = null;
    let suppressNextClick = false;

    function closeMenu() {
        if (!menuEl) return;
        menuEl.remove();
        menuEl = null;
        document.removeEventListener('click', closeMenu);
        document.removeEventListener('scroll', closeMenu, true);
        document.removeEventListener('keydown', onKeydown);
    }

    function onKeydown(event) {
        if (event.key === 'Escape') closeMenu();
    }

    function openMenu(actionsCell, x, y) {
        closeMenu();

        const acciones = actionsCell.querySelectorAll('a, button');
        if (! acciones.length) return;

        menuEl = document.createElement('div');
        menuEl.className = 'context-menu';

        acciones.forEach((original) => {
            const label = original.textContent.trim();
            if (! label) return;

            const esPeligrosa = original.classList.contains('text-red-600');
            const item = document.createElement('button');
            item.type = 'button';
            item.className = esPeligrosa ? 'context-menu-item-danger' : 'context-menu-item';
            item.textContent = label;
            item.addEventListener('click', () => {
                closeMenu();
                original.click();
            });
            menuEl.appendChild(item);
        });

        document.body.appendChild(menuEl);

        const rect = menuEl.getBoundingClientRect();
        const maxX = window.innerWidth - rect.width - 8;
        const maxY = window.innerHeight - rect.height - 8;
        menuEl.style.left = `${Math.max(8, Math.min(x, maxX))}px`;
        menuEl.style.top = `${Math.max(8, Math.min(y, maxY))}px`;

        setTimeout(() => {
            document.addEventListener('click', closeMenu);
            document.addEventListener('scroll', closeMenu, true);
            document.addEventListener('keydown', onKeydown);
        }, 0);
    }

    // Clic derecho (escritorio).
    document.addEventListener('contextmenu', (event) => {
        const fila = event.target.closest('tr');
        const acciones = fila?.querySelector('.row-actions');
        if (! acciones) return;

        event.preventDefault();
        openMenu(acciones, event.clientX, event.clientY);
    });

    // Pulsación larga (móvil/táctil).
    document.addEventListener('touchstart', (event) => {
        const fila = event.target.closest('tr');
        const acciones = fila?.querySelector('.row-actions');
        if (! acciones) return;

        const touch = event.touches[0];
        const { clientX, clientY } = touch;
        let cancelado = false;

        const cancelar = () => { cancelado = true; };
        fila.addEventListener('touchmove', cancelar, { once: true });

        const temporizador = setTimeout(() => {
            if (cancelado) return;
            suppressNextClick = true;
            openMenu(acciones, clientX, clientY);
            setTimeout(() => { suppressNextClick = false; }, 350);
        }, LONG_PRESS_MS);

        const limpiar = () => {
            clearTimeout(temporizador);
            fila.removeEventListener('touchmove', cancelar);
        };
        fila.addEventListener('touchend', limpiar, { once: true });
        fila.addEventListener('touchcancel', limpiar, { once: true });
    }, { passive: true });

    // Evita que el "click" fantasma tras la pulsación larga navegue de golpe.
    document.addEventListener('click', (event) => {
        if (! suppressNextClick) return;
        event.preventDefault();
        event.stopPropagation();
    }, true);
}
