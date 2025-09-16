<?php

class Car {
    private $brand;
    private $model;
    private $year;
    private $mileage;
    
    public function __construct($brand, $model, $year, $mileage) {
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->mileage = $mileage;
    }
    
    public function getBrand() {
        return $this->brand;
    }
    
    public function getModel() {
        return $this->model;
    }
    
    public function getYear() {
        return $this->year;
    }
    
    public function getMileage() {
        return $this->mileage;
    }
    
    public function getCarInfo() {
        return $this->year . "年式 " . $this->brand . " " . $this->model . "（走行距離: " . number_format($this->mileage) . "km）";
    }
    
    public function drive($distance) {
        $this->mileage += $distance;
        return number_format($distance) . "kmドライブしました。総走行距離: " . number_format($this->mileage) . "km";
    }
}

?>