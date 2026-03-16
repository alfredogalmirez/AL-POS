@props(['active' => false])

@php
    $classes = $active ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800';
@endphp

<a
    {{ $attributes->merge(['class' => 'flex items-center px-4 py-3 rounded-2xl font-bold transition-all ' . $classes]) }}>
    {{ $slot }}
</a>
