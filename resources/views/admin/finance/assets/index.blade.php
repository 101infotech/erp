@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Asset Register</h2>
        <a href="{{ route('admin.finance.assets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Asset
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="company_id" class="form-select">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id')==$company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                        <option value="disposed" {{ request('status')=='disposed' ? 'selected' : '' }}>Disposed</option>
                        <option value="sold" {{ request('status')=='sold' ? 'selected' : '' }}>Sold</option>
                        <option value="transferred" {{ request('status')=='transferred' ? 'selected' : '' }}>Transferred
                        </option>
                        <option value="under_maintenance" {{ request('status')=='under_maintenance' ? 'selected' : ''
                            }}>Under Maintenance</option>
                        <option value="written_off" {{ request('status')=='written_off' ? 'selected' : '' }}>Written Off
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="asset_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="tangible" {{ request('asset_type')=='tangible' ? 'selected' : '' }}>Tangible
                        </option>
                        <option value="intangible" {{ request('asset_type')=='intangible' ? 'selected' : '' }}>
                            Intangible</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control"
                        placeholder="Search by name, number, serial..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Asset Number</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Purchase Cost</th>
                            <th>Book Value</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td><code>{{ $asset->asset_number }}</code></td>
                            <td>{{ $asset->asset_name }}</td>
                            <td>{{ $asset->company->name }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($asset->asset_type) }}</span></td>
                            <td>NPR {{ number_format($asset->purchase_cost, 2) }}</td>
                            <td>NPR {{ number_format($asset->book_value, 2) }}</td>
                            <td>
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
                            </td>
                            <td>{{ $asset->location ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.finance.assets.show', $asset) }}"
                                    class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('admin.finance.assets.edit', $asset) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No assets found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $assets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection