    async function initDB() {
        const formData = new FormData();
        formData.append('action', 'init');
        
        await fetch('income.php', { method: 'POST', body: formData });
    }

    async function addTransaction() {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('amount', document.getElementById('amount').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('date', document.getElementById('transactionDate').value);
        formData.append('transactionType', document.getElementById('transactionType').value);
        formData.append('frequency', document.getElementById('frequency').value);

        if (document.getElementById('frequency').value == 'once') {
            formData.append('numberRecurring', 1);
        } else if (document.getElementById('numberRecurring').value == "") {
                formData.append('numberRecurring', 2); // Default to 2 if not specified. Not ideal
            } else {
                formData.append('numberRecurring', document.getElementById('numberRecurring').value);
            }

        await fetch('income.php', { method: 'POST', body: formData });
        loadTransactions();
    }

    async function clearTransactions() {
        const formData = new FormData();
        formData.append('action', 'clear');
        await fetch('income.php', { method: 'POST', body: formData });
        loadTransactions();
    }  
    
    async function loadTransactions() {
        const formData = new FormData();
        formData.append('action', 'get');
        const response = await fetch('income.php', { method: 'POST', body: formData });
        const transactions = await response.json();
        
        let html = '<h2>Transactions</h2>';
        for (const [id, trans] of Object.entries(transactions)) {
            html += `<div class="transaction-item">
                ${trans.transactionType}: ${trans.description} - $${trans.amount} (${trans.transactionDate}) [${trans.frequency}]
                <button onclick="deleteTransaction('${id}')">Delete</button>
            </div>`;
        }
        document.getElementById('transactionList').innerHTML = html;
    }
    
    async function deleteTransaction(id) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        await fetch('income.php', { method: 'POST', body: formData });
        loadTransactions();
    }
