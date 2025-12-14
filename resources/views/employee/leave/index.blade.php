<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">Leave Requests</h1>
                    <p class="text-slate-400">Manage your leave requests and balance</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('employee.leave.create') }}"
                        class="px-6 py-3 bg-lime-500 text-slate-900 rounded-lg font-semibold hover:bg-lime-400 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Request Leave
                    </a>
                    <a href="{{ route('employee.dashboard') }}"
                        class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600">
                        ← Back
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 mb-6">
                <p class="text-green-400">{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 mb-6">
                <p class="text-red-400">{{ session('error') }}</p>
            </div>
            @endif

            @if(isset($message))
            <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6">
                <p class="text-yellow-400">{{ $message }}</p>
            </div>
            @else
            <!-- Leave Balance Cards -->
            <div class="grid grid-cols-1 md:grid-cols-{{ count($stats) > 0 ? min(count($stats), 4) : 3 }} gap-6 mb-8">
                @forelse($stats as $type => $stat)
                @php
                $colors = [
                'sick' => ['label' => 'Sick Leave', 'color' => 'red'],
                'casual' => ['label' => 'Casual Leave', 'color' => 'blue'],
                'annual' => ['label' => 'Annual Leave', 'color' => 'lime'],
                'period' => ['label' => 'Period Leave', 'color' => 'pink'],
                'unpaid' => ['label' => 'Unpaid Leave', 'color' => 'gray']
                ];
                $config = $colors[$type] ?? ['label' => ucfirst($type) . ' Leave', 'color' => 'slate'];
                @endphp
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-white">{{ $config['label'] }}</h3>
                        <svg class="w-8 h-8 text-{{ $config['color'] }}-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="mb-3">
                        <div class="flex items-baseline justify-between mb-2">
                            <span class="text-3xl font-bold text-{{ $config['color'] }}-400">{{
                                $stat['available'] ?? 0 }}</span>
                            <span class="text-slate-400 text-sm">/ {{ $stat['quota'] ?? 0 }} days</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            @php
                            $total = $stat['quota'] ?? 1;
                            $available = $stat['available'] ?? 0;
                            $percentage = ($available / $total) * 100;
                            @endphp
                            <div class="bg-{{ $config['color'] }}-400 h-2 rounded-full transition-all"
                                style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400">{{ $stat['used'] ?? 0 }} days used this year</p>
                </div>
                @empty
                <div class="col-span-full bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-6">
                    <p class="text-yellow-400 text-center">No leave policies have been configured by your administrator
                        yet.</p>
                </div>
                @endforelse
            </div>

            <!-- Leave Requests Table -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-700">
                    <h2 class="text-xl font-bold text-white">My Leave Requests</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-700/50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Leave Type</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Start Date</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    End Date</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Days</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @forelse($leaves as $leave)
                            <tr class="hover:bg-slate-700/30 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="capitalize text-white font-medium">{{ $leave->leave_type }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    {{ $leave->start_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    {{ $leave->end_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-white font-semibold">{{ $leave->total_days }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($leave->status === 'approved')
                                    <span
                                        class="px-3 py-1 bg-green-500/20 text-green-400 rounded-full text-xs font-semibold">Approved</span>
                                    @elseif($leave->status === 'rejected')
                                    <span
                                        class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-semibold">Rejected</span>
                                    @elseif($leave->status === 'cancelled')
                                    <span
                                        class="px-3 py-1 bg-slate-500/20 text-slate-400 rounded-full text-xs font-semibold">Cancelled</span>
                                    @else
                                    <span
                                        class="px-3 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-xs font-semibold">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('employee.leave.show', $leave->id) }}"
                                            class="text-lime-400 hover:text-lime-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if($leave->status === 'pending')
                                        <form method="POST" action="{{ route('employee.leave.cancel', $leave->id) }}"
                                            class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-400 hover:text-red-300"
                                                onclick="return confirm('Are you sure you want to cancel this leave request?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    No leave requests found. Click "Request Leave" to create one.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($leaves->hasPages())
                <div class="p-6 border-t border-slate-700">
                    {{ $leaves->links() }}
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>