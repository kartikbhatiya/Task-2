<?php
include '../controller/admins.php';

$errors = [];
$data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = $_POST;
    $response = insertAdmin($data);

    if (!$response['status']) {
        $errors = $response['errors'];
    }
    else{
        header('Location: /pages/login.php');
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>
    <style>
        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h2>Signup Form</h2>
    <form method="post" action="./signup.php">

        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($data['name'] ?? ''); ?>"><br>
        <?php if (isset($errors['name'])): ?>
            <span class="error"><?php echo $errors['name']; ?></span><br>
        <?php endif; ?>
        <br>

        Email: <input type="text" name="email" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>"><br>
        <?php if (isset($errors['email'])): ?>
            <span class="error"><?php echo $errors['email']; ?></span><br>
        <?php endif; ?>
        <br>

        Password: <input type="password" name="password" value="<?php echo htmlspecialchars($data['password'] ?? '');  ?>"><br>
        <?php if (isset($errors['password'])): ?>
            <span class="error"><?php echo $errors['password']; ?></span><br>
        <?php endif; ?>
        <br>

        <input type="submit" value="Signup">
    </form>
    <a href="./login.php">Login</a>
</body>
</html>