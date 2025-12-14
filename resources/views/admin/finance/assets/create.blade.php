@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Register New Asset</h2>
        <a href="{{ route('admin.finance.assets.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.finance.assets.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <select name="company_id" class="form-select @error('company_id') is-invalid @enderror"
                            required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id')==$company->id ? 'selected' : '' }}>
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
                            <option value="{{ $category->id }}" {{ old('category_id')==$category->id ? 'selected' : ''
                                }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Asset Name <span class="text-danger">*</span></label>
                        <input type="text" name="asset_name"
                            class="form-control @error('asset_name') is-invalid @enderror"
                            value="{{ old('asset_name') }}" required>
                        @error('asset_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Asset Type <span class="text-danger">*</span></label>
                        <select name="asset_type" class="form-select @error('asset_type') is-invalid @enderror"
                            required>
                            <option value="">Select Type</option>
                            <option value="tangible" {{ old('asset_type')=='tangible' ? 'selected' : '' }}>Tangible
                            </option>
                            <option value="intangible" {{ old('asset_type')=='intangible' ? 'selected' : '' }}>
                                Intangible</option>
                        </select>
                        @error('asset_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Asset Category <span class="text-danger">*</span></label>
                        <input type="text" name="asset_category"
                            class="form-control @error('asset_category') is-invalid @enderror"
                            value="{{ old('asset_category') }}" placeholder="e.g., Furniture, Vehicle" required>
                        @error('asset_category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="2"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>
                <h5>Purchase Details</h5>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Purchase Cost <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="purchase_cost"
                            class="form-control @error('purchase_cost') is-invalid @enderror"
                            value="{{ old('purchase_cost') }}" required>
                        @error('purchase_cost')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Purchase Date (BS) <span class="text-danger">*</span></label>
                        <input type="text" name="purchase_date_bs"
                            class="form-control @error('purchase_date_bs') is-invalid @enderror"
                            value="{{ old('purchase_date_bs') }}" placeholder="YYYY-MM-DD" required>
                        @error('purchase_date_bs')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fiscal Year (BS) <span class="text-danger">*</span></label>
                        <input type="text" name="fiscal_year_bs"
                            class="form-control @error('fiscal_year_bs') is-invalid @enderror"
                            value="{{ old('fiscal_year_bs') }}" placeholder="2081-2082" required>
                        @error('fiscal_year_bs')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Serial Number</label>
                        <input type="text" name="serial_number"
                            class="form-control @error('serial_number') is-invalid @enderror"
                            value="{{ old('serial_number') }}">
                        @error('serial_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vendor Name</label>
                        <input type="text" name="vendor_name"
                            class="form-control @error('vendor_name') is-invalid @enderror"
                            value="{{ old('vendor_name') }}">
                        @error('vendor_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Invoice Number</label>
                        <input type="text" name="invoice_number"
                            class="form-control @error('invoice_number') is-invalid @enderror"
                            value="{{ old('invoice_number') }}">
                        @error('invoice_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5>Depreciation Settings</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Depreciation Method <span class="text-danger">*</span></label>
                        <select name="depreciation_method"
                            class="form-select @error('depreciation_method') is-invalid @enderror" required>
                            <option value="">Select Method</option>
                            <option value="straight_line" {{ old('depreciation_method')=='straight_line' ? 'selected'
                                : '' }}>Straight Line</option>
                            <option value="declining_balance" {{ old('depreciation_method')=='declining_balance'
                                ? 'selected' : '' }}>Declining Balance</option>
                            <option value="sum_of_years" {{ old('depreciation_method')=='sum_of_years' ? 'selected' : ''
                                }}>Sum of Years Digits</option>
                            <option value="units_of_production" {{ old('depreciation_method')=='units_of_production'
                                ? 'selected' : '' }}>Units of Production</option>
                            <option value="none" {{ old('depreciation_method')=='none' ? 'selected' : '' }}>None
                            </option>
                        </select>
                        @error('depreciation_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Useful Life (Years)</label>
                        <input type="number" name="useful_life_years"
                            class="form-control @error('useful_life_years') is-invalid @enderror"
                            value="{{ old('useful_life_years') }}">
                        @error('useful_life_years')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Months</label>
                        <input type="number" name="useful_life_months"
                            class="form-control @error('useful_life_months') is-invalid @enderror"
                            value="{{ old('useful_life_months') }}" min="0" max="11">
                        @error('useful_life_months')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Salvage Value</label>
                        <input type="number" step="0.01" name="salvage_value"
                            class="form-control @error('salvage_value') is-invalid @enderror"
                            value="{{ old('salvage_value') }}">
                        @error('salvage_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">Depreciation Rate (%)</label>
                        <input type="number" step="0.01" name="depreciation_rate"
                            class="form-control @error('depreciation_rate') is-invalid @enderror"
                            value="{{ old('depreciation_rate') }}">
                        @error('depreciation_rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>
                <h5>Location & Assignment</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                            value="{{ old('location') }}" placeholder="Office location, department, etc.">
                        @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assigned To</label>
                        <input type="text" name="assigned_to"
                            class="form-control @error('assigned_to') is-invalid @enderror"
                            value="{{ old('assigned_to') }}" placeholder="Employee name or department">
                        @error('assigned_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>Note:</strong> Asset number will be auto-generated as AST-YYYY-XXXXXX based on fiscal year.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.finance.assets.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Register Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection