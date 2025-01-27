<?php 

include __DIR__ .'\..\db\index.php';

function insertImages($id, $images){
    global $pdo;
    $errors = [];
   
    $sql = "INSERT INTO images (customer_id, image_path) VALUES (:customer_id, :image)";
    $stmt = $pdo->prepare($sql);
    try {
        foreach($images as $path){
            $result = $stmt->execute([
                'customer_id' => $id,
                'image_path' => $path
            ]);
        }
        if($result !== false && count($errors) > 0 ){
            return ['status' => false,'message'=> "Some of Images Not Uploaded ", 'errors' => $errors];
        }
        if ($result !== false) {
            return ['status' => true,'message'=> "Data successfully inserted!", 'data' => []];
        }

    } catch (PDOException $e) {
        echo $e->getMessage();
        return ['status' => false,'message'=> "Database error: " . $e->getMessage()];
    }

}

function fetchImages($id){
    global $pdo;
    $sql = "SELECT * FROM images WHERE customer_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetchAll() ?? [];
}

?>