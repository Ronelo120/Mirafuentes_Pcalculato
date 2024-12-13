<?php
session_start();

if (!isset($_SESSION['payroll_results'])) {
    header("Location: calculate_payroll.php");
    exit();
}

$results = $_SESSION['payroll_results'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Results</title>
    <link rel="stylesheet" href="css/payroll_results.css"> 
</head>
<body>
    <div class="mate-background">
        <div class="result-container">
            <h1>Payroll Results</h1>
            <table class="results-table">
                <tbody>
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td><?php echo htmlspecialchars($results['employee_id']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo htmlspecialchars($results['employee_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Position:</strong></td>
                        <td><?php echo htmlspecialchars($results['position']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Gross Pay:</strong></td>
                        <td>₱<?php echo number_format($results['gross_pay'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Deductions:</strong></td>
                        <td>₱<?php echo number_format($results['total_deductions'], 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Net Pay:</strong></td>
                        <td>₱<?php echo number_format($results['net_pay'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="button-container">
                <a href="dashboard.php" class="back-button">Back to Calculator</a>
                <a href="worksheet.php" class="history-button">View Payroll History</a>
            </div>
        </div>
    </div>
</body>
</html>
