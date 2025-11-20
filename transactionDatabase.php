<?php
class TransactionDatabase {
   /* Maintains a list of transactions (class Transaction), sorted by transactionDate (ascending). */

    private $transactions = [];

    /*  Money may have been received and spent before using the software.
        If it has been, the starting balance can be recorded. */
    private $initialBalance = 0.00;

    public function __construct($balance = 0.00) {
        $this->initialBalance = $balance;
    }  

    private function sortDate(Transaction $a, Transaction $b) {
        return $a->getTransactionDate() <=> $b->getTransactionDate();
    }

    public function getBalance(string $date) {
        /* ['finalDate'->String, 'balance'->Float] */

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
    
    private function getEndOfMonthDates(DateTime $startDate, DateTime $endDate): array
{
    $endOfMonthDates = [];
    $loopDate = clone $startDate;

    // Loop through each month from the start date to the end date
    while ($loopDate <= $endDate) {
        // Clone the current date to avoid modifying the loop variable
        $currentMonth = clone $loopDate;

        // Modify to the last day of the current month
        $currentMonth->modify('last day of this month');

        // Add the end of month date to the array
        $endOfMonthDates[] = $currentMonth;

        // Move to the first day of the next month for the next iteration
        $loopDate->modify('first day of next month');
    }

    return $endOfMonthDates;
}

    public function generateCashFlow($months, $startDate = new DateTime()) {
        /*  Generate cash flow from startDate for number of months specified.
            startDate defaults to today's date. 
            Returns ['OpeningBalance'-> Float, 'ClosingBalance'-> Float, 'Income'-> TransactionFlow, 'Expenses'-> TransactionFlow] */

        $endDate = clone $startDate;
        $endDate->modify("+$months months");
        $reportingDates = $this->getEndOfMonthDates($startDate, $endDate);

        $previousDate = clone $startDate;
        $previousDate->modify("-1 day");

        $startBalance = $this->getBalance($previousDate->format('Y-m-d'));

        $income = $this->generateTransactionFlow("income", $startDate, $reportingDates, $startBalance['balance']);
        $expense = $this->generateTransactionFlow("expense", $startDate, $reportingDates, $startBalance['balance']);

        return ['OpeningBalance' => $startBalance, 'ClosingBalance' => $this->getBalance($endDate->format('Y-m-d')),'Income'=> $income,'Expenses'=> $expense];
    }
     private function generateTransactionFlow($type, DateTime $startDate, $reportingDates, float $startingBalance) {
        /*  Generate cash flow of specified type over reporting dates. 
            Returns the end date for each reporting period (currently months) along with the total for that period and the end balance. 
            Returns "TransactionFlow", i.e array (indexed by date) of ['type'->String, 'total'->Float, 'balance'->Float]*/

        $cashFlow = [];
        $periodStartDate = clone $startDate;
        
        foreach ($reportingDates as $periodEndDate) {
            $totalAmounts = $this->totalTransactions($type, $periodStartDate->format('Y-m-d'), $periodEndDate->format('Y-m-d'));
            $cashFlow[$periodEndDate->format('Y-m-d')] = [
                'type' => $type,
                'total' => $totalAmounts,
                'balance' => $this->getBalance($periodEndDate->format('Y-m-d'))['balance'] - $startingBalance // Get balance at end of month
            ];
            $periodStartDate->modify('first day of next month');
        }
        return $cashFlow;
    }
    private function totalTransactions($type, $startDate, $endDate) {
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

/*
    private function generateDepositFlow(DateTime $startDate, $reportingDates) {
        $cashFlow = [];
        $periodStartDate = clone $startDate;
        
        foreach ($reportingDates as $periodEndDate) {
            $totalData = $this->totalTransactions('income', $periodStartDate->format('Y-m-d'), $periodEndDate->format('Y-m-d'));
            $cashFlow[] = [
                'date' => $periodEndDate,
                'total' => $totalData,
                'balance' => $this->getBalance($periodEndDate) // Get balance at end of month
            ];
            $periodStartDate->modify('first day of next month');
        }
        return $cashFlow;
    }
    private function generateWithdrawFlow($period) {
        $cashFlow = [];
        
        foreach ($period as $date) {
            $totalData = $this->getBalance($date->format('Y-m-t')); // Get balance at end of month
            $cashFlow[] = [
                'date' => $date->format('Y-m-t'),
                'total' => $totalData['total']
            ];
        }
        return $cashFlow;
    }
*/
    /* Record income and expense transactions. */
    public function addTransaction(bool $isIndividual, Transaction $transaction, int $numberRecurring) {
        if ($isIndividual || $transaction->getFrequency() == 'once') {
            $this->transactions[$transaction->getId()] = $transaction;
        } else {
            $this->generateRecurringTransactions($transaction, $numberRecurring);  
        }
        // Sort by 'transactionDate' in ascending order
        usort($this->transactions, "self::sortDate");    
    }
    
    /* Retrieve transaction history (already sorted). */
    public function getTransactions(): array|Transaction {
        return $this->transactions;
    }
    
    public function getTransactionById($id) {
        return $this->transactions[$id] ?? null;
    }
    
    public function deleteTransaction($id) {
        /* Currently only deletes a single transaction, not recurring series. */
        unset($this->transactions[$id]);
    }
    
    public function clear() {
        $this->transactions = [];
    }

    private function generateRecurringTransactions(Transaction $transaction, $numberRecurring) {
        //Modified in the loop as needed for the future dates.
        $date = new DateTime($transaction->getTransactionDate());
        
        for ($i = 0; $i < $numberRecurring; $i++) {
            $newTransaction = new Transaction(
                $transaction->getAmount(),
                $transaction->getDescription(),
                $transaction->getTransactionType(),
                $date,
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
