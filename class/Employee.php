<?php
class Employee {
    private $id;
    private $name;
    private $position;
    private $basicSalary;

    public function __construct($id, $name, $position, $basicSalary) {
        $this->id = $id;
        $this->name = $name;
        $this->position = $position;
        $this->basicSalary = $basicSalary;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPosition() {
        return $this->position;
    }

    public function getBasicSalary() {
        return $this->basicSalary;
    }

    public function setBasicSalary($basicSalary) {
        $this->basicSalary = $basicSalary;
    }
}
?>
