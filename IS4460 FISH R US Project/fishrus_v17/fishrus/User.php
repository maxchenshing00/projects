<?php
require_once 'utility/login-cred.php';
$conn = new mysqli($hn, $un, $pw, $db);
if($conn->connect_error) die($conn->connect_error);

class User{
    public $username;
    public $first_name;
    public $last_name;
    public $id;
    public $roles = Array();

    function __construct($username) {
        global $conn;

        $this->username=$username;

        $query = "SELECT * FROM user WHERE Username = '$username'";
        $result = $conn->query($query);
        if(!$result) die($conn->error);

        $rows = $result->num_rows;
        for($i=0; $i<$rows; $i++){
            $row = $result->fetch_array(MYSQLI_ASSOC);

            $this->first_name=$row['First_Name'];
            $this->last_name=$row['Last_Name'];
            $this->id=$row['ID'];
        }

        $query = "SELECT * FROM `role` WHERE Username = '$this->username'";
        $result = $conn->query($query);
        if(!$result) die($conn->error);

        $roles = Array();

        $rows = $result->num_rows;
        for($i=0; $i<$rows; $i++){
            $row = $result->fetch_array(MYSQLI_ASSOC);

            array_push($roles, $row['Position']);
        }

        $this->roles=$roles;
    }
}


?>