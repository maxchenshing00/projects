<?php

    if(isset($_POST['deletedcustomer'])) {
        
        require_once 'utility/login-cred.php';

        $conn = new mysqli($hn, $un, $pw, $db);
        if($conn->connect_error) die($conn->connect_error);

        $username = $_POST['Username'];

        $query = "DELETE FROM user WHERE Username='$username'";

        $result = $conn->query($query);
        if(!$result) die("Unable to delete $username");


    }

    header("Location: viewcustomer.php");
?>