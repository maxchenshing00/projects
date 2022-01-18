<html> 
    <body>
    <header align='center'>
        <title>DELETE EDIT PAGE</title>
        <body>
        <h1>To Edit</h1>
        <h2>Click On Vendor Name Link</h2>


<?php

require_once 'utility/login-cred.php';

$conn = new mysqli($hn, $un, $pw, $db);
if($conn->connect_error) die($conn->connect_error);

$query = "SELECT * FROM vendor";

$result = $conn->query($query);
if(!$result) die("No Result");

$numRows = $result->num_rows;

for($j = 0; $j < $numRows; ++$j) {
    $row = $result->fetch_array(MYSQLI_ASSOC);

echo <<<_END
    <pre>
        Vendor ID: $row[Vendor_ID]
        Vendor Name: <a href="updatevendors.php?Vendor_ID=$row[Vendor_ID]">$row[Vendor_Name]</a>
        Phone Number: $row[Phone_Number]
        <form action="deletevendor.php" method="POST">
            <input type="hidden" name="deletedvendor" value="yes">
            <input type="hidden" name="Vendor_Name" value="$row[Vendor_Name]">
            <input type="submit" value="DELETE RECORD">
            <tb>
            <input type="button" value="Cancel" onclick="location.href='admin.php'">
        </form>
    </pre>
_END;
}

?>

</body>
</html>