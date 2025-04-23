<?php 
include __DIR__ .'\..\db\index.php';
include __DIR__ .'\..\utils\functions.php';

function getAdmin($email){
    global $pdo;
    $sql = "SELECT * FROM admins WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    return $stmt->fetch() ?? [];
}


function insertAdmin($data){
    global $pdo;
    $errors = [];

    $name = $data['name'];
    if(empty($name)){
        $errors['name'] = "Name is required";
    }
    
    $email = $data['email'];
    if(empty($email)){
        $errors['email'] = "Email is required";
    }
    
    $admin = getAdmin($email);

    $adminError = validateEmail($email);
    if(isset($adminError)){
        $errors['email'] = $adminError;
    }
    
    if(!empty($admin)){
        $errors['email'] = "Email already exists";
    }

    $password = $data['password'];
    $passwordError = validatePassword($password);
    if(isset($passwordError)){
        $errors['password'] = $passwordError;
    }

    if(count($errors) > 0){
        return ['status' => false,'message'=> "Validation Erros",'errors' => $errors];
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $pdo->prepare($sql);
    try {
        $result = $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);
        if ($result !== false) {
            return [
            "status" => true,    
            "message" => "Data successfully inserted!",
            "data" => []
            ];  
        }
    } catch (PDOException $e) {
        return [
            "status" => false,
            "message" . $e->getMessage(),
            "data" => []];
    }
}

function loginAdmin($data){
    $errors = [];

    $email = $data['email'];
    if(empty($email)){
        $errors['email'] = "Email is required";
    }
    
    $password = $data['password'];
    if(empty($password)){
        $errors['password'] = "Password is required";
    }
    
    $admin = getAdmin($email);
    if(empty($admin)){
        $errors['email'] = "Admin not found";
        return ['status' => false,'message'=> "Validation Erros",'errors' => $errors];
    }

    if(!password_verify($password, $admin['password'])){
        $errors['password'] = "Invalid Credentials";
    }

    if(count($errors) > 0){
        return ['status' => false,'message'=> "Validation Erros",'errors' => $errors];
    }

    // PrintAdmin($admin);

    return [
        "status" => true,
        "message" => "Login successful",
        "data" => $admin
    ];
}


function PrintAdmin($admin){
    echo "<pre>";
    foreach($admin as $key => $value){
        echo $key . " : " . $value . "<br>";
    }
    echo "</pre>";  

}



?>