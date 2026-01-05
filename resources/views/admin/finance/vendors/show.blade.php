@extends('admin.layouts.app')

@section('title', 'Vendor Details')
@section('page-title', 'Vendor Details')

@section('content')
<div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6">
    <div class="mb-6">
        <a href="{{ route('admin.finance.vendors.index', ['company_id' => $vendor->company_id]) }}"
            class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Vendors
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
            <div class="text-blue-600 dark:text-blue-400 text-sm font-medium">Total Purchases</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-2">NPR {{ number_format($totalPurchases, 2)
                }}</div>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg">
            <div class="text-green-600 dark:text-green-400 text-sm font-medium">Paid</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-2">NPR {{ number_format($paidPurchases, 2)
                }}</div>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900/20 p-6 rounded-lg">
            <div class="text-orange-600 dark:text-orange-400 text-sm font-medium">Outstanding</div>
            <div class="text-2xl font-bold text-slate-900 dark:text-white mt-2">NPR {{ number_format($outstanding, 2) }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Vendor Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">Vendor Code</dt>
                    <dd class="text-slate-900 dark:text-white font-medium">{{ $vendor->vendor_code }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">Vendor Name</dt>
                    <dd class="text-slate-900 dark:text-white font-medium">{{ $vendor->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">Company</dt>
                    <dd class="text-slate-900 dark:text-white font-medium">{{ $vendor->company->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">Type</dt>
                    <dd>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($vendor->vendor_type === 'supplier') bg-blue-100 text-blue-800
                            @elseif($vendor->vendor_type === 'contractor') bg-purple-100 text-purple-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $vendor->vendor_type)) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">Status</dt>
                    <dd>
                        @if($vendor->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Contact Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">PAN Number</dt>
                    <dd class="text-slate-900 dark:text-white font-medium">{{ $vendor->pan_number ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">Contact Person</dt>
                    <dd class="text-slate-900 dark:text-white font-medium">{{ $vendor->contact_person ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">Email</dt>
                    <dd class="text-slate-900 dark:text-white font-medium">{{ $vendor->email ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">Phone</dt>
                    <dd class="text-slate-900 dark:text-white font-medium">{{ $vendor->phone ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500 dark:text-slate-400">Address</dt>
                    <dd class="text-slate-900 dark:text-white font-medium">{{ $vendor->address ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="flex justify-end space-x-4">
        <a href="{{ route('admin.finance.vendors.edit', $vendor) }}"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Edit Vendor
        </a>
    </div>

    <div class="mt-8">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Recent Purchases ({{
            $vendor->purchases->count() }})</h3>
        <div class="w-full">
            <table class="w-full table-auto divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                            Purchase #</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                            Date</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                            Amount</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($vendor->purchases->take(10) as $purchase)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">{{
                            $purchase->purchase_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{
                            $purchase->purchase_date_bs }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white font-medium">NPR {{
                            number_format($purchase->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($purchase->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($purchase->payment_status === 'partial') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($purchase->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-slate-500 dark:text-slate-400">No purchases
                            found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Documents Section -->
    <div class="bg-white dark:bg-slate-800 shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Documents</h3>
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                Upload Document
            </button>
        </div>
        <div class="p-6">
            @if($vendor->documents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($vendor->documents as $document)
                <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                @if($document->file_type === 'pdf')
                                <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                                </svg>
                                @else
                                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                        clip-rule="evenodd" />
                                </svg>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-slate-900 dark:text-white truncate">{{ $document->title
                                        }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ ucfirst($document->type) }}
                                    </p>
                                </div>
                            </div>
                            @if($document->description)
                            <p class="text-sm text-slate-600 dark:text-slate-300 mb-2">{{ $document->description }}</p>
                            @endif
                            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <span>{{ $document->file_size_formatted }}</span>
                                <span>{{ $document->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('admin.finance.documents.download', $document) }}"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs text-center">
                            Download
                        </a>
                        <form action="{{ route('admin.finance.documents.destroy', $document) }}" method="POST"
                            class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this document?')"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded text-xs">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center text-slate-500 dark:text-slate-400 py-8">No documents uploaded yet.</p>
            @endif
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Upload Document</h3>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.finance.documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="documentable_type" value="vendor">
            <input type="hidden" name="documentable_id" value="{{ $vendor->id }}">
            <input type="hidden" name="company_id" value="{{ $vendor->company_id }}">

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Document Type *</label>
                <select name="type" required
                    class="w-full border-slate-300 dark:border-slate-600 dark:bg-slate-700 rounded-lg">
                    <option value="invoice">Invoice</option>
                    <option value="receipt">Receipt</option>
                    <option value="contract">Contract</option>
                    <option value="agreement">Agreement</option>
                    <option value="pan_card">PAN Card</option>
                    <option value="registration">Registration</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Title *</label>
                <input type="text" name="title" required
                    class="w-full border-slate-300 dark:border-slate-600 dark:bg-slate-700 rounded-lg">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Description</label>
                <textarea name="description" rows="2"
                    class="w-full border-slate-300 dark:border-slate-600 dark:bg-slate-700 rounded-lg"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">File * (Max
                    5MB)</label>
                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                    class="w-full border-slate-300 dark:border-slate-600 dark:bg-slate-700 rounded-lg">
                <p class="text-xs text-slate-500 mt-1">Accepted: PDF, JPG, PNG, DOC, DOCX</p>
            </div>

            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')"
                    class="flex-1 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white px-4 py-2 rounded-lg">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>
@endsection