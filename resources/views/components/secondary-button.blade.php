@use('App\Constants\Design')
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center {{ Design::BTN_PADDING }}
    bg-slate-800 border border-slate-700 rounded-md {{ Design::FONT_SEMIBOLD }} text-xs text-slate-300 uppercase
    tracking-widest shadow-sm hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2
    focus:ring-offset-slate-950 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>