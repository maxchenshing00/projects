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
      <style>
          .center{
              margin-left:auto;
              margin-right:auto;
              width:40%;
              padding: 10px;
          }

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

    <!-- Start of Transaction History -->
    <h1>Transactions</h1>
  </body>
</html>

<?php
  // 1. Get user session information for query
  if (!array_key_exists('user',$_SESSION)){
    header("Location: login.php");
  } else {
      $user = $_SESSION['user'];
      $user_id = $user->id;
      $roles = $user->roles;
  }
  
  // 2. Display transaction history from orders table
  $query  = "SELECT * FROM orders WHERE Customer_ID = $user_id";
  $result = $conn->query($query);
  if (!$result) die("Fatal Error");

  $rows = $result->num_rows;

  for ($j = 0; $j < $rows; ++$j){
    $row = $result->fetch_array(MYSQLI_ASSOC);

    $date = sanitizeString($row['Order_Date']);
    $card_type = sanitizeString($row['Card_Type']);
    $card_end = '**** '.sanitizeString($row['Credit_Card_End']);
    $total_price = '-$'.sanitizeString($row['Total_Price']);
    $order_num = 'Order #'.sanitizeString($row['Order_ID']);

    $card_info = $card_type.': '.$card_end;

    echo <<<_END
      <html>
        <body>
          <table class="center">
            <tr>
                <td><strong>$order_num</strong><br></td>
            </tr>
            <tr>
                <td>$date<br></td>
            </tr>
            <tr>
                <td>$card_info</td>
                <td align="right">$total_price</td>
            </tr>
            <tr>
              <td><br></td>
            </tr>
          </table>
        </body>
      </html>
_END;
  }
?>

<html>
  <br>
  <input type="button" value="Back" onclick="history.back()">
</html>
