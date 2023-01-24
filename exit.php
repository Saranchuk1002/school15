<?php
    include_once  'db.php';
    session_start();
    $_SESSION['login_mail']='';
    header ('location: login.php');
    ?>