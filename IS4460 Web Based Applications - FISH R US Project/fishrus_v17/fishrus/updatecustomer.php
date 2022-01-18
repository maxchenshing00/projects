<html> 
    <body>
    <header align='center'>
        <title>EDIT PAGE</title>
        <body>
        <h1>Edit Customer</h1>

<?php

if(isset($_GET['ID'])) {
    $customerId = $_GET['ID'];

    require_once 'utility/login-cred.php';
    
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($conn->connect_error);

    $query = "SELECT * FROM user WHERE ID='$customerId'";

    $result = $conn->query($query);
    if(!$result) die("No Result");

    $numRows = $result->num_rows;

    for($j = 0; $j < $numRows; $j++) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        }

    echo <<<_END
        <pre>
            <form method="POST" action="updatecustomer.php">
                Customer ID: <input type="hidden" name="ID" value="$row[ID]">
                Username: <input type="hidden" name="Username" value="$row[Username]">
                Password: <input type="hidden" name="Password" value="$row[Password]">
                First Name: <input type="text" name="First_Name" value="$row[First_Name]">
                Last Name: <input type="text" name="Last_Name" value="$row[Last_Name]">
                Phone Number: <input type="text" name="Phone_Number" value="$row[Phone_Number]">
                Street: <input type="text" name="Street" value="$row[Street]">
                City: <input type="text" name="City" value="$row[City]">
                State: <input type="text" name="State" value="$row[State]">
                Zip: <input type="text" name="ZIP" value="$row[ZIP]">
                Email: <input type="text" name="Email" value="$row[Email]">
                <br>
                <input type="submit" value="UPDATE">
                <tb>
                <input type="button" value="Cancel" onclick="location.href='viewcustomer.php'">
            </form>
        </pre>
    _END;
    
}


if(isset($_POST['ID'])) {

    $customerId = $_POST['ID'];
    $username = $_POST['Username'];
    $password = $_POST['Password'];
    $firstname = $_POST['First_Name'];
    $lastname = $_POST['Last_Name'];
    $phone = $_POST['Phone_Number'];
    $email = $_POST['Email'];
    $state = $_POST['State'];
    $city = $_POST['City'];
    $street = $_POST['Street'];
    $zip = $_POST['ZIP'];
 
    require_once 'utility/login-cred.php';

    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die($db);

    $query = "UPDATE user SET First_Name='$firstname', Last_Name='$lastname', Phone_Number='$phone', Email='$email', City='$city', Street='$street', ZIP='$zip' WHERE Username='$username'";

    $result = $conn->query($query);
    if(!$result) die("No Result");

    header("Location: viewcustomer.php");
}

?>

</body>
</html>
