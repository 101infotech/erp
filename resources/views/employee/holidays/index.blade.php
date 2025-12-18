<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Company Holidays</h1>
                    <p class="text-slate-400">View upcoming company-wide holidays</p>
                </div>
                <a href="{{ route('employee.dashboard') }}"
                    class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">← Back to Dashboard</a>
            </div>

            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                @if($holidays->isEmpty())
                <p class="text-slate-400">No holidays have been published yet.</p>
                @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($holidays as $holiday)
                    <div class="bg-slate-900/60 border border-slate-700 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-400">{{ $holiday->date->format('l, M d, Y') }}</p>
                                <h3 class="text-lg font-semibold text-white">{{ $holiday->name }}</h3>
                            </div>
                            <span
                                class="px-3 py-1 text-xs rounded-full {{ $holiday->is_company_wide ? 'bg-lime-500/20 text-lime-300' : 'bg-blue-500/20 text-blue-300' }}">
                                {{ $holiday->is_company_wide ? 'Company-wide' : 'Partial' }}
                            </span>
                        </div>
                        @if($holiday->description)
                        <p class="text-slate-300 mt-2">{{ $holiday->description }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>