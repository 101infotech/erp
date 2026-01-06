@use('App\Constants\Design')
@props(['value'])

<label {{ $attributes->merge(['class' => Design::FORM_LABEL]) }}>
    {{ $value ?? $slot }}
</label>