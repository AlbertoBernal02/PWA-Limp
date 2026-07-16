<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Perfil
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="card p-4 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="card p-4 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
</x-app-layout>
