<html> 
    <body>
    <header align='center'>
        <title>DELETE EDIT PAGE</title>
        <body>
        <h1>To Edit</h1>
        <h2>Click On Product Name Link</h2>


<?php

require_once 'utility/login-cred.php';

$conn = new mysqli($hn, $un, $pw, $db);
if($conn->connect_error) die($conn->connect_error);

$query = "SELECT * FROM product";

$result = $conn->query($query);
if(!$result) die("No Result");

$numRows = $result->num_rows;

for($j = 0; $j < $numRows; ++$j) {
    $row = $result->fetch_array(MYSQLI_ASSOC);

    
echo <<<_END
    <pre>
        ProductID: $row[Product_ID]
        Product Name: <a href="updateproduct.php?Product_ID=$row[Product_ID]">$row[Product_Name]</a>
        Product Price: $row[Product_Price]
        Cateogry: $row[Category]
        Vendor ID: $row[Vendor_ID]
        <form action="deleteproduct.php" method="POST">
            <input type="hidden" name="deleted" value="yes">
            <input type="hidden" name="Product_ID" value="$row[Product_ID]">
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