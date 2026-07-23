<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Documentación</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-6">
            <nav class="space-y-1">
                @foreach ($indice as $slug => $titulo)
                    <a href="{{ route('documentacion.index', $slug) }}"
                       class="block px-3 py-2 rounded-md text-sm {{ $slug === $activa ? 'bg-brand-50 dark:bg-brand-500/15 text-brand-700 dark:text-brand-400 font-medium' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                        {{ $titulo }}
                    </a>
                @endforeach
            </nav>

            <div class="space-y-6">
                <article class="card p-6 prose-doc">
                    {!! $html !!}
                </article>

                <p class="text-sm text-slate-500 dark:text-slate-400">
                    ¿Tienes dudas? Usa el botón de chat de abajo a la derecha para preguntarle al asistente.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
