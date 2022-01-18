<?php
require_once "utility/sanitize.php";
require_once "utility/login-cred.php";
require_once "User.php";

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Error: Failed to connect to database.");

if (!isset($_SESSION)) { session_start(); }
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

    <!-- "Trending Products" Section -->
    <table>
        <tr>
            <h1>Trending Products!</h1>
        </tr>
        <tr>
            <td>
                <a href="product.php?product=Amazon Sword Plant"><img src="product-images\fresh-water-plants\Amazon-Sword-Plant.jpg" height="250" width="250"/></a>
            </td>
            <td>
                <a href="product.php?product=Blue Tang"><img src="product-images\marine-fish\Blue-Tang.jpg" height="250" width="250"/></a>
            </td>
            <td>
                <a href="product.php?product=Piscine Energetics PE Calanus Frozen Fish Food"><img src="product-images\frozen-food\Piscine-Energetics-PE-Calanus-Frozen-Fish-Food.jpg" height="250" width="250"/></a>
            </td>
        </tr>

        <tr>
            <td class="table-item">
                <span style="background-color:yellow;font-style:bold">ON SALE!<br></span>
                <a href="product.php?product=Amazon Sword Plant">Amazon Sword Plant</a><br>
                (Echinodorus amazonicus)<br><br>
                Starting at <span style="font-style:bold">$5.59</span>
            </td>
            <td class="table-item">
                <br>
                <a href="product.php?product=Blue Tang">Blue Tang</a><br>
                (Paracanthurus hepatus)<br><br>
                Starting at <span style="font-style:bold">$84.99</span>
            </td>
            <td class="table-item">
                <br>
                <a href="product.php?product=Piscine Energetics PE Calanus Frozen Fish Food">Piscine Energetics PE Calanus Frozen Fish Food</a><br><br>
                Starting at <span style="font-style:bold">$32.99</span>
            </td>
        </tr>
    </table>

    <!-- "On Sale!" Section -->
    <table>
        <tr>
            <h1>On Sale!</h1>
        </tr>
        <tr>
            <td>
                <a href="product-list.php?product-list=On-sale"><img src="img\on-sale.png"/></a>
            </td>
        </tr>
    </table>

    <!-- "FISH R US Picks!" Section -->
    <table>
        <tr>
            <h1>FISH R US Picks!</h1>
        </tr>
        <tr>
            <td>
                <a href="product-list.php?product-list=Fish"><img src="product-images\marine-fish\Blue-Tang.jpg" height="150" width="150"/></a>
            </td>
            <td>
                <a href="product-list.php?product-list=Food"><img src="product-images\frozen-food\Piscine-Energetics-PE-Calanus-Frozen-Fish-Food.jpg" height="150" width="150"/></a>
            </td>
            <td>
                <a href="product-list.php?product-list=Plant"><img src="product-images\fresh-water-plants\Amazon-Sword-Plant.jpg" height="150" width="150"/></a>
            </td>
            <td>
                <a href="product-list.php?product-list=Invert"><img src="product-images\marine-inverts\derasa-clam-striped-with-blue-rim.PNG" height="150" width="150"/></a>
            </td>
        </tr>
        <tr>
            <td class="table-item">
                <br><a href="product-list.php?product-list=Fish">Marine Fish></a><br><br>
            </td>
            <td class="table-item">
                <br><a href="product-list.php?product-list=Food">Frozen Food></a><br><br>
            </td>
            <td class="table-item">
                <br><a href="product-list.php?product-list=Plant">Fresh Water Plants></a><br><br>
            </td>
            <td class="table-item">
                <br><a href="product-list.php?product-list=Invert">Marine Inverts></a><br><br>
            </td>
        </tr>
        <tr>
            <td>
                <a href="product-list.php?product-list=Aquarium"><img src="product-images\aquariums\Live-Aquaria-Beginner-Shrimp-Aquarium-Kit-Orbi-Black.PNG" height="150" width="150"/></a>
            </td>
            <td>
                <a href="product-list.php?product-list=Aquascaping"><img src="product-images\aquascaping\malaysian-driftwood.PNG" height="150" width="150"/></a>
            </td>
        </tr>
        <tr>
            <td class="table-item">
                <br><a href="product-list.php?product-list=Aquarium">Aquariums></a><br><br>
            </td>
            <td class="table-item">
                <br><a href="product-list.php?product-list=Aquascaping">Aquascaping></a><br><br>
            </td>
        </tr>
    </table>
  </body>
</html>

