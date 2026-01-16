<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Main Content -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('employee.dashboard') }}"
                    class="inline-flex items-center text-lime-400 hover:text-lime-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">My Profile</h1>
                <p class="text-slate-400">Manage your profile information and settings</p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
            <div class="mb-6 bg-lime-500/10 border border-lime-500/30 rounded-lg p-4">
                <p class="text-lime-400">{{ session('success') }}</p>
            </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
            <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-lg p-4">
                <p class="text-red-400">{{ session('error') }}</p>
            </div>
            @endif

            <!-- Profile Avatar Section -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700 mb-6">
                <h2 class="text-xl font-bold text-white mb-6">Profile Picture</h2>

                <div class="flex items-center space-x-6">
                    <!-- Avatar Preview -->
                    <div class="relative">
                        @if($employee->avatar)
                        <img id="avatar-preview" src="{{ asset('storage/' . $employee->avatar) }}" alt="Profile Picture"
                            class="w-32 h-32 rounded-full object-cover border-4 border-lime-400">
                        @else
                        <div id="avatar-preview"
                            class="w-32 h-32 bg-lime-400 rounded-full flex items-center justify-center border-4 border-lime-500">
                            <span class="text-slate-900 font-bold text-4xl">{{ substr($employee->full_name, 0, 2)
                                }}</span>
                        </div>
                        @endif

                        <!-- Loading Spinner (hidden by default) -->
                        <div id="upload-spinner" style="display: none;"
                            class="absolute inset-0 bg-slate-900/70 rounded-full flex items-center justify-center">
                            <svg class="animate-spin h-8 w-8 text-lime-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <!-- Upload Controls -->
                    <div class="flex-1">
                        <p class="text-slate-300 mb-4">Upload a new profile picture. JPG, PNG or GIF (max 2MB).</p>

                        <div class="flex items-center space-x-3">
                            <label for="avatar-input"
                                class="px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 rounded-lg cursor-pointer font-medium transition">
                                Choose Photo
                            </label>
                            <input type="file" id="avatar-input" accept="image/*" class="hidden">

                            @if($employee->avatar)
                            <button type="button" id="remove-avatar"
                                class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg font-medium transition">
                                Remove
                            </button>
                            @endif
                        </div>

                        <!-- Upload Status Message -->
                        <div id="upload-message" class="mt-3 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Profile Information Section -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700 mb-6">
                <h2 class="text-xl font-bold text-white mb-6">Personal Information</h2>

                <!-- Read-only Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Full Name</label>
                        <div class="px-4 py-3 bg-slate-700/30 rounded-lg text-white">{{ $employee->full_name }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Email</label>
                        <div class="px-4 py-3 bg-slate-700/30 rounded-lg text-white">{{ $employee->email }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Employee Code</label>
                        <div class="px-4 py-3 bg-slate-700/30 rounded-lg text-white">{{ $employee->code }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Position</label>
                        <div class="px-4 py-3 bg-slate-700/30 rounded-lg text-white">{{ $employee->position ?? 'Not
                            specified' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Department</label>
                        <div class="px-4 py-3 bg-slate-700/30 rounded-lg text-white">{{ $employee->department->name ??
                            'Not assigned' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Join Date</label>
                        <div class="px-4 py-3 bg-slate-700/30 rounded-lg text-white">{{ $employee->hire_date ?
                            $employee->hire_date->format('M d, Y') : 'Not specified' }}</div>
                    </div>
                </div>

                <!-- Editable Form -->
                <form method="POST" action="{{ route('employee.profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-400 mb-2">Phone
                                Number</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}"
                                class="w-full px-4 py-3 bg-slate-700/30 border border-slate-600 rounded-lg text-white focus:border-lime-400 focus:ring-1 focus:ring-lime-400">
                            @error('phone')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-slate-400 mb-2">Address</label>
                            <input type="text" id="address" name="address"
                                value="{{ old('address', $employee->address) }}"
                                class="w-full px-4 py-3 bg-slate-700/30 border border-slate-600 rounded-lg text-white focus:border-lime-400 focus:ring-1 focus:ring-lime-400">
                            @error('address')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-white mb-4">Emergency Contact</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="emergency_contact_name"
                                class="block text-sm font-medium text-slate-400 mb-2">Contact Name</label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                                class="w-full px-4 py-3 bg-slate-700/30 border border-slate-600 rounded-lg text-white focus:border-lime-400 focus:ring-1 focus:ring-lime-400">
                            @error('emergency_contact_name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="emergency_contact_phone"
                                class="block text-sm font-medium text-slate-400 mb-2">Contact Phone</label>
                            <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
                                value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                                class="w-full px-4 py-3 bg-slate-700/30 border border-slate-600 rounded-lg text-white focus:border-lime-400 focus:ring-1 focus:ring-lime-400">
                            @error('emergency_contact_phone')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="emergency_contact_relationship"
                                class="block text-sm font-medium text-slate-400 mb-2">Relationship</label>
                            <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship"
                                value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}"
                                placeholder="e.g., Spouse, Parent, Sibling"
                                class="w-full px-4 py-3 bg-slate-700/30 border border-slate-600 rounded-lg text-white focus:border-lime-400 focus:ring-1 focus:ring-lime-400">
                            @error('emergency_contact_relationship')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 bg-lime-500 hover:bg-lime-600 text-slate-900 rounded-lg font-semibold transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Leave Requests Section -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">Recent Leave Requests</h2>
                    <a href="{{ route('employee.leave.index') }}"
                        class="text-lime-400 hover:text-lime-300 text-sm font-medium">
                        View All →
                    </a>
                </div>

                @php
                $recentLeaves = \App\Models\HrmLeaveRequest::where('employee_id', $employee->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                @endphp

                @if($recentLeaves->count() > 0)
                <div class="space-y-3">
                    @foreach($recentLeaves as $leave)
                    <div
                        class="bg-slate-700/30 rounded-lg p-4 border border-slate-600 hover:border-slate-500 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $leave->leave_type === 'sick' ? 'bg-red-500/20 text-red-400' : '' }}
                                        {{ $leave->leave_type === 'casual' ? 'bg-blue-500/20 text-blue-400' : '' }}
                                        {{ $leave->leave_type === 'annual' ? 'bg-green-500/20 text-green-400' : '' }}
                                        {{ $leave->leave_type === 'unpaid' ? 'bg-slate-500/20 text-slate-400' : '' }}">
                                        {{ ucfirst($leave->leave_type) }}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $leave->status === 'approved' ? 'bg-lime-500/20 text-lime-400' : '' }}
                                        {{ $leave->status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                                        {{ $leave->status === 'rejected' ? 'bg-red-500/20 text-red-400' : '' }}">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </div>
                                <p class="text-slate-300 text-sm mb-1">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} -
                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                    <span class="text-slate-500">({{ $leave->total_days }} {{ Str::plural('day',
                                        $leave->total_days) }})</span>
                                </p>
                                @if($leave->reason)
                                <p class="text-slate-400 text-xs line-clamp-1">{{ $leave->reason }}</p>
                                @endif
                            </div>
                            <div class="text-right text-xs text-slate-500">
                                {{ $leave->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-slate-400">No leave requests yet</p>
                    <a href="{{ route('employee.leave.create') }}"
                        class="inline-block mt-3 text-lime-400 hover:text-lime-300 text-sm">
                        Request Leave →
                    </a>
                </div>
                @endif
            </div>

            <!-- Compensation Details Section (Read-only) -->
            <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-6 border border-slate-700 mb-6">
                <h2 class="text-xl font-bold text-white mb-6">Compensation Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Base Salary</label>
                        <div class="px-4 py-3 bg-slate-700/30 rounded-lg text-white">
                            NPR {{ number_format($employee->base_salary ?? 0, 2) }}
                        </div>
                    </div>

                    @if($employee->allowances && is_array($employee->allowances) && count($employee->allowances) > 0)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Allowances</label>
                        <div class="bg-slate-700/30 rounded-lg p-4">
                            <div class="space-y-2">
                                @foreach($employee->allowances as $allowance)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-300">{{ $allowance['name'] ?? 'Allowance' }}</span>
                                    <span class="text-lime-400 font-medium">+ NPR {{ number_format($allowance['amount']
                                        ?? 0, 2) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($employee->deductions && is_array($employee->deductions) && count($employee->deductions) > 0)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Deductions</label>
                        <div class="bg-slate-700/30 rounded-lg p-4">
                            <div class="space-y-2">
                                @foreach($employee->deductions as $deduction)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-300">{{ $deduction['name'] ?? 'Deduction' }}</span>
                                    <span class="text-red-400 font-medium">- NPR {{ number_format($deduction['amount']
                                        ?? 0, 2) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if((!$employee->allowances || count($employee->allowances) == 0) && (!$employee->deductions ||
                    count($employee->deductions) == 0))
                    <div class="md:col-span-2">
                        <p class="text-slate-500 text-sm text-center py-4">No additional allowances or deductions
                            configured</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    @push('scripts')
    <script>
        window.profileAvatar = {
            init() {
                const avatarInput = document.getElementById('avatar-input');
                const removeAvatarBtn = document.getElementById('remove-avatar');
                
                if (avatarInput) {
                    avatarInput.addEventListener('change', async (e) => {
                        await this.handleAvatarChange(e);
                    });
                }
                
                if (removeAvatarBtn) {
                    removeAvatarBtn.addEventListener('click', () => {
                        window.dispatchEvent(new CustomEvent('open-confirm', { detail: 'remove-avatar' }));
                    });
                }
            },
            
            async handleAvatarChange(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                const avatarInput = document.getElementById('avatar-input');
                const avatarPreview = document.getElementById('avatar-preview');
                const uploadSpinner = document.getElementById('upload-spinner');
                const uploadMessage = document.getElementById('upload-message');
                
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    this.showMessage('File size must be less than 2MB', 'error');
                    avatarInput.value = '';
                    return;
                }
                
                // Show loading
                uploadSpinner.style.display = 'flex';
                
                const formData = new FormData();
                formData.append('avatar', file);
                
                try {
                    const response = await window.axios.post('{{ route("employee.profile.avatar.upload") }}', formData);
                    const data = response.data;
                    
                    if (data.success) {
                        // Update preview
                        if (avatarPreview.tagName === 'IMG') {
                            avatarPreview.src = data.avatar_url;
                        } else {
                            const img = document.createElement('img');
                            img.id = 'avatar-preview';
                            img.src = data.avatar_url;
                            img.alt = 'Profile Picture';
                            img.className = 'w-32 h-32 rounded-full object-cover border-4 border-lime-400';
                            avatarPreview.parentNode.replaceChild(img, avatarPreview);
                        }
                        
                        this.showMessage(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        this.showMessage(data.error || 'Upload failed', 'error');
                    }
                } catch (error) {
                    this.showMessage('An error occurred during upload', 'error');
                } finally {
                    uploadSpinner.style.display = 'none';
                    avatarInput.value = '';
                }
            },
            
            async removeAvatar() {
                const uploadSpinner = document.getElementById('upload-spinner');
                uploadSpinner.style.display = 'flex';
                
                try {
                    const response = await window.axios.delete('{{ route("employee.profile.avatar.delete") }}');
                    const data = response.data;
                    
                    if (data.success) {
                        this.showMessage(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        this.showMessage(data.error || 'Failed to remove avatar', 'error');
                    }
                } catch (error) {
                    this.showMessage('An error occurred', 'error');
                } finally {
                    uploadSpinner.style.display = 'none';
                }
            },
            
            showMessage(message, type) {
                const uploadMessage = document.getElementById('upload-message');
                uploadMessage.textContent = message;
                uploadMessage.className = `mt-3 p-3 rounded-lg text-sm ${
                    type === 'success' 
                        ? 'bg-lime-500/10 text-lime-400 border border-lime-500/30' 
                        : 'bg-red-500/10 text-red-400 border border-red-500/30'
                }`;
                uploadMessage.classList.remove('hidden');
                
                setTimeout(() => {
                    uploadMessage.classList.add('hidden');
                }, 5000);
            }
        };
        
        window.confirmRemoveAvatar = function() {
            window.profileAvatar.removeAvatar();
        };
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => window.profileAvatar.init());
        } else {
            window.profileAvatar.init();
        }
    </script>

    <!-- Remove Avatar Confirmation Dialog -->
    <x-confirm-dialog name="remove-avatar" title="Remove Profile Picture"
        message="Are you sure you want to remove your profile picture?" type="danger" confirmText="Remove"
        onConfirm="confirmRemoveAvatar()" />

    @endpush
</x-app-layout>