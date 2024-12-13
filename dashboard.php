<?php

session_start();


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}


require_once "config.php"; 


$sqlTotalEmployees = "SELECT COUNT(*) AS total FROM employee_list";
$resultTotalEmployees = mysqli_query($link, $sqlTotalEmployees);
$rowTotalEmployees = mysqli_fetch_assoc($resultTotalEmployees);
$totalEmployees = $rowTotalEmployees['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to the Admin Dashboard.</h1>

        <nav class="nav">
            <a href="employee-list.php" class="nav-link">View Employees</a>
            <a href="calculate_payroll.php" class="nav-link">Calculate Payroll</a>
            <a href="reset-password.php" class="action-btn">Reset Your Password</a>
        </nav>

        <div class="stats">
            <div class="stat">
                <h3>Total Employees Registered</h3>
                <p><?php echo $totalEmployees; ?></p> 
            </div>

            <!-- <div class="stat">
                <h3>Active Sessions</h3>
                <p>30</p> 
            </div> -->

            <!-- <div class="stat">
                <h3>Balance</h3>
                <p>300</p> 
            </div> -->
        </div>

        <p>
            <a href="logout.php" class="action-btn">Sign Out of Your Account</a>
        </p>
    </div>
</body>
</html>

<?php

mysqli_close($link);
?>
