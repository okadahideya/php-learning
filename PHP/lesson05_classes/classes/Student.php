<?php

require_once 'Person.php';

class Student extends Person {
    private $studentId;
    private $grade;
    
    public function __construct($name, $age, $email, $studentId, $grade) {
        parent::__construct($name, $age, $email);
        $this->studentId = $studentId;
        $this->grade = $grade;
    }
    
    public function getStudentId() {
        return $this->studentId;
    }
    
    public function getGrade() {
        return $this->grade;
    }
    
    public function introduce() {
        return parent::introduce() . " 学生番号は" . $this->studentId . "で、成績は" . $this->grade . "です。";
    }
    
    public function study($subject) {
        return $this->name . "さんが" . $subject . "を勉強しています。";
    }
}

?>