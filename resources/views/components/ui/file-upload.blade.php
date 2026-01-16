{{--
File Upload Component

A file input with drag & drop support and file preview.

Props:
- label: string (optional) - Upload label
- accept: string (optional) - Accepted file types (e.g., "image/*")
- multiple: boolean (default: false) - Allow multiple files
- maxSize: string (optional) - Max file size display (e.g., "5MB")
- error: string (optional) - Error message
- help: string (optional) - Help text
- required: boolean (default: false) - Required indicator

Usage:
<x-ui.file-upload name="avatar" label="Profile Picture" accept="image/*" maxSize="2MB"
    help="PNG, JPG or GIF up to 2MB" />

<x-ui.file-upload name="documents" label="Documents" :multiple="true" accept=".pdf,.doc,.docx" />
--}}

@props([
'label' => null,
'accept' => null,
'multiple' => false,
'maxSize' => null,
'error' => null,
'help' => null,
'required' => false,
])

@php
$uniqueId = $attributes->get('id') ?? $attributes->get('name') ?? 'file-' . uniqid();
@endphp

<div class="w-full">
    @if($label)
    <x-ui.label :for="$uniqueId" :required="$required">
        {{ $label }}
    </x-ui.label>
    @endif

    <div x-data="{
            files: [],
            dragging: false,
            handleFiles(fileList) {
                this.files = Array.from(fileList).map(file => ({
                    name: file.name,
                    size: this.formatFileSize(file.size),
                    type: file.type
                }));
            },
            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            },
            removeFile(index) {
                this.files.splice(index, 1);
                if (this.files.length === 0) {
                    $refs.fileInput.value = '';
                }
            }
        }"
        @drop.prevent="dragging = false; handleFiles($event.dataTransfer.files); $refs.fileInput.files = $event.dataTransfer.files"
        @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" class="relative">
        <div :class="{ 'border-primary-500 bg-primary-500/5': dragging, 'border-slate-700': !dragging }"
            class="border-2 border-dashed rounded-lg p-6 text-center transition-colors {{ $error ? 'border-danger-500' : '' }}">
            <input {{ $attributes->merge([
            'type' => 'file',
            'id' => $uniqueId,
            'accept' => $accept,
            'multiple' => $multiple,
            'class' => 'sr-only',
            'required' => $required,
            ]) }}
            x-ref="fileInput"
            @change="handleFiles($event.target.files)"
            />

            <label for="{{ $uniqueId }}" class="cursor-pointer">
                <div class="flex flex-col items-center gap-2">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>

                    <div>
                        <span class="text-primary-400 hover:text-primary-300 font-medium">Click to upload</span>
                        <span class="text-slate-400"> or drag and drop</span>
                    </div>

                    @if($maxSize || $accept)
                    <p class="text-xs text-slate-500">
                        @if($accept)
                        {{ str_replace(',', ', ', $accept) }}
                        @endif
                        @if($maxSize)
                        {{ $accept ? ' - ' : '' }}up to {{ $maxSize }}
                        @endif
                    </p>
                    @endif
                </div>
            </label>
        </div>

        <!-- File List -->
        <div x-show="files.length > 0" class="mt-4 space-y-2">
            <template x-for="(file, index) in files" :key="index">
                <div class="flex items-center justify-between p-3 bg-slate-800 border border-slate-700 rounded-lg">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <svg class="w-8 h-8 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate" x-text="file.name"></p>
                            <p class="text-xs text-slate-400" x-text="file.size"></p>
                        </div>
                    </div>
                    <button type="button" @click="removeFile(index)"
                        class="ml-4 text-slate-400 hover:text-danger-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </div>

    @if($error)
    <p class="mt-2 text-sm text-danger-400 flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd" />
        </svg>
        {{ $error }}
    </p>
    @elseif($help)
    <p class="mt-2 text-sm text-slate-500">{{ $help }}</p>
    @endif
</div>