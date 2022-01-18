<?php

    if(isset($_POST['deleted'])) {
        
        require_once 'utility/login-cred.php';

        $conn = new mysqli($hn, $un, $pw, $db);
        if($conn->connect_error) die($conn->connect_error);

        $productID = $_POST['Product_ID'];

        $query = "DELETE FROM product WHERE Product_ID='$productID'";

        $query2 = "DELETE FROM inventory WHERE Product_ID='$productID'";

        $result = $conn->query($query);
        if(!$result) die("Unable to delete $productID");

        $result2 = $conn->query($query2);
        if(!$result2) die("Unable to delete $productID");


    }

    header("Location: viewproduct.php");
?>