<html> 
    <body>
    <header align='center'>
        <title>EDIT PAGE</title>
        <body>
        <h1>Edit Store</h1>

<?php

if(isset($_GET['Store_ID'])) {
    $storeId = $_GET['Store_ID'];

    require_once 'utility/login-cred.php';
    
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($conn->connect_error);

    $query = "SELECT * FROM store WHERE Store_ID='$storeId'";

    $result = $conn->query($query);
    if(!$result) die("No Result");

    $numRows = $result->num_rows;

    for($j = 0; $j < $numRows; $j++) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        }

    echo <<<_END
        <pre>
            <form method="POST" action="updatestore.php">
                Store ID: <input type="hidden" name="Store_ID" value="$row[Store_ID]">
                Phone Number: <input type="text" name="Phone" value="$row[Phone]">
                Street: <input type="text" name="Street" value="$row[Street]">
                City: <input type="text" name="City" value="$row[City]">
                State: <input type="text" name="State" value="$row[State]">
                Zip: <input type="text" name="ZIP" value="$row[ZIP]">
                Open: <input type="text" name="Start_Hour" value="$row[Start_Hour]">
                Close: <input type="text" name="End_Hour" value="$row[End_Hour]">
                <input type="submit" value="UPDATE">
                <input type="button" value="Cancel" onclick="location.href='viewstores.php'">
            </form>
        </pre>
    _END;
    
}


if(isset($_POST['Store_ID'])) {

    $storeId = $_POST['Store_ID'];
    $phone = $_POST['Phone'];
    $street = $_POST['Street'];
    $city = $_POST['City'];
    $state = $_POST['State'];
    $zip = $_POST['ZIP'];
    $open = $_POST['Start_Hour'];
    $close = $_POST['End_Hour'];
 
    require_once 'utility/login-cred.php';

    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($db);

    $query = "UPDATE store SET Phone='$phone', Street='$street', City='$city', State='$state', ZIP='$zip', Start_Hour='$open', End_Hour='$close' WHERE Store_ID='$storeId'";

    $result = $conn->query($query);
    if(!$result) die("No Result");

    header("Location: viewstores.php");
}

?>

</body>
</html>
