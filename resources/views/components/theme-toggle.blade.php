<button
    type="button"
    role="switch"
    x-data="{ oscuro: document.documentElement.classList.contains('dark') }"
    @click="
        oscuro = !oscuro;
        document.documentElement.classList.toggle('dark', oscuro);
        localStorage.setItem('tema', oscuro ? 'oscuro' : 'claro');
    "
    :aria-checked="oscuro"
    :aria-label="oscuro ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
    :class="oscuro ? 'bg-brand-600' : 'bg-slate-200'"
    {{ $attributes->merge(['class' => 'relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800']) }}
>
    <span
        class="inline-flex h-4 w-4 transform items-center justify-center rounded-full bg-white shadow transition-transform duration-300"
        :class="oscuro ? 'translate-x-6' : 'translate-x-1'"
    >
        <svg x-show="!oscuro" class="w-2.5 h-2.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 9a1 1 0 100 2h1a1 1 0 100-2h-1zM4.464 4.343a1 1 0 010 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 0zm-.121 10.607a1 1 0 001.414 0l.707-.706a1 1 0 10-1.414-1.414l-.707.707a1 1 0 000 1.413zM3 9a1 1 0 100 2h1a1 1 0 100-2H3zm7 9a1 1 0 01-1-1v-1a1 1 0 112 0v1a1 1 0 01-1 1z" />
        </svg>
        <svg x-show="oscuro" class="w-2.5 h-2.5 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
    </span>
</button>
