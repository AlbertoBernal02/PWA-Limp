@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-500 text-start text-base font-medium text-brand-700 dark:text-brand-300 bg-brand-50 dark:bg-brand-500/10 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:border-slate-300 dark:hover:border-slate-500 focus:outline-none focus:text-slate-800 dark:focus:text-slate-100 focus:bg-slate-50 dark:focus:bg-slate-700/50 focus:border-slate-300 dark:focus:border-slate-500 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
