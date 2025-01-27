<?php 

function validatePassword($password){
    $error = NULL;
    if(empty($password)){
        $error = $error ?  $error . "Password is required" : "Password is required. ";
    }
    if(strlen($password) < 8){
        $error = $error ?  $error . "Password must be at least 8 characters long. " : "Password must be at least 8 characters long. ";
    }
    if(!preg_match('/[A-Z]/', $password)){
        $error = $error ?  $error ."Password must contain at least one uppercase letter. " : "Password must contain at least one uppercase letter. " ;
    }
    if(!preg_match('/[a-z]/', $password)){
        $error = $error ?  $error . "Password must contain at least one lowercase letter. " :  "Password must contain at least one lowercase letter. " ;
    }
    if(!preg_match('/[0-9]/', $password)){
         $error = $error ?  $error . "Password must contain at least one number. " : "Password must contain at least one number. " ;
    }
    if(!preg_match('/[^a-zA-Z0-9]/', $password)){
        $error = $error ?  $error . "Password must contain at least one special character. " : "Password must contain at least one special character. " ;
    }
    return $error;
}


function validateEmail ($email){
    $error = NULL;
    if(empty($email)){
        $error = $error ?  $error . "Email is required. " : "Email is required. ";
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = $error ?  $error . "Email is not valid. " : "Email is not valid. ";
    }
    return $error;
}

?>