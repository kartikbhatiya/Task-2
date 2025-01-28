<?php

$host = 'localhost';
$db = 'php_task_1';
$customer = 'postgres';
$pass = 'jarvis';
$dsn = "pgsql:host=$host;dbname=$db";

try {
    $pdo = new PDO($dsn, $customer, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $sql = "
    CREATE TABLE IF NOT EXISTS admins (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

    $result = $pdo->exec($sql);

    if ($result === false) {
        echo "Error creating table: Admins ". $pdo->errorInfo();
        exit();
    }

    $sql = "
    CREATE TABLE IF NOT EXISTS customers (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        age INT,
        birthday DATE,
        gender VARCHAR(10),
        subscribe TEXT,
        country VARCHAR(50),
        message TEXT,
        isdeleted BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $result = $pdo->exec($sql);

    if ($result === false) {
        echo "Error creating table: Customers"  . $pdo->errorInfo();
        exit();
    }

    $sql = "
    CREATE TABLE IF NOT EXISTS images (
        id SERIAL PRIMARY KEY,
        customer_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        isdeleted BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
    ); 
    ";

    $result = $pdo->exec($sql);
    
    if ($result === false) {
        echo "Error creating table: Images" . $pdo->errorInfo();
        exit();
    } 
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}
?>
