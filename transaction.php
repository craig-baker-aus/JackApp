<?php
    enum TransactionFrequency: string {
        case ONCE = "once";
        case DAILY = "daily";
        case WEEKLY = "weekly";
        case MONTHLY = "monthly";
    }

    enum TransactionType: string {
        case INCOME = "income";
        case EXPENSE = "expense";
    }

class Transaction {
    /*  Implement the core unit to manipulate individual cash transactions in memory. */

    private $id;
     /*  Ideally, these properties should be private thus enforcing the getters and setters, but json_encode
        only works with public properties. Implementing the JsonSerializable interface would solve the problem. */

   public float $amount;
    public string $description;
    public Date $transactionDate;
    public TransactionType $transType; // 'income' or 'expense'
    public TransactionFrequency $frequency; // 'once', 'daily', 'weekly', 'monthly'
    
    public function __construct(float $amount, $description, TransactionType $type, Date $transDate, TransactionFrequency $frequency) {
        $this->id = uniqid();
        $this->setAmount($amount);
        $this->setDescription($description);
        $this->setTransactionType($type);
        $this->setTransactionDate($transDate);
        $this->setFrequency($frequency);
    }

    // ID
    public function getId() {
        return $this->id;
    }

    // Amount
    public function getAmount() {
        return $this->amount;
    }

    public function setAmount(float $amount) {
        $this->amount = $amount;
        return $this;
    }

    // Description
    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    // Transaction Date
    public function getTransactionDate() {
        return $this->transactionDate;
    }

    public function setTransactionDate(Date $transactionDate) {
        $this->transactionDate = $transactionDate;
        return $this;
    }

    // Transaction Type
    public function getTransactionType() {
        return $this->transType;
    }

    public function setTransactionType(TransactionType $transactionType) {
        $this->transType = $transactionType;
        return $this;
    }

    // Frequency
    public function getFrequency() {
        return $this->frequency;
    }

    public function setFrequency(TransactionFrequency $frequency) {
        $this->frequency = $frequency;
        return $this;
    }
}

