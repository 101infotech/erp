<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Expense Claims</h1>
                    <p class="text-slate-400">Submit and track your expense reimbursements</p>
                </div>
                <a href="{{ route('employee.expense-claims.create') }}"
                    class="px-4 py-2 bg-lime-500 text-slate-900 rounded-lg font-semibold hover:bg-lime-400">New
                    Claim</a>
            </div>

            @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/40 text-red-200 rounded-lg p-4">
                {{ session('error') }}
            </div>
            @endif

            @if(session('success'))
            <div class="bg-lime-500/10 border border-lime-500/40 text-lime-200 rounded-lg p-4">
                {{ session('success') }}
            </div>
            @endif

            @if(isset($employeeMissing) && $employeeMissing)
            <div class="bg-yellow-500/10 border border-yellow-500/40 text-yellow-200 rounded-lg p-4">
                Your account is not linked to an employee profile. Please contact admin.
            </div>
            @else
            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4">
                @if($claims->isEmpty())
                <p class="text-slate-400">No expense claims yet.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-900 text-xs text-slate-300 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Title</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-right">Amount</th>
                                <th class="px-4 py-3 text-left">Date</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @foreach($claims as $claim)
                            <tr class="hover:bg-slate-700/40 text-slate-200">
                                <td class="px-4 py-3 font-semibold">{{ $claim->title }}</td>
                                <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $claim->expense_type) }}</td>
                                <td class="px-4 py-3 text-right font-medium">{{ $claim->currency }} {{
                                    number_format($claim->amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $claim->expense_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($claim->status==='approved') bg-blue-500/20 text-blue-300
                                        @elseif($claim->status==='paid') bg-lime-500/20 text-lime-300
                                        @elseif($claim->status==='rejected') bg-red-500/20 text-red-300
                                        @else bg-amber-500/20 text-amber-200 @endif">
                                        {{ ucfirst($claim->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $claim->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $claims->links() }}
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>