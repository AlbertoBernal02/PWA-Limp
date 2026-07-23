const CLAVE_HISTORIAL = 'chat-ia-historial';

function leerHistorial() {
    try {
        return JSON.parse(sessionStorage.getItem(CLAVE_HISTORIAL) || '[]');
    } catch (error) {
        return [];
    }
}

function guardarHistorial(historial) {
    sessionStorage.setItem(CLAVE_HISTORIAL, JSON.stringify(historial));
}

export function initChatFlotante() {
    // Al cerrar sesión hay que vaciar el chat: si no, la siguiente cuenta que
    // entre en el mismo navegador vería (y seguiría) la conversación de la
    // cuenta anterior, porque sessionStorage no sabe de usuarios, solo de pestañas.
    document.addEventListener('submit', (event) => {
        if (! event.target.matches?.('[data-logout-form]')) return;

        sessionStorage.removeItem(CLAVE_HISTORIAL);
    });

    window.chatFlotante = () => ({
        // "abierto" NO se guarda en sessionStorage a propósito: cada página
        // (Turbo recrea este componente en cada navegación) empieza siempre
        // cerrado, así nunca se abre solo al cambiar de sección. Cerrarlo
        // (por el botón flotante o por la X del panel) nunca borra nada,
        // porque lo único que persiste es la conversación (historial).
        abierto: false,
        mensaje: '',
        cargando: false,
        historial: leerHistorial(),

        alternar() {
            this.abierto = ! this.abierto;
            if (this.abierto) this.$nextTick(() => this.scrollAbajo());
        },

        vaciar() {
            this.historial = [];
            guardarHistorial(this.historial);
        },

        async enviar() {
            const texto = this.mensaje.trim();
            if (! texto || this.cargando) return;

            this.historial.push({ id: Date.now(), rol: 'usuario', texto });
            guardarHistorial(this.historial);
            this.mensaje = '';
            this.cargando = true;
            this.$nextTick(() => this.scrollAbajo());

            const historialParaApi = this.historial
                .slice(-12)
                .map((t) => ({ rol: t.rol, texto: t.texto }));

            try {
                const respuesta = await fetch('/documentacion/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ mensaje: texto, historial: historialParaApi }),
                });

                if (! respuesta.ok) throw new Error('http-' + respuesta.status);

                const datos = await respuesta.json();
                this.historial.push({ id: Date.now() + 1, rol: 'modelo', texto: datos.respuesta });
            } catch (error) {
                this.historial.push({
                    id: Date.now() + 2,
                    rol: 'modelo',
                    texto: 'No se pudo contactar con el asistente. Comprueba tu conexión e inténtalo de nuevo.',
                });
            } finally {
                guardarHistorial(this.historial);
                this.cargando = false;
                this.$nextTick(() => this.scrollAbajo());
            }
        },

        scrollAbajo() {
            this.$refs.historialEl?.scrollTo({ top: this.$refs.historialEl.scrollHeight });
        },
    });
}
