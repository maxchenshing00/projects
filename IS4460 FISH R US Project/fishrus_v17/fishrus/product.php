<?php
require_once "utility/sanitize.php";
require_once "utility/login-cred.php";
require_once "utility/useful-functions.php";
require_once "User.php";

// Starting connection with database
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Error: Failed to connect to database.");

if (!isset($_SESSION)) { session_start(); }
?>

<?php
    // Check to see if product needs to be added to cart
    if(isset($_POST['submitted'])){
        addProductToCart($conn);
    }

    // Record the product details for display in the page
    $productDetails = displayProductDetails($conn);
    $product_id = $productDetails['product_id'];
    $product = $productDetails['product_name'];
    $product_price = $productDetails['product_price'];
    $product_category = $productDetails['product_category'];
    $quantity = $productDetails['quantity'];
    $stock_message = $productDetails['stock_message'];
    $discount_message = $productDetails['discount_message'];
    $genus = $productDetails['genus'];
    $image_relative_path = $productDetails['image_path'];
    

    // Check if quantity for the product needs to be decreased if it is already in the cart table
    if (array_key_exists('user',$_SESSION)){ // fixed bug
        $user = $_SESSION['user'];
        $user_id = $user->id;

        $query = "SELECT * FROM cart_item WHERE ID=$user_id";
        $result = $conn->query($query);
        if(!$result) die ($result);

        $minus_from_quantity = 0;

        $numRows = $result->num_rows;
        for ($i = 0; $i < $numRows; ++$i)
        {
            $row = $result->fetch_array(MYSQLI_ASSOC);

            if($product_id == $row['Product_ID']){
                $minus_from_quantity += $row['Quantity'];
            }
        }

        $quantity -= $minus_from_quantity;

        if ($quantity == 0){
            $stock_message = "OUT OF STOCK";
        }
    }
?>

<html> 
    <head>
        <title>FISH R US</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            /* start of nav bar */
            .top-nav {
                background-color:#3587b8;
                overflow: hidden;
                height:46px; 
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

            /* start of paragraph formatting */
            p.format {
                margin-top: 0;
                margin-bottom: 0;
                margin-left: 1em;
                margin-right: 1em;
                padding: 0px 0px 0px 0px;
            }
            /* end of paragraph formatting */
        </style>
    </head>

    <body>
        <!-- nav bar v8 -->
        <header class="top-nav">
            <div>
                <a href="home.php"><p class="format">FISH R US</p></a>
            </div>

            <div>
                <form method="GET" action="product-list.php" name="site-search" style="display:inline">
                    <p class="format">
                    <div> 
                        <input type="text" size="50" name="search_term"> <!-- size of search bar -->
                        <input type="submit" value="Go">
                    </div>
                    </p>
                </form>
            </div>

            <div class="dropdown">
                <button class="dropbtn"><p class="format">Account</p> 
                    <i class="fa fa-caret-down"></i>
                </button>
                <div class="dropdown-content">
                    <?php
                        if(array_key_exists('user',$_SESSION)){
                        ?>
                            <a href="accountdetails.php">Profile</a>
                            <a href="orderhistory.php">Transactions</a>
                            <a href="logout.php">Log Out</a>
                    <?php }else{ ?>
                            <a href="login.php">Sign In</a>
                            <a href="register.php">Register</a>
                    <?php } ?>
                </div>
            </div>

            <div>
                <a style="float:right;" href="cart.php">
                    <img src="img\cart-icon.png" width="20" height="20">
                </a>
            </div>

            <?php 
                $user = $_SESSION['user'];

                $query = "SELECT * FROM `role` WHERE `Username` = '$user->username'";
                $result = $conn->query($query);
                if (!$result) die ("Error: Database access failed, home.php");

                $rows = $result->num_rows;

                if($rows >= 1){
            ?>
                <div>
                    <a href="admin.php"><p class="format">Admin</p></a>
                </div>
            <?php
                }
            ?>

            <?php 
                if(array_key_exists('user',$_SESSION)){
            ?>
            <p class="format">
                <a style="background-color:#3587b8;">
                Hello, <?php echo "$user->first_name"; ?>
                </a>
            </p>
            <?php
                }
            ?>
        </header>

        <!-- displaying product page below using heredoc-->
        <?php
echo <<<_END
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
                        <form method="POST" action="product.php" name="add-to-cart" style="display:inline">
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
                                <input type="hidden" name="product_id" value="$product_id">
                                <input type="hidden" name="submitted" value="submitted">
                            </div>
                        </form>
                    </td>
                </tr>
                </table>
                    
                </body>
            </html>
_END;
        ?>

<?php
/**
 * Add a product to the cart page.
 */
function addProductToCart($conn){
    // If user is not logged in
    if(!array_key_exists('user',$_SESSION)){
        header("Location: login.php");
    // Else user is logged in
    } else {
        //print_r($_POST); Array ([quantity] => 1[product] => Amazon Sword Plant[product_id] => 19[submitted] => submitted )
        //print_r($_SESSION); Array ([Username] => john123[First_Name] => John[Last_Name] => Smith[ID] => 1 ) 
        $user = $_SESSION['user'];
        $user_id = sanitizeString($user->id);
        $product_id = sanitizeString($_POST['product_id']);
        $quantity = sanitizeString($_POST['quantity']);

        $query = "INSERT INTO cart_item(ID, Product_ID, Quantity) VALUES ('$user_id','$product_id','$quantity')";
        $result = $conn->query($query);
        if(!$result) die($result);

        header("Location: cart.php");
    }
}

/**
 * Returns an array with the product details
 * 
 * @param $conn The connection to database
 * 
 * @return array the array with the product details
 */
function displayProductDetails($conn){
    //// 1. Retrieve the product name from the URL (for querying the database)
    if(isset($_GET['product'])){
        $product = sanitizeString($_GET['product']);
        $product = addslashes($product); // rod's food to rod\'s food
    }else{
        $product = "ERROR";
    }

    //// 2. Create the variables to put into the productDetails array
    // Query the product table
    $query = "SELECT * FROM product WHERE Product_Name='$product'";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed in product.php 1");

    $row = $result->fetch_array(MYSQLI_ASSOC);

    $product_price = sanitizeMySQL($conn, $row['Product_Price']); // product price
    $product_id = sanitizeMySQL($conn, $row['Product_ID']); // product id
    $product_category = sanitizeMySQL($conn, $row['Category']); // product category

    // Query the inventory table
    $query = "SELECT * FROM inventory WHERE Product_ID='$product_id'";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed in product.php 2");

    $rows = $result->num_rows;

    $quantity = 0; // total quantity
    $stock_message = "IN STOCK"; // "IN/OUT STOCK" message

    for ($i = 0; $i < $rows; ++$i)
    {
        $row = $result->fetch_array(MYSQLI_ASSOC);

        $sub_quantity = sanitizeMySQL($conn, $row['Quantity']);
        $quantity += $sub_quantity;
    }

    if($quantity == 0){
        $stock_message = "OUT OF STOCK";
    }

    // Query the discount table
    $query = "SELECT * FROM discount WHERE Product_ID='$product_id'";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed in product.php 3");

    $rows = $result->num_rows;

    $discount_message = ""; // discount message
    
    if ($rows != 0){
        $discount_message = "ON SALE!";

        $row = $result->fetch_array(MYSQLI_ASSOC);

        $discount = sanitizeMySQL($conn, $row['Discount']);

        $product_price = $product_price - ($product_price * $discount);

        $product_price = round($product_price, 2);
        $product_price = sprintf('%0.2f', $product_price); // product price
    }

    // Query the genus table
    $query = "SELECT * FROM genus WHERE Product_ID='$product_id'";
    $result = $conn->query($query);
    if (!$result) die ("Database access failed in product.php 4");

    $rows = $result->num_rows;

    $genus = ""; // genus
    
    if ($rows != 0){
        $row = $result->fetch_array(MYSQLI_ASSOC);

        $genus = sanitizeMySQL($conn, $row['Genus']);
        $genus = stripslashes($genus);
        $genus = '('.$genus.')';
    }

    $product = stripslashes($product); // rod\'s food back to rod's food for the display on page

    $image_relative_path = createImagePath($product, $product_category); // image relative path

    //// 3. Enter variables into the productDetails array
    $productDetails[] = array();

    $productDetails['product_id'] = $product_id;
    $productDetails['product_name'] = $product;
    $productDetails['product_price'] = $product_price;
    $productDetails['product_category'] = $product_category;
    $productDetails['quantity'] = $quantity;
    $productDetails['stock_message'] = $stock_message;
    $productDetails['discount_message'] = $discount_message;
    $productDetails['genus'] = $genus;
    $productDetails['image_path'] = $image_relative_path;

    return $productDetails;
}
?>