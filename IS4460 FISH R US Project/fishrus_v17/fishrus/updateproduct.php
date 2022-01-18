<html> 
    <body>
    <header align='center'>
        <title>EDIT PAGE</title>
        <body>
        <h1>Edit Product</h1>

<?php

if(isset($_GET['Product_ID'])) {
    $productId = $_GET['Product_ID'];

    require_once 'utility/login-cred.php';
    
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($conn->connect_error);

    $query = "SELECT * FROM product WHERE Product_ID='$productId'";

    $result = $conn->query($query);
    if(!$result) die("No Result");

    $numRows = $result->num_rows;

    for($j = 0; $j < $numRows; $j++) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        }

    echo <<<_END
        <pre>
            <form method="POST" action="updateproduct.php">
                Product_ID: <input type="hidden" name="Product_ID" value="$row[Product_ID]">
                Product_Name: <input type="text" name="Product_Name" value="$row[Product_Name]">
                Product_Price: <input type="text" name="Product_Price" value="$row[Product_Price]">
                Category: <input type="text" name="Category" value="$row[Category]">
                Vendor_ID: <input type="text" name="Vendor_ID" value="$row[Vendor_ID]">
                <input type="submit" value="UPDATE">
                <input type="button" value="Cancel" onclick="location.href='viewproduct.php'">
            </form>
        </pre>
    _END;
    
}



if(isset($_POST['Product_ID'])) {

    $productId = $_POST['Product_ID'];
    $productName = $_POST['Product_Name'];
    $productPrice = $_POST['Product_Price'];
    $category = $_POST['Category'];
    $vendorId = $_POST['Vendor_ID'];

    require_once 'utility/login-cred.php';

    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($conn->connect_error);

    $query = "UPDATE product SET Product_Name='$productName', Product_Price='$productPrice', Category='$category', Vendor_ID='$vendorId'
    WHERE Product_ID='$productId'";

    $result = $conn->query($query);
    if(!$result) die("No Result");

    header("Location: viewproduct.php");

}

?>

</body>
</html>