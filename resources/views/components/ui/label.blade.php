{{--
Label Component

Form field label with optional/required indicators and help text.

Props:
- for: string (optional) - ID of the input element
- required: boolean (default: false) - Show required indicator
- optional: boolean (default: false) - Show optional text

Usage:
<x-ui.label for="email" :required="true">Email Address</x-ui.label>
<x-ui.label for="phone" :optional="true">Phone Number</x-ui.label>
--}}

@props([
'for' => null,
'required' => false,
'optional' => false,
])

<label {{ $attributes->merge(['for' => $for, 'class' => 'block text-sm font-medium text-slate-300 mb-1.5']) }}>
    {{ $slot }}

    @if($required)
    <span class="text-danger-400 ml-1" aria-label="required">*</span>
    @endif

    @if($optional)
    <span class="text-slate-500 text-xs font-normal ml-1.5">(optional)</span>
    @endif
</label>