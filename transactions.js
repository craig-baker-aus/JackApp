// Callbacks for various buttons and actions in the cash flow forecasting application.
// Updates the main page with results from operations. Very basic interface for demonstration purposes.
// Grouped in a single file for organisation and to improve debugging.

async function initDB() {
        const formData = new FormData();
        formData.append('action', 'init');
        
        await fetch('cashflowForecastingAPI.php', { method: 'POST', body: formData });
    }

    async function generateCashFlow() {
        const formData = new FormData();
        formData.append('action', 'cashFlow');
        if (document.getElementById('numberMonths').value == "") {
            formData.append('numberMonths', 2); // Default to 2 if not specified. Not ideal but provides simple error handling.
        } else {
            formData.append('numberMonths', document.getElementById('numberMonths').value);
        }
       
        const response = await fetch('cashflowForecastingAPI.php', { method: 'POST', body: formData });
        const cashFlowInfo = await response.json();
        const formattedOpeningBalance = formatCurrency(cashFlowInfo.OpeningBalance.balance);
        const formattedClosingBalance = formatCurrency(cashFlowInfo.ClosingBalance.balance);
        
        let html = '<h2>Projected Cash Flow</h2>';
        html += `<h4>Starting Balance: ${cashFlowInfo.OpeningBalance.finalDate} ${formattedOpeningBalance}</h4>`;
        html += '<h3>Income</h3>';
        for (const [date, summary] of Object.entries(cashFlowInfo.Income)) {
            let formattedTotal = formatCurrency(summary.total);
            html += `<div>${date}: ${summary.type} - ${formattedTotal} (Balance: ${formatCurrency(summary.balance)})</div>`;
        }
        html += '<h3>Expenses</h3>';
        for (const [date, summary] of Object.entries(cashFlowInfo.Expenses)) {
            let formattedTotal = formatCurrency(summary.total);
            html += `<div>${date}: ${summary.type} - ${formattedTotal} (Balance: ${formatCurrency(summary.balance)})</div>`;
        }
        html += `<h4>Closing Balance: ${cashFlowInfo.ClosingBalance.finalDate} ${formattedClosingBalance}</h4>`;
        document.getElementById('cashFlowList').innerHTML = html;
        document.getElementById('balanceDisplay').innerHTML = "";
        document.getElementById('transactionList').innerHTML = "";
    }

    async function loadTransactions() {
        const formData = new FormData();
        formData.append('action', 'transactions');
        // To provide a specific action, we must use POST here instead of GET.
        const response = await fetch('cashflowForecastingAPI.php', { method: 'POST', body: formData });
        const transactions = await response.json();
        
        let html = '<h2>Transactions</h2>';
        for (const [id, trans] of Object.entries(transactions)) {
            let formattedAmount = formatCurrency(trans.amount);
            html += `<div class="transaction-item">
                ${trans.transactionType}: ${trans.description} - ${formattedAmount} (${trans.transactionDate}) [${trans.frequency}]
                <button onclick="deleteTransaction('${id}')">Delete</button>
            </div>`;
        }
        document.getElementById('transactionList').innerHTML = html;
        document.getElementById('balanceDisplay').innerHTML = "";
        document.getElementById('cashFlowList').innerHTML = "";
    }
    
    async function getBalance(projectedDate) {
        const formData = new FormData();
        formData.append('action', 'balance');
        formData.append('projectedDate', projectedDate);
       
        const response = await fetch('cashflowForecastingAPI.php', { method: 'POST', body: formData });
        const balanceInfo = await response.json();
        const formattedBalance = formatCurrency(balanceInfo.balance);
        
        let html = '<h2>Current Balance</h2>';
        html += `<div>${balanceInfo.finalDate}: ${formattedBalance}</div>`;
        document.getElementById('balanceDisplay').innerHTML = html;
        document.getElementById('transactionList').innerHTML = "";
        document.getElementById('cashFlowList').innerHTML = "";
   }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-AU', { style: 'currency', currency: 'AUD' }).format(amount);
    }

    async function addTransaction() {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('amount', document.getElementById('amount').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('transactionDate', document.getElementById('transactionDate').value);
        formData.append('transactionType', document.getElementById('transactionType').value);
        formData.append('frequency', document.getElementById('frequency').value);

        if (document.getElementById('frequency').value == 'once') {
            formData.append('numberRecurring', 1);
        } else {
            if (document.getElementById('numberRecurring').value == "") {
                formData.append('numberRecurring', 2); // Default to 2 if not specified. Not ideal but simplifies error handling.
            } else {
                formData.append('numberRecurring', document.getElementById('numberRecurring').value);
            }
        }
        await fetch('cashflowForecastingAPI.php', { method: 'POST', body: formData });
        loadTransactions();
    }

    async function clearTransactions() {
        const formData = new FormData();
        formData.append('action', 'clear');
        await fetch('cashflowForecastingAPI.php', { method: 'PUT', body: formData });
        loadTransactions();
    }  
    
    async function deleteTransaction(id) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        await fetch('cashflowForecastingAPI.php', { method: 'POST', body: formData });
        loadTransactions();
    }
