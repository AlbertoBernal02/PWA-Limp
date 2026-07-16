<section>
    <header>
        <h2 class="text-lg font-medium text-slate-800">
            Datos personales
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Actualiza tu nombre y correo electrónico.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-4" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" value="Foto de perfil" />
            <div class="flex items-center gap-4 mt-1">
                <x-avatar :src="$user->avatarUrl()" :iniciales="$user->iniciales()" class="w-14 h-14 text-lg" />
                <input id="avatar" name="avatar" type="file" accept="image/*" class="field-input" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-500"
                >Guardado.</p>
            @endif
        </div>
    </form>
</section>
