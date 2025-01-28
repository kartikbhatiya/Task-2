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

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_image_id'])) {
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
    if (!empty($images['name'][0])) {
        print_r($images);
        foreach ($images["full_path"] as $key => $image) {
            $sanitizedImage = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $image);
            $upload_file = $upload_dir . basename($sanitizedImage);
            if (move_uploaded_file($images['tmp_name'][$key], $upload_file)) {
                $uploadedImages[] = $upload_file;
            } else {
                $errors['images'] = basename($image) . "Failed to upload file.";
            }
        }
    }

    $USER = $_POST;

    // Sanitize and validate other fields
    [$sanitizedData, $validationErrors] = SanitizeFields($_POST);
    $data                               = array_merge($data, $sanitizedData);
    $errors                             = array_merge($errors, $validationErrors);

    // Display results

    if (empty($errors)) {
        $data['subscribe'] = json_encode($data['subscribe'] ?? []);

        // if $id Not Exists means it is New User 
        if ($id == NULL) {
            echo "Inserting new customer";
            $customer = insertCustomer($data);

            if (!$customer['status']) {
                global $errors;
                // echo "Error inserting customer" . $customer['message'];
                // echo "Error inserting customer" . implode(',', $customer['errors']);
                PrintData($USER);
                $errors['email'] = $customer['message'];
            }

            $id = $customer['data']['id'];

            // echo "Uploading image" . implode(',', $uploadedImages);

            // echo "Uploading image";
            $images = insertImages($id, $uploadedImages);

            if (!$images['status']) {
                $imagesError = $images['message'];
            }

            $message = $customer['message'];
        } else {
            $newUser = fetchCustomer($id);
            global $message;
            if (!isset($newUser)) {
                $message = "Customer not found";
            } else {
                $data = getDifferences($data, $newUser);
                if (!empty($data)) {
                    $message = editCustomer($data, $id);
                }
                if (!empty($uploadedImages)) {
                    $images = insertImages($id, $uploadedImages);
                    !$images['status'] ? $imagesError = $images['message'] : $imagesError = NULL;
                }
            }
        }
    } else {
        // echo "Errors found";
        // print_r($errors);
        $uploadedImages = $id ? fetchImages($id) : $uploadedImages;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    global $id;
    global $USER;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $customer = fetchCustomer($id);
        if (isset($customer)) {
            $USER = $customer;
        }
        $uploadedImages = fetchImages($id);
        //    print_r($uploadedImages);
        //     if($uploadedImages){
        //         $uploadedImages = array_column($uploadedImages, 'image_path', 'id');
        //     }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_image_id'])) {
    $USER = $_POST;
    global $id;
    $id = $_POST['id'] ?? $id;
    $imageId = $_POST['delete_image_id'];
    // echo "Deleting image with id: " . $imageId;
    if (deleteImage($imageId)) {
        $uploadedImages = fetchImages($id);
    } else {
        $errors['images'] = "Failed to delete image.";
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

        .container{
            margin-top: 75px;
            padding-top: 75px;
        }

        img {
            max-width: 100px;
            height: auto;
        }

        .rich-text-editor {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 100px;
        }

        .toolbar {
            margin-bottom: 10px;
        }

        .toolbar button {
            margin-right: 5px;
        }

        @media (max-height: 800px) {
            form {
                overflow-y: scroll;
            }
        }
    </style>

</head>

<body>

    <div class="container">
        <div>
            <a href="./index.php">Back to index</a>
        </div>

        <form method="post" action="./form.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php global $id;
                                                    echo $id ? htmlspecialchars($id) : NULL ?>">
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

                <option value="" <?php
                    if (!isset($USER['country'])) {
                        echo "selected";
                    }
                    ?>>Select Country</option>

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

            <!-- Message: <textarea name="message">  <?php if (isset($USER['message'])) echo htmlspecialchars($USER['message']); ?>  </textarea><br> -->
            <label for="richTextEditor">Message:</label>
            <div class="toolbar">
                <button type="button" onclick="execCmd('bold')"><b>Bold</b></button>
                <button type="button" onclick="execCmd('italic')"><i>Italic</i></button>
                <button type="button" onclick="execCmd('underline')"><u>Underline</u></button>
                <button type="button" onclick="execCmd('strikeThrough')"><s>Strike</s></button>
                <button type="button" onclick="execCmd('justifyLeft')">Left</button>
                <button type="button" onclick="execCmd('justifyCenter')">Center</button>
                <button type="button" onclick="execCmd('justifyRight')">Right</button>
                <button type="button" onclick="execCmd('justifyFull')">Justify</button>
                <button type="button" onclick="execCmd('insertUnorderedList')">UL</button>
                <button type="button" onclick="execCmd('insertOrderedList')">OL</button>
                <button type="button" onclick="execCmd('undo')">Undo</button>
                <button type="button" onclick="execCmd('redo')">Redo</button>
            </div>
            <div id="richTextEditor" class="rich-text-editor" contenteditable="true">
                <?php echo htmlspecialchars_decode($USER['message'] ?? ''); ?>
            </div>
            <textarea name="message" id="message" style="display: none;"><?php echo htmlspecialchars($USER['message'] ?? ''); ?></textarea><br>


            <?php if (isset($errors['message'])): ?>
                <span class="error"><?php echo $errors['message']; ?></span><br>
            <?php endif; ?>

            Profile Pictures: <input type="file" name="profile_picture[]" multiple><br>
            <?php
            $uploadedImages = $uploadedImages ?? [];
            echo "<div style='display: inline-block; gap: 10px;'>";

            if (count($uploadedImages) == 0) {
                echo "<span> No images uploaded </span>";
            } else {
                foreach ($uploadedImages as $image) { ?>
                    <div style='display: inline-block; margin: 10px;'>
                        <img src='<?php echo $image['image_path']; ?>' alt='Profile Picture'><br>
                        <button type='submit' name='delete_image_id' value='<?php echo $image['id']; ?>'>Delete</button>
                    </div>
            <?php }
            }

            echo "</div>";
            ?>

            <?php if (isset($imagesError)): ?>
                <span class="error"><?php echo $imagesError ?? ''; ?></span><br>
            <?php endif; ?>

            <br>
            <input type="submit" value="Submit">

            <input type="reset" value="Reset">

            <span><?php echo $message ?? ""; ?></span><br>
        </form>
    </div>

    <script>
        function execCmd(command) {
            document.execCommand(command, false, null);
        }

        document.querySelector('form').addEventListener('submit', function() {
            var richTextEditor = document.getElementById('richTextEditor');
            var message = document.getElementById('message');
            message.value = richTextEditor.innerHTML;
        });
    </script>
</body>

</html>