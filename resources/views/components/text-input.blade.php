@use('App\Constants\Design')
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-slate-800 border-slate-700 text-white
focus:border-lime-500 focus:ring-lime-500 rounded-md shadow-sm']) }}>