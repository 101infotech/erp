@extends('admin.layouts.app')

@section('title', 'Financial Reports')
@section('page-title', 'Financial Reports')

@section('content')
<div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 -m-8 p-8 min-h-screen">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Financial Reports</h1>
        <p class="text-slate-400">Generate comprehensive financial reports for your business</p>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Profit & Loss Report -->
        <div
            class="bg-gradient-to-br from-blue-500/20 to-blue-600/10 rounded-2xl p-6 border border-blue-500/30 hover:border-blue-500/50 transition group">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-blue-300 px-3 py-1 bg-black/20 rounded-full">#P&L</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Profit & Loss</h3>
            <p class="text-sm text-blue-200/80 mb-4">Revenue and expense breakdown with net profit calculation</p>

            <div class="flex gap-2">
                <button onclick="viewReport('profit-loss')"
                    class="flex-1 px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg text-sm font-medium transition">
                    View
                </button>
                <button onclick="downloadPDF('profit-loss')"
                    class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg text-sm font-medium transition">
                    PDF
                </button>
                <button onclick="downloadExcel('profit-loss')"
                    class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg text-sm font-medium transition">
                    Excel
                </button>
            </div>
        </div>

        <!-- Balance Sheet Report -->
        <div
            class="bg-gradient-to-br from-purple-500/20 to-purple-600/10 rounded-2xl p-6 border border-purple-500/30 hover:border-purple-500/50 transition group">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-purple-300 px-3 py-1 bg-black/20 rounded-full">#Balance</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Balance Sheet</h3>
            <p class="text-sm text-purple-200/80 mb-4">Assets, liabilities, and equity snapshot</p>

            <div class="flex gap-2">
                <button onclick="viewReport('balance-sheet')"
                    class="flex-1 px-4 py-2 bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 rounded-lg text-sm font-medium transition">
                    View
                </button>
                <button onclick="downloadPDF('balance-sheet')"
                    class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg text-sm font-medium transition">
                    PDF
                </button>
                <button onclick="downloadExcel('balance-sheet')"
                    class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg text-sm font-medium transition">
                    Excel
                </button>
            </div>
        </div>

        <!-- Cash Flow Report -->
        <div
            class="bg-gradient-to-br from-green-500/20 to-green-600/10 rounded-2xl p-6 border border-green-500/30 hover:border-green-500/50 transition group">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-12 h-12 bg-green-500/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-green-300 px-3 py-1 bg-black/20 rounded-full">#CashFlow</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Cash Flow</h3>
            <p class="text-sm text-green-200/80 mb-4">Operating, investing, and financing activities</p>

            <div class="flex gap-2">
                <button onclick="viewReport('cash-flow')"
                    class="flex-1 px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg text-sm font-medium transition">
                    View
                </button>
                <button onclick="downloadPDF('cash-flow')"
                    class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg text-sm font-medium transition">
                    PDF
                </button>
                <button onclick="downloadExcel('cash-flow')"
                    class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg text-sm font-medium transition">
                    Excel
                </button>
            </div>
        </div>

        <!-- Trial Balance Report -->
        <div
            class="bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 rounded-2xl p-6 border border-yellow-500/30 hover:border-yellow-500/50 transition group">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-12 h-12 bg-yellow-500/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-yellow-300 px-3 py-1 bg-black/20 rounded-full">#Trial</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Trial Balance</h3>
            <p class="text-sm text-yellow-200/80 mb-4">Debit and credit balances for all accounts</p>

            <div class="flex gap-2">
                <button onclick="viewReport('trial-balance')"
                    class="flex-1 px-4 py-2 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-300 rounded-lg text-sm font-medium transition">
                    View
                </button>
                <button onclick="downloadPDF('trial-balance')"
                    class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg text-sm font-medium transition">
                    PDF
                </button>
                <button onclick="downloadExcel('trial-balance')"
                    class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg text-sm font-medium transition">
                    Excel
                </button>
            </div>
        </div>

        <!-- Expense Summary Report -->
        <div
            class="bg-gradient-to-br from-red-500/20 to-red-600/10 rounded-2xl p-6 border border-red-500/30 hover:border-red-500/50 transition group">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-red-300 px-3 py-1 bg-black/20 rounded-full">#Expenses</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Expense Summary</h3>
            <p class="text-sm text-red-200/80 mb-4">Monthly and category-wise expense analysis</p>

            <div class="flex gap-2">
                <button onclick="viewReport('expense-summary')"
                    class="flex-1 px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg text-sm font-medium transition">
                    View
                </button>
                <button onclick="downloadPDF('expense-summary')"
                    class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg text-sm font-medium transition">
                    PDF
                </button>
                <button onclick="downloadExcel('expense-summary')"
                    class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg text-sm font-medium transition">
                    Excel
                </button>
            </div>
        </div>

        <!-- Consolidated Report -->
        <div
            class="bg-gradient-to-br from-cyan-500/20 to-cyan-600/10 rounded-2xl p-6 border border-cyan-500/30 hover:border-cyan-500/50 transition group">
            <div class="flex items-center justify-between mb-4">
                <div
                    class="w-12 h-12 bg-cyan-500/20 rounded-full flex items-center justify-center group-hover:scale-110 transition">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-cyan-300 px-3 py-1 bg-black/20 rounded-full">#Multi</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Consolidated Report</h3>
            <p class="text-sm text-cyan-200/80 mb-4">Multi-company consolidated financials</p>

            <div class="flex gap-2">
                <button onclick="viewReport('consolidated')"
                    class="flex-1 px-4 py-2 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 rounded-lg text-sm font-medium transition">
                    View
                </button>
                <button onclick="downloadPDF('consolidated')"
                    class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg text-sm font-medium transition">
                    PDF
                </button>
                <button onclick="downloadExcel('consolidated')"
                    class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-lg text-sm font-medium transition">
                    Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Report Parameters Modal -->
    <div id="report-modal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-slate-800 rounded-2xl p-8 max-w-md w-full mx-4 border border-slate-700">
            <h3 class="text-2xl font-bold text-white mb-6" id="modal-title">Generate Report</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Company</label>
                    <select id="modal-company"
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-lime-500 focus:ring-1 focus:ring-lime-500">
                        <option value="">All Companies</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Start Date</label>
                    <input type="date" id="modal-start-date"
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-lime-500 focus:ring-1 focus:ring-lime-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">End Date</label>
                    <input type="date" id="modal-end-date"
                        class="w-full px-4 py-2 bg-slate-900 border border-slate-700 rounded-lg text-white focus:border-lime-500 focus:ring-1 focus:ring-lime-500">
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button onclick="closeModal()"
                    class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition">
                    Cancel
                </button>
                <button onclick="generateReport()"
                    class="flex-1 px-4 py-2 bg-lime-500 hover:bg-lime-600 text-slate-900 rounded-lg font-medium transition">
                    Generate
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentReportType = '';
    let currentAction = '';

    // Load companies on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadCompaniesForModal();
        setDefaultDates();
    });

    function setDefaultDates() {
        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        
        document.getElementById('modal-start-date').value = firstDayOfMonth.toISOString().split('T')[0];
        document.getElementById('modal-end-date').value = today.toISOString().split('T')[0];
    }

    function viewReport(reportType) {
        currentReportType = reportType;
        currentAction = 'view';
        document.getElementById('modal-title').textContent = `Generate ${formatReportName(reportType)}`;
        document.getElementById('report-modal').classList.remove('hidden');
    }

    function downloadPDF(reportType) {
        currentReportType = reportType;
        currentAction = 'pdf';
        document.getElementById('modal-title').textContent = `Download ${formatReportName(reportType)} (PDF)`;
        document.getElementById('report-modal').classList.remove('hidden');
    }

    function downloadExcel(reportType) {
        currentReportType = reportType;
        currentAction = 'excel';
        document.getElementById('modal-title').textContent = `Download ${formatReportName(reportType)} (Excel)`;
        document.getElementById('report-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('report-modal').classList.add('hidden');
    }

    function generateReport() {
        const company = document.getElementById('modal-company').value;
        const startDate = document.getElementById('modal-start-date').value;
        const endDate = document.getElementById('modal-end-date').value;

        let url = `/api/v1/finance/reports/${currentReportType}`;
        
        if (currentAction === 'pdf') {
            url += '/pdf';
        } else if (currentAction === 'excel') {
            url += '/excel';
        }

        const params = new URLSearchParams();
        if (company) params.append('company_id', company);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        if (params.toString()) {
            url += '?' + params.toString();
        }

        if (currentAction === 'view') {
            // For JSON view, open in new tab or display in modal
            window.open(url, '_blank');
        } else {
            // For PDF/Excel, trigger download
            window.location.href = url;
        }

        closeModal();
    }

    function formatReportName(reportType) {
        const names = {
            'profit-loss': 'Profit & Loss',
            'balance-sheet': 'Balance Sheet',
            'cash-flow': 'Cash Flow',
            'trial-balance': 'Trial Balance',
            'expense-summary': 'Expense Summary',
            'consolidated': 'Consolidated Report'
        };
        return names[reportType] || reportType;
    }

    function loadCompaniesForModal() {
        fetch('/api/v1/finance/companies?page=1&per_page=100', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const select = document.getElementById('modal-company');
                data.data.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company.id;
                    option.textContent = company.name;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading companies:', error));
    }
</script>
@endpush
@endsection