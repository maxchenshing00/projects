<?php
require_once "utility/sanitize.php";
require_once "utility/login-cred.php";
require_once "User.php";

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Error: Failed to connect to database.");

if (!isset($_SESSION)) { session_start(); }
?>

<?php
//// Processing Checkout
if(isset($_POST["submitted"])){
	// 1. Records customer information for the order.
	recordCustomerInfo($conn);

	// 2. Records order information for the order.
	recordOrderInfo($conn);

	// 3. Update quantity in different inventories.
	updateQuantityInInventory($conn);

	// 4. Remove all cart items
	removeCartItems($conn); 

	// Go to confirm.php
	header("Location: confirm.php");
}

?>

<?php
//// Setting default text for the html textfields
if (array_key_exists('user',$_SESSION)){
	$user = $_SESSION['user'];
	$user_id = sanitizeMySQL($conn, $user->id);
    $query = "SELECT * FROM user WHERE ID='$user_id'";
    $result = $conn->query($query);
	if(!$result) die ($result);

	$numRows = $result->num_rows;
	for($j=0; $j<$numRows; ++$j){
		$row = $result->fetch_array(MYSQLI_ASSOC);

		$name = sanitizeString($row['First_Name']).' '.sanitizeMySQL($conn, $row['Last_Name']);
		$street = sanitizeString($row['Street']);
		$city = sanitizeString($row['City']);
		$state = sanitizeString($row['State']);
		$zip = sanitizeString($row['ZIP']);
		$phone = sanitizeString($row['Phone_Number']);
		$email = sanitizeString($row['Email']);
	}
} else {
	header("Location: login.php");
}
?>

<html>
	<head>
        <title>Checkout Page</title>
	</head>
	
	<body>
		<header align='center'>
			<img src='img/cart-icon.png' alt="Checkout" style="width:128px;height:128px;">
		</header>
		<br>
		<form action='checkout.php' method='POST'>
		<table class='table-border' align='center' width='50%'>
			<tr>
				<td colspan='2'><h2>Customer Info</h2></td>
			</tr>
			<tr>
				<td>Name:</td>
				<td><input type='text' name='custName' size='50' value='<?php echo "$name" ?>' required></td>				
			</tr>
			<tr>
				<td>Street:</td>
				<td><input type='text' name='street' size='50' value='<?php echo "$street" ?>' required></td>				
			</tr>
			<tr>
				<td>City:</td>
				<td><input type='text' name='city' size='50' value='<?php echo "$city" ?>' required></td>				
			</tr>
			<tr>
				<td>State:</td>
				<td><input type='text' name='state' size='2' maxlength="2" placeholder="UT" value='<?php echo "$state" ?>' required></td>				
			</tr>
			<tr>
				<td>Zip:</td>
				<td><input type="text" name='zip' size='5' maxlength="5" placeholder="73022" pattern="[0-9]*" value='<?php echo "$zip" ?>' required></td>				
			</tr>
			<tr>
				<td>Phone Number:</td>
				<td><input type="tel" id="phone" name="phone" size='12' placeholder="123-456-7890" maxlength="12" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" value='<?php echo "$phone" ?>' required></td>				
			</tr>
			<tr>
				<td>Email:</td>
				<td><input type='text' name='email' size='50' placeholder="name@gmail.com" value='<?php echo "$email" ?>' required></td>				
			</tr>	
			<tr>
				<td colspan='2'><h2>Credit Card</h2></td>
			</tr>
			<tr>
				<td>Card Type:</td>
				<td><input type='text' name='cardType' size='50' placeholder="Visa" required></td>				
			</tr>
			<tr>
				<td>Card Number:</td>
				<td><input type='text' name='cardNum' size='50' maxlength="16" pattern="[0-9]*" placeholder="1111222233334444" required></td>				
			</tr>
			<tr>
				<td>Expiration Date:</td>
				<td><input type='text' name='cardExp' size='50' pattern="^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$" placeholder="2025-03-28" required></td>
			</tr>
			<tr>
				<td>Security Code:</td>
				<td><input type='text' name='cardCode' maxlength="4" size='50' pattern="[0-9]*" placeholder="123 or 1234" required></td>				
			</tr>	
			<tr>
				<input type="hidden" name="user_id" value="<?php echo "$user_id" ?>">
				<input type="hidden" name="submitted" value="yes">
				<td><br><input type='submit' value='Submit'></td>
				<td></td>
			</tr>
			<tr>
				<td>
					<br><br>
					<input type="button" value="Cancel" onclick="history.back()">
				</td>
				<td></td>
			</tr>
		</table>
		</form>
	</body>
</html>	

<?php
/**
 * 1. Records customer information for the order.
 *   a. Record customer's phone number, email, and address. Updates the user table.
 *   b. Add the customer's credit card to the credit_card table if the credit card does not already exist.
 * 
 * @param $conn The connection to the database
 */
function recordCustomerInfo($conn){
	updateCustomerInfo($conn);
	verifyCreditCard($conn);
}

/**
 * Record customer's phone number, email, and address. Updates the user table.
 * 
 * @param $conn The connection to the database
 */
function updateCustomerInfo($conn){
	$user_id = sanitizeMySQL($conn, $_POST['user_id']);
	$phone = sanitizeMySQL($conn, $_POST['phone']);
	$email = sanitizeMySQL($conn, $_POST['email']);
	$state = sanitizeMySQL($conn, $_POST['state']);
	$city = sanitizeMySQL($conn, $_POST['city']);
	$street = sanitizeMySQL($conn, $_POST['street']);
	$zip = sanitizeMySQL($conn, $_POST['zip']);

	$query = "UPDATE user SET Phone_Number='$phone', Email='$email', `State`='$state', City='$city', Street='$street', ZIP='$zip' WHERE ID='$user_id'";
	$result = $conn->query($query);
	if(!$result) die("No result 1");
}

/**
 * Add the customer's credit card to the credit_card table if the credit card does not already exist. 
 * 
 * @param $conn The connection to the database
 */
function verifyCreditCard($conn){
	$user_id = sanitizeMySQL($conn, $_POST['user_id']);
	$card_num = sanitizeMySQL($conn, $_POST['cardNum']);
	$card_hash = password_hash("$card_num", PASSWORD_DEFAULT);
	$card_type = sanitizeMySQL($conn, $_POST['cardType']);
	$card_exp = sanitizeMySQL($conn, $_POST['cardExp']);
	$card_sec = sanitizeMySQL($conn, $_POST['cardCode']);

	$query = "SELECT * FROM credit_card WHERE ID='$user_id'";
	$result = $conn->query($query);
	if(!$result) die("No result 2");

	$card_exists = FALSE;
	$numRows = $result->num_rows;
	for($x=0; $x<$numRows; ++$x){
		$row = $result->fetch_array(MYSQLI_ASSOC);
		
		$card_hashing = $row["Card_Number"];
		if (password_verify("$card_num", $card_hashing)){
			$card_exists = TRUE;
			break;
		}
	}

	if(!$card_exists){
		$query = "INSERT INTO credit_card (ID, Card_Number, Card_Type, Expiration, Security_Code) VALUES ('$user_id', '$card_hash', '$card_type', '$card_exp', '$card_sec')";
		$result = $conn->query($query);
		if(!$result) die("No result 3");
	}
}

/**
 * 2. Records order information for the order. Add order to the orders table and add orderlines to the orderline table.
 * 
 * @param $conn The connection to the database
 */
function recordOrderInfo($conn) {
	$user_id = sanitizeMySQL($conn, $_POST['user_id']);
	$curr_date = time();
	$curr_date = date("Y-m-d",$curr_date);
	$card_num = sanitizeMySQL($conn, $_POST['cardNum']);
	$card_end = substr($card_num, -4);
	$card_type = sanitizeMySQL($conn, $_POST['cardType']);
	$card_hash = password_hash("$card_num", PASSWORD_DEFAULT);
	$query = "INSERT INTO orders (Order_Date, Customer_ID, Credit_Card_Num, Credit_Card_End, Card_Type) VALUES ('$curr_date', '$user_id', '$card_hash', '$card_end', '$card_type')"; //!!!!!!!!!!!!!!!!!!!!!!!!
	$result = $conn->query($query);
	if(!$result) die("No result 4");

	$query = "SELECT Order_ID FROM orders ORDER BY Order_ID DESC LIMIT 1";
	$result = $conn->query($query);
	if(!$result) die("No result 5");

	$numRows = $result->num_rows;
	for($x=0; $x<$numRows; ++$x){
		$row = $result->fetch_array(MYSQLI_ASSOC);
		
		$order_id = sanitizeMySQL($conn, $row['Order_ID']);
	}

	addOrderLines($conn, $order_id);
}

/**
 * Add orderlines to the orderline table.
 * 
 * @param $conn     The connection to the database
 * @param $order_id The order id of the order to add the orderlines into
 */
function addOrderLines($conn, $order_id){
	$user_id = sanitizeMySQL($conn, $_POST['user_id']);

	$result = queryCartItems($conn, $user_id);

	$numRows = $result->num_rows;
	for($x=0; $x<$numRows; ++$x){
		$row = $result->fetch_array(MYSQLI_ASSOC);
		
		// Add orderlines
		$product_id = sanitizeMySQL($conn, $row['Product_ID']);
		$quantity = sanitizeMySQL($conn, $row['Quantity']);
		$subquery = "INSERT INTO orderline (Order_ID, Product_ID, Quantity) VALUES ('$order_id', '$product_id', '$quantity')";
		$subresult = $conn->query($subquery);
		if(!$subresult) die("No result 7");
	}
}

/**
 * 3. Update quantity in different inventories. Updates the quantity column in the inventory table.
 * 
 * @param $conn The connection to the database
 */
function updateQuantityInInventory($conn){
	$user_id = sanitizeMySQL($conn, $_POST['user_id']);

	$result = queryCartItems($conn, $user_id);

	$numRows = $result->num_rows;
	for($x=0; $x<$numRows; ++$x){
		$row = $result->fetch_array(MYSQLI_ASSOC);

		$product_id = sanitizeMySQL($conn, $row['Product_ID']);
		$quantity = sanitizeMySQL($conn, $row['Quantity']);

		// Update quantity in different inventories for each cart item
		$subquery = "SELECT * FROM inventory WHERE Product_ID='$product_id'";
		$subresult = $conn->query($subquery);
		if(!$subresult) die("No result 8");

		$subNumRows = $subresult->num_rows;
		for ($y=0; $y<$subNumRows && $quantity > 0; ++$y) {
			$subrow = $subresult->fetch_array(MYSQLI_ASSOC);

			$inv_id = $subrow["Inventory_ID"];
			$subquantity = $subrow["Quantity"];
			if ($subquantity<=0) {
				continue;
			} else if ($subquantity >= $quantity){
				$update_quantity = $subquantity - $quantity;
				$nestedquery = "UPDATE inventory SET Quantity='$update_quantity' WHERE Inventory_ID='$inv_id'";
				$nestedresult = $conn->query($nestedquery);
				if(!$nestedresult) die("No result 9 a");

				$quantity -= $subquantity;
			} else {
				$nestedquery = "UPDATE inventory SET Quantity='0' WHERE Inventory_ID='$inv_id'";
				$nestedresult = $conn->query($nestedquery);
				if(!$nestedresult) die("No result 9 b");

				$quantity -= $subquantity;
			}
		}
	}
}

/**
 * 4. Remove all cart items once processing the order is complete.
 * 
 * @param $conn The connection to the database
 */
function removeCartItems($conn){
	$user_id = sanitizeMySQL($conn, $_POST['user_id']);
	$query = "DELETE FROM cart_item WHERE ID='$user_id'";
	$result = $conn->query($query);
	if(!$result) die("No result 10");
}

/**
 * Query to get all cart items for a specific user.
 * 
 * @param $conn The connection to the database
 */
function queryCartItems($conn) {
	$user_id = sanitizeMySQL($conn, $_POST['user_id']);
	$query = "SELECT * FROM cart_item WHERE ID='$user_id'";
	$result = $conn->query($query);
	if(!$result) die("No result 6");
	return $result;
}
?>