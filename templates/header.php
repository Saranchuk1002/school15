<?php session_start() ?>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" style="color: #fefefe " href="admin.php">
                <img src="img/logo1.png" alt="logo" width="50" height="50">
                Школа №15
            </a>
            <?php if (isset($_SESSION['login_mail'])): ?>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Вы вошли как: <?php echo $_SESSION['login_mail']; ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="results.php?logout=true">Результаты тестов</a>
                        <a class="dropdown-item" href="login.php?logout=true">Выйти</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</header>
