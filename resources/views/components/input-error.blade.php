@use('App\Constants\Design')
@props(['messages'])

@if ($messages)
<ul {{ $attributes->merge(['class' => Design::TEXT_SM . ' text-red-400 space-y-1']) }}>
    @foreach ((array) $messages as $message)
    <li>{{ $message }}</li>
    @endforeach
</ul>
@endif