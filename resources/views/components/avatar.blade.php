@props(['src' => null, 'iniciales' => '?'])

@if ($src)
    <img src="{{ $src }}" alt="" {{ $attributes->merge(['class' => 'rounded-full object-cover bg-slate-100 dark:bg-slate-700 shrink-0']) }}>
@else
    <span {{ $attributes->merge(['class' => 'rounded-full bg-brand-100 dark:bg-brand-500/20 text-brand-700 dark:text-brand-300 flex items-center justify-center font-semibold shrink-0']) }}>
        {{ $iniciales }}
    </span>
@endif
