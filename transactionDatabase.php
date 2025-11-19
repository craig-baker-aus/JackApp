<?php
class TransactionDatabase {
    private $transactions = [];

    public function addTransaction(bool $isIndividual, Transaction $transaction, int $numberRecurring) {
        if ($isIndividual || $transaction->getFrequency() == 'once') {
            $this->transactions[$transaction->getId()] = $transaction;
        } else {
            $this->generateRecurringTransactions($transaction, $numberRecurring);  
        }
    }
    
    public function getTransactions(): array|Transaction {
        return $this->transactions;
    }
    
    public function getTransactionById($id) {
        return $this->transactions[$id] ?? null;
    }
    
    public function deleteTransaction($id) {
        unset($this->transactions[$id]);
    }
    
    public function clear() {
        $this->transactions = [];
    }

    private function generateRecurringTransactions(Transaction $transaction, $numberRecurring) {
        $date = new DateTime($transaction->getTransactionDate());
        
        for ($i = 0; $i < $numberRecurring; $i++) {
            $newTransaction = new Transaction(
                $transaction->getAmount(),
                $transaction->getDescription(),
                $transaction->getTransactionType(),
                $date->format('Y-m-d'),
                $transaction->getFrequency()
            );
            $this->addTransaction(true, $newTransaction, 1);
            
            switch($transaction->getFrequency()) {
                case 'daily': $date->modify('+1 day'); break;
                case 'weekly': $date->modify('+1 weeks'); break;
                case 'monthly': $date->modify('+1 months'); break;
                default: break;
            };
        }
    }
}
