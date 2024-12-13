

<?php

include 'config.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['id'];
    $new_basic_salary = $_POST['basic_salary'];

    $overtime_hours = !empty($_POST['overtime_hours']) ? $_POST['overtime_hours'] : 0;

    $stmt = $link->prepare("SELECT * FROM employee_list WHERE id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $employee = $stmt->get_result()->fetch_assoc();

    if ($employee) {

        $name = $employee['name'];
        $position = $employee['position'];
        $working_days = $employee['working_days'];
        $current_absences = $employee['absences']; 
        $created_at = date('Y-m-d H:i:s');

        if (empty($new_basic_salary) || !is_numeric($new_basic_salary) || $new_basic_salary <= 0) {
            $error = "Please enter a valid Basic Salary.";
        } elseif (!is_numeric($current_absences) || $current_absences < 0) {
            $error = "Error fetching current absences.";
        } elseif (!is_numeric($overtime_hours) || $overtime_hours < 0) {
            $error = "Please enter a valid number of Overtime Hours.";
        } else {

            $sss = $new_basic_salary * 0.11;
            $philhealth = $new_basic_salary * 0.04; 
            $pagibig = 100;  
            $tax = $new_basic_salary * 0.12; 
 
            $input_absences = !empty($_POST['absences']) ? $_POST['absences'] : 0; 
            $total_absences = $current_absences + (int)$input_absences; 


            $deduction_for_absences = ($total_absences >= $working_days) ? $new_basic_salary : ($total_absences * ($new_basic_salary / $working_days));
            $gross_pay = $new_basic_salary + ($overtime_hours * 100) - $deduction_for_absences; 
            $net_pay = $gross_pay - ($sss + $philhealth + $pagibig + $tax);


            $stmt = $link->prepare("UPDATE employee_list SET gross_pay = ?, net_pay = ?, sss = ?, philhealth = ?, pagibig = ?, tax = ?, created_at = ?, basic_salary = ?, overtime_hours = ?, absences = ? WHERE id = ?");
            $stmt->bind_param("ddddddssssd", $gross_pay, $net_pay, $sss, $philhealth, $pagibig, $tax, $created_at, $new_basic_salary, $overtime_hours, $total_absences, $employee_id);
            
            if ($stmt->execute()) {
                $message = "Payroll updated successfully for $name!";
            } else {
                $error = "Error updating payroll data: " . $stmt->error;
            }
        }
    } else {
        $error = "Employee not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculate Payroll</title>
    <link rel="stylesheet" href="css/calculate_payroll.css">

    <script>

        function fillName() {
            const employeeId = document.querySelector('input[name="id"]').value;
            if (employeeId) {
                fetch(`get-employee.php?id=${employeeId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.querySelector('input[name="name"]').value = data.name;
                        } else {
                            document.querySelector('input[name="name"]').value = '';
                        }
                    })
                    .catch(error => console.error('Error fetching employee data:', error));
            } else {
                document.querySelector('input[name="name"]').value = '';
            }
        }
    </script>
</head>
<body>
    <h2 class="container">Calculate Payroll</h2>
    
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="id">Employee ID:</label>
        <input type="text" name="id" required oninput="fillName()"><br>
        
        <label for="name">Employee Name:</label>
        <input type="text" name="name" required disabled><br>
        
        <label for="basic_salary">Basic Salary:</label>
        <input type="text" name="basic_salary" required><br>

        <label for="absences">Absences:</label>
        <input type="text" name="absences"><br>
        
        <label for="overtime_hours">Overtime Hours:</label>
        <input type="text" name="overtime_hours"><br>
        
        <button type="submit">Calculate Payroll</button>
    </form>

    <a href="dashboard.php">Back to homepage</a>
</body>
</html>
