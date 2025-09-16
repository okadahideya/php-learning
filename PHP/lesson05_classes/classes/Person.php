<?php

class Person {
    public $name;
    public $age;
    private $email;
    
    public function __construct($name, $age, $email) {
        $this->name = $name;
        $this->age = $age;
        $this->email = $email;
    }
    
    public function introduce() {
        return "私の名前は" . $this->name . "で、" . $this->age . "歳です。";
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($newEmail) {
        $this->email = $newEmail;
    }
    
    public function celebrateBirthday() {
        $this->age++;
        return $this->name . "さん、" . $this->age . "歳のお誕生日おめでとうございます！";
    }
}

?>