<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Module Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-slate-950 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Finance Module API Test</h1>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Test Dashboard KPIs</h2>
            <button onclick="testKPIs()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Test KPIs Endpoint
            </button>
            <pre id="kpi-result"
                class="mt-4 bg-gray-50 dark:bg-slate-700 p-4 rounded text-sm overflow-auto max-h-96 text-gray-900 dark:text-gray-100"></pre>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Test Companies</h2>
            <button onclick="testCompanies()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Test Companies Endpoint
            </button>
            <pre id="companies-result"
                class="mt-4 bg-gray-50 dark:bg-slate-700 p-4 rounded text-sm overflow-auto max-h-96 text-gray-900 dark:text-gray-100"></pre>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Test Transactions</h2>
            <button onclick="testTransactions()" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                Test Transactions Endpoint
            </button>
            <pre id="transactions-result"
                class="mt-4 bg-gray-50 dark:bg-slate-700 p-4 rounded text-sm overflow-auto max-h-96 text-gray-900 dark:text-gray-100"></pre>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Test Reports</h2>
            <select id="report-type"
                class="border dark:border-slate-600 rounded px-3 py-2 mb-4 bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                <option value="profit-loss">Profit & Loss</option>
                <option value="balance-sheet">Balance Sheet</option>
                <option value="cash-flow">Cash Flow</option>
                <option value="trial-balance">Trial Balance</option>
                <option value="expense-summary">Expense Summary</option>
                <option value="consolidated">Consolidated</option>
            </select>
            <button onclick="testReport()" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                Test Report Endpoint
            </button>
            <pre id="report-result"
                class="mt-4 bg-gray-50 dark:bg-slate-700 p-4 rounded text-sm overflow-auto max-h-96 text-gray-900 dark:text-gray-100"></pre>
        </div>
    </div>

    <script>
        async function testKPIs() {
            const resultDiv = document.getElementById('kpi-result');
            resultDiv.textContent = 'Loading...';
            
            try {
                const response = await fetch('/api/v1/finance/dashboard/kpis', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                resultDiv.textContent = JSON.stringify(data, null, 2);
                
                if (response.ok) {
                    resultDiv.classList.add('text-green-700');
                } else {
                    resultDiv.classList.add('text-red-700');
                }
            } catch (error) {
                resultDiv.textContent = 'Error: ' + error.message;
                resultDiv.classList.add('text-red-700');
            }
        }

        async function testCompanies() {
            const resultDiv = document.getElementById('companies-result');
            resultDiv.textContent = 'Loading...';
            
            try {
                const response = await fetch('/api/v1/finance/companies?page=1&per_page=10', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                resultDiv.textContent = JSON.stringify(data, null, 2);
                
                if (response.ok) {
                    resultDiv.classList.add('text-green-700');
                } else {
                    resultDiv.classList.add('text-red-700');
                }
            } catch (error) {
                resultDiv.textContent = 'Error: ' + error.message;
                resultDiv.classList.add('text-red-700');
            }
        }

        async function testTransactions() {
            const resultDiv = document.getElementById('transactions-result');
            resultDiv.textContent = 'Loading...';
            
            try {
                const response = await fetch('/api/v1/finance/transactions?page=1&per_page=10', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                resultDiv.textContent = JSON.stringify(data, null, 2);
                
                if (response.ok) {
                    resultDiv.classList.add('text-green-700');
                } else {
                    resultDiv.classList.add('text-red-700');
                }
            } catch (error) {
                resultDiv.textContent = 'Error: ' + error.message;
                resultDiv.classList.add('text-red-700');
            }
        }

        async function testReport() {
            const resultDiv = document.getElementById('report-result');
            const reportType = document.getElementById('report-type').value;
            resultDiv.textContent = 'Loading...';
            
            try {
                const response = await fetch(`/api/v1/finance/reports/${reportType}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                resultDiv.textContent = JSON.stringify(data, null, 2);
                
                if (response.ok) {
                    resultDiv.classList.add('text-green-700');
                } else {
                    resultDiv.classList.add('text-red-700');
                }
            } catch (error) {
                resultDiv.textContent = 'Error: ' + error.message;
                resultDiv.classList.add('text-red-700');
            }
        }
    </script>
</body>

</html>