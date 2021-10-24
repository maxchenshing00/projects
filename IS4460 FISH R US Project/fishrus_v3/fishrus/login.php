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

        </style>
    </head>

    <body>
        <h1>FISH R US</h1>

        <form action="home.php" method="get">
        <table class="center" style="background-color:#3587b8">
            <tr>
                <td colspan='2'><h2>Sign In</h2><br></td>
            </tr>
            <tr>
                <td><label for="email">Email</label><br>
                <input type="text" name="email"><br><br></td>
            </tr>
            <tr>
                <td><label for="password">Password</label><br>
                <input type="text" name="password"><br><br></td>
            </tr>
            <tr>
                <td><input type="submit" value="Sign In" ></td>
            </tr>
        </table>
        </form>

        <br><br>
        <form action="home.php" method="get">
            <table class="center">
                <tr>
                    <td style="text-align:center"><body>New?</body></td>
                </tr>
                <tr>
                    <td style="text-align:center"><input type="submit" value="Register"></td>
                </tr>
            </table>
        </form>

    </body> 
</html>