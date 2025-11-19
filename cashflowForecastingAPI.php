<?php

include "transaction.php";
include "transactionDatabase.php";

class CashflowForecastingAPI {
    private $db;
    
    public function __construct() {
        $this->db = $_SESSION['database'] ?? new TransactionDatabase();//$database;
    }
    
     public function handleRequest($method, $data) {
        header('Content-Type: text/html'); //Do I need this?
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
    
    
/*   public function handleRequest($method, $data) {
        if ($method === 'POST') {
            return $this->recordTransaction($data);
        } elseif ($method === 'GET') {
            return $this->getTransactions();
        }
        return ['error' => 'Invalid method'];
    }*/
    
    private function recordTransaction($data) {
        /*if (!isset($data['amount']) || !isset($data['description'])) {
            return ['error' => 'Missing required fields'];
        }*/
        
        $transaction = new Transaction(
            $data['amount'],
            $data['description'],
            $data['transactionType'],
            $data['transactionDate'],
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
/* Use the session to persist the database across requests. Initialise the database if this is the first access of the session. */
//$staticDB = $_SESSION['database'] ?? new TransactionDatabase();
$api = new CashflowForecastingAPI();
// Handle POST requests
$api->handleRequest($_SERVER['REQUEST_METHOD'], $_REQUEST);
//$_SESSION['database'] = $staticDB;
exit;

/*if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
/*        echo json_encode(['success' => true]);
    } elseif ($action === 'init') {
        /* On the init action, clear the session database. 
        This is necessary because the session variable persists across runs on the Visual Studio Code built-in server. */
/*        $_SESSION['database'] = null;
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
}*/
?>