@use('App\Constants\Design')
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center {{ Design::BTN_PADDING }} bg-red-600 border border-transparent rounded-md {{ Design::FONT_SEMIBOLD }} text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-slate-950 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>