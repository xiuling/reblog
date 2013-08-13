<?php
    session_start();
    unset($_SESSION['username']);
    unset($_SESSION['logged']);
    unset($_SESSION['authCode']);
    session_destroy();//释放所有session
   	header("location:index.php");
?>