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

    <!-- List of Products -->
    <table>
        <tr>
            <h3>Results for "Goldfish"</h3>
        </tr>
        <tr>
            <td>
                <a href="product.php?product=Fantail Goldfish Red"><img src="product-images\marine-fish\Fantail-Goldfish-Red.jpg" height="150" width="150"/></a>
            </td>
            <td>
                <a href="product.php?product=Fantail Goldfish Calico"><img src="product-images\marine-fish\Fantail-Goldfish-Calico.jpg" height="150" width="150"/></a>
            </td>
            <td>
                <a href="product.php?product=Calico Ryukin Goldfish"><img src="product-images\marine-fish\Calico-Ryukin-Goldfish.jpg" height="150" width="150"/></a>
            </td>
            <td>
                <a href="product.php?product=Red Cap Oranda Goldfish"><img src="product-images\marine-fish\Red-Cap-Oranda-Goldfish.jpg" height="150" width="150"/></a>
            </td>
        </tr>

        <tr>
            <td class="table-item">
                <a href="product.php?product=Fantail Goldfish Red">Fantail Goldfish, Red</a><br>
                (Carassius auratus)<br><br>
                Starting at <span style="font-style:bold">$3.99</span><br>
                <span style="font-style:italic">IN STOCK</span>
            </td>
            <td class="table-item">
                <span style="background-color:yellow;font-style:bold">ON SALE!<br></span>
                <a href="product.php?product=Fantail Goldfish Calico">Fantail Goldfish, Calico</a><br>
                (Carassius auratus)<br><br>
                Starting at <span style="font-style:bold">$4.99</span><br>
                <span style="font-style:italic">IN STOCK</span>
            </td>
            <td class="table-item">
                <a href="product.php?product=Calico Ryukin Goldfish">Calico Ryukin Goldfish</a><br>
                (Carassius auratus)<br><br>
                Starting at <span style="font-style:bold">$31.99</span><br>
                <span style="font-style:italic">IN STOCK</span>
            </td>
            <td class="table-item">
                <a href="product.php?product=Red Cap Oranda Goldfish">Red Cap Oranda Goldfish</a><br>
                (Carassius auratus)<br><br>
                Starting at <span style="font-style:bold">$12.99</span><br>
                <span style="font-style:italic">IN STOCK</span>
            </td>
        </tr>

        <tr>
            <td>
                <a href="product.php?product=Oranda Goldfish Assorted"><img src="product-images\marine-fish\Oranda-Goldfish-Assorted.jpg" height="150" width="150"/></a>
            </td>
            <td>
                <a href="product.php?product=Black Moor Goldfish"><img src="product-images\marine-fish\Black-Moor-Goldfish.jpg" height="150" width="150"/></a>
            </td>
        </tr>

        <tr>
            <td class="table-item">
                <a href="product.php?product=Oranda Goldfish Assorted">Orand Goldfish Assorted</a><br>
                (Carassius auratus)<br><br>
                Starting at <span style="font-style:bold">$8.49</span><br>
                <span style="font-style:italic">IN STOCK</span>
            </td>
            <td class="table-item">
                <a href="product.php?product=Black Moor Goldfish">Black Moor Goldfish</a><br>
                (Carassius auratus)<br><br>
                Starting at <span style="font-style:bold">$8.99</span><br>
                <span style="font-style:italic">IN STOCK</span>
            </td>
        </tr>

    </table>

  </body>
</html>