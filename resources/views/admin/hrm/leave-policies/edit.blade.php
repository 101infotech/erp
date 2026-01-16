@extends('admin.layouts.app')

@section('content')
<div class="px-6 md:px-8 py-6">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-3 mb-2">
            <a href="{{ route('admin.hrm.leave-policies.index') }}" class="text-slate-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-white">Edit Leave Policy</h1>
        </div>
        <p class="text-slate-400 ml-9">Update leave policy settings</p>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.hrm.leave-policies.update', $leavePolicy) }}" method="POST"
        class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <!-- Company -->
            <div>
                <label for="company_id" class="block text-sm font-medium text-slate-300 mb-2">Company *</label>
                <select name="company_id" id="company_id" required
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $leavePolicy->company_id)==$company->id ?
                        'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Policy Name -->
            <div>
                <label for="policy_name" class="block text-sm font-medium text-slate-300 mb-2">Policy Name *</label>
                <input type="text" name="policy_name" id="policy_name"
                    value="{{ old('policy_name', $leavePolicy->policy_name) }}" required
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                    placeholder="e.g., Standard Annual Leave Policy">
            </div>

            <!-- Leave Type -->
            <div>
                <label for="leave_type" class="block text-sm font-medium text-slate-300 mb-2">Leave Type *</label>
                <select name="leave_type" id="leave_type" required
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <option value="">Select Leave Type</option>
                    <option value="annual" {{ old('leave_type', $leavePolicy->leave_type)=='annual' ? 'selected' : ''
                        }}>
                        Annual Leave</option>
                    <option value="sick" {{ old('leave_type', $leavePolicy->leave_type)=='sick' ? 'selected' : ''
                        }}>Sick
                        Leave</option>
                    <option value="casual" {{ old('leave_type', $leavePolicy->leave_type)=='casual' ? 'selected' : ''
                        }}>
                        Casual Leave</option>
                    <option value="unpaid" {{ old('leave_type', $leavePolicy->leave_type)=='unpaid' ? 'selected' : ''
                        }}>
                        Unpaid Leave</option>
                    <option value="period" {{ old('leave_type', $leavePolicy->leave_type)=='period' ? 'selected' : ''
                        }}>
                        Period Leave</option>
                </select>
            </div>

            <!-- Gender Restriction -->
            <div>
                <label for="gender_restriction" class="block text-sm font-medium text-slate-300 mb-2">Gender Restriction
                    *</label>
                <select name="gender_restriction" id="gender_restriction" required
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-lime-500 focus:border-transparent">
                    <option value="none" {{ old('gender_restriction', $leavePolicy->gender_restriction)=='none' ?
                        'selected' : '' }}>No Restriction (All)</option>
                    <option value="male" {{ old('gender_restriction', $leavePolicy->gender_restriction)=='male' ?
                        'selected' : '' }}>Male Only</option>
                    <option value="female" {{ old('gender_restriction', $leavePolicy->gender_restriction)=='female' ?
                        'selected' : '' }}>Female Only</option>
                </select>
                <p class="mt-1 text-xs text-slate-500">Restrict this policy to specific gender (e.g., period leave for
                    females)</p>
            </div>

            <!-- Annual Quota -->
            <div>
                <label for="annual_quota" class="block text-sm font-medium text-slate-300 mb-2">Annual Quota (Days)
                    *</label>
                <input type="number" name="annual_quota" id="annual_quota"
                    value="{{ old('annual_quota', $leavePolicy->annual_quota) }}" required min="0" step="0.5"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                    placeholder="e.g., 15">
                <p class="mt-1 text-xs text-slate-500">Total number of leave days allowed per year</p>
            </div>

            <!-- Carry Forward -->
            <div class="flex items-start space-x-3">
                <input type="checkbox" name="carry_forward_allowed" id="carry_forward_allowed" value="1" {{
                    old('carry_forward_allowed', $leavePolicy->carry_forward_allowed) ? 'checked' : '' }}
                class="mt-1 w-4 h-4 text-lime-500 bg-slate-900 border-slate-700 rounded focus:ring-lime-500"
                onchange="toggleCarryForward()">
                <div class="flex-1">
                    <label for="carry_forward_allowed" class="block text-sm font-medium text-slate-300">
                        Allow Carry Forward
                    </label>
                    <p class="text-xs text-slate-500">Allow unused leave days to be carried forward to next year</p>
                </div>
            </div>

            <!-- Max Carry Forward -->
            <div id="carry_forward_section" class="hidden">
                <label for="max_carry_forward" class="block text-sm font-medium text-slate-300 mb-2">Maximum Carry
                    Forward (Days)</label>
                <input type="number" name="max_carry_forward" id="max_carry_forward"
                    value="{{ old('max_carry_forward', $leavePolicy->max_carry_forward) }}" min="0" step="0.5"
                    class="w-full bg-slate-900 border border-slate-700 text-white rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-lime-500 focus:border-transparent"
                    placeholder="e.g., 5">
                <p class="mt-1 text-xs text-slate-500">Maximum days that can be carried forward</p>
            </div>

            <!-- Requires Approval -->
            <div class="flex items-start space-x-3">
                <input type="checkbox" name="requires_approval" id="requires_approval" value="1" {{
                    old('requires_approval', $leavePolicy->requires_approval) ? 'checked' : '' }}
                class="mt-1 w-4 h-4 text-lime-500 bg-slate-900 border-slate-700 rounded focus:ring-lime-500">
                <div class="flex-1">
                    <label for="requires_approval" class="block text-sm font-medium text-slate-300">
                        Requires Approval
                    </label>
                    <p class="text-xs text-slate-500">Leave requests must be approved by manager/admin</p>
                </div>
            </div>

            <!-- Is Active -->
            <div class="flex items-start space-x-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active',
                    $leavePolicy->is_active) ? 'checked' : '' }}
                class="mt-1 w-4 h-4 text-lime-500 bg-slate-900 border-slate-700 rounded focus:ring-lime-500">
                <div class="flex-1">
                    <label for="is_active" class="block text-sm font-medium text-slate-300">
                        Active Policy
                    </label>
                    <p class="text-xs text-slate-500">Enable this policy for use</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-slate-700">
            <a href="{{ route('admin.hrm.leave-policies.index') }}"
                class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition">
                Cancel
            </a>
            <button type="submit"
                class="px-4 py-2 bg-lime-500 text-slate-950 rounded-lg font-medium hover:bg-lime-400 transition">
                Update Policy
            </button>
        </div>
    </form>
</div>

<script>
    function toggleCarryForward() {
        const checkbox = document.getElementById('carry_forward_allowed');
        const section = document.getElementById('carry_forward_section');
        
        if (checkbox.checked) {
            section.classList.remove('hidden');
        } else {
            section.classList.add('hidden');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleCarryForward();
    });
</script>
@endsection