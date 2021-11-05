<?php

    if(isset($_POST['deleted'])){
        require_once "utility/login-cred.php";
        $conn = new mysqli($hn,$un,$pw,$db);
        if($conn->connect_error) die($conn->$connect_error);

        $product_id = $_POST['product_id'];

        $query = "DELETE FROM cart_item WHERE product_id = '$product_id'";
        $result = $conn->query($query);
        if(!$result) die($result);
    }

    header("Location: cart.php");
?>