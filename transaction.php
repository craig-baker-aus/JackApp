<?php

class Transaction {
    /* Ideally, these should be private thus enforcing the getters and setters, but json_encode
    only works with public properties. Implementing the JsonSerializable interface would solve the problem. */

    private $id;
    public $amount;
    public $description;
    public $transactionDate;
    public $transactionType; // 'income' or 'expense'
    public $frequency; // 'once', 'daily', 'weekly', 'monthly'
    
    public function __construct($amount, $description, $type, $transDate, $frequency) {
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

 /*   public function setId($id) {
        $this->id = $id;
        return $this;
    }*/

    // Amount
    public function getAmount() {
        return $this->amount;
    }

    public function setAmount($amount) {
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

    public function setTransactionDate(DateTime $transactionDate) {
        $this->transactionDate = $transactionDate->format('Y-m-d');
        return $this;
    }

    // Transaction Type
    public function getTransactionType() {
        return $this->transactionType;
    }

    public function setTransactionType($transactionType) {
        $this->transactionType = $transactionType;
        return $this;
    }

    // Frequency
    public function getFrequency() {
        return $this->frequency;
    }

    public function setFrequency($frequency) {
        $this->frequency = $frequency;
        return $this;
    }
}

