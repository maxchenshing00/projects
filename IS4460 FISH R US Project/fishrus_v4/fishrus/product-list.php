<?php
    require_once "utility/sanitize.php";
    require_once 'utility/login-cred.php';

    // Starting connection with database
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die("Error: Failed to connect to database.");

    $isSearchTerm = FALSE;
    $isOnSale = FALSE;
    $isCategory = FALSE;
    $categoryType = "";

    // Getting the product-list text from URL
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

            if ($product_list == ""){
                header("Location: home.php");
            }

            $isSearchTerm = TRUE;
        }else{
            $product_list = "ERROR";
        }
    }

    // Query all into 1 table
    $query = "";
    if ($isSearchTerm) {
        $query = <<<ttttt
            SELECT product.Product_ID, product.Product_Name, product.Product_Price, product.Category, genus.Genus, 
            discount.Discount, inventory.Inventory_ID, inventory.Quantity 
            FROM product 
            LEFT JOIN genus ON genus.Product_ID = product.Product_ID 
            LEFT JOIN discount ON discount.Product_ID = product.Product_ID 
            LEFT JOIN inventory ON inventory.Product_ID = product.Product_ID 
            WHERE product.Product_Name LIKE '%$product_list%' OR product.Category LIKE '%$product_list%' OR genus.Genus LIKE '%$product_list%'
        ttttt;
    }else{
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

    // Record all dynamic variables in array
    $displayed_products = array();
    $rows = $result->num_rows;

    for ($i = 0; $i < $rows; ++$i)
    {
        $row = $result->fetch_array(MYSQLI_ASSOC);

        $product_category = sanitizeMySQL($conn, $row["Category"]);

        // Checking if category, on sale, or search bar
        if ($isCategory AND $product_category == $categoryType){
            // Setting dynamic variables
            $product_name = sanitizeMySQL($conn, $row["Product_Name"]);
            $product_name = stripslashes($product_name); //rod's food

            $price = floatval(sanitizeMySQL($conn, $row["Product_Price"]));
            $discount = floatval(sanitizeMySQL($conn, $row["Discount"]));
            $product_price = $price - $price * $discount;
            $product_price = round($product_price, 2);
            $product_price = sprintf('%0.2f', $product_price); 

            $onsale = FALSE;
            
            if ($discount != 0){
                $onsale = TRUE;
            }

            $genus = "";
            if ($row["Genus"] != NULL){
                $genus = "(".sanitizeMySQL($conn, $row["Genus"]).")";
            }

            $quantity = sanitizeMySQL($conn, $row['Quantity']);

            $image_relative_path = createImagePath($product_name, $product_category);

            // Recording dynamic variable details
            $product_details = array();
            $product_details["product_name"] = $product_name;
            $product_details["product_price"] = $product_price;
            $product_details["genus"] = $genus;
            $product_details["quantity"] = $quantity;
            $product_details["image_relative_path"] = $image_relative_path;
            $product_details["sale"] = $onsale;

            $displayed_products[] = $product_details;
        } else if ($isOnSale){
            // Setting dynamic variables
            $price = floatval(sanitizeMySQL($conn, $row["Product_Price"]));
            $discount = floatval(sanitizeMySQL($conn, $row["Discount"]));
            $product_price = $price - $price * $discount;
            $product_price = round($product_price, 2);
            $product_price = sprintf('%0.2f', $product_price); 

            $onsale = FALSE;
            
            if ($discount != 0){
                $onsale = TRUE;
            }

            // Check condition - if item is not on sale, don't include
            if (!$onsale){
                continue;
            }

            $product_name = sanitizeMySQL($conn, $row["Product_Name"]);
            $product_name = stripslashes($product_name); //rod's food

            $genus = "";
            if ($row["Genus"] != NULL){
                $genus = "(".sanitizeMySQL($conn, $row["Genus"]).")";
            }

            $quantity = sanitizeMySQL($conn, $row['Quantity']);

            $image_relative_path = createImagePath($product_name, $product_category);

            // Recording dynamic variable details
            $product_details = array();
            $product_details["product_name"] = $product_name;
            $product_details["product_price"] = $product_price;
            $product_details["genus"] = $genus;
            $product_details["quantity"] = $quantity;
            $product_details["image_relative_path"] = $image_relative_path;
            $product_details["sale"] = $onsale;

            $displayed_products[] = $product_details;
        } else if ($isSearchTerm) {
            // Setting dynamic variables
            $product_name = sanitizeMySQL($conn, $row["Product_Name"]);
            $product_name = stripslashes($product_name); //rod's food

            $price = floatval(sanitizeMySQL($conn, $row["Product_Price"]));
            $discount = floatval(sanitizeMySQL($conn, $row["Discount"]));
            $product_price = $price - $price * $discount;
            $product_price = round($product_price, 2);
            $product_price = sprintf('%0.2f', $product_price); 

            $onsale = FALSE;
            
            if ($discount != 0){
                $onsale = TRUE;
            }

            $genus = "";
            if ($row["Genus"] != NULL){
                $genus = "(".sanitizeMySQL($conn, $row["Genus"]).")";
            }

            $quantity = sanitizeMySQL($conn, $row['Quantity']); //needs work!!! //need to make sure quantity remains true...

            $image_relative_path = createImagePath($product_name, $product_category);

            // Recording dynamic variable details
            $product_details = array();
            $product_details["product_name"] = $product_name;
            $product_details["product_price"] = $product_price;
            $product_details["genus"] = $genus;
            $product_details["quantity"] = $quantity;
            $product_details["image_relative_path"] = $image_relative_path;
            $product_details["sale"] = $onsale;

            $displayed_products[] = $product_details;
        }
    }

    // Setting display name
    $renamed_name = "";
    if ($isOnSale){
        $renamed_name = "On Sale";
    } else if ($isSearchTerm) {
        $renamed_name = '"'.$product_list.'"';
    } else {
        $renamed_name = renameCategory($categoryType);
    }

    // Removing duplicated products
    $displayed_products = removeDuplicates($displayed_products);
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

                /*vertical gap between rows*/
                border-collapse: separate;
                border-spacing: 0 1em;
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
                        <input type="text" size="50" name="search_term"> <!-- size of search bar -->
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

    <!-- List of Products -->
    <?php
        $col_counter = 0;
    ?>

    <table>
        <tr>
            <h3>Results for <?php echo $renamed_name ?></h3>
        </tr>
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

    function removeDuplicates($list_of_products){
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
                array_push($product_names, $product["product_name"]); // normal array adding doesn't work...
            }
        }

        return $processed_array;
    }
?>