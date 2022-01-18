<?php

    if(isset($_POST['deletedstore'])) {
        
        require_once 'utility/login-cred.php';

        $conn = new mysqli($hn, $un, $pw, $db);
        if($conn->connect_error) die($conn->connect_error);

        $street = $_POST['Street'];

        $query = "DELETE FROM store WHERE Street ='$street'";

        $result = $conn->query($query);
        if(!$result) die(mysqli_error($conn));


    }

    header("Location: viewstores.php");
?>