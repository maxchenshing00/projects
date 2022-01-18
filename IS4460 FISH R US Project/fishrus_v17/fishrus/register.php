<?php
	require_once "utility/sanitize.php";
	require_once 'utility/login-cred.php';
	require_once "User.php";

	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die("Fatal Error");
?>

<?php
	// If form has been filled out, then add the user to the database and log them in.
	if(isset($_POST['userName'])){
		$username = sanitizeMySQL($conn, $_POST['userName']);
		$password = sanitizeMySQL($conn, $_POST['Password']);
		$password_confirm = sanitizeMySQL($conn, $_POST['confirmPassword']);
		$firstname = sanitizeMySQL($conn, $_POST['custFName']);
		$lastname = sanitizeMySQL($conn, $_POST['custLName']);
		$email = sanitizeMySQL($conn, $_POST['email']);
		$phone = sanitizeMySQL($conn, $_POST['phone']);

		if($password != $password_confirm){
			header("Location: register.php?error=passwords do not match");
			exit();
		}else{
			// Adding the user to the database
			$token = password_hash($password,PASSWORD_DEFAULT); 
			add_user($conn, $firstname, $lastname, $username, $token, NULL, NULL, NULL, NULL, $phone, $email);

			// Logging in the user
			session_start();

			$user = new User($username);
			$_SESSION['user'] = $user;
			
			header("Location: home.php");
			exit();
		}
	}
?>


<html>
	<head>
        <title>Register Page</title>
	</head>
	
	<body>
		<header align='center'>
			<img src='img/register-icon.png' alt="Register" style="width:300px;height:300px;">
		</header>
		<br>
		<form action='register.php' method='POST'>
		<table class='table-border' align='center' width='50%'>
			<tr>
				<td colspan='2'><h2>New Customer</h2></td>
			</tr>
			<tr>
				<td>Username:</td>
				<td><input type='text' name='userName' size='50' maxlength='50' placeholder="Username" required></td>				
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type='password' name='Password' size='50' maxlength='50' placeholder="Password" required></td>				
			</tr>
			<tr>
				<td>Confirm Password:</td>
				<td><input type='password' name='confirmPassword' size='50' maxlength='50' placeholder="Password" required></td>				
			</tr>
			<tr>
				<td>First Name:</td>
				<td><input type='text' name='custFName' size='50' maxlength='50' placeholder="First" required></td>				
			</tr>
			<tr>
                <td>Last Name:</td>
				<td><input type='text' name='custLName' size='50' maxlength='50' placeholder="Last" required></td>				
			</tr>
			<tr>
				<td>Email:</td>
				<td><input type='text' name='email' size='50' placeholder="name@gmail.com" required></td>				
			</tr>
			<tr>
				<td>Phone Number:</td>
				<td><input type="tel" id="phone" name="phone" size='12' placeholder="123-456-7890" maxlength="12" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required></td>				
			</tr>
            <tr>
				<td>
					<br><br>
					<input type="button" value="Cancel" onclick="history.back()">
					<input type="submit" value="Submit">
				</td>
			</tr>
			<tr>
				<?php
					if (isset($_GET['error'])){
				?>
					<td style="color:red;">
						<br><br>
						<?php echo "Error: ".$_GET['error']; ?>
					</td>
				<?php
					}
				?>
				
			</tr>
		</table>
	</body>
</html>

<?php
/**
 * Add user to user table
 */
function add_user($conn, $forename, $surname, $username, $token, $city, $street, $zip, $state, $phone, $email){
	//code to add user here
	$query = "insert into user(First_Name, Last_Name, Username, Password, City, Street, ZIP, State, Phone_Number, Email) values ('$forename', '$surname', '$username', '$token', '$city', '$street', '$zip', '$state', '$phone', '$email')";
	$result = $conn->query($query);
	if(!$result) die($conn->error);
}
?>