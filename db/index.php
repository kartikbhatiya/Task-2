<?php

$host = 'localhost';
$db = 'php_task_1';
$user = 'postgres';
$pass = 'jarvis';
$dsn = "pgsql:host=$host;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the users table if it doesn't exist
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
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
        profile_picture VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $result = $pdo->exec($sql);
    
    // How to check whether query executed successfully or not
    
    // if ($result == false) {
    //     echo "Failed to create table 'users'.<br>";
    //     exit();
    // }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}
?>
