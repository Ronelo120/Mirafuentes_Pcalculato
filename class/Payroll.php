<?php
class Payroll {
    private $employee;
    private $workingDays;
    private $overtimeHours;
    private $deductions;

    public function __construct(Employee $employee, $workingDays, $overtimeHours, $deductions) {
        $this->employee = $employee;
        $this->workingDays = $workingDays;
        $this->overtimeHours = $overtimeHours;
        $this->deductions = $deductions;
    }

    public function calculateGrossPay() {
        $dailyRate = $this->employee->getBasicSalary() / 22; 
        $overtimePay = $this->overtimeHours * ($dailyRate / 8); 
        return ($dailyRate * $this->workingDays) + $overtimePay;
    }

    public function calculateNetPay() {
        $grossPay = $this->calculateGrossPay();
        return $grossPay - $this->deductions;
    }

    public function generatePayslip() {
        return [
            'Employee Name' => $this->employee->getName(),
            'Position' => $this->employee->getPosition(),
            'Gross Pay' => $this->calculateGrossPay(),
            'Deductions' => $this->deductions,
            'Net Pay' => $this->calculateNetPay()
        ];
    }
}
?>
