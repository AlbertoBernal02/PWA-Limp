<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">Nuevo operario</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('admin.operarios.store') }}" class="space-y-4" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="avatar" value="Foto de perfil" />
                        <input id="avatar" name="avatar" type="file" accept="image/*" class="field-input" />
                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="name" value="Nombre" />
                        <x-text-input id="name" name="name" type="text" class="w-full" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Correo electrónico" />
                        <x-text-input id="email" name="email" type="email" class="w-full" :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="telefono" value="Teléfono" />
                        <x-text-input id="telefono" name="telefono" type="tel" class="w-full" :value="old('telefono')" placeholder="Ej. 600123456" />
                        <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" value="Contraseña" />
                        <x-text-input id="password" name="password" type="password" class="w-full" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end pt-2 gap-4">
                        <a href="{{ route('admin.operarios.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:underline">Cancelar</a>
                        <x-primary-button>Crear operario</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
