<?php
require_once "utility/sanitize.php";
require_once "utility/login-cred.php";

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Error: Failed to connect to database.");

if (!isset($_SESSION)) { session_start(); }
?>

<?php
$user_id = "";
if (!array_key_exists('ID',$_SESSION)){
    header("Location: login.php");
} else {
    $user_id = $_SESSION['ID'];
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
        <!-- nav bar v3 -->
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
                        if(array_key_exists('ID',$_SESSION)){
                        ?>
                            <a href="#account details page">Account Details</a>
                            <a href="#order history page">Order History</a>
                            <a href="logout.php">Log Out</a>
                    <?php }else{ ?>
                            <a href="login.php">Sign In</a>
                            <a href="register page">Register</a>
                    <?php } ?>
                </div>
            </div>

            <div>
                <a style="float:right;" href="cart.php">
                    <img src="img\cart-icon.png" width="20" height="20">
                </a>
            </div>

            <?php 
                $query = "SELECT * FROM admin WHERE `ID` = '$_SESSION[ID]'";
                $result = $conn->query($query);
                if (!$result) die ("Error: Database access failed, home.php");

                $rows = $result->num_rows;

                if($rows === 1){
            ?>
                <div>
                    <a href="#admin page"><p class="format">Admin</p></a>
                </div>
            <?php
                }
            ?>

            <?php 
                if(array_key_exists('ID',$_SESSION)){
            ?>
            <p class="format">
                <a style="background-color:#3587b8;">
                Hello, <?php echo "$_SESSION[First_Name]"; ?>
                </a>
            </p>
            <?php
                }
            ?>
        </header>
    
        <!-- product info -->
        <table>
        <tr>
            <h1>Shopping Cart</h1>
        </tr>

        <?php
        //Cart_Item_ID ID Product_ID Quantity Product_Name Product_Price Category Vendor_ID Genus Discount
        $query = <<<ttttt
        SELECT cart_item.Cart_Item_ID, cart_item.ID, cart_item.Product_ID, cart_item.Quantity, product.Product_Name, product.Product_Price,
        product.Category, product.Vendor_ID, genus.Genus, discount.Discount
        FROM cart_item 
        LEFT JOIN product ON product.Product_ID = cart_item.Product_ID 
        LEFT JOIN genus ON genus.Product_ID = product.Product_ID 
        LEFT JOIN discount ON discount.Product_ID = product.Product_ID 
        WHERE cart_item.ID = '$user_id';
        ttttt;
        $result = $conn->query($query);
        if(!$result) die ($result);

        $numRows = $result->num_rows;

        $disabled_text = "";
        if($numRows == 0){
            $disabled_text = "disabled";
        }

        for($j=0; $j<$numRows; ++$j){
            $row = $result->fetch_array(MYSQLI_ASSOC);

            // product id
            $product_id = sanitizeMySQL($conn, $row["Product_ID"]);

            // product name
            $product_name = sanitizeMySQL($conn, $row["Product_Name"]);
            $product_name = stripslashes($product_name); //rod's food

            // genus
            $genus = "";
            if ($row["Genus"] != NULL){
                $genus = "(".sanitizeMySQL($conn, $row["Genus"]).")";
                $genus = stripslashes($genus);
            }

            // price (with discount accounted for)
            $price = floatval(sanitizeMySQL($conn, $row["Product_Price"]));
            $discount = floatval(sanitizeMySQL($conn, $row["Discount"]));
            $product_price = $price - $price * $discount;
            $product_price = round($product_price, 2);
            $product_price = sprintf('%0.2f', $product_price); 

            // on sale message
            $onsale = "";
            
            if ($discount != 0){
                $onsale = "ON SALE!";
            }

            // quantity (that the customer wants to buy)
            $quantity = $row['Quantity'];

            // image url 
            $image_relative_path = createImagePath($product_name, $row['Category']);

            echo <<<_END
            <tr>
                <td>
                    <img src=$image_relative_path height="200" width="200"/>
                </td>
                <td style="text-align:left;">
                    <span style="background-color:yellow;font-style:bold">$onsale</span><br>
                    $product_name<br>
                    $genus<br><br>
                    Starting at <span style="font-style:bold">$$product_price</span><br>
                    Quantity: $quantity
                    <br><br><br>
                    <form method="POST" action="delete-cart-item.php" style="display:inline">
                        <div> 
                            <input type="hidden" name="deleted" value="yes">
                            <input type="hidden" name="product_id" value="$product_id">
                            <input type="submit" value="Remove from cart">
                        </div>
                    </form>
                </td>
            </tr>
        _END;
        
        } 
        ?>
        
        <tr></tr>
        <tr>
            <td>
            </td>
            <td style="text-align:left;">
                <br><br><br>
                <form method="GET" action="checkout.php" name="proceed-checkout" style="display:inline">
                    <div> 
                        <input type="submit" value="Proceed to Checkout" <?php echo "$disabled_text" ?>>
                    </div>
                </form>
            </td>
        </tr>
        </table>
    </body>
</html>



<?php
    function createImagePath($product_name, $product_category){
        $image_relative_path = 'product-images\\'; //image relative path
        $image_name = str_replace(' ', '-', $product_name);

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
            $image_relative_path = $image_relative_path.'aquariums\\';
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

        return $image_relative_path;
    }
?>