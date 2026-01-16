<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">Resource Requests</h1>
                    <p class="text-slate-400">Submit and track your resource needs</p>
                </div>
                <a href="{{ route('employee.resource-requests.create') }}"
                    class="px-4 py-2 bg-lime-500 text-slate-900 rounded-lg font-semibold hover:bg-lime-400">New
                    Request</a>
            </div>

            @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/40 text-red-200 rounded-lg p-4">
                {{ session('error') }}
            </div>
            @endif

            @if(isset($employeeMissing) && $employeeMissing)
            <div class="bg-yellow-500/10 border border-yellow-500/40 text-yellow-200 rounded-lg p-4">
                Your account is not linked to an employee profile. Please contact admin.
            </div>
            @else
            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4">
                @if($requests->isEmpty())
                <p class="text-slate-400">No requests yet.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-900 text-xs text-slate-300 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Item</th>
                                <th class="px-4 py-3 text-left">Category</th>
                                <th class="px-4 py-3 text-left">Priority</th>
                                <th class="px-4 py-3 text-left">Quantity</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Submitted</th>
                                <th class="px-4 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            @foreach($requests as $req)
                            <tr class="hover:bg-slate-700/40 text-sm text-slate-200">
                                <td class="px-4 py-3 font-semibold">{{ $req->item_name }}</td>
                                <td class="px-4 py-3">{{ ucwords(str_replace('_',' ', $req->category)) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($req->priority==='urgent') bg-red-500/20 text-red-300
                                        @elseif($req->priority==='high') bg-orange-500/20 text-orange-300
                                        @elseif($req->priority==='medium') bg-blue-500/20 text-blue-300
                                        @else bg-slate-700 text-slate-200 @endif">
                                        {{ ucfirst($req->priority) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $req->quantity }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($req->status==='approved') bg-blue-500/20 text-blue-300
                                        @elseif($req->status==='fulfilled') bg-lime-500/20 text-lime-300
                                        @elseif($req->status==='rejected') bg-red-500/20 text-red-300
                                        @else bg-amber-500/20 text-amber-200 @endif">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $req->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('employee.resource-requests.show', $req->id) }}"
                                        class="text-blue-400 hover:text-blue-300 text-sm font-semibold">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $requests->links() }}
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>