<!DOCTYPE HTML>
<html>

<?php
session_start();
if (!isset($_SESSION['adminId'])) {
    header('Location: pages/login.php');
}

include 'validation.php';
include './controller/customers.php';
include './controller/images.php';


$USER;
$id;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // echo "POST request received <br>";
    $errors = [];
    $data   = [];
    $images = [];
    $imagesError;
    $message;

    $id = $_POST['id'] ?? NULL;

    unset($_POST['id']);


    // $_POST['profile_picture'] = $_FILES['profile_picture'] ?? [];

    $upload_dir  = './uploads/';
    $uploadedImages = [];
    $images = $_FILES['profile_picture'];
    foreach ($images["full_path"] as $key => $image) {
        $sanitizedImage = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $image);
        $upload_file = $upload_dir . basename($sanitizedImage);
        if (move_uploaded_file($images['tmp_name'][$key], $upload_file)) {
            $uploadedImages[] = $upload_file;
        } else {
            $errors['images'] = basename($image) . "Failed to upload file.";
        }
    }
    if (count($errors) > 0) {
        return ['status' => false, 'message' => "Validation Erros", 'errors' => $errors];
    }

    $USER = $_POST;

    // Sanitize and validate other fields
    [$sanitizedData, $validationErrors] = SanitizeFields($_POST);
    $data                               = array_merge($data, $sanitizedData);
    $errors                             = array_merge($errors, $validationErrors);

    // Display results

    if (empty($errors)) {
        $data['subscribe'] = json_encode($data['subscribe'] ?? []);
        if ($id == NULL) {
            $customer = insertCustomer($data);

            if (!$customer['status']) {
                global $errors;
                // echo "Error inserting customer" . $customer['message'];
                // echo "Error inserting customer" . implode(',', $customer['errors']);
                PrintData($USER);
                $errors['email'] = $customer['message'];
            }

            $id = $customer['data']['id'];

            echo "Uploading image" . implode(',', $uploadedImages);

            echo "Uploading image";
            $images = insertImages($id, $uploadedImages);
            
            if (!$images['status']) {
                $imagesError = $images['message'];
            }

            $message = $customer['message'];
        } else {
            $newUser = fetchCustomer($id);
            $data = getDifferences($data, $newUser);
            echo "Uploading image" . implode(',', $uploadedImages);
            $images = insertImages($id, $uploadedImages);
            if (!$images['status']) {
                $imagesError = $images['message'];
            }
            $message = editCustomer($data, $id);
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    global $id;
    global $USER;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $customer = fetchCustomer($id);
        if ($customer) {
            $USER = $customer;
        }
    }
}


function PrintData($data)
{
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $key => $value) {
                if (is_array($value)) {
                    echo $key . " : " . implode('\n', $value) . "<br>";
                } else {
                    echo $key . " : " . $value . "<br>";
                }
            }
            echo "<br>";
        } else {
            echo $key . " : " . $value . "<br>";
        }
    }
}

function getDifferences($data, $USER)
{
    $differences = [];

    foreach ($data as $key => $value) {
        if (array_key_exists($key, $USER) && $USER[$key] != $value) {
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

    <div>
        <div>
            <a href="./index.php">Back to index</a>
        </div>

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
                                                            ?> required> Male

            <input type="radio" name="gender" value="female" <?php
                                                                if (isset($USER['gender']) && $USER['gender'] == 'female') {
                                                                    echo "checked";
                                                                }
                                                                ?> required> Female<br>

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

            Profile Pictures: <input type="file" name="profile_picture[]" multiple><br>
            <?php
            $uploadedImages = $uploadedImages ?? [];
            foreach ($uploadedImages as $image) {
                echo "<img src=" . htmlspecialchars($image) . " alt='Profile Picture'><br>";
            }
            ?>

            <?php if (isset($errors['profile_picture'])): ?>
                <span class="error"><?php echo $imagesError; ?></span><br>
            <?php endif; ?>

            <input type="submit" value="Submit">

            <input type="reset" value="Reset">

            <span><?php echo $message ?? ""; ?></span><br>
        </form>
    </div>
</body>

</html>