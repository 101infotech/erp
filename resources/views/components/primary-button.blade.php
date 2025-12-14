<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-lime-500 border
    border-transparent rounded-md font-semibold text-xs text-slate-950 uppercase tracking-widest hover:bg-lime-600
    focus:bg-lime-600 active:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-lime-500 focus:ring-offset-2
    focus:ring-offset-slate-950 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>