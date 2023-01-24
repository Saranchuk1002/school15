<?php
error_reporting(0);
    include_once  'db.php';
    session_start();
    if($_POST['email']!='' && $_POST['password']!='')
    {
        $email= (string) $_POST['email'];
        $password= (string) $_POST['password'];
        echo $email;
        echo $password;
        $res = $db->query("SELECT * FROM admin_logins WHERE email = '{$email}'");
        if(!empty($res))
        {
            $row = $res->fetch();
            $db_password = $row['password'];
            if($password==$db_password)
            {
                $_SESSION['login_mail']=$email;
                header ('location: admin.php');
            }
            else{
                $append_this="<div class='text-center' '>
                <h3> Неверный логин или пароль!</h3>
                </div>";
            }
        }
        else{
            $append_this="<div class='text-center' '>
            <h3> Такой пользователь не зарегистрирован!</h3>
            </div>";
        }

    }
    if($_SESSION['login_mail']!='')
    {
        header ('location: admin.php');
    }
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Вход в систему</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/results.css">
</head>
<nav class="navbar navbar-expand-lg bg-body-tertiary" style="background-color: darkgray">
    <div class="container-fluid">
        <a class="navbar-brand"  style="color: black" href="admin.php">Система тестирования</a>
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarScroll">
        </div>

        </form>
        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
            <li class="nav-item dropdown">

                <ul class="dropdown-menu">
                    <li><a class="dropdown-item"  href="exit.php">Выйти</a></li>
                </ul>
            </li>
        </ul>
    </div>
    </div>
</nav>
<body >
<form action="login.php" method="post">
<div class="container">
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="mt-4">
        <div class="text-center" class="card-header">
            <h1> Авторизация</h1>
            </div>
            <?php echo $append_this; ?>
            <div class='card-body text-center'>
            <div>
                <input type='email' name='email' placeholder='Логин' required >
            </div>
            </div>
            <div class='card-body text-center'>
                <div>
                    <input type='password' name='password' placeholder='Пароль' required >
                </div>
            </div>
            <div class="text-center">
            <button class="btn btn-primary" type="submit">Вход</button>
            </div>
            </div>
        </div>
        </div>
    </div>
</form>
</div>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
