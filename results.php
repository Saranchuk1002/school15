<?php
    include_once  'db.php';
    session_start();
    if($_SESSION['login_mail']=='')
    {
        header ('location: login.php');
    }
    else{
    $id = (int) $_GET['id'];
    if ($id < 1) {
        header ('location: admin.php');
    }

    $testId = $id;

    $res = $db->query("SELECT * FROM tests WHERE id = {$testId}");
    $row = $res->fetch();
    $testTitle = $row['title'];

    $res = $db->query("SELECT * FROM test_result WHERE test_id = {$testId}");
    $results = $res->fetchAll();

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Система тестирования</title>
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
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <b><?php echo $_SESSION['login_mail'];
                        ?></b>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item"  href="exit.php">Выйти</a></li>
                </ul>
            </li>
        </ul>
    </div>
    </div>
</nav>
<div class="container">
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="mt-4">
        <div class="text-center">
            <table class="table table-bordered" style="background-color: grey">
            <tr>
            <th>ФИО</th>
            <th>E-Mail</th>
            <th>Название теста</th>
            <th>Баллы</th>
            <th>Всего баллов в тесте</th>
            <th>Процент</th>
            <th>Оценка</th>


            </tr>
            <?php
            if(empty($results))
            {
                echo "</table>";
                echo "<h3> Этот тест еще не проходился! </h3>";
            }
            else {
                echo"</div><div class='card-body'>";
                foreach ($results AS $result){ ?>
                <tr class='other-rows text-center'><th class='other-th'><?php echo $result['name']; ?></th>
                <th class='other-th '><?php echo $result['email']; ?></th>
                <th class='other-th'><?php echo $testTitle; ?></th>
                <th class='other-th'><?php echo $result['score']; ?></th>
                <th class='other-th'><?php echo $result['max_score']; ?></th>
                <th class='other-th'><?php echo $result['score']/$result['max_score'] * 100; ?></th>
                <th class='other-th'><?php if ($result['score']/$result['max_score'] * 100 <=60) echo "2"; elseif ($result['score']/$result['max_score'] * 100 >60 && $result['score']/$result['max_score'] * 100 <= 80) echo "3"; else echo "5" ?></th>
                <th <form action="deleteres.php.php" method="post">
                        <input type="hidden" name="testId" value="<?php echo $testId;?>">
                        <button class="btn btn-danger">Удалить результат</button>
                    </form></th></tr>
            <?php  }echo "</div></table>";?>
            <?php  } ?>
            <p>
            <form action="delete.php" method="post">
                <input type="hidden" name="testId" value="<?php echo $testId;?>">
                <button class="btn btn-danger">Удалить тест</button>
            </form>
            <script>
                function copyLink() {
                    var $temp="localhost/test?id=<?php echo $testId;?>";
                    navigator.clipboard.writeText($temp);
                }
            </script>
        </div>
        </div>
    </div>
</div>
</div>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
<?php } ?>
