<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Editar operario</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('admin.operarios.update', $operario) }}" class="space-y-4" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="avatar" value="Foto de perfil" />
                        <div class="flex items-center gap-4 mt-1">
                            <x-avatar :src="$operario->avatarUrl()" :iniciales="$operario->iniciales()" class="w-14 h-14 text-lg" />
                            <input id="avatar" name="avatar" type="file" accept="image/*" class="field-input" />
                        </div>
                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" type="text" class="w-full" :value="old('name', $operario->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Correo electrónico" />
                        <x-text-input id="email" name="email" type="email" class="w-full" :value="old('email', $operario->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="telefono" value="Teléfono" />
                        <x-text-input id="telefono" name="telefono" type="tel" class="w-full" :value="old('telefono', $operario->telefono)" placeholder="Ej. 600123456" />
                        <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Nueva contraseña (opcional)" />
                        <x-text-input id="password" name="password" type="password" class="w-full" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end pt-2 gap-4">
                        <a href="{{ route('admin.operarios.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar cambios</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="card p-6 mt-6">
                <h3 class="font-semibold text-slate-800 dark:text-slate-100 mb-1">Vacaciones / ausencias</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                    Mientras un periodo esté activo, las tareas recurrentes que caigan en esas fechas se omitirán para este operario (sin necesidad de desactivarlo).
                </p>

                @if ($ausencias->isEmpty())
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Sin ausencias registradas.</p>
                @else
                    <ul class="space-y-2 mb-4">
                        @foreach ($ausencias as $ausencia)
                            <li class="flex justify-between items-center text-sm {{ now()->between($ausencia->fecha_inicio, $ausencia->fecha_fin) ? 'text-amber-700 dark:text-amber-400 font-medium' : 'text-slate-600 dark:text-slate-300' }}">
                                <span>
                                    {{ $ausencia->fecha_inicio->format('d/m/Y') }} — {{ $ausencia->fecha_fin->format('d/m/Y') }}
                                    @if ($ausencia->motivo)
                                        · {{ $ausencia->motivo }}
                                    @endif
                                    @if (now()->between($ausencia->fecha_inicio, $ausencia->fecha_fin))
                                        · en curso
                                    @endif
                                </span>
                                <form method="POST" action="{{ route('admin.operarios.ausencias.destroy', $ausencia) }}" onsubmit="return confirm('¿Eliminar esta ausencia?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('admin.operarios.ausencias.store', $operario) }}" class="flex flex-wrap items-end gap-4">
                    @csrf
                    <div>
                        <x-input-label for="fecha_inicio" value="Desde" />
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="field-input" required>
                    </div>
                    <div>
                        <x-input-label for="fecha_fin" value="Hasta" />
                        <input type="date" id="fecha_fin" name="fecha_fin" class="field-input" required>
                    </div>
                    <div>
                        <x-input-label for="motivo" value="Motivo (opcional)" />
                        <input type="text" id="motivo" name="motivo" class="field-input" placeholder="Ej. Vacaciones">
                    </div>
                    <button type="submit" class="btn-primary">Añadir</button>
                </form>
                <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-2" />
                <x-input-error :messages="$errors->get('fecha_fin')" class="mt-2" />
            </div>
        </div>
    </div>
</x-app-layout>
