<?php
    require_once "utility/sanitize.php";
    require_once "utility/login-cred.php";

    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die("Error: Failed to connect to database.");


    if (isset($_POST['uname']) && isset($_POST['password'])){
        $uname = sanitizeString($_POST['uname']);
        $pass = sanitizeString($_POST['password']);

        if (empty($uname)){
            header("Location: login.php?error=User Name is required");
            exit();
        }else if(empty($pass)){
            header("Location: login.php?error=Password is required");
            exit();
        }else{
            $query="SELECT * FROM user WHERE Username = '$uname' AND Password='$pass'"; //create a new table
            $result = $conn->query($query);
            if (!$result) die ("Error: Database access failed, login");

            $rows = $result->num_rows;

            if($rows === 1){
                $row = mysqli_fetch_assoc($result);
                if($row['Username'] === $uname && $row['Password']===$pass){
                    session_start();
                    $_SESSION['Username'] = $row['Username'];
                    $_SESSION['First_Name'] = $row['First_Name'];
                    $_SESSION['Last_Name'] = $row['Last_Name'];
                    $_SESSION['ID'] = $row['ID'];
                    header("Location: home.php");
                    exit();
                }else{
                    header("Location: login.php?error=Incorrect User name or password");
                    exit();
                }
            }else{
                header("Location: login.php?error=Incorrect User name or password");
                exit();
            }
        }

    }else{
        header("Location: login.php");
        exit();
    }
?>


