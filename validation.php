<?php

function SanitizeFields($postData) {
    $sanitizedArray = [];
    $errors = [];
    foreach ($postData as $key => $value) {
        $sanitizedValue = sanitize($value, $key);
        $res = validateField($sanitizedValue, $key);
        if(isset($res)) {
           $errors[$key] = $res;
        }
        else {
            $sanitizedArray[$key] = $sanitizedValue;
        }
    }
    return [$sanitizedArray, $errors];
}

function sanitize($data, $key) {
    if (!is_array($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    foreach ($data as $key => $value) {
        $data[$key] = trim($value);
        $data[$key] = stripslashes($value);
        $data[$key] = htmlspecialchars($value);
    }
    return $data;
}

function validateField($value, $key) {
    switch ($key) {
        case 'name':
            return validateString($value);
        case 'email':
            return validateEmail($value);
        case 'password':
            return validatePassword($value);
        case 'age':
            return validateAge($value);
        case 'birthday':
            return validateBirthday($value);
        case 'country':
            return validateCountry($value);
        default:
            return NULL;
    }
}

function validateCountry($value) {
    if(empty($value)){
        return "Country is required.";
    }
    return NULL;
}


function validateString($string) {
    if(empty($string)){
        return "Name is required.";
    }
    if(!preg_match("/^[a-zA-Z ]*$/", $string)){
        return "Name can only contain letters and white spaces.";
    }
    return NULL;
}

function validateEmail($email) {
    if(empty($email)){
        return "Email is required.";
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return "Invalid email format.";
    }
    return NULL;
}

function validatePassword($password) {
    if(empty($password)){
        return "Password is required.";
    }
    if(strlen($password) < 8){
        return "Password must be at least 8 characters long.";
    }
    $message = "";
    if(!preg_match("/[A-Z]/", $password)){
        $message .= "Password must contain at least one uppercase letter. ";
    }
    if(!preg_match("/[a-z]/", $password)){
        $message .= "Password must contain at least one lowercase letter. ";
    }
    if(!preg_match("/[0-9]/", $password)){
        $message .= "Password must contain at least one number.";
    }
    if(!preg_match("/[\W_]/", $password)){
        $message .= "Password must contain at least one special character. ";
    }
    return empty($message) ? NULL : $message;
}

function validateAge($age) {
    if(empty($age)){
        return "Age is required.";
    }
    if(!filter_var($age, FILTER_VALIDATE_INT)){
        return "Age must be a number.";
    }
    if($age < 0 || $age > 101){
        return "Age must be between 1 and 100.";
    }
    return NULL;
}

function validateBirthday($date) {
    if(empty($date)){
        return "Birthday is required.";
    }

    $d = DateTime::createFromFormat('Y-m-d', $date);
    if($d > new DateTime()){
        return "Birthday must be in the past.";
    }
    
    return NULL;
}

function validateFile($file) {

    if($file['error'] === UPLOAD_ERR_NO_FILE){
        return "Profile picture is required.";
    }
    if($file['error'] !== UPLOAD_ERR_OK){
        return "Error uploading file.";
    }
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if(!in_array($file['type'], $allowedTypes)){
        return "Invalid file type.";
    }
    if($file['size'] > 40 * 1024 ){
        return "File size must be less than 40 KB.";
    }

    return NULL;
}

?>