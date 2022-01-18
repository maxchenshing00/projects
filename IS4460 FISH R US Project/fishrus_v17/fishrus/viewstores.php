<html> 
    <body>
    <header align='center'>
        <title>DELETE EDIT PAGE</title>
        <body>
        <h1>To Edit</h1>
        <h2>Click On Store Street Link</h2>


<?php

require_once 'utility/login-cred.php';

$conn = new mysqli($hn, $un, $pw, $db);
if($conn->connect_error) die($conn->connect_error);

$query = "SELECT * FROM store";

$result = $conn->query($query);
if(!$result) die("No Result");

$numRows = $result->num_rows;

for($j = 0; $j < $numRows; ++$j) {
    $row = $result->fetch_array(MYSQLI_ASSOC);

echo <<<_END
    <pre>
        Store ID: $row[Store_ID]
        State: $row[State]
        City: $row[City]
        Street: <a href="updatestore.php?Store_ID=$row[Store_ID]">$row[Street]</a>
        ZIP: $row[ZIP]
        Phone: $row[Phone]
        Open: $row[Start_Hour]
        Close: $row[End_Hour]
        
        <form action="deletestore.php" method="POST">
            <input type="hidden" name="deletedstore" value="yes">
            <input type="hidden" name="Street" value="$row[Street]">
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