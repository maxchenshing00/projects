<html>
	<head>
        <title>Checkout Page</title>
	</head>
	
	<body>
		<header align='center'>
			<img src='img/cart-icon.png' alt="Checkout" style="width:128px;height:128px;">
		</header>
		<br>
		<form action='confirm.php'>
		<table class='table-border' align='center' width='50%'>
			<tr>
				<td colspan='2'><h2>Customer Info</h2></td>
			</tr>
			<tr>
				<td>Name:</td>
				<td><input type='text' name='custName' size='50'></td>				
			</tr>
			<tr>
				<td>Address:</td>
				<td><input type='text' name='address' size='50'></td>				
			</tr>
			<tr>
				<td>State:</td>
				<td><input type='text' name='state' size='50'></td>				
			</tr>
			<tr>
				<td>Zip:</td>
				<td><input type='text' name='zip' size='50'></td>				
			</tr>
			<tr>
				<td>Phone Number:</td>
				<td><input type='text' name='phone' size='50'></td>				
			</tr>
			<tr>
				<td>Email:</td>
				<td><input type='text' name='email' size='50'></td>				
			</tr>	
			<tr>
				<td colspan='2'><h2>Credit Card</h2></td>
			</tr>
			<tr>
				<td>Card Type:</td>
				<td><input type='text' name='cardtype' size='50'></td>				
			</tr>
			<tr>
				<td>Card Number:</td>
				<td><input type='text' name='cardNum' size='50'></td>				
			</tr>
			<tr>
				<td>Expiration Date:</td>
				<td><input type='text' name='cardexp' size='50'></td>				
			</tr>
			<tr>
				<td>Security Code:</td>
				<td><input type='text' name='cardcode' size='50'></td>				
			</tr>	
			<tr>
				<td><input type='submit' value='Submit'></td>
				<td></td>				
			</tr>
		</table>
		</form>
	</body>
</html>	
	