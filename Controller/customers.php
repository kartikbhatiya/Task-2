<?php

include __DIR__ .'\..\db\index.php';

function insertCustomer($data)
{
    global $pdo;
    $email = $data['email'];

    if(checkCustEmail($email)){
        $errors['email'] = "Email already exists";
        return ['status' => false,'message'=> "Validation Erros",'errors' => $errors];
    }

    $sql = "INSERT INTO customers (name, email, password, age, birthday, gender, subscribe, country, message) 
        VALUES (:name, :email, :password, :age, :birthday, :gender, :subscribe, :country, :message)";
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
            'message' => $data['message']
        ]);
        if ($result !== false) {
            $id = $pdo->lastInsertId();
            return ['status' => true, 'message' => "Data successfully inserted!", 'data' => ['id' => $id]];
        }
        else{
            return ['status' => false, 'message' => "Failed to insert data.", 'errors' => []];
        }
    } catch (PDOException $e) {
        return ['status' => false, 'message' => "Database error: " . $e->getMessage(),'errors' => []];
    }
}

function fetchCustomers()
{
    global $pdo;
    $sql = "SELECT * FROM customers ORDER BY id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll() ?? [];
}

function searchCustomers($searchTerm)
{
    global $pdo;
    $sql = "SELECT * FROM customers WHERE id::text LIKE :search OR name LIKE :search OR email LIKE :search ORDER BY id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['search' => '%' . $searchTerm . '%']);
    return $stmt->fetchAll() ?? [];
}

function checkCustEmail($email){
    global $pdo;
    $sql = "SELECT * FROM customers WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $result = $stmt->fetch();
    if($result){
        return true;
    }
    return false;
}

function fetchCustomer($id){
    global $pdo;
    $sql = "SELECT * FROM customers WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    
    // PDO::FETCH_ASSOC -> This Defines that fetch data as Associative Array
    // By Default it fetches data as both Associative and Numeric Array
    
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);   
    $customer['subscribe'] = json_decode($customer['subscribe'], true);
    return $customer ?? [];
}

function editCustomer($data, $id){
    global $pdo;
    $sql = "UPDATE customers SET ";
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

    return $result ? "Customer updated successfully!" : "Failed to update customer.";
}

function deleteCustomer($id) {
    global $pdo;
    $sql = "DELETE FROM customers WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute(['id' => $id]);

    return $result ? "Customer deleted successfully!" : "Failed to delete customer.";
}

function updateCustomerStatus($id, $status) {
    global $pdo;
    $sql = "UPDATE customers SET isdeleted = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute(['status' => $status, 'id' => $id]);
}

?>