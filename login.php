<?php
include('templates/header.php');
error_reporting(0);
include_once 'db.php';
session_start();

if ($_GET['logout'] == 'true') {
    unset($_SESSION['login_mail']);
    header('location: login.php');
    exit;
}

if ($_POST['email'] != '' && $_POST['password'] != '') {
    $email = (string) $_POST['email'];
    $password = (string) $_POST['password'];

    $res = $db->query("SELECT * FROM users WHERE username = '{$email}' AND password = '{$password}'");
    if ($res->rowCount() > 0) {
        $row = $res->fetch();
        $user_type = $row['type'];
        if ($user_type == 'admin' || 'user') {
            $_SESSION['login_mail'] = $row['username'];
            header('location: admin.php');
            exit;
        } else {
            $append_this = "<div class='text-center'>
        <h3> Неверный логин или пароль!</h3>
        </div>";
        }
    }
}

if ($_SESSION['login_mail'] != '') {
    $login_mail = $_SESSION['login_mail'];
}

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center" style="color: #222222">
                    <h3>Авторизация</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($append_this)): ?>
                        <div class="alert alert-danger mb-4" role="alert"><?php echo $append_this; ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="form-group">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Логин" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Пароль" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Войти</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<style>
    body {
        background-color: #222;
        color: #fff;
    }

    .container {
        margin: 20px auto;
        max-width: 800px;
        padding: 20px;
        background-color: #333;
        border-radius: 8px;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
    }

    form {
        text-align: center;
        margin-bottom: 20px;
    }

    label {
        color: #fff;
    }

    select {
        padding: 8px 12px;
        font-size: 16px;
        border: none;
        background-color: #555;
        color: #fff;
        border-radius: 4px;
    }

    .subject {
        margin-bottom: 30px;
    }

    .subject-title {
        font-size: 24px;
        margin-bottom: 10px;
        transition: color 0.3s ease;
    }

    .subject-title:hover {
        color: #ffca28;
    }

    .test-list li {
        margin-bottom: 5px;
    }


    .btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        background-color: #ffca28;
        color: #222;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .btn:hover {
        background-color: #ffc107;
    }
</style>
</body>
</html>

