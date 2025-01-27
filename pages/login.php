<?php

include '../controller/admins.php';

session_start();

$errors = [];
$data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $response = loginAdmin($data);

    if (!$response['status']) {
        $errors = $response['errors'];
    } else {
        echo $response['data']['id'];
        $_SESSION['adminId'] = $response['data']['id'];
        header("Location: ../index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <style>
        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h2>Login Form</h2>
    <form method="post" action="login.php">
        <!-- Email Input -->
        Email: <input type="email" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>"><br>
        <?php if (isset($errors['email'])): ?>
            <span class="error"><?php echo $errors['email']; ?></span><br>
        <?php endif; ?>
        <br>

        <!-- Password Input -->
        Password: <input type="password" name="password"><br>
        <?php if (isset($errors['password'])): ?>
            <span class="error"><?php echo $errors['password']; ?></span><br>
        <?php endif; ?>
        <br>

        <!-- Submit Button -->
        <input type="submit" value="Login">
    </form>
    <a href="./signup.php">Create Account</a>
</body>
</html>