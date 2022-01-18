<html>
    <head>
        <title>Add Product</title>
    </head>
    <body>
    <header align='center'>
        <h1>Add New Product</h1>
    </body> 
</html>

<pre>
    <form method="POST" action="addproduct.php">
        Product Name: <input type="text" name="Product_Name" placeholder="Goldfish" required>
        Product Price: <input type="text" name="Product_Price" placeholder="1.99" required>
        Category: <input type="text" name="Category" placeholder="Fish, Food, Plant" required>
        Vendor ID: <input type="text" name="Vendor_ID" placeholder="1" required>
        Store ID: <input type="text" name="Store_ID" placeholder="1" required>
        Quantity: <input type="text" name="Quantity" placeholder="1" required>
        <br>
        <input type="submit" value="SUBMIT">
        <tb>
        <input type="button" value="Cancel" onclick="location.href='admin.php'">
    </form>
</pre>



<?php

    require_once 'utility/login-cred.php';

    if(isset($_POST['Product_Name'])) {
        $conn = new mysqli($hn, $un, $pw, $db);
        if($conn->connect_error) die($conn->connect_error);

        $productName = $_POST['Product_Name'];
        $productPrice = $_POST['Product_Price'];
        $category = $_POST['Category'];
        $vendorId = $_POST['Vendor_ID'];
        $storeId = $_POST['Store_ID'];
        $quantity = $_POST['Quantity'];

        $query = "
        INSERT INTO product (Product_Name, Product_Price, Category, Vendor_ID)
        VALUES ('$productName', '$productPrice', '$category', '$vendorId');";

        $query2 = "SET @newprodid = (SELECT Product_ID FROM product ORDER BY Product_ID DESC LIMIT 1);";

        $query3 = "
        INSERT INTO inventory (Product_ID, Vendor_ID, Store_ID, Inventory_Date, Quantity)
        VALUES (@newprodid,'$vendorId','$storeId',CURRENT_DATE,'$quantity');";

        

        $result = $conn->query($query);
        if(!$result) die(mysqli_error($conn));

        $result2 = $conn->query($query2);
        if(!$result2) die(mysqli_error($conn));

        $result3 = $conn->query($query3);
        if(!$result2) die(mysqli_error($conn));

        header("Location: viewproduct.php");

    }

?>

