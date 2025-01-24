<?php 

    include './Controller/User.php';

    $id = $_GET['id'] ?? NULL;
    if($id == NULL){
        echo "ID is required!";
        return;
    }
    else{
        echo deleteUser($id);
    }

?>