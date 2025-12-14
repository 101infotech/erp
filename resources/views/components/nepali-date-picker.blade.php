@props([
'name' => 'date',
'value' => '',
'required' => false,
'id' => null,
'placeholder' => 'YYYY-MM-DD',
'class' => '',
])

@php
$inputId = $id ?? 'nepali-date-' . $name;
$inputClass = 'nepali-datepicker w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-3 py-2
focus:ring-2 focus:ring-lime-500 focus:border-transparent ' . $class;
@endphp

<input type="text" name="{{ $name }}" id="{{ $inputId }}" value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}" class="{{ $inputClass }}" {{ $required ? 'required' : '' }} {{ $attributes }}
    autocomplete="off" />

@once
@push('styles')
<link href="https://nepalidatepicker.sajanmaharjan.com.np/nepali.datepicker/css/nepali.datepicker.v4.0.1.min.css"
    rel="stylesheet" type="text/css" />
<style>
    /* Nepali Calendar Dark Mode Styling */
    #ndp-nepali-box {
        background-color: #1e293b !important;
        border: 1px solid #475569 !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
        color: #f1f5f9 !important;
    }

    #ndp-nepali-box .ndp-header {
        background-color: #334155 !important;
        color: #f1f5f9 !important;
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }

    #ndp-nepali-box .ndp-title {
        color: #f1f5f9 !important;
    }

    #ndp-nepali-box .ndp-nav-btn {
        color: #f1f5f9 !important;
        background-color: #475569 !important;
        border-radius: 0.25rem !important;
    }

    #ndp-nepali-box .ndp-nav-btn:hover {
        background-color: #64748b !important;
    }

    #ndp-nepali-box table {
        background-color: #1e293b !important;
    }

    #ndp-nepali-box table thead th {
        color: #94a3b8 !important;
        font-weight: 600 !important;
    }

    #ndp-nepali-box table tbody td {
        color: #f1f5f9 !important;
        background-color: #1e293b !important;
    }

    #ndp-nepali-box table tbody td:hover {
        background-color: #475569 !important;
        color: #fff !important;
    }

    #ndp-nepali-box table tbody td.tdOtherMonth {
        color: #64748b !important;
    }

    #ndp-nepali-box table tbody td.tdCurrentDay {
        background-color: #3b82f6 !important;
        color: white !important;
        font-weight: bold !important;
    }

    #ndp-nepali-box table tbody td.active {
        background-color: #84cc16 !important;
        color: #1e293b !important;
        font-weight: bold !important;
    }

    #ndp-nepali-box .ndp-footer {
        background-color: #1e293b !important;
        border-top: 1px solid #475569 !important;
    }

    #ndp-nepali-box .ndp-footer button {
        background-color: #475569 !important;
        color: #f1f5f9 !important;
        border-radius: 0.25rem !important;
    }

    #ndp-nepali-box .ndp-footer button:hover {
        background-color: #64748b !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://nepalidatepicker.sajanmaharjan.com.np/nepali.datepicker/js/nepali.datepicker.v4.0.1.min.js"
    type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all Nepali date pickers
        const dateInputs = document.querySelectorAll('.nepali-datepicker');
        
        dateInputs.forEach(input => {
            $(input).nepaliDatePicker({
                dateFormat: "%y-%m-%d",
                closeOnDateSelect: true,
                ndpEnglishInput: 'englishDate'
            });
        });
    });
</script>
@endpush
@endonce