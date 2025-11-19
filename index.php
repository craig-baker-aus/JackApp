<!DOCTYPE html>
<html>
<head>
    <title>Income Tracker</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .form-group { margin: 10px 0; }
        input, select { padding: 5px; }
        button { padding: 8px 15px; cursor: pointer; }
        .transaction-list { margin-top: 20px; }
        .transaction-item { border: 1px solid #ddd; padding: 10px; margin: 5px 0; }
    </style>
</head>
<!-- Create one database instance in memory for the session. Used instead of an SQL database. -->
<body onload="initDB()">
    <h1>Income Transaction Tracker</h1>
    
    <div>
        <div class="form-group">
            <input type="number" id="amount" placeholder="Amount" step="0.01" required value="7">
        </div>
        <div class="form-group">
            <input type="text" id="description" placeholder="Description" required value="Desc">
        </div>
        <div class="form-group">
            <input type="date" id="transactionDate" required value="<?php echo date('Y-m-d'); ?>">
        </div>
         <div class="form-group">
            <select id="transactionType">
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>
        </div>
       <div class="form-group">
            <select id="frequency">
                <option value="once">Once</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>
        <div class="form-group">
            <input type="number" id="numberRecurring" placeholder="Total repeats">
        </div>
        <button onclick="addTransaction()">Add Transaction</button>
        <button onclick="clearTransactions()">Clear</button>
   </div>
    
    <div class="transaction-list" id="transactionList"></div>
    
    <script src="transactions.js"></script>
</body>
</html>