<?php

if (!isset($_SESSION['adminId'])) {
    header('Location: pages/login.php');
}

include './controller/customers.php';

$id = $_GET['id'] ?? NULL;
if ($id == NULL) {
    echo "ID is required!";
    return;
} else {
    echo deleteCustomer($id);
}
