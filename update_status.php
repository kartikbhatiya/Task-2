<?php
// filepath: /c:/laragon/www/Task-1/update_status.php
include './controller/customers.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['user_id'];
    $status = $_POST['status'];

    $result = updateCustomerStatus($userId, $status);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
}
?>