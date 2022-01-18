<html>
    <head>
        <title>Add Vendor</title>
    </head>
    <body>
    <header align='center'>
        <h1>Add New Vendor</h1>
    </body> 
</html>

<pre>
    <form method="POST" action="addvendor.php">
        Vendor ID: <input type="hidden" name="Vendor_ID">
        Vendor Name: <input type="text" name="Vendor_Name" placeholder="Name" required>
        Phone Number: <input type="text" name="Phone_Number" placeholder="123-456-7890" required>
        <br>
        <input type="submit" value="SUBMIT">
        <tb>
        <input type="button" value="Cancel" onclick="location.href='admin.php'">
    </form>
</pre>



<?php

    require_once 'utility/login-cred.php';

    if(isset($_POST['Vendor_Name'])) {
        $conn = new mysqli($hn, $un, $pw, $db);
        if($conn->connect_error) die($conn->connect_error);

        $vendorName = $_POST['Vendor_Name'];
        $phoneNumber = $_POST['Phone_Number'];
 

        $query = "
        INSERT INTO vendor (Vendor_Name, Phone_Number)
        VALUES ('$vendorName', '$phoneNumber');";

        $result = $conn->query($query);
        if(!$result) die(mysqli_error($conn));

        header("Location: viewvendors.php");

    }

?>