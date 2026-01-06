@use('App\Constants\Design')
<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start ' . Design::TEXT_SM . ' leading-5 text-slate-300
    hover:bg-slate-700 hover:text-white focus:outline-none focus:bg-slate-700 transition duration-150 ease-in-out'])
    }}>{{ $slot }}</a>