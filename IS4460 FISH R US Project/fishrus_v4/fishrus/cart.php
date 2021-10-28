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
                border-spacing: 0 2em;
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

        <!-- product info -->
        <table>
        <tr>
            <h3>Shopping Cart</h3>
        </tr>
        <tr>
            <td>
                <img src="product-images\marine-fish\calico-ryukin-goldfish.jpg" height="200" width="200"/>
            </td>
            <td style="text-align:left;">
                Calico Ryukin Goldfish<br>
                (Carassius auratus)<br><br>
                Starting at <span style="font-style:bold">$31.99</span><br>
                <span style="font-style:italic">IN STOCK</span>
                <br><br><br>
                <form method="GET" action="cart.php" name="remove" style="display:inline">
                    <div> 
                        <input type="submit" value="Remove">
                    </div>
                </form>
            </td>
        </tr>
        <tr>
            <td>
                <img src="product-images\marine-fish\red-cap-oranda-goldfish.jpg" height="200" width="200"/>
            </td>
            <td style="text-align:left;">
                Red Cap Oranda Goldfish<br>
                (Carassius auratus)<br><br>
                Starting at <span style="font-style:bold">$12.99</span><br>
                <span style="font-style:italic">IN STOCK</span>
                <br><br><br>
                <form method="GET" action="cart.php" name="remove" style="display:inline">
                    <div> 
                        <input type="submit" value="Remove">
                    </div>
                </form>
            </td>
        </tr>
        <tr></tr>
        <tr>
            <td>
            </td>
            <td style="text-align:left;">
                <form method="GET" action="checkout.php" name="proceed-checkout" style="display:inline">
                    <div> 
                        <input type="submit" value="Proceed to Checkout">
                    </div>
                </form>
            </td>
        </tr>
        </table>
    </body>
</html>