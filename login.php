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
        <?php require("important/header.php"); ?>
        <center><h1 style="display: inline-block;">4Grounds - Login</h1><br>
            <?php 
                if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['password'] && $_POST['username']) 
                {
                    if(isset($_POST['remember'])) {
                        $rememberMe = true;
                    } else {
                        $rememberMe = false;
                    }
                    $stmt = $conn->prepare("SELECT password FROM `users` WHERE username=?");
                    $stmt->bind_param("s", $_POST['username']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if(!mysqli_num_rows($result)){ { $error = "incorrect username or password"; goto skip; } }
                    
                    $row = $result->fetch_assoc();
                    $hash = $row['password'];
                    
                    if(!password_verify($_POST['password'], $hash)){ $error = "incorrect username or password"; goto skip; }
            
                    if($rememberMe == true) {
                        ini_set('session.gc_maxlifetime', 360*1000);
                        $_SESSION['user'] = htmlspecialchars($_POST["username"]);
                    } else {
                        $_SESSION['user'] = htmlspecialchars($_POST["username"]);
                    }
                    header("Location: home.php");
                } 
                skip:

                if(isset($error)) { echo "<small style='color:red'>".$error."</small>"; } 
            ?>
            <form method="post">
                <input required placeholder="Username" type="text" name="username"><br>
                <input required placeholder="Password" type="password" name="password"><br><br>
                <input type="checkbox" name="remember"> Remember me<br><br>
                <input type="submit" value="Login">
            </form>
            <a href="index.php"><< Back</a>
        </center>
    </body>
</html>