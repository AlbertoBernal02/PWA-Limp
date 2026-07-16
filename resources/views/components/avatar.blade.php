@props(['src' => null, 'iniciales' => '?'])

@if ($src)
    <img src="{{ $src }}" alt="" {{ $attributes->merge(['class' => 'rounded-full object-cover bg-slate-100 shrink-0']) }}>
@else
    <span {{ $attributes->merge(['class' => 'rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-semibold shrink-0']) }}>
        {{ $iniciales }}
    </span>
@endif
