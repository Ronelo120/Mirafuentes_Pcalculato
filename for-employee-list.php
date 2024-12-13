<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "payroll";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateRandomId($length = 8) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomId = '';
    for ($i = 0; $i < $length; $i++) {
        $randomId .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomId;
}

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = generateRandomId(); 
    $name = $conn->real_escape_string($_POST['name']);
    $position = $conn->real_escape_string($_POST['position']);
    $address = $conn->real_escape_string($_POST['address']);
    $contact_no = $conn->real_escape_string($_POST['contact_no']);
    $working_days_type = $conn->real_escape_string($_POST['working_days']); 
    $basic_salary = (float)$_POST['basic_salary']; 

    switch ($working_days_type) {
        case "daily":
            $working_days = 1;
            break;
        case "weekly":
            $working_days = 5;
            break;
        case "monthly":
            $working_days = 20;
            break;
        case "semi-monthly":
            $working_days = 10;
            break;
        case "yearly":
            $working_days = 240;
            break;
        default:
            $working_days = 0;
    }

    $checkEmployee = "SELECT * FROM employee_list WHERE contact_no = '$contact_no'";
    $result = $conn->query($checkEmployee);
    
    if ($result->num_rows > 0) {
        $error = "Employee with this contact number already exists.";
    } else {

        $time_in = date("H:i:s");
        $time_out = date("H:i:s", strtotime('+8 hours'));

        $sql = "INSERT INTO employee_list (id, name, position, address, contact_no, time_in, time_out, working_days, basic_salary) 
                VALUES ('$employee_id', '$name', '$position', '$address', '$contact_no', '$time_in', '$time_out', '$working_days', $basic_salary)";

        if ($conn->query($sql) === TRUE) {

            $sss = $basic_salary * 0.11;
            $philhealth = $basic_salary * 0.04;
            $pagibig = 100;  
            $tax = $basic_salary * 0.12;
            $overtime_hours = 0; 
            
            $absences = 0; 
            $deduction_for_absences = 0; 

            $gross_pay = $basic_salary + ($overtime_hours * 100) - $deduction_for_absences; 
            $net_pay = $gross_pay - ($sss + $philhealth + $pagibig + $tax);

            $update_sql = "UPDATE employee_list 
                           SET sss = ?, philhealth = ?, pagibig = ?, tax = ?, gross_pay = ?, net_pay = ?, overtime_hours = ? 
                           WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ddddddss", $sss, $philhealth, $pagibig, $tax, $gross_pay, $net_pay, $overtime_hours, $employee_id);
            
            if ($update_stmt->execute()) {
                $message = "New employee added successfully with ID: " . $employee_id;
            } else {
                $error = "Error updating payroll data: " . $update_stmt->error;
            }

            header("refresh:5;url=employee-list.php"); 
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();

$positions = [
    "Manager",
    "Developer",
    "Designer",
    "HR",
    "Sales",
    "Support"
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Employee</title>
    <link rel="stylesheet" href="css/for-employee-list.css">
</head>
<body>
    <h2 class="container">Add New Employee</h2>
    <?php if($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="for-employee-list.php" method="POST" class="matrix-form">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label for="position">Position:</label>
            <select name="position" required>
                <option value="" disabled selected>Select Position</option>
                <?php foreach ($positions as $pos): ?>
                    <option value="<?php echo $pos; ?>"><?php echo $pos; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" name="address">
        </div>
        <div class="form-group">
            <label for="contact_no">Contact No.:</label>
            <input type="text" name="contact_no" required>
        </div>
        <div class="form-group">
            <label for="working_days">Working Days:</label>
            <select name="working_days" required>
                <option value="" disabled selected>Select working days type</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="semi-monthly">Semi-Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>
        <div class="form-group">
            <label for="basic_salary">Basic Salary:</label>
            <input type="number" name="basic_salary" required step="0.01" min="0">
        </div>
        <div class="button-container">
    <button type="submit">Add Employee</button>
    <a href="employee-list.php" class="back-to-employee-list">Back to Employee List</a>
</div>
</body>
</html>
