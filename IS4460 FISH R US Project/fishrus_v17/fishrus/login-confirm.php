<?php
    require_once "utility/sanitize.php";
    require_once "utility/login-cred.php";
    require_once "User.php";

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
            $query="SELECT * FROM user WHERE Username = '$uname'"; //need hashing code in future, password_verify
            $result = $conn->query($query);
            if (!$result) die ("Error: Database access failed, login");

            $rows = $result->num_rows;

            if($rows === 1){
                $row = mysqli_fetch_assoc($result);
                if($row['Username']===$uname && password_verify($pass, $row['Password'])){
                    session_start();

                    $user = new User($uname);
                    $_SESSION['user'] = $user;
                    
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


