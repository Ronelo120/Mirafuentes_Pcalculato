<?php
require_once 'Employee.php';
abstract class AbstractPayroll {
    protected $employee;
    protected $workingDays;
    protected $overtimeHours;
    protected $deductions;

    public function __construct(Employee $employee, $workingDays, $overtimeHours, $deductions) {
        $this->employee = $employee;
        $this->workingDays = $workingDays;
        $this->overtimeHours = $overtimeHours;
        $this->deductions = $deductions;
    }

    abstract public function calculateGrossPay();
    abstract public function calculateNetPay();
    abstract public function generatePayslip();
}

class Payroll extends AbstractPayroll {
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

// Usage example
$employee = new Employee(1, 'Ronelo Mirafuentes', 'Manager', 50000);
$payroll = new Payroll($employee, 20, 10, 5000);
$payslip = $payroll->generatePayslip();
print_r($payslip);
?>
