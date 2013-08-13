<?php
    session_start();
   // include 'db.inc.php';
    include 'db.inc.php'; 
    
    $username = isset($_POST['username'])?trim($_POST['username']):'';
    $password = isset($_POST['password'])?trim($_POST['password']):'';

    
    if (isset($_POST['submit'])) {
        $uid = $redis->get("user:$username:uid");
        if($uid == NULL){
            $error = 'wrong username';
            header('Location:login.php?&error='.$error);
        }else{
            $pass=$redis->get("user:$uid:password");
            if($pass != $password){
                $error = 'wrong password';
                header('Location:login.php?&error='.$error);
            }
        }

        if (empty($error)) {
            $_SESSION['username'] = $username;
            $_SESSION['logged'] = 1;
            $_SESSION['authorId'] = $redis->get("user:$uid:authorId");

            header ('Refresh: 1; URL=admin.php');
            echo ' <p> You will be redirected to your original page request. </p> ';
            echo ' <p> If your browser doesn\'t redirect you properly ' . 
                'automatically, <a href="admin.php" >click here </a> . </p> ';

        } else {
            $_SESSION['username'] = '';
            $_SESSION['logged'] = 0;
            $_SESSION['authorId'] = 0;

            $error = ' <p> <strong> You have supplied an invalid username and/or ' .
                    'password! </strong> Please <a href="register.php"> click here ' .
                    'to register </a> if you have not done so already. </p> ';
            
        }
    } else{
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Blogs</title>
    <link rel="stylesheet" type="text/css" href="css/page.css" />
    
</head>
<body>
<div id="wrap">
    <div id="head">
        <div id="banner"><h1><a href="index.php">Blogs</a></h1></div>
        <div id="search">
            <form method="get" action="search.php">
                <label for="search">Search</label>
                <?php
                    echo '<input type="text" name="search" ';
                    if(isset($_GET['search'])){
                        echo 'value="' . htmlspecialchars($_GET['search']) . '"';
                    }
                    echo '/>';
                ?>
                <input type="submit" value="Search" />
            </form>
        </div>
        <div class="clear"></div>
    </div>
    <div id="nav">
        <ul>
            <li id="home"><a href="index.php">Home</a></li>
            <li id="about"><a href="about.php">About</a></li>
        </ul>
    </div>
    <div class="main">
    <?php
        if (isset($_GET['error'])) {
            echo $_GET['error'];
        }
    ?>
    <form action="login.php" method="post">
    <table>
        <tr>
            <td> Username: </td>
            <td> <input type="text" name="username" maxlength="20" size="20" 
                value=" <?php echo $username; ?> " /> </td>
        </tr> 
        <tr>
            <td> Password: </td>
            <td> <input type="password" name="password" maxlength="20" size="20"
                value=" <?php echo $password; ?> " /> </td>
        </tr> 
        <tr>
            <td> </td>
            <td><input type="submit" name="submit" value="Login" /></td>
        </tr>
    </table>
    </form>
</div>

<?php
include 'foot.inc.php';
}
?>