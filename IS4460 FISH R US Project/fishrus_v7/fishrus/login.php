<hmtl>
    <head>
        <title>FISH R US</title>
        <style>
            h1 {
                text-align:center;
            }

            .center{
                margin-left:auto;
                margin-right:auto;
                width:20%;
                padding: 10px;
            }

            .error {
                color: red;
                padding: 10px;
                width: 95%;
                margin:20px auto;
            }

        </style>
    </head>

    <body>
        <h1>FISH R US</h1>

        <form action="login-confirm.php" method="post">
        <table class="center" style="background-color:#3587b8">
            <tr>
                <td colspan='2'><h2>Sign In</h2><br></td>
            </tr>

            <tr>
                <td><label for="uname">User Name</label><br>
                <input type="text" name="uname" placeholder="User Name"><br><br></td>
            </tr>
            <tr>
                <td><label for="password">Password</label><br>
                <input type="password" name="password" placeholder="Password"><br><br></td>
            </tr>
            <tr>
                <td><button type="submit">Login</button></td>
            </tr>
        </table>
        <table class="center">
            <?php if (isset($_GET['error'])){ ?>
                <tr>
                <td style="text-align:center">
                    <p class="error"><?php echo $_GET['error']; ?></p>
                </td>
                </tr>
            <?php } ?>
        </table>
        </form>

        <!-- <br><br>
        <form action="home.php" method="post">
            <table class="center">
                <tr>
                    <td style="text-align:center"><body>New?</body></td>
                </tr>
                <tr>
                    <td style="text-align:center"><input type="submit" value="Register"></td>
                </tr>
            </table>
        </form> -->

    </body> 
</html>