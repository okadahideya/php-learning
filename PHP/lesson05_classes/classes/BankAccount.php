<?php

class BankAccount {
    protected $accountNumber;
    private $balance;
    protected $ownerName;
    
    public function __construct($accountNumber, $balance, $ownerName) {
        $this->accountNumber = $accountNumber;
        $this->balance = $balance;
        $this->ownerName = $ownerName;
    }
    
    public function getAccountNumber() {
        return $this->accountNumber;
    }
    
    public function getBalance() {
        return $this->balance;
    }
    
    public function getOwnerName() {
        return $this->ownerName;
    }
    
    public function deposit($amount) {
        if ($amount > 0) {
            $this->balance += $amount;
            return number_format($amount) . "円入金しました。残高: " . number_format($this->balance) . "円";
        } else {
            return "入金額は0より大きい必要があります。";
        }
    }
    
    public function withdraw($amount) {
        if ($amount > $this->balance) {
            return "残高不足です。現在の残高: " . number_format($this->balance) . "円";
        } else if ($amount <= 0) {
            return "出金額は0より大きい必要があります。";
        } else {
            $this->balance -= $amount;
            return number_format($amount) . "円出金しました。残高: " . number_format($this->balance) . "円";
        }
    }
    
    public function checkBalance() {
        return $this->ownerName . "さんの口座残高: " . number_format($this->balance) . "円";
    }
}

?>