<?php

include "transaction.php";
include "transactionDatabase.php";

class CashflowForecastingAPI {
    private $db;
    
    public function __construct() {
        $this->db = $_SESSION['database'] ?? new TransactionDatabase();
    }
    
     public function handleRequest($method, $data) {
        //header('Content-Type: text/html'); //Do I need this?
        $action = $data["action"];
        if ($method === 'POST') {
            if ($action === 'add') {
                $this->recordTransaction($data);
                echo json_encode(['success' => true]);
            } elseif ($action === 'delete') {
                $this->db->deleteTransaction($data['id']);
                echo json_encode(['success' => true]);        
            } elseif ($action === 'transactions') {
                echo json_encode($this->db->getTransactions());
            } elseif ($action === 'balance') {
                echo json_encode($this->db->getBalance($data['projectedDate']));
            } elseif ($action === 'cashFlow') {
                echo json_encode($this->db->generateCashFlow($data['numberMonths']));
            } elseif ($action === 'init') {
                /*  On the init action, clear the session database. 
                    This is necessary because the session variable persists across runs on the Visual 
                    Studio Code built-in server. 
                */
                $_SESSION['database'] = null;
                $this->db = $_SESSION['database'];
            }
        } elseif ($method === 'PUT') {
            $this->db->clear();
            echo json_encode(['success' => true]);
        }
        $_SESSION['database'] = $this->db;
        //return ['error' => 'Invalid method'];
    }
        
    private function recordTransaction($data) {

        /* Redundant for now as front end ensures these fields are present. */
        if (!isset($data['amount']) || !isset($data['description'])) {
            return ['error' => 'Missing required fields'];
        }
        
        $transaction = new Transaction(
            $data['amount'],
            $data['description'],
            $data['transactionType'],
            new DateTime($data['transactionDate']),
            $data['frequency']
        );
        
        $this->db->addTransaction(false, $transaction, $data['numberRecurring']);
        echo json_encode(['success' => true]);
        //return ['success' => true, 'id' => $id, 'transaction' => $transaction];
    }
    
    /*private function getTransactions() {
        return ['transactions' => array_values($this->db->getAll())];
    }*/
}

session_start();
// Create one database instance in memory for the session. Used instead of an SQL database.
// Would ideally not create a new API every request, but it uses the same database instance.
$api = new CashflowForecastingAPI();
$api->handleRequest($_SERVER['REQUEST_METHOD'], $_REQUEST);
exit;

?>