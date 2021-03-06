<!DOCTYPE html>
<html>
    <head>
        <?php
            require("func/func.php");
            require("func/conn.php"); 
        ?>
        <title>4Grounds - Hub</title>
        <link rel="stylesheet" href="/css/global.css">
        <link rel="stylesheet" href="/css/header.css">
    </head>
    <body> 
        <?php require("important/header.php");  ?>
        <center><h1 style="display: inline-block;">4Grounds - Register</h1><br>
            <?php
                if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['password'] && $_POST['username']) 
                {
                    $email = htmlspecialchars(@$_POST['email']);
                    $username = htmlspecialchars(@$_POST['username']);
                    $password = @$_POST['password'];
                    $passwordhash = password_hash(@$password, PASSWORD_DEFAULT);
                    
                    if($_POST['password'] !== $_POST['confirm']){ $error = "password and confirmation password do not match"; goto skip; }

                    if(strlen($username) > 21) { $error = "your username must be shorter than 21 characters"; goto skip; }
                    if(strlen($password) < 8) { $error = "your password must be at least 8 characters long"; goto skip; }
                    if(!preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $password)) { $error = "please include both letters and numbers in your password"; goto skip; }

                    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result->num_rows) { $error = "there's already a user with that same name!"; goto skip; }

                    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result->num_rows) { $error = "there's already a user with that same email!"; goto skip; }
                            
                    //TODO: add cloudflare ip thing 
                    $stmt = $conn->prepare("INSERT INTO `users` (`username`, `email`, `password`) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $username, $email, $passwordhash);
                    $stmt->execute();

                    $stmt = $conn->prepare("SELECT `id` FROM `users` WHERE `username` = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                                        
                    $stmt->close();
                    $conn->close();
                    session_set_cookie_params(69420000);
                    $_SESSION['user'] = htmlspecialchars($username);
                    header("Location: home.php");
                }
                skip:
            
            if(isset($error)) { echo "<small style='color:red'>".$error."</small>"; } ?>
            <form method="post">
                <input required placeholder="Username" type="text" name="username"><br>
                <input required placeholder="E-Mail" type="email" name="email"><br><br>
                <input required placeholder="Password" type="password" name="password"><br>
                <input required placeholder="Confirm Password" type="password" name="confirm"><br><br>
                <input type="submit" value="Register">
            </form>
            <a href="index.php"><< Back</a>
        </center>
    </body>
</html>