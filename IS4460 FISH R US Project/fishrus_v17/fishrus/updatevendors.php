<html> 
    <body>
    <header align='center'>
        <title>EDIT PAGE</title>
        <body>
        <h1>Edit Vendor</h1>

<?php

if(isset($_GET['Vendor_ID'])) {
    $vendorId = $_GET['Vendor_ID'];

    require_once 'utility/login-cred.php';
    
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($conn->connect_error);

    $query = "SELECT * FROM vendor WHERE Vendor_ID='$vendorId'";

    $result = $conn->query($query);
    if(!$result) die("No Result");

    $numRows = $result->num_rows;

    for($j = 0; $j < $numRows; $j++) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        }

    echo <<<_END
        <pre>
            <form method="POST" action="updatevendors.php">
                Vendor ID: <input type="hidden" name="Vendor_ID" value="$row[Vendor_ID]">
                Vendor Name: <input type="text" name="Vendor_Name" value="$row[Vendor_Name]">
                Phone Number: <input type="text" name="Phone_Number" value="$row[Phone_Number]">
                <input type="submit" value="UPDATE">
                <input type="button" value="Cancel" onclick="location.href='viewvendors.php'">
            </form>
        </pre>
    _END;
    
}


if(isset($_POST['Vendor_ID'])) {

    $vendorId = $_POST['Vendor_ID'];
    $vendorName = $_POST['Vendor_Name'];
    $phoneNumber = $_POST['Phone_Number'];
    
    require_once 'utility/login-cred.php';

    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($db);

    $query = "UPDATE vendor SET Vendor_Name='$vendorName', Phone_Number='$phoneNumber' WHERE Vendor_ID='$vendorId'";

    $result = $conn->query($query);
    if(!$result) die("No Result");

    header("Location: viewvendors.php");
}

?>

</body>
</html>