<?php

    if(isset($_POST['deletedvendor'])) {
        
        require_once 'utility/login-cred.php';

        $conn = new mysqli($hn, $un, $pw, $db);
        if($conn->connect_error) die($conn->connect_error);

        $vendorName = $_POST['Vendor_Name'];

        $query = "DELETE FROM vendor WHERE Vendor_Name='$vendorName'";

        $result = $conn->query($query);
        if(!$result) die("Unable to delete $vendorName");


    }

    header("Location: viewvendors.php");
?>