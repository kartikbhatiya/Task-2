<!DOCTYPE HTML>
<html>

<?php

include 'validation.php';
include './Controller/User.php';

$USER = [];
$id;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // echo "POST request received <br>";
    $errors = [];
    $data   = [];
    $message;

    $profile_picture = $_FILES['profile_picture'];
    $res = validateFile($profile_picture);

    $id = $_POST['id'] ?? NULL;
    
    unset($_POST['id']);

    $USER = $_POST;

    if (!isset($id) && isset($res)) {
        $errors['profile_picture'] = $res;
    } 
    
    if($res == NULL) {
        $upload_dir  = __DIR__ . '/uploads/';
        $upload_file = $upload_dir . basename($profile_picture['name']);
        if (move_uploaded_file($profile_picture['tmp_name'], $upload_file)) {
            global $data;
            $data['profile_picture'] = $upload_file;
        } else {
            $errors['profile_picture'] = "Failed to upload file.";
        }
    }

    // Sanitize and validate other fields
    [$sanitizedData, $validationErrors] = SanitizeFields($_POST);
    $data                               = array_merge($data, $sanitizedData);
    $errors                             = array_merge($errors, $validationErrors);

    // Display results

    if (empty($errors)) {
        $data['subscribe'] = json_encode($data['subscribe']);
        if($id == NULL){
            // PrintData($data);
            $message = insertUser($data);
        }
        else{
            $USER = fetchUser($id);
            $data = getDifferences($data, $USER);
            // PrintData($data);
            $message = editUser($data, $id);
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    global $id;
    global $USER;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $user = fetchUser($id);
        if ($user) {
            $USER = $user;
        }
    }
}


function PrintData($data){
    foreach($data as $key => $value){
        if(is_array($value)){
            echo $key . " : " . implode(", ", $value) . "<br>";
        }
        else{
        echo $key . " : " . $value . "<br>";
        }
    }
}

function getDifferences($data, $USER) {
    $differences = [];

    foreach ($data as $key => $value) {
        if(array_key_exists($key, $USER) && $USER[$key] != $value) {
            $differences[$key] = $value;
        }
    }

    return $differences;
}

?>



<head>
    <title>Form Example</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <style>
        .error {
            color: red;
            font-size: 0.9em;
        }

        img {
            max-width: 100px;
            height: auto;
        }
    </style>

</head>

<body>
    <a href="./index.php">Back to index</a>

    <div>
        <form method="post" action="./form.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id ? htmlspecialchars($id) : NULL ?>">
            Name: <input type="text" name="name" value="<?php
                if (isset($USER['name'])) {
                    echo $USER['name'];
                }
                ?>"><br>

            <?php if (isset($errors['name'])): ?>
                <span class="error"><?php echo $errors['name']; ?></span><br>
            <?php endif; ?>

            Password: <input type="text" name="password" value="<?php
                if (isset($USER['password'])) {
                    echo $USER['password'];
                }
                ?>"><br>

            <?php if (isset($errors['password'])): ?>
                <span class="error"><?php echo $errors['password']; ?></span><br>
            <?php endif; ?>

            Email: <input type="text" name="email" value="<?php
                    if (isset($USER['email'])) {
                        echo $USER['email'];
                    }
                    ?>"><br>

            <?php if (isset($errors['email'])): ?>
                <span class="error"><?php echo $errors['email']; ?></span><br>
            <?php endif; ?>

            Age: <input type="number" name="age" value="<?php
                    if (isset($USER['age'])) {
                        echo $USER['age'];
                    }
                    ?>"><br>

            <?php if (isset($errors['age'])): ?>
                <span class="error"><?php echo $errors["age"]; ?></span><br>
            <?php endif; ?>

            Birthday: <input type="date" name="birthday" value="<?php
                    if (isset($USER['birthday'])) {
                        echo htmlspecialchars($USER['birthday']);
                    }
                    ?>"><br>

            <?php if (isset($errors['birthday'])): ?>
                <span class="error"><?php echo $errors['birthday']; ?></span><br>
            <?php endif; ?>

            Gender:
            <input type="radio" name="gender" value="male" <?php
                if (isset($USER['gender']) && $USER['gender'] == 'male') {
                    echo "checked";
                }
                ?>> Male

            <input type="radio" name="gender" value="female" <?php
                    if (isset($USER['gender']) && $USER['gender'] == 'female') {
                        echo "checked";
                    }
                    ?>> Female<br>

            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($USER['gender'])): ?>
                <span class="error"><?php echo "Gender is Required"; ?></span><br>
            <?php endif; ?>

            Subscribe:
            <input type="checkbox" name="subscribe[]" value="newsletter" <?php
                if (isset($USER['subscribe']) && is_array($USER['subscribe']) && in_array('newsletter', $USER['subscribe'])) {
                    echo "checked";
                }
                ?>> Newsletter

            <input type="checkbox" name="subscribe[]" value="updates" <?php
            if (isset($USER['subscribe']) && is_array($USER['subscribe']) && in_array('updates', $USER['subscribe'])) {
                echo "checked";
            }
            ?>> Updates

            <input type="checkbox" name="subscribe[]" value="offers" <?php
            if (isset($USER['subscribe']) && is_array($USER['subscribe']) && in_array('offers', $USER['subscribe'])) {
                echo "checked";
            }
            ?>> Offers <br>

            <?php if (isset($errors['subscribe'])): ?>
                <span class="error"><?php echo $errors['subscribe']; ?></span><br>
            <?php endif; ?>

            Country:
            <select name="country" aria-placeholder="Select Country">

                <option value="usa" <?php
                    if (isset($USER['country']) && $USER['country'] == 'usa') {
                        echo "selected";
                    }
                    ?>>USA</option>

                <option value="canada" <?php
                        if (isset($USER['country']) && $USER['country'] == 'canada') {
                            echo "selected";
                        }
                        ?>>Canada</option>

            </select><br>

            <?php if (isset($errors['country'])): ?>
                <span class="error"><?php echo $errors['country']; ?></span><br>
            <?php endif; ?>

            Message: <textarea name="message">  <?php echo htmlspecialchars($USER['message'] ?? ''); ?>  </textarea><br>

            <?php if (isset($errors['message'])): ?>
                <span class="error"><?php echo $errors['message']; ?></span><br>
            <?php endif; ?>

            Profile Picture: <input type="file" name="profile_picture"><br>
            <?php if (isset($USER['profile_picture']) && ! isset($errors['profile_picture'])): ?>
                <img src="<?php echo 'uploads/' . htmlspecialchars(basename($USER['profile_picture'])); ?>" alt="Profile Picture"><br>
            <?php endif; ?>

            <?php if (isset($errors['profile_picture'])): ?>
                <span class="error"><?php echo $errors['profile_picture']; ?></span><br>
            <?php endif; ?>

            <input type="submit" value="Submit">

            <input type="reset" value="Reset">

            <span><?php echo $message ?? ""; ?></span><br>
        </form>
    </div>
</body>

</html>