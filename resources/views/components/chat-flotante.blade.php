<div
    x-data="chatFlotante()"
    class="fixed right-4 z-50 flex flex-col items-end bottom-[calc(4rem+env(safe-area-inset-bottom)+0.75rem)] sm:bottom-4"
>
    <div
        x-show="abierto"
        x-transition
        x-cloak
        class="mb-3 w-[calc(100vw-2rem)] sm:w-96 h-[28rem] max-h-[70vh] card flex flex-col overflow-hidden shadow-xl"
    >
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-700 shrink-0">
            <div>
                <h3 class="font-medium text-slate-800 dark:text-slate-100 text-sm">Asistente</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Documentación y datos de la app</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="vaciar" class="text-xs text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300" title="Vaciar conversación">
                    Vaciar
                </button>
                <button type="button" @click="abierto = false" class="text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300" aria-label="Cerrar chat">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto overscroll-contain p-3 space-y-2" x-ref="historialEl">
            <p x-show="historial.length === 0" class="text-sm text-slate-400 dark:text-slate-500 text-center mt-6">
                Pregúntame cómo hacer algo en la app, o sobre datos como el inventario o tus tareas.
            </p>
            <template x-for="turno in historial" :key="turno.id">
                <div :class="turno.rol === 'usuario' ? 'text-right' : 'text-left'">
                    <span class="inline-block px-3 py-2 rounded-lg text-sm max-w-[85%] whitespace-pre-line"
                          :class="turno.rol === 'usuario' ? 'bg-brand-600 text-white' : 'bg-slate-100 dark:bg-slate-700 dark:text-slate-100'"
                          x-text="turno.texto"></span>
                </div>
            </template>
            <p x-show="cargando" class="text-sm text-slate-400 dark:text-slate-500">Pensando…</p>
        </div>

        <form @submit.prevent="enviar" class="p-3 border-t border-slate-200 dark:border-slate-700 shrink-0">
            <div class="flex gap-2">
                <input type="text" x-model="mensaje" :disabled="cargando" maxlength="500"
                       class="field-input flex-1 text-sm" placeholder="Escribe tu pregunta...">
                <button type="submit" class="btn-primary" :disabled="cargando || ! mensaje.trim()">Enviar</button>
            </div>
        </form>
    </div>

    <button
        type="button"
        @click="alternar"
        class="w-14 h-14 rounded-full bg-brand-600 text-white shadow-lg flex items-center justify-center hover:bg-brand-700 transition"
        aria-label="Abrir chat del asistente"
    >
        <svg x-show="! abierto" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm3.75 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm3.75 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
        </svg>
        <svg x-show="abierto" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>
