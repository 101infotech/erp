@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Asset</h2>
        <a href="{{ route('admin.finance.assets.show', $asset) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.finance.assets.update', $asset) }}">
                @csrf
                @method('PUT')

                <div class="alert alert-info">
                    <strong>Asset Number:</strong> <code>{{ $asset->asset_number }}</code><br>
                    <small>Purchase details (cost, dates, depreciation settings) cannot be changed after
                        creation.</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-select @error('company_id') is-invalid @enderror"
                            required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $asset->company_id) == $company->id
                                ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('company_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Asset Category</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $asset->category_id) ==
                                $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                    <input type="text" name="asset_name" class="form-control @error('asset_name') is-invalid @enderror"
                        value="{{ old('asset_name', $asset->asset_name) }}" required>
                    @error('asset_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="2"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $asset->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                            value="{{ old('location', $asset->location) }}">
                        @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assigned To</label>
                        <input type="text" name="assigned_to"
                            class="form-control @error('assigned_to') is-invalid @enderror"
                            value="{{ old('assigned_to', $asset->assigned_to) }}">
                        @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : ''
                            }}>Disposed</option>
                        <option value="sold" {{ old('status', $asset->status) == 'sold' ? 'selected' : '' }}>Sold
                        </option>
                        <option value="transferred" {{ old('status', $asset->status) == 'transferred' ? 'selected' : ''
                            }}>Transferred</option>
                        <option value="under_maintenance" {{ old('status', $asset->status) == 'under_maintenance' ?
                            'selected' : '' }}>Under Maintenance</option>
                        <option value="written_off" {{ old('status', $asset->status) == 'written_off' ? 'selected' : ''
                            }}>Written Off</option>
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.finance.assets.show', $asset) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection