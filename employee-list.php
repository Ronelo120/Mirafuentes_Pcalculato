<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "payroll";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

if (!empty($search)) {
    $sql = "SELECT * FROM employee_list WHERE id LIKE '%$search%' OR name LIKE '%$search%' OR position LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM employee_list";
}

$result = $conn->query($sql);

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    
    $delete_sql = "DELETE FROM employee_list WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("s", $delete_id);

    if ($delete_stmt->execute()) {
       
        header("Location: " . $_SERVER['PHP_SELF']);
        exit(); 
    } else {
        echo "<script>alert('Error deleting employee: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <link rel="stylesheet" href="css/employee-list.css"> 
</head>
<body>
<div class="table-background">
    <h2>Employee Payroll List</h2>

    <div class="top-section">
        <a href="for-employee-list.php" class="add-employee-btn">Add Employee</a> 
        <form method="POST" action="">
            <input type="text" name="search" placeholder="Search by ID, name, or position" class="search-bar" value="<?php echo isset($search) ? htmlspecialchars($search) : ''; ?>">
            <button type="submit" class="search-btn">Search</button>
        </form>
    </div>


        <table class="table-header">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Address</th>
                    <th>Contact No.</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th></th> 
                </tr>
            </thead>
        </table>



        <div class="worksheet-table-container">
            <table class="worksheet-table">
                
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>"; 
                        echo "<td class='name'><a href='employee-dashboard.php?id=" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</a></td>"; 
                        echo "<td>" . htmlspecialchars($row['position']) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row['address']) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row['contact_no']) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row['time_in']) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row['time_out']) . "</td>"; 
                        echo "<td><a href='?delete_id=" . htmlspecialchars($row['id']) . "' onclick=\"return confirm('Are you sure you want to delete this employee?');\">Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No employee records available</td></tr>"; 
                }
                ?>
            </tbody>
            </table>
        </div>

        <a href="dashboard.php" class="back-to-payroll-btn">Back to Payroll Calculator</a>

    </div>
</body>
</html>

