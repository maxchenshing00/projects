<html> 
    <body>
    <header align='center'>
        <title>DELETE EDIT PAGE</title>
        <body>
        <h1>To Edit</h1>
        <h2>Click On Username Link</h2>


<?php

require_once 'utility/login-cred.php';

$conn = new mysqli($hn, $un, $pw, $db);
if($conn->connect_error) die($conn->connect_error);

$query = "SELECT * FROM user";

$result = $conn->query($query);
if(!$result) die("No Result");

$numRows = $result->num_rows;

for($j = 0; $j < $numRows; ++$j) {
    $row = $result->fetch_array(MYSQLI_ASSOC);

echo <<<_END
    <pre>
        Customer ID: $row[ID]
        Username: <a href="updatecustomer.php?ID=$row[ID]">$row[Username]</a>
        First Name: $row[First_Name]
        Last Name: $row[Last_Name]
        Phone Number: $row[Phone_Number]
        Street: $row[Street]
        City: $row[City]
        State: $row[State]
        Zip: $row[ZIP]
        Email: $row[Email]
        <form action="deletecustomer.php" method="POST">
            <input type="hidden" name="deletedcustomer" value="yes">
            <input type="hidden" name="Username" value="$row[Username]">
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