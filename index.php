<?php

session_start();

if (!isset($_SESSION['adminId'])) {
    header('Location: pages/login.php');
}

include 'validation.php';
include './controller/customers.php';

$searchTerm = $_GET['search'] ?? '';
$customers = searchCustomers($searchTerm);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
    <link>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: black;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        img {
            max-width: 100px;
            height: auto;
        }

        a {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #007BFF;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div style="text-align: right;">
        <a href="./form.php">Add Customer</a>
        <a href="./logout.php">Logout</a>
    </div>

    </div>
    <h2> Customers </h2>
    <form method="get" action="./index.php">
        <input type="text" name="search" placeholder="Search by Name or Email" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <input type="submit" value="Search">
    </form>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Age</th>
                <th>Birthday</th>
                <th>Gender</th>
                <th>Subscribe</th>
                <th>Country</th>
                <th>Message</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th> Delete User </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($customers as $customer):
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['id']); ?></td>
                    <td><?php echo htmlspecialchars($customer['name']); ?></td>
                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                    <td><?php echo htmlspecialchars($customer['age']); ?></td>
                    <td><?php echo htmlspecialchars($customer['birthday']); ?></td>
                    <td><?php echo htmlspecialchars($customer['gender']); ?></td>
                    <td><?php echo htmlspecialchars($customer['subscribe']); ?></td>
                    <td><?php echo htmlspecialchars($customer['country']); ?></td>
                    <td><?php echo htmlspecialchars_decode($customer['message']); ?></td>
                    <td><?php echo htmlspecialchars($customer['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($customer['updated_at']); ?></td>
                    <td>
                        <label class="toggle-button">
                            <input type="checkbox" id="status-<?php echo htmlspecialchars($customer['id']); ?>" <?php echo $customer['isdeleted'] ? 'checked' : ''; ?> onclick="deleteUser(<?php echo $customer['id']; ?>, this.checked)">
                            <span class="slider"></span>
                        </label>
                    </td>
                    <td>
                        <a href="./form.php?id=<?php echo $customer['id']; ?>">Edit</a>
                        <a href="./view.php?id=<?php echo $customer['id']; ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="script.js"></script>

</body>

</html>