<!-- Confirmation Modal Component -->
<div x-data="{ 
    showModal: false, 
    modalTitle: '', 
    modalMessage: '', 
    modalType: 'danger',
    confirmAction: null,
    confirmButtonText: 'Confirm',
    cancelButtonText: 'Cancel'
}" @open-confirm-modal.window="
    showModal = true;
    modalTitle = $event.detail.title;
    modalMessage = $event.detail.message;
    modalType = $event.detail.type || 'danger';
    confirmAction = $event.detail.onConfirm;
    confirmButtonText = $event.detail.confirmText || 'Confirm';
    cancelButtonText = $event.detail.cancelText || 'Cancel';
">
    <!-- Modal Backdrop -->
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50" @click="showModal = false" style="display: none;">
    </div>

    <!-- Modal Dialog -->
    <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div @click.away="showModal = false"
                class="relative bg-slate-800 rounded-2xl shadow-2xl border border-slate-700 max-w-md w-full p-6">

                <!-- Icon -->
                <div class="mb-4 flex justify-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center" :class="{
                            'bg-red-500/20': modalType === 'danger',
                            'bg-yellow-500/20': modalType === 'warning',
                            'bg-blue-500/20': modalType === 'info',
                            'bg-lime-500/20': modalType === 'success'
                        }">
                        <!-- Danger Icon -->
                        <svg x-show="modalType === 'danger'" class="w-8 h-8 text-red-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <!-- Warning Icon -->
                        <svg x-show="modalType === 'warning'" class="w-8 h-8 text-yellow-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <!-- Info Icon -->
                        <svg x-show="modalType === 'info'" class="w-8 h-8 text-blue-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <!-- Success Icon -->
                        <svg x-show="modalType === 'success'" class="w-8 h-8 text-lime-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Title -->
                <h3 class="text-xl font-bold text-white text-center mb-2" x-text="modalTitle"></h3>

                <!-- Message -->
                <p class="text-slate-300 text-center mb-6" x-html="modalMessage"></p>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button @click="showModal = false"
                        class="flex-1 px-4 py-2.5 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition font-medium"
                        x-text="cancelButtonText">
                    </button>
                    <button @click="if(confirmAction) confirmAction(); showModal = false;"
                        class="flex-1 px-4 py-2.5 rounded-lg transition font-medium" :class="{
                            'bg-red-500 text-white hover:bg-red-600': modalType === 'danger',
                            'bg-yellow-500 text-slate-900 hover:bg-yellow-600': modalType === 'warning',
                            'bg-blue-500 text-white hover:bg-blue-600': modalType === 'info',
                            'bg-lime-500 text-slate-900 hover:bg-lime-600': modalType === 'success'
                        }" x-text="confirmButtonText">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Helper function to show confirmation modal
function confirmAction(options) {
    const event = new CustomEvent('open-confirm-modal', {
        detail: {
            title: options.title || 'Confirm Action',
            message: options.message || 'Are you sure you want to proceed?',
            type: options.type || 'danger',
            confirmText: options.confirmText || 'Confirm',
            cancelText: options.cancelText || 'Cancel',
            onConfirm: options.onConfirm || function() {}
        }
    });
    window.dispatchEvent(event);
}
</script>