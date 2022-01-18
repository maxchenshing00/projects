<?php
require_once 'utility/login-cred.php';
$conn = new mysqli($hn, $un, $pw, $db);
if($conn->connect_error) die($conn->connect_error);

//// 1. Make changes to the database
// Password in user table needs to be large enough to store password hash
password_column_change($conn);

// Dropping admin table and creating role table
admin_table_changes($conn);

// Deleting existing users (john123, adam123, bianca123)
delete_phony_users($conn);


//// 2. Add new users and their roles
//Abe Baboon
$forename = 'Abe';
$surname = 'Baboon';
$username = 'abe';
$password = 'abe';
$city = 'Salt Lake City';
$street = 'Baboon Street';
$zip = '12345';
$state = 'UT';
$phone = '111-111-1111';
$email = 'abe@gmail.com';

$token = password_hash($password,PASSWORD_DEFAULT); 

add_user($conn, $forename, $surname, $username, $token, $city, $street, $zip, $state, $phone, $email);

//Betty Monkey
$forename = 'Betty';
$surname = 'Monkey';
$username = 'betty';
$password = 'betty';
$city = 'Salt Lake City';
$street = 'Monkey Street';
$zip = '12345';
$state = 'UT';
$phone = '111-111-1112';
$email = 'betty@gmail.com';

$token = password_hash($password,PASSWORD_DEFAULT); 

add_user($conn, $forename, $surname, $username, $token, $city, $street, $zip, $state, $phone, $email);

//Charlie Chimp
$forename = 'Charlie';
$surname = 'Chimp';
$username = 'charlie';
$password = 'charlie';
$city = 'Salt Lake City';
$street = 'Chimpanzee Street';
$zip = '12345';
$state = 'UT';
$phone = '222-222-2222';
$email = 'charlie@gmail.com';

$token = password_hash($password,PASSWORD_DEFAULT); 

add_user($conn, $forename, $surname, $username, $token, $city, $street, $zip, $state, $phone, $email);

$position = 'manager';
$store = '1';

add_role($conn, $username, $position, $store);

$position = 'admin';
$store = '1';

add_role($conn, $username, $position, $store);

//Donald Gorilla
$forename = 'Donald';
$surname = 'Gorilla';
$username = 'donald';
$password = 'donald';
$city = 'Salt Lake City';
$street = 'Gorilla Street';
$zip = '12345';
$state = 'UT';
$phone = '222-222-2223';
$email = 'donald@gmail.com';

$token = password_hash($password,PASSWORD_DEFAULT); 

add_user($conn, $forename, $surname, $username, $token, $city, $street, $zip, $state, $phone, $email);

$position = 'sales';
$store = '1';

add_role($conn, $username, $position, $store);

//Peter Jones - user
$forename = 'Peter';
$surname = 'Jones';
$username = 'pjones';
$password = 'pass';
$city = 'Dallas';
$street = 'XYZ Street';
$zip = '14392';
$state = 'TX';
$phone = '345-343-2223';
$email = 'pjones@gmail.com';

$token = password_hash($password,PASSWORD_DEFAULT); 

add_user($conn, $forename, $surname, $username, $token, $city, $street, $zip, $state, $phone, $email);

//Bob Smith - user
$forename = 'Bob';
$surname = 'Smith';
$username = 'bsmith';
$password = 'pass';
$city = 'Las Vegas';
$street = 'ABC Street';
$zip = '58934';
$state = 'NV';
$phone = '493-009-3948';
$email = 'bsmith@gmail.com';

$token = password_hash($password,PASSWORD_DEFAULT); 

add_user($conn, $forename, $surname, $username, $token, $city, $street, $zip, $state, $phone, $email);


/**
 * Add user to user table
 */
function add_user($conn, $forename, $surname, $username, $token, $city, $street, $zip, $state, $phone, $email){
	//code to add user here
	$query = "insert into user(First_Name, Last_Name, Username, Password, City, Street, ZIP, State, Phone_Number, Email) values ('$forename', '$surname', '$username', '$token', '$city', '$street', '$zip', '$state', '$phone', '$email')";
	$result = $conn->query($query);
	if(!$result) die($conn->error);
}

/**
 * Add authorization to role table
 */
function add_role($conn, $username, $position, $store){
	//code to add role here
	$query = "insert into role(Username, Position, Store_ID) values ('$username', '$position', '$store')";
	$result = $conn->query($query);
	if(!$result) die($conn->error);
}

/**
 * Changing password data type in the user table to be large enough to store password hash
 */
function password_column_change($conn){
	$query = "ALTER TABLE user DROP COLUMN Password";
	$result = $conn->query($query);
	if(!$result) die($conn->error);

	$query = "ALTER TABLE user ADD Password varchar(150)";
	$result = $conn->query($query);
	if(!$result) die($conn->error);
}

/**
 * Adding username column to admin table
 */
function admin_table_changes($conn){
	$query = "DROP TABLE IF EXISTS `admin`";
	$result = $conn->query($query);
	if(!$result) die($conn->error);

	$query = "DROP TABLE IF EXISTS `role`";
	$result = $conn->query($query);
	if(!$result) die($conn->error);

	$query = <<<ttttt
	CREATE TABLE `role` (
		`ID` int(11) NOT NULL AUTO_INCREMENT,
		`Username` varchar(45) NOT NULL,
		`Position` varchar(45) NOT NULL,
		`Store_ID` int(11) NOT NULL,
		PRIMARY KEY (`ID`),
		KEY `Store_ID_idx` (`Store_ID`),
		CONSTRAINT `fk_role_ID` FOREIGN KEY (`ID`) REFERENCES `user` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
		CONSTRAINT `fk_role_StoreID` FOREIGN KEY (`Store_ID`) REFERENCES `store` (`Store_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
		CONSTRAINT `fk_role_Username` FOREIGN KEY (`Username`) REFERENCES `user` (`Username`) ON DELETE NO ACTION ON UPDATE CASCADE
	);
	ttttt;
	$result = $conn->query($query);
	if(!$result) die($conn->error);
}

/**
 * Deleting the old users
 */
function delete_phony_users($conn){
	$query = "DELETE FROM user";
	$result = $conn->query($query);
	if(!$result) die($conn->error);
}
?>