<?php

class Product {
    private $name;
    private $price;
    private $stock;
    
    public function __construct($name, $price, $stock) {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getStock() {
        return $this->stock;
    }
    
    public function sell($quantity) {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            $total = $this->price * $quantity;
            return [
                'success' => true,
                'message' => $this->name . "を" . $quantity . "個販売しました。",
                'total' => $total,
                'remaining_stock' => $this->stock
            ];
        } else {
            return [
                'success' => false,
                'message' => "在庫が不足しています。現在の在庫: " . $this->stock . "個"
            ];
        }
    }
    
    public function addStock($quantity) {
        $this->stock += $quantity;
        return $this->name . "の在庫を" . $quantity . "個追加しました。現在の在庫: " . $this->stock . "個";
    }
    
    public function getInfo() {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock
        ];
    }
}

?>