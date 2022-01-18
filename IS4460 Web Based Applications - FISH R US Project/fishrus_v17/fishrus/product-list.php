<?php
require_once "utility/sanitize.php";
require_once "utility/login-cred.php";
require_once "User.php";

// Starting connection with database
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Error: Failed to connect to database.");

if (!isset($_SESSION)) { session_start(); }
?>

<?php
    //// 1. Determining the criteria for the products to display
    $criteria = determineGetArrayCriteria();
    $isSearchTerm = $criteria['isSearchTerm'];
    $searchTerm = $criteria['searchTerm'];
    $isOnSale = $criteria['isOnSale'];
    $isCategory = $criteria['isCategory'];
    $categoryType = $criteria['categoryType'];

    //// 2. Selecting the query based on the criteria
    $query = "";
    if ($isSearchTerm) {
        // Query for similar products to search term
        $query = <<<ttttt
            SELECT product.Product_ID, product.Product_Name, product.Product_Price, product.Category, genus.Genus, 
            discount.Discount, inventory.Inventory_ID, inventory.Quantity 
            FROM product 
            LEFT JOIN genus ON genus.Product_ID = product.Product_ID 
            LEFT JOIN discount ON discount.Product_ID = product.Product_ID 
            LEFT JOIN inventory ON inventory.Product_ID = product.Product_ID 
            WHERE product.Product_Name LIKE '%$searchTerm%' OR genus.Genus LIKE '%$searchTerm%' OR product.Category LIKE '%$searchTerm%' 
        ttttt;
    }else{
        // Query for grouping by on-sale or by category
        $query = <<<ttttt
            SELECT product.Product_ID, product.Product_Name, product.Product_Price, product.Category, genus.Genus, 
            discount.Discount, inventory.Inventory_ID, inventory.Quantity 
            FROM product 
            LEFT JOIN genus ON genus.Product_ID = product.Product_ID 
            LEFT JOIN discount ON discount.Product_ID = product.Product_ID 
            LEFT JOIN inventory ON inventory.Product_ID = product.Product_ID;
        ttttt;
    }
    $result = $conn->query($query);
    if (!$result) die ("Error: Database access failed in product-list.php");

    //// 3. Recording all dispalyed products and their details in an array for easier access
    $displayed_products = allProductDetails($conn, $result, $isSearchTerm, $isOnSale, $isCategory, $categoryType);

    //// 4. Setting the display name for "Results for ..." message at the top of the page
    $renamed_name = createDisplayName($isOnSale, $isSearchTerm, $categoryType, $searchTerm);

    //// 5. Checking and updating quantity if cart contains the product(s)
    if (array_key_exists('user',$_SESSION)){ // fixed bug
        $user = $_SESSION['user'];
        $user_id = $user->id;

        $query = "SELECT * FROM cart_item WHERE ID=$user_id";
        $result = $conn->query($query);
        if(!$result) die ($result);

        $numRows = $result->num_rows;
        for ($i = 0; $i < $numRows; ++$i)
        {
            $row = $result->fetch_array(MYSQLI_ASSOC);

            for ($w = 0; $w < count($displayed_products); ++$w){
                $product_id = $displayed_products[$w]['product_id'];

                if($product_id == $row['Product_ID']){
                    $displayed_products[$w]['quantity'] -= $row['Quantity'];
                }
            }
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
        
    <!-- List of Products -->
    <?php
        $col_counter = 0;
    ?>

    <table>
        <tr>
            <h3>Results for <?php echo $renamed_name ?></h3>
        </tr>

        <!-- uses php to display the products -->
        <?php foreach($displayed_products as $product){ ?>
            <tr>
            <?php for($k=0;$k<4;$k++){ ?>
                <td> 
                    <?php 
                        $product_name = $displayed_products[$col_counter]['product_name'];
                        $product_price = $displayed_products[$col_counter]['product_price'];
                        $genus = $displayed_products[$col_counter]['genus'];
                        $quantity = $displayed_products[$col_counter]['quantity'];
                        $image_relative_path = $displayed_products[$col_counter]['image_relative_path'];
                        $sale = $displayed_products[$col_counter]['sale'];
                        
                        echo '<a href="product.php?product='.$product_name.'"><img src="'.$image_relative_path.'" height="150" width="150"/></a>'.'<br>';
                        if($sale){
                            echo '<span style="background-color:yellow;font-style:bold">ON SALE!</span>';
                        }
                        echo '<br>';
                        echo '<a href="product.php?product='.$product_name.'">'.$product_name.'</a><br>';
                        echo $genus.'<br><br>';
                        echo 'Starting at <span style="font-style:bold">$'.$product_price.'</span><br>';
                        if($quantity>0){
                            echo '<span style="font-style:italic">IN STOCK</span>';
                        }else{
                            echo '<span style="font-style:italic">OUT OF STOCK</span>';
                        }
                
                        $col_counter++;
                        if($col_counter >= count($displayed_products)){
                            break;
                        }
                    ?>
                </td>
                
            <?php } ?>

            </tr>

            <?php
            if($col_counter >= count($displayed_products)){
                break;
            }
            ?>
        <?php } ?>
    </table>

  </body>
</html>

<?php
/**
 * Determines criteria for the display of product-list.php based on $_GET['product-list'] or $_GET['search_term'].
 * 
 * The possible values of $_GET['product-list'] are 'On-sale', 'Fish', 'Plant', 'Invert', 'Aquascaping', 'Food', 'Aquarium', and ''. 
 *   1. If value is 'On-sale', then product-list.php will display products that are on sale.
 *   2. If value is a category ('Fish', 'Plant', 'Invert', 'Aquascaping', 'Food', or 'Aquarium'), then product-list.php will display 
 *      products within the given category.
 *   3. If the value is '' (or NULL), then $_GET['search_term'] will have a value instead.
 * 
 * The possible values of $_GET['search_term'] are search terms that the user entered into the search bar.
 *   1. If the search term is not '', then products related to the search term will be displayed.
 *   2. If the search term is '', then user will be redirected to home.php. 
 * 
 * @param boolean $isSearchTerm  True if it is search term criteria
 * @param boolean $isOnSale      True if it is on sale criteria
 * @param boolean $isCategory    True if it is category criteria
 * @param string  $categoryType  The category type name
 * 
 * @return array the modified criteria
 */
function determineGetArrayCriteria(){
    $isSearchTerm = FALSE;
    $searchTerm = "";
    $isOnSale = FALSE;
    $isCategory = FALSE;
    $categoryType = "";

    if(isset($_GET['product-list'])){
        $product_list = sanitizeString($_GET['product-list']);

        if($product_list == "On-sale"){
            $isOnSale = TRUE;
        }else{
            $isCategory = TRUE;
            if($product_list == "Fish"){
                $categoryType = "Fish";
            }else if($product_list == "Plant"){
                $categoryType = "Plant";
            }else if($product_list == "Invert"){
                $categoryType = "Invert";
            }else if($product_list == "Aquascaping"){
                $categoryType = "Aquascaping";
            }else if($product_list == "Food"){
                $categoryType = "Food";
            }else if($product_list == "Aquarium"){
                $categoryType = "Aquarium";
            }
        }
    }else{
        if(isset($_GET['search_term'])){
            $product_list = sanitizeString($_GET['search_term']);
            $searchTerm = $product_list;

            if ($product_list == ""){
                header("Location: home.php");
            }

            $isSearchTerm = TRUE;
        }else{
            $product_list = "ERROR";
        }
    }

    $criteria = array();
    $criteria['isSearchTerm'] = $isSearchTerm;
    $criteria['searchTerm'] = $searchTerm;
    $criteria['isOnSale'] = $isOnSale;
    $criteria['isCategory'] = $isCategory;
    $criteria['categoryType'] = $categoryType;

    return $criteria;
}

/**
 * Places the product details into an array. Product details include product id, product name,
 * price, on-sale message, genus, sub-quantity, and relative image path.
 * 
 * @param         $conn              The connection to the database.
 * @param array   $row               The row array from the query.
 * @param string  $product_category  The product category the product belongs to.
 * 
 * @return array the product details
 */
function productDetails($conn, $row, $product_category){
    // product id
    $product_id = sanitizeMySQL($conn, $row["Product_ID"]);

    // product name
    $product_name = sanitizeMySQL($conn, $row["Product_Name"]);
    $product_name = stripslashes($product_name); //rod's food

    // price
    $price = floatval(sanitizeMySQL($conn, $row["Product_Price"]));
    $discount = floatval(sanitizeMySQL($conn, $row["Discount"]));
    $product_price = $price - $price * $discount;
    $product_price = round($product_price, 2);
    $product_price = sprintf('%0.2f', $product_price); 

    // on sale message
    $onsale = FALSE;

    if ($discount != 0){
        $onsale = TRUE;
    }

    // genus
    $genus = "";
    if ($row["Genus"] != NULL){
        $genus = "(".sanitizeMySQL($conn, $row["Genus"]).")";
        $genus = stripslashes($genus);
    }

    // SUB quantity
    $quantity = sanitizeMySQL($conn, $row['Quantity']);

    // image path
    $image_relative_path = createImagePath($product_name, $product_category);

    // Recording dynamic variable details
    $product_details = array();
    $product_details["product_id"] = $product_id;
    $product_details["product_name"] = $product_name;
    $product_details["product_price"] = $product_price;
    $product_details["genus"] = $genus;
    $product_details["quantity"] = $quantity;
    $product_details["image_relative_path"] = $image_relative_path;
    $product_details["sale"] = $onsale;

    return $product_details;
}

/**
 * Determines if the product is on-sale or not.
 * 
 * @param        $conn  The connection to the database.
 * @param array  $row   The row array from the query.
 * 
 * @return boolean True if product is on-sale
 */
function isProductOnSale($conn, $row){
    $discount = floatval(sanitizeMySQL($conn, $row["Discount"]));

    if ($discount != 0){
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Records all product details in a 2d array.
 * 
 * [0] -> ['product_id':1, 'product_name':goldfish, ...]
 * [1] -> ['product_id':3, 'product_name':green plant, ...]
 * ...
 * 
 * @param         $conn          The connection to the database
 * @param         $result        The result of the query
 * @param boolean $isSearchTerm  The criteria for seach term
 * @param boolean $isOnSale      The criteria for on-sale
 * @param boolean $isCategory    The criteria for category
 * @param boolean $categoryType  The name of the category type
 * 
 * @return array 2d array of all the product details
 */
function allProductDetails($conn, $result, $isSearchTerm, $isOnSale, $isCategory, $categoryType){
    $displayed_products = array();

    $rows = $result->num_rows;
    for ($i = 0; $i < $rows; ++$i)
    {
        $row = $result->fetch_array(MYSQLI_ASSOC);

        $product_category = sanitizeMySQL($conn, $row["Category"]);

        // Determines if the criteria is category, on-sale, or search term.
        if ($isCategory AND $product_category == $categoryType){
            $product_details = productDetails($conn, $row, $product_category);
            $displayed_products[] = $product_details;
        } else if ($isOnSale){
            if(isProductOnSale($conn, $row)){
                $product_details = productDetails($conn, $row, $product_category);
                $displayed_products[] = $product_details;
            }
        } else if ($isSearchTerm) {
            $product_details = productDetails($conn, $row, $product_category);
            $displayed_products[] = $product_details;
        }
    }
    
    // Removes duplicate products and updates the total quantity for that product
    $displayed_products = removeDuplicatesAndUpdateQuantity($displayed_products);

    return $displayed_products;
}

/**
 * Creates the dispaly name depending on criteria.
 * 
 * @param boolean $isOnSale      The criteria for on-sale
 * @param boolean $isSearchTerm  The criteria for seach term
 * @param boolean $isCategory    The criteria for category
 * 
 * @return string the dispaly name
 */
function createDisplayName($isOnSale, $isSearchTerm, $categoryType, $searchTerm){
    $display_name = "";
    if ($isOnSale){
        $display_name = "On Sale";
    } else if ($isSearchTerm) {
        $display_name = '"'.$searchTerm.'"';
    } else {
        $display_name = renameCategory($categoryType);
    }
    return $display_name;
}

/**
 * Create the relative path for a product image.
 * 
 * @param string $product_name      The product name
 * @param string $product_category  The category the product belongs in
 * 
 * @return string the relative path for the product image
 */
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

/**
 * Rename a product category to its display name.
 * 
 * @param string $product_category  The category the product belongs to
 * 
 * @return string the renamed name
 */
function renameCategory($product_category){
    $renamed = "";

    if($product_category == "Fish")
    {
        $renamed = 'Marine Fish';
    }
    else if($product_category == "Invert")
    {
        $renamed = 'Marine Invert';
    }
    else if($product_category == "Food")
    {
        $renamed = 'Frozen Food';
    }
    else if($product_category == "Plant")
    {
        $renamed = 'Fresh Water Plants';
    }
    else if($product_category == "Aquascaping")
    {
        $renamed = 'Aquascaping';
    }
    else if($product_category == "Aquarium")
    {
        $renamed = 'Aquarium';
    }
    else
    {
        $renamed = 'Error';
    }

    return $renamed;
}

/**
 * Remove duplicates and update quantity of the 2d array that contains all the product details. 
 * 
 * @param array $list_of_products  The 2d array that contains all the product details
 * 
 * @return array the processed array with no duplicates and updated quantity
 */
function removeDuplicatesAndUpdateQuantity($list_of_products){
    $processed_array = array();
    $product_names = array();

    foreach ($list_of_products as $product){
        if(in_array($product["product_name"], $product_names)){
            for($i = 0; $i < count($processed_array); ++$i){
                if($processed_array[$i]["product_name"]==$product["product_name"]){
                    $processed_array[$i]["quantity"] += $product["quantity"];
                    break;
                }
            }
        }else{
            $processed_array[] = $product;
            array_push($product_names, $product["product_name"]); // normal array adding doesn't work here...
        }
    }

    return $processed_array;
}
?>