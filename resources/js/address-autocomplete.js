const DEBOUNCE_MS = 350;
const MIN_CHARS = 3;

// Detecta "<calle>, <número>" mientras se escribe, para buscar solo por la calle
// y añadir el número tecleado, ya que Photon/OSM no siempre indexa el portal exacto.
const PATRON_NUMERO = /^(.*),\s*(\d+\s*\w*)$/;

function formatearResultado(feature, numeroForzado) {
    const p = feature.properties;
    const numero = numeroForzado ?? p.housenumber;
    // Si forzamos el número, el nombre de la calle puede venir en "name" (resultados tipo "street").
    const nombreCalle = p.street ?? (numeroForzado ? p.name : null);
    const calle = [nombreCalle, numero].filter(Boolean).join(', ');
    const nombrePoi = ! numeroForzado && ! p.street && p.name ? p.name : null;

    return [nombrePoi, calle, p.postcode, p.city, p.state].filter(Boolean).join(', ');
}

async function buscarDirecciones(query, signal) {
    const coincidencia = query.match(PATRON_NUMERO);
    const consulta = coincidencia ? coincidencia[1].trim() : query;
    const numeroForzado = coincidencia ? coincidencia[2].trim() : null;

    const url = `https://photon.komoot.io/api/?q=${encodeURIComponent(consulta)}&limit=5`;

    try {
        const respuesta = await fetch(url, { signal });
        if (! respuesta.ok) return [];

        const datos = await respuesta.json();
        const soloCalles = (datos.features || []).filter((f) => f.properties.osm_key !== 'place' || f.properties.type === 'street');
        const features = numeroForzado && soloCalles.length ? soloCalles : (datos.features || []);

        return [...new Set(features.map((f) => formatearResultado(f, numeroForzado)).filter(Boolean))];
    } catch (error) {
        return [];
    }
}

export function initAddressAutocomplete() {
    let debounceTimer = null;
    let controller = null;
    let dropdown = null;
    let inputActivo = null;

    function cerrarDropdown() {
        dropdown?.remove();
        dropdown = null;
        inputActivo = null;
        document.removeEventListener('click', onDocumentClick);
    }

    function onDocumentClick(event) {
        if (inputActivo && ! inputActivo.parentElement.contains(event.target)) {
            cerrarDropdown();
        }
    }

    function mostrarResultados(input, direcciones) {
        cerrarDropdown();
        if (! direcciones.length) return;

        dropdown = document.createElement('div');
        dropdown.className = 'address-autocomplete-list';

        direcciones.forEach((direccion) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'address-autocomplete-item';
            item.textContent = direccion;
            item.addEventListener('click', () => {
                input.value = direccion;
                cerrarDropdown();
                input.focus();
            });
            dropdown.appendChild(item);
        });

        input.parentElement.appendChild(dropdown);
        inputActivo = input;
        document.addEventListener('click', onDocumentClick);
    }

    document.addEventListener('input', (event) => {
        const input = event.target;
        if (! input.matches?.('[data-address-autocomplete]')) return;

        clearTimeout(debounceTimer);
        const valor = input.value.trim();

        if (valor.length < MIN_CHARS) {
            cerrarDropdown();
            return;
        }

        debounceTimer = setTimeout(() => {
            controller?.abort();
            controller = new AbortController();

            buscarDirecciones(valor, controller.signal).then((direcciones) => {
                mostrarResultados(input, direcciones);
            });
        }, DEBOUNCE_MS);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') cerrarDropdown();
    });
}
