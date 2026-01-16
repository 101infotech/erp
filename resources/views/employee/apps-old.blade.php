@extends('employee.layouts.app-launcher')

@section('title', 'My Apps')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-6 lg:px-8 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Welcome Section -->
        <div class="mb-12">
            <div class="mb-6">
                <h1
                    class="text-4xl lg:text-5xl font-bold text-white mb-3 bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">
                    Welcome, {{ explode(' ', Auth::user()->name)[0] }}
                </h1>
                <p class="text-lg text-slate-400">Choose an app to manage your work</p>
            </div>

            <!-- Search Bar -->
            <div class="relative max-w-2xl">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="moduleSearch" placeholder="Search for apps or features..."
                    class="w-full pl-12 pr-4 py-3.5 bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-lime-500 focus:ring-2 focus:ring-lime-500/20 transition-all text-sm">
            </div>
        </div>

        <!-- App Grid -->
        <div id="moduleGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($modules as $key => $module)
            @php
            $colors = config('modules.colors.' . $module['color']);
            @endphp
            <a href="{{ route($module['route']) }}" data-module-name="{{ strtolower($module['name']) }}"
                data-module-description="{{ strtolower($module['description']) }}" class="module-card group relative bg-gradient-to-br from-slate-800/80 to-slate-800/40 backdrop-blur-sm rounded-2xl p-8 border border-slate-700/50
                          hover:border-{{ $module['color'] }}-500/50 hover:shadow-2xl hover:shadow-{{ $module['color'] }}-500/10
                          transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">

                <!-- Gradient Overlay on Hover -->
                <div
                    class="absolute inset-0 bg-gradient-to-br from-{{ $module['color'] }}-500/0 to-{{ $module['color'] }}-500/0 group-hover:from-{{ $module['color'] }}-500/5 group-hover:to-{{ $module['color'] }}-500/10 transition-all duration-500 rounded-2xl">
                </div>

                <!-- Content -->
                <div class="relative z-10">
                    <!-- Module Icon -->
                    <div class="mb-6 {{ $colors['bg'] }} {{ $colors['border'] }} border-2 rounded-2xl w-16 h-16 flex items-center justify-center 
                                   transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg">
                        @include('components.icons.' . $module['icon'], ['class' => 'w-8 h-8 ' . $colors['text']])
                    </div>

                    <!-- Module Info -->
                    <div class="mb-6">
                        <h3
                            class="text-2xl font-bold text-white mb-3 group-hover:text-{{ $module['color'] }}-400 transition-colors">
                            {{ $module['name'] }}
                        </h3>
                        <p class="text-sm text-slate-400 leading-relaxed line-clamp-2">
                            {{ $module['description'] }}
                        </p>
                    </div>

                    <!-- Arrow Icon -->
                    <div class="flex items-center justify-end">
                        <div
                            class="opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300">
                            <svg class="w-5 h-5 {{ $colors['text'] }}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Shine Effect on Hover -->
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <!-- Quick Info Section -->
        <div class="mt-20 pt-12 border-t border-slate-700/30">
            <h2 class="text-2xl font-bold text-white mb-8 flex items-center">
                <span class="w-1 h-8 bg-lime-500 rounded-full mr-4"></span>
                Quick Overview
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Available Apps -->
                <div
                    class="group bg-gradient-to-br from-blue-500/10 to-blue-600/5 backdrop-blur-sm rounded-2xl p-6 border border-blue-500/20 hover:border-blue-500/40 transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm mb-2">Available Apps</p>
                    <h3 class="text-4xl font-bold text-white">{{ count($modules) }}</h3>
                </div>

                <!-- Today's Date -->
                <div
                    class="group bg-gradient-to-br from-lime-500/10 to-lime-600/5 backdrop-blur-sm rounded-2xl p-6 border border-lime-500/20 hover:border-lime-500/40 transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-lime-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm mb-2">Today</p>
                    <h3 class="text-2xl font-bold text-white">{{ now()->format('M d, Y') }}</h3>
                </div>

                <!-- Status -->
                <div
                    class="group bg-gradient-to-br from-green-500/10 to-green-600/5 backdrop-blur-sm rounded-2xl p-6 border border-green-500/20 hover:border-green-500/40 transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm mb-2">Status</p>
                    <h3 class="text-2xl font-bold text-green-400">Active</h3>
                </div>

                <!-- User Info -->
                <div
                    class="group bg-gradient-to-br from-purple-500/10 to-purple-600/5 backdrop-blur-sm rounded-2xl p-6 border border-purple-500/20 hover:border-purple-500/40 transition-all duration-300 hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-slate-400 text-sm mb-2">Role</p>
                    <h3 class="text-xl font-bold text-white">Employee</h3>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.moduleSearch = {
        init() {
            const searchInput = document.getElementById('moduleSearch');
            const moduleCards = document.querySelectorAll('.module-card');
            
            if (!searchInput) return;
            
            searchInput.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                
                moduleCards.forEach(card => {
                    const moduleName = card.dataset.moduleName || '';
                    const moduleDescription = card.dataset.moduleDescription || '';
                    
                    if (moduleName.includes(searchTerm) || moduleDescription.includes(searchTerm)) {
                        card.style.display = 'block';
                        card.style.animation = 'fadeIn 0.3s ease-in-out';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    };
    
    // Add fadeIn animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.moduleSearch.init());
    } else {
        window.moduleSearch.init();
    }
</script>
@endpush
@endsection
</div>
</div>
</div>
</div>

<!-- Help Section -->
<div class="mt-12 bg-gradient-to-r from-lime-500/10 to-lime-600/10 border border-lime-500/20 rounded-2xl p-8">
    <div class="flex items-start gap-6">
        <div class="w-12 h-12 bg-lime-500/20 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-lime-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="flex-1">
            <h3 class="text-xl font-bold text-white mb-2">Need Help?</h3>
            <p class="text-slate-300 mb-4">If you have any questions or need assistance with any of the apps, please
                contact your HR department or IT support team.</p>
            <a href="{{ route('employee.complaints.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-lime-500 hover:bg-lime-600 text-white rounded-lg transition">
                Submit a Query
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </div>
    </div>
</div>
</div>
@endsection