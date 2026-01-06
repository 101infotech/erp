@use('App\Constants\Design')
@props(['active'])

@php
$classes = ($active ?? false)
? 'inline-flex items-center px-1 pt-1 border-b-2 border-lime-400 ' . Design::TEXT_SM . ' ' . Design::FONT_MEDIUM . '
leading-5 text-white
focus:outline-none focus:border-lime-500 transition duration-150 ease-in-out'
: 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent ' . Design::TEXT_SM . ' ' . Design::FONT_MEDIUM . '
leading-5 text-slate-300
hover:text-white hover:border-slate-700 focus:outline-none focus:text-white focus:border-slate-700 transition
duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>