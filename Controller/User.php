<?php

include __DIR__ .'\..\db\index.php';

function insertUser($data)
{
    global $pdo;

    $sql = "INSERT INTO users (name, email, password, age, birthday, gender, subscribe, country, message, profile_picture) 
        VALUES (:name, :email, :password, :age, :birthday, :gender, :subscribe, :country, :message, :profile_picture)";
    $stmt = $pdo->prepare($sql);
    try {
        $result = $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'age' => $data['age'],
            'birthday' => $data['birthday'],
            'gender' => $data['gender'],
            'subscribe' => $data['subscribe'],
            'country' => $data['country'],
            'message' => $data['message'],
            'profile_picture' => $data['profile_picture']
        ]);
        if ($result !== false) {
            return "Data successfully inserted!";
        }
    } catch (PDOException $e) {
        return "Database error: " . $e->getMessage();
    }
}

function fetchUsers()
{
    global $pdo;
    $sql = "SELECT * FROM users";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll() ?? [];
}

function fetchUser($id){
    global $pdo;
    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    // PDO::FETCH_ASSOC -> This Defines that fetch data as Associative Array
    // By Default it fetches data as both Associative and Numeric Array
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);   
    $user['subscribe'] = json_decode($user['subscribe'], true);
    return $user ?? [];
}

function editUser($data, $id){
    global $pdo;
    $sql = "UPDATE users SET ";
    $fields = [];
    $params = [];

    foreach ($data as $key => $value) {
        if ($key == 'id') {
            continue;
        }
        else {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
    }
    $fields[] = "updated_at = CURRENT_TIMESTAMP";

    $sql .= implode(', ', $fields);
    $sql .= " WHERE id = :id";
    $params['id'] = $id;


    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);

    return $result ? "User updated successfully!" : "Failed to update user.";
}

function deleteUser($id) {
    global $pdo;
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(['id' => $id]);

    return $result ? "User deleted successfully!" : "Failed to delete user.";
}

?>