@use('App\Constants\Design')
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => Design::FONT_MEDIUM . ' ' . Design::TEXT_SM . ' text-green-600']) }}>
        {{ $status }}
    </div>
@endif
