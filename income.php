<?php

include "transaction.php";
include "transactionDatabase.php";


session_start();
/* Use the session to persist the database across requests. Initialise the database if this is the first access of the session. */
$staticDB = $_SESSION['database'] ?? new TransactionDatabase();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: text/html');

    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $transaction = new Transaction(
            $_POST['amount'],
            $_POST['description'],
            $_POST['transactionType'],
            $_POST['date'],
            $_POST['frequency'] 
        );
        
        $staticDB->addTransaction(false, $transaction, $_POST['numberRecurring']);
       /*if ($transaction->getFrequency() !== 'once') {
            $staticDB->generateRecurringTransactions($transaction, 3);
        } else {
            $staticDB->addTransaction($transaction);
        }  */      
        echo json_encode(['success' => true]);
    } elseif ($action === 'init') {
        /* On the init action, clear the session database. 
        This is necessary because the session variable persists across runs on the Visual Studio Code built-in server. */
        $_SESSION['database'] = null;
        $staticDB = $_SESSION['database'];
    } elseif ($action === 'get') {
        echo json_encode($staticDB->getTransactions());
    } elseif ($action === 'delete') {
        $staticDB->deleteTransaction($_POST['id']);
        echo json_encode(['success' => true]);
    } elseif ($action === 'clear') {
        $staticDB->clear();
        echo json_encode(['success' => true]);
    }
    $_SESSION['database'] = $staticDB;
    exit;
}
?>

