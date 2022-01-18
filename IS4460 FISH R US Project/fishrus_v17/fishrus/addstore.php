<html>
    <head>
        <title>Add Store</title>
    </head>
    <body>
    <header align='center'>
        <h1>Add New Store</h1>
    </body> 
</html>

<pre>
    <form method="POST" action="addstore.php">
        Store ID: <input type="hidden" name="Store_ID">
        Street: <input type="text" name="Street" placeholder="123 Fake Street" required>
        City: <input type="text" name="City" placeholder="Las Vegas" required>
        State: <input type="text" name="State" placeholder="NV" required>
        Zip: <input type="text" name="ZIP" placeholder="90210" required>
        Phone Number: <input type="text" name="Phone" placeholder="123-456-7890" required>
        <br>
        <input type="submit" value="SUBMIT">
        <tb>
        <input type="button" value="Cancel" onclick="location.href='admin.php'">
    </form>
</pre>



<?php

    require_once 'utility/login-cred.php';

    if(isset($_POST['Store_ID'])) {
        $conn = new mysqli($hn, $un, $pw, $db);
        if($conn->connect_error) die($conn->connect_error);

        $street = $_POST['Street'];
        $city = $_POST['City'];
        $state = $_POST['State'];
        $zip = $_POST['ZIP'];
        $phone = $_POST['Phone'];

 

        $query = "
        INSERT INTO store (State, City, Street, ZIP, Phone )
        VALUES ('$state','$city','$street','$zip','$phone');";

        $result = $conn->query($query);
        if(!$result) die(mysqli_error($conn));

        header("Location: viewstores.php");

    }

?>