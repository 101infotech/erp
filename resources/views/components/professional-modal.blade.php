{{-- Professional Modal Component --}}
{{-- Usage:
<x-professional-modal id="exampleModal" title="Modal Title" icon="trash">
    <div class="space-y-4">
        <p class="text-slate-300">Modal content goes here</p>
    </div>
    <x-slot name="footer">
        <button type="button" onclick="closeModal('exampleModal')"
            class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition">
            Cancel
        </button>
        <button type="submit"
            class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition">
            Confirm
        </button>
    </x-slot>
</x-professional-modal>
--}}

@use('App\Constants\Design')
@props([
'id' => 'modal-' . uniqid(),
'title' => '',
'subtitle' => '',
'icon' => 'exclamation', // exclamation, trash, check, info, warning, question
'iconColor' => 'red', // red, blue, green, yellow, purple
'maxWidth' => 'max-w-md', // max-w-sm, max-w-md, max-w-lg, max-w-xl, max-w-2xl
'footer' => true
])

<div id="{{ $id }}" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div
        class="bg-slate-800 border border-slate-700 rounded-lg shadow-2xl {{ $maxWidth }} w-full overflow-hidden transition-all transform">
        {{-- Header {{ Design::MODAL_HEADER_PADDING }}
        <div class="flex items-center justify-between p-6 border-b border-slate-700 bg-slate-800/50">
            <div class="flex items-center gap-4 flex-1">
                {{-- Icon --}}
                @if($icon)
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full @switch($iconColor)
                        @case('red') bg-red-500/20 @break
                        @case('blue') bg-blue-500/20 @break
                        @case('green') bg-green-500/20 @break
                        @case('yellow') bg-yellow-500/20 @break
                        @case('purple') bg-purple-500/20 @break
                        @default bg-slate-700/50 @endswitch flex items-center justify-center">
                        @switch($icon)
                        @case('trash')
                        <svg class="w-6 h-6 @switch($iconColor)
                                    @case('red') text-red-400 @break
                                    @case('blue') text-blue-400 @break
                                    @case('green') text-green-400 @break
                                    @case('yellow') text-yellow-400 @break
                                    @case('purple') text-purple-400 @break
                                    @default text-slate-400 @endswitch" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        @break
                        @case('check')
                        <svg class="w-6 h-6 @switch($iconColor)
                                    @case('red') text-red-400 @break
                                    @case('blue') text-blue-400 @break
                                    @case('green') text-green-400 @break
                                    @case('yellow') text-yellow-400 @break
                                    @case('purple') text-purple-400 @break
                                    @default text-slate-400 @endswitch" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        @break
                        @case('info')
                        <svg class="w-6 h-6 @switch($iconColor)
                                    @case('red') text-red-400 @break
                                    @case('blue') text-blue-400 @break
                                    @case('green') text-green-400 @break
                                    @case('yellow') text-yellow-400 @break
                                    @case('purple') text-purple-400 @break
                                    @default text-slate-400 @endswitch" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @break
                        @case('warning')
                        <svg class="w-6 h-6 @switch($iconColor)
                                    @case('red') text-red-400 @break
                                    @case('blue') text-blue-400 @break
                                    @case('green') text-green-400 @break
                                    @case('yellow') text-yellow-400 @break
                                    @case('purple') text-purple-400 @break
                                    @default text-slate-400 @endswitch" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4v2m0 4v2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @break
                        @case('question')
                        <svg class="w-6 h-6 @switch($iconColor)
                                    @case('red') text-red-400 @break
                                    @case('blue') text-blue-400 @break
                                    @case('green') text-green-400 @break
                                    @case('yellow') text-yellow-400 @break
                                    @case('purple') text-purple-400 @break
                                    @default text-slate-400 @endswitch" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @break
                        @default
                        <svg class="w-6 h-6 @switch($iconColor)
                                    @case('red') text-red-400 @break
                                    @case('blue') text-blue-400 @break
                                    @case('green') text-green-400 @break
                                    @case('yellow') text-yellow-400 @break
                                    @case('purple') text-purple-400 @break
                                    @default text-slate-400 @endswitch" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        @endswitch
                    </div>
                </div>
                @endif

                {{-- Title --}}
                <div>{{ Design::TEXT_XL }} {{ Design::FONT_SEMIBOLD }} text-white">{{ $title }}</h3>
                    @if($subtitle)
                    <p class="{{ Design::TEXT_SM }}
                    <p class="text-sm text-slate-400 mt-1">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            {{-- Close Button --}}
            <button type="button" onclick="closeModal('{{ $id }}')"
                class="text-slate-400 hover:text-white transition-colors flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Body --{{ Design::MODAL_PADDING }}">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @if($footer && isset($footer))
        <div class="flex justify-end gap-3 {{ Design::MODAL_PADDING }}
        <div class="flex justify-end gap-3 p-6 border-t border-slate-700 bg-slate-800/30">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>