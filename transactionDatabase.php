<?php
class TransactionDatabase {
   /* Maintains a list of transactions (class Transaction) in memory, sorted by transactionDate (ascending). */

    private $transactions = [];

    /*  Money may have been received and spent before using the software.
        If it has been, the starting balance can be recorded. */
    private $initialBalance = 0.00;

    public function __construct(float $balance = 0.00) {
        $this->initialBalance = $balance;
    }  

    private function sortDate(Transaction $a, Transaction $b) {
        return $a->getTransactionDate() <=> $b->getTransactionDate();
    }

    public function getBalance(string $date) {
        /* Returns ['finalDate'->String, 'balance'->Float] */

        $balance = $this->initialBalance;
        foreach ($this->transactions as $transaction) {
            if ($transaction->getTransactionDate() > $date) {
                break;
            }
            if ($transaction->getTransactionType() === 'income') {
                $balance += $transaction->getAmount();
            } elseif ($transaction->getTransactionType() === 'expense') {
                $balance -= $transaction->getAmount();
            }
        }
        return ['finalDate' => $date, 'balance' => $balance];
    }
    
    private function getEndOfMonthDates(Date $startDate, Date $endDate): array
    /* Returns an array of DateTime, representing the end of month dates in the provided range. */
    {
    $endOfMonthDates = [];
    $currentMonth = $startDate;

    // Loop through each month from the start date to the end date
    while ($currentMonth <= $endDate) {
        // Modify to the last day of the current month
        $currentMonth = $currentMonth->modify('last day of this month');;

        // Add the end of month date to the array
        $endOfMonthDates[] = $currentMonth;

        // Move to the first day of the next month for the next iteration
        $currentMonth = $currentMonth->modify('first day of next month');
    }
    return $endOfMonthDates;
}

    public function generateCashFlow($months, Date $startDate = new Date()) {
        /*  Generate cash flow from startDate for number of months specified.
            startDate defaults to today's date. 
            Returns ['OpeningBalance'-> Float, 'ClosingBalance'-> Float, 'Income'-> TransactionFlow, 'Expenses'-> TransactionFlow] */

        $endDate = $startDate->modify("+$months months");
        $reportingDates = $this->getEndOfMonthDates($startDate, $endDate);

        $previousDate = $startDate->modify("-1 day");

        $startBalance = $this->getBalance($previousDate->__toString());

        $income = $this->generateTransactionFlow(TransactionType::INCOME, $startDate, $reportingDates, $startBalance['balance']);
        $expense = $this->generateTransactionFlow(TransactionType::EXPENSE, $startDate, $reportingDates, $startBalance['balance']);

        return ['OpeningBalance' => $startBalance, 'ClosingBalance' => $this->getBalance($endDate->__toString()),'Income'=> $income,'Expenses'=> $expense];
    }
     private function generateTransactionFlow(TransactionType $type, Date $startDate, $reportingDates, float $startingBalance) {
        /*  Generate cash flow of specified type over reporting dates. 
            Returns the end date for each reporting period (currently months) along with the total for that period and the end balance. 
            Physically returns "TransactionFlow", i.e array (indexed by date) of ['type'->String, 'total'->Float, 'balance'->Float]*/

        $cashFlow = [];
        $periodStartDate = $startDate;
        
        foreach ($reportingDates as $periodEndDate) {
            $totalAmounts = $this->totalTransactions($type, $periodStartDate, $periodEndDate);
            $cashFlow[$periodEndDate->format('Y-m-d')] = [
                'type' => $type,
                'total' => $totalAmounts,
                'balance' => $this->getBalance($periodEndDate->format('Y-m-d'))['balance'] - $startingBalance // Get balance at end of month
            ];
            $periodStartDate->modify('first day of next month');
        }
        return $cashFlow;
    }
    private function totalTransactions($type, Date $startDate, Date $endDate) {
        /*  Calculate total of transactions of specified type between startDate and endDate (inclusive). 
            Returns a "Float". */

        $total = 0.00;
        foreach ($this->transactions as $transaction) {
            // The list of transactions is sorted by date, so can optimize the loop.
            $transDate = $transaction->getTransactionDate();
            if ($transDate < $startDate) {
                // Do nothing if before start date.
                continue;
            }
            elseif ($transDate > $endDate) {
                // Exit loop if past end date.
                break;
            }
            if ($type == $transaction->getTransactionType()) {
                $total += $transaction->getAmount();
            }
        }
        return $total;
    }

    /* Record income and expense transactions. */
    public function addTransaction(bool $isIndividual, Transaction $transaction, int $numberRecurring) {
        if ($isIndividual || $transaction->getFrequency() === TransactionFrequency::ONCE) {
            $this->transactions[$transaction->getId()] = $transaction;
        } else {
            $this->addRecurringTransactions($transaction, numberRecurring: $numberRecurring);  
        }
        // Sort by 'transactionDate' in ascending order
        usort($this->transactions, "self::sortDate");    
    }
    
    /* Retrieve transaction history (already sorted). */
    public function getTransactions(): array|Transaction {
        return $this->transactions;
    }
    
    public function getTransactionById($id) {
        /* Not currently used. */
        return $this->transactions[$id] ?? null;
    }
    
    public function deleteTransaction($id) {
        /* Currently only deletes a single transaction, not the recurring series. */
        unset($this->transactions[$id]);
    }
    
    public function clear() {
        $this->transactions = [];
    }

    private function addRecurringTransactions(Transaction $transaction, $numberRecurring) {
        /*  Adds numberRecurring instances of transaction, on the appropriate dates.
            transaction specifies the frequency of the transaction. */

        //date is modified in the loop as needed for the future dates.
        $date = new Date($transaction->getTransactionDate());
        
        for ($i = 0; $i < $numberRecurring; $i++) {
            $newTransaction = new Transaction(
                $transaction->getAmount(),
                $transaction->getDescription(),
                $transaction->getTransactionType(),
                $date,
                $transaction->getFrequency()
            );
            $this->addTransaction(true, $newTransaction, 1);
            
            // Calculate the next date in the series.
            switch($transaction->getFrequency()) {
                case TransactionFrequency::DAILY: $date = $date->modify('+1 day'); break;
                case TransactionFrequency::WEEKLY: $date = $date->modify('+1 weeks'); break;
                case TransactionFrequency::MONTHLY: $date = $date->modify('+1 months'); break;
                default: break;
            };
        }
    }
}
