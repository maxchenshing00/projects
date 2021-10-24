<?php
    require_once "utility/sanitize.php";
    require_once 'utility/login-cred.php';

    // Starting connection with database
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die("Fatal Error");

    // Getting the product name from URL
    if(isset($_GET['product'])){
        $product = sanitizeString($_GET['product']);
    }else{
        $product = "ERROR ERROR ERROR";
    }

    // Setting variables for display in product.php
    // query for product table
    $query = "SELECT * FROM product WHERE Product_Name='$product'";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed in product.php");

    $row = $result->fetch_array(MYSQLI_ASSOC);

    $product_price = sanitizeMySQL($conn, $row['Product_Price']); // product price
    $product_id = sanitizeMySQL($conn, $row['Product_ID']);
    $product_category = sanitizeMySQL($conn, $row['Category']); // used down below for image relative path

    // query for inventory table
    $query = "SELECT * FROM inventory WHERE Product_ID='$product_id'";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed in product.php");

    $rows = $result->num_rows;

    $quantity = 0; // quantity drop down
    $stock_message = "IN STOCK"; //in stock message

    for ($i = 0; $i < $rows; ++$i)
    {
        $row = $result->fetch_array(MYSQLI_ASSOC);

        $sub_quantity = sanitizeMySQL($conn, $row['Quantity']);
        $quantity += $sub_quantity;
    }

    if($quantity == 0){
        $sales_message = "";
    }

    // query for discount table
    $query = "SELECT * FROM discount WHERE Product_ID='$product_id'";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed in product.php");

    $rows = $result->num_rows;

    $discount_message = ""; // discount message
    
    if ($rows != 0){
        $discount_message = "ON SALE!";

        $row = $result->fetch_array(MYSQLI_ASSOC);

        $discount = sanitizeMySQL($conn, $row['Discount']);

        $product_price = $product_price - ($product_price * $discount);

        $product_price = round($product_price, 2);
        $product_price = sprintf('%0.2f', $product_price); 
    }

    // query for genus table
    $query = "SELECT * FROM genus WHERE Product_ID='$product_id'";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed in product.php");

    $rows = $result->num_rows;

    $genus = ""; //genus
    
    if ($rows != 0){
        $row = $result->fetch_array(MYSQLI_ASSOC);

        $genus = sanitizeMySQL($conn, $row['Genus']);
        $genus = '('.$genus.')';
    }

    // create image relative path
    $image_relative_path = 'product-images\\'; //.str_replace(' ', '-', $product); //image relative path
    $image_name = str_replace(' ', '-', $product);

    if($product_category == "Fish")
    {
        $image_relative_path = $image_relative_path.'marine-fish\\';
    }
    else if($product_category == "Invert")
    {
        $image_relative_path = $image_relative_path.'marine-inverts\\';
    }
    else if($product_category == "Food")
    {
        $image_relative_path = $image_relative_path.'frozen-food\\';
    }
    else if($product_category == "Plant")
    {
        $image_relative_path = $image_relative_path.'fresh-water-plants\\';
    }
    else if($product_category == "Aquascaping")
    {
        $image_relative_path = $image_relative_path.'aquascaping\\';
    }
    else if($product_category == "Aquarium")
    {
        $image_relative_path = $image_relative_path.'aquarium\\';
    }
    else
    {
        $image_relative_path = 'product-images\\';
    }

    if (file_exists($image_relative_path.$image_name.'.png')) 
    {   
        $image_relative_path = $image_relative_path.$image_name.'.png';
    } 
    else if (file_exists($image_relative_path.$image_name.'.jpg')) 
    {
        $image_relative_path = $image_relative_path.$image_name.'.jpg';
    } 
    else {
        $image_relative_path = 'product-images\\';
    }

    echo <<<_END
    <html>
        <head>
            <title>FISH R US</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <style>
                /* start of nav bar */
                .top-nav {
                    background-color:#3587b8;
                    overflow: hidden;
                    height:48px; 
                }

                .top-nav a {
                    float: left;
                    color: black;
                    text-align: center;
                    padding: 14px 16px;
                    text-decoration: none;
                    font-size: 17px; 
                }

                .top-nav form {
                    float: left;
                    color: black;
                    text-align: center;
                    padding: 12px 16px;
                    text-decoration: none;
                }

                .top-nav a:hover{
                    background-color: #ddd;
                    color: black;
                }

                .dropdown {
                    float: left;
                    overflow: hidden;
                }

                .dropdown .dropbtn {
                    font-size: 17px;  
                    border: none; 
                    outline: none;
                    color: black;
                    padding: 12px 16px; 
                    background-color: #3587b8;
                    font-family: inherit;
                    margin: 0;
                }

                .dropdown:hover .dropbtn {
                    background-color:#ddd;
                }

                .dropdown-content {
                    display: none;
                    position: absolute;
                    background-color: #f9f9f9;
                    min-width: 160px;
                    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                    z-index: 1;
                }

                .dropdown-content a {
                    float: none;
                    color: black;
                    padding: 14px 16px;
                    text-decoration: none;
                    display: block;
                    text-align: left;
                }

                .dropdown-content a:hover {
                    background-color: #ddd;
                }

                .dropdown:hover .dropdown-content {
                display: block;
                }
                /* end of nav bar */

                /* start of table css */
                table {
                    width:100%;
                    align:left;
                }

                td {
                    text-align:center;
                }

                .table-item {
                    width: 200px;
                    text-align: center;
                }
                /* end of table css */
            </style>
        </head>

        <body>
            <!-- nav bar -->
            <header class="top-nav">
                <div>
                    <a href="home.php">FISH R US</a>
                </div>

                <div>
                    <form method="GET" action="product-list.php" name="site-search" style="display:inline">
                        <div> 
                            <input type="text" size="50"> <!-- size of search bar -->
                            <input type="submit" value="Go">
                        </div>
                    </form>
                </div>

                <div class="dropdown">
                    <button class="dropbtn">Account 
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="login.php">Sign In</a>
                        <a href="#">Register</a>
                    </div>
                </div>

                <div>
                    <a style="float:right;" href="cart.php">
                        <img src="img\cart-icon.png" width="20" height="20">
                    </a>
                </div>
            </header>

            <!-- product info -->
            <table style="width:50%;margin-left:auto;margin-right:auto">
            <tr>
                <h3>$product</h3>
            </tr>
            <tr>
                <td>
                    <img src="$image_relative_path" height="250" width="250"/>
                </td>
                <td style="text-align:left;">
                    <span style="background-color:yellow;font-style:bold">$discount_message<br><br></span>
                    <br>
                    $product<br>
                    $genus<br><br>
                    Starting at <span style="font-style:bold">$$product_price</span><br>
                    <span style="font-style:italic">$stock_message</span>
                    <br><br>
                    <form method="GET" action="product.php?product=$product" name="add-to-cart" style="display:inline">
                        Quantity 
                        <select name="quantity" size="1">
_END;
                            for ($i = 1; $i < ($quantity + 1); ++$i){
                                echo <<<_END
                                    <option value="$i">$i</option>
                                _END;
                            }
    echo <<<_END
                        </select>
                        <br><br><br>
                        <div> 
                            <input type="submit" value="Add to Cart">
                        </div>
                        <div> 
                            <input type="hidden" name="product" value="$product">
                            <input type="hidden" name="submitted" value="submitted">
                        </div>
                    </form>
                </td>
            </tr>
            </table>
            <table>
                <tr>
                    <td>
_END;
                // Confirming product added to cart
                if(isset($_GET['submitted'])){
                    echo <<<_END
                        <br><br><br><span style="font-style:italic">Your product has been added to the shopping cart.</span>
                    _END;
                } 
echo <<<_END
                    </td>
                </tr>
            </table>
        </body>
    </html>
_END;
?>

