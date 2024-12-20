<?php

include 'config.php';

$employee_id = '';
$employee = null;
$deductions = [];
$message = '';
$error = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $employee_id = $_GET['id'];

    $stmt = $link->prepare("SELECT * FROM employee_list WHERE id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $employee = $stmt->get_result()->fetch_assoc();

    if ($employee) {

        $basic_salary = $employee['basic_salary'];
        $absences = $employee['absences'];
        $sss = $basic_salary * 0.11;
        $philhealth = $basic_salary * 0.04;
        $pagibig = 100; 
        $tax = $basic_salary * 0.12;

        $working_days = 22; 
        $deduction_for_absences = ($absences >= $working_days) ? $basic_salary : ($absences * ($basic_salary / $working_days));

        $deductions = [
            'SSS' => $sss,
            'PhilHealth' => $philhealth,
            'Pag-IBIG' => $pagibig,
            'Tax' => $tax,
            'Deductions for Absences' => $deduction_for_absences,
            'Total Deductions' => $sss + $philhealth + $pagibig + $tax + $deduction_for_absences
        ];

        $total_deductions = array_sum($deductions);
        $net_pay = $basic_salary - $total_deductions;
    } else {
        $error = "Employee not found.";
    }
} else {
    $error = "No employee ID provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="css/employee-dashboard.css">
    <style>
        .print-btn {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .print-btn:hover {
            background-color: #0056b3;
        }

        @media print {
            .back-btn, .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Employee Dashboard</h2>

        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php else: ?>
            <h3><?php echo htmlspecialchars($employee['name']); ?> (ID: <?php echo htmlspecialchars($employee['id']); ?>)</h3>
            <p>Position: <?php echo htmlspecialchars($employee['position']); ?></p>
            <p>Basic Salary: ₱<?php echo number_format($employee['basic_salary'], 2); ?></p>
            <p>Absences: <?php echo htmlspecialchars($employee['absences']); ?></p>

            <div class="deductions">
                <h4>Deductions:</h4>
                <?php foreach ($deductions as $key => $value): ?>
                    <p><?php echo htmlspecialchars($key); ?>: ₱<?php echo number_format($value, 2); ?></p>
                <?php endforeach; ?>
                <p><strong>Total Deductions: ₱<?php echo number_format($deductions['Total Deductions'], 2); ?></strong></p>
                <p><strong>Net Pay: ₱<?php echo number_format(num: $net_pay); ?></strong></p>
            </div>

            <!-- Back Button -->
            <a href="employee-list.php" class="back-btn">Back to Employee List</a>

            <!-- Print Button -->
            <form method="post">
                <button type="button" class="print-btn" onclick="window.print()">Print Details</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
