@extends('admin.layouts.app')

@section('title', 'Resource Request Details')
@section('page-title', 'Resource Request #' . $request->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Resource Request #{{ $request->id }}</h1>
            <p class="text-slate-400 mt-1">View request details and take action</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.hrm.resource-requests.index') }}"
                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                Back to List
            </a>
            @if($request->isPending())
            <a href="{{ route('admin.hrm.resource-requests.edit', $request->id) }}"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                Edit
            </a>
            @endif
        </div>
    </div>

    <!-- Status & Priority Badges -->
    <div class="flex gap-3">
        <span class="px-4 py-2 rounded-lg text-sm font-semibold
            @if($request->status === 'fulfilled') bg-green-500/20 text-green-400 border border-green-500/30
            @elseif($request->status === 'approved') bg-blue-500/20 text-blue-400 border border-blue-500/30
            @elseif($request->status === 'pending') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
            @else bg-red-500/20 text-red-400 border border-red-500/30
            @endif">
            Status: {{ ucfirst($request->status) }}
        </span>
        <span class="px-4 py-2 rounded-lg text-sm font-semibold
            @if($request->priority === 'urgent') bg-red-500/20 text-red-400 border border-red-500/30
            @elseif($request->priority === 'high') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
            @elseif($request->priority === 'medium') bg-blue-500/20 text-blue-400 border border-blue-500/30
            @else bg-slate-500/20 text-slate-400 border border-slate-500/30
            @endif">
            Priority: {{ ucfirst($request->priority) }}
        </span>
    </div>

    <!-- Request Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
            <h2 class="text-xl font-semibold text-white mb-4">Request Information</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Employee</label>
                    <p class="text-white font-medium">{{ $request->employee->name ?? 'N/A' }}</p>
                    @if($request->employee)
                    <p class="text-sm text-slate-400">{{ $request->employee->code }}</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Item Name</label>
                    <p class="text-white font-medium">{{ $request->item_name }}</p>
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Quantity</label>
                    <p class="text-white font-medium">{{ $request->quantity }}</p>
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Category</label>
                    <p class="text-white font-medium capitalize">{{ str_replace('_', ' ', $request->category) }}</p>
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Request Date</label>
                    <p class="text-white">{{ $request->created_at->format('F j, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Description & Notes -->
        <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
            <h2 class="text-xl font-semibold text-white mb-4">Details</h2>
            <div class="space-y-4">
                @if($request->description)
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Description</label>
                    <p class="text-white">{{ $request->description }}</p>
                </div>
                @endif
                @if($request->purpose)
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Purpose</label>
                    <p class="text-white">{{ $request->purpose }}</p>
                </div>
                @endif
                @if($request->admin_notes)
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Admin Notes</label>
                    <p class="text-white bg-slate-900 p-3 rounded border border-slate-600">{{ $request->admin_notes }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Approval Information -->
    @if($request->approver || $request->fulfiller)
    <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
        <h2 class="text-xl font-semibold text-white mb-4">Processing Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($request->approver)
            <div>
                <label class="block text-sm text-slate-400 mb-1">Approved By</label>
                <p class="text-white font-medium">{{ $request->approver->name }}</p>
                @if($request->approved_at)
                <p class="text-sm text-slate-400">{{ $request->approved_at->format('M d, Y \a\t g:i A') }}</p>
                @endif
            </div>
            @endif
            @if($request->fulfiller)
            <div>
                <label class="block text-sm text-slate-400 mb-1">Fulfilled By</label>
                <p class="text-white font-medium">{{ $request->fulfiller->name }}</p>
                @if($request->fulfilled_at)
                <p class="text-sm text-slate-400">{{ $request->fulfilled_at->format('M d, Y \a\t g:i A') }}</p>
                @endif
            </div>
            @endif
            @if($request->rejected_at)
            <div>
                <label class="block text-sm text-slate-400 mb-1">Rejected At</label>
                <p class="text-white">{{ $request->rejected_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Actions -->
    @if($request->isPending() || $request->isApproved())
    <div class="bg-slate-800 rounded-lg p-6 border border-slate-700">
        <h2 class="text-xl font-semibold text-white mb-4">Actions</h2>
        <div class="flex flex-wrap gap-3">
            @if($request->isPending())
            <form action="{{ route('admin.hrm.resource-requests.approve', $request->id) }}" method="POST"
                class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition"
                    onclick="return confirm('Are you sure you want to approve this request?')">
                    Approve Request
                </button>
            </form>
            <button type="button" onclick="openRejectModal()"
                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                Reject Request
            </button>
            @endif

            @if($request->isApproved())
            <button type="button" onclick="openFulfillModal()"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                Mark as Fulfilled
            </button>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Reject Modal -->
<div id="rejectModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-lg max-w-md w-full p-6 border border-slate-700">
        <h3 class="text-xl font-semibold text-white mb-4">Reject Request</h3>
        <form action="{{ route('admin.hrm.resource-requests.reject', $request->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm text-slate-300 mb-2">Reason for rejection (optional)</label>
                <textarea name="admin_notes" rows="4"
                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500"
                    placeholder="Enter reason for rejection..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                    Reject Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Fulfill Modal -->
<div id="fulfillModal"
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-slate-800 rounded-lg max-w-md w-full p-6 border border-slate-700">
        <h3 class="text-xl font-semibold text-white mb-4">Mark as Fulfilled</h3>
        <form action="{{ route('admin.hrm.resource-requests.fulfill', $request->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm text-slate-300 mb-2">Fulfillment notes (optional)</label>
                <textarea name="admin_notes" rows="4"
                    class="w-full bg-slate-900 border border-slate-600 text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500"
                    placeholder="Enter any notes about fulfillment..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeFulfillModal()"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition">
                    Mark as Fulfilled
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function openFulfillModal() {
    document.getElementById('fulfillModal').classList.remove('hidden');
}

function closeFulfillModal() {
    document.getElementById('fulfillModal').classList.add('hidden');
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
        closeFulfillModal();
    }
});

// Close modals on background click
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

document.getElementById('fulfillModal').addEventListener('click', function(e) {
    if (e.target === this) closeFulfillModal();
});
</script>
@endpush
@endsection