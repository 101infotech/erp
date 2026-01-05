@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Asset Details</h2>
        <div>
            <a href="{{ route('admin.finance.assets.edit', $asset) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.finance.assets.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Asset Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Asset Number:</strong><br>
                            <code>{{ $asset->asset_number }}</code>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            @switch($asset->status)
                            @case('active')
                            <span class="badge bg-success">Active</span>
                            @break
                            @case('disposed')
                            <span class="badge bg-warning">Disposed</span>
                            @break
                            @case('sold')
                            <span class="badge bg-secondary">Sold</span>
                            @break
                            @case('transferred')
                            <span class="badge bg-primary">Transferred</span>
                            @break
                            @case('under_maintenance')
                            <span class="badge bg-info">Under Maintenance</span>
                            @break
                            @case('written_off')
                            <span class="badge bg-danger">Written Off</span>
                            @break
                            @endswitch
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Asset Name:</strong><br>
                            {{ $asset->asset_name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Company:</strong><br>
                            {{ $asset->company->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Category:</strong><br>
                            {{ $asset->category->name ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Asset Type:</strong><br>
                            <span class="badge bg-info">{{ ucfirst($asset->asset_type) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Asset Category:</strong><br>
                            {{ $asset->asset_category }}
                        </div>
                    </div>

                    @if($asset->description)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>Description:</strong><br>
                            {{ $asset->description }}
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Location:</strong><br>
                            {{ $asset->location ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Assigned To:</strong><br>
                            {{ $asset->assigned_to ?? 'N/A' }}
                        </div>
                    </div>

                    @if($asset->serial_number)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Serial Number:</strong><br>
                            <code>{{ $asset->serial_number }}</code>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Purchase Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Purchase Cost:</strong><br>
                            NPR {{ number_format($asset->purchase_cost, 2) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Purchase Date (BS):</strong><br>
                            {{ $asset->purchase_date_bs }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Fiscal Year:</strong><br>
                            {{ $asset->fiscal_year_bs }}
                        </div>
                        @if($asset->vendor_name)
                        <div class="col-md-6">
                            <strong>Vendor:</strong><br>
                            {{ $asset->vendor_name }}
                        </div>
                        @endif
                    </div>

                    @if($asset->invoice_number)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Invoice Number:</strong><br>
                            {{ $asset->invoice_number }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Depreciation Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Depreciation Method:</strong><br>
                            {{ ucwords(str_replace('_', ' ', $asset->depreciation_method)) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Useful Life:</strong><br>
                            {{ $asset->useful_life_years }} years {{ $asset->useful_life_months }} months
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Salvage Value:</strong><br>
                            NPR {{ number_format($asset->salvage_value ?? 0, 2) }}
                        </div>
                        @if($asset->depreciation_rate)
                        <div class="col-md-6">
                            <strong>Depreciation Rate:</strong><br>
                            {{ $asset->depreciation_rate }}%
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Financial Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Purchase Cost:</strong><br>
                        <h4>NPR {{ number_format($asset->purchase_cost, 2) }}</h4>
                    </div>
                    <div class="mb-3">
                        <strong>Accumulated Depreciation:</strong><br>
                        <h4 class="text-danger">NPR {{ number_format($asset->accumulated_depreciation, 2) }}</h4>
                    </div>
                    <div class="mb-3">
                        <strong>Current Book Value:</strong><br>
                        <h4 class="text-success">NPR {{ number_format($asset->book_value, 2) }}</h4>
                    </div>
                </div>
            </div>

            @if($asset->status == 'active')
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary w-100 mb-2" data-bs-toggle="modal"
                        data-bs-target="#depreciationModal">
                        <i class="bi bi-calculator"></i> Calculate Depreciation
                    </button>
                    <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal"
                        data-bs-target="#transferModal">
                        <i class="bi bi-arrow-left-right"></i> Transfer Asset
                    </button>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                        data-bs-target="#disposeModal">
                        <i class="bi bi-trash"></i> Dispose Asset
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Depreciation Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fiscal Year</th>
                                    <th>Month</th>
                                    <th>Opening Book Value</th>
                                    <th>Depreciation</th>
                                    <th>Accumulated Depreciation</th>
                                    <th>Closing Book Value</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asset->depreciationRecords as $record)
                                <tr>
                                    <td>{{ $record->fiscal_year_bs }}</td>
                                    <td>{{ $record->fiscal_month_bs }}</td>
                                    <td>NPR {{ number_format($record->opening_book_value, 2) }}</td>
                                    <td class="text-danger">NPR {{ number_format($record->depreciation_amount, 2) }}</td>
                                    <td>NPR {{ number_format($record->accumulated_depreciation, 2) }}</td>
                                    <td class="text-success">NPR {{ number_format($record->closing_book_value, 2) }}</td>
                                    <td><span
                                            class="badge bg-{{ $record->status == 'posted' ? 'success' : 'warning' }}">{{
                                            ucfirst($record->status) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No depreciation records yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Depreciation Modal -->
<div class="modal fade" id="depreciationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.finance.assets.calculate-depreciation') }}">
                @csrf
                <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Calculate Depreciation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fiscal Year (BS)</label>
                        <input type="text" name="fiscal_year_bs" class="form-control" placeholder="2081-2082" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fiscal Month (BS)</label>
                        <select name="fiscal_month_bs" class="form-select" required>
                            <option value="">Select Month</option>
                            @for($i = 1; $i <= 12; $i++) <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{
                                str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Calculate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Dispose Modal -->
<div class="modal fade" id="disposeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.finance.assets.dispose', $asset) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Dispose Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Disposal Date (BS)</label>
                        <input type="text" name="disposal_date_bs" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Disposal Value</label>
                        <input type="number" step="0.01" name="disposal_value" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="disposal_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Dispose</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.finance.assets.transfer', $asset) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">To Company</label>
                        <select name="to_company_id" class="form-select" required>
                            <option value="">Select Company</option>
                            @foreach(\App\Models\FinanceCompany::active()->where('id', '!=', $asset->company_id)->get()
                            as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transfer Date (BS)</label>
                        <input type="text" name="transfer_date_bs" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="transfer_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection