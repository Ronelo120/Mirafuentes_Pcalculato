<?php
include 'config.php';

if (isset($_GET['id'])) {
    $employee_id = $_GET['id'];
    $stmt = $link->prepare("SELECT name FROM employee_list WHERE id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo json_encode($employee);
    } else {
        echo json_encode(null);
    }
}
?>
