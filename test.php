<?php
    error_reporting(0);
    include_once  'db.php';
    session_start();

    $id = (int) $_GET['id'];
    if ($id < 1) {
        header ('location: admin.php');
    }


    $testId = $id;
    if (!isset($_SESSION['test_id']) || $_SESSION['test_id'] != $testId) {
        $_SESSION['test_id'] = $testId;
        $_SESSION['test_score'] = 0;
    }

    $res = $db->query("SELECT * FROM tests WHERE id = {$testId}");
    $row = $res->fetch();
    $testTitle = $row['title'];

    $questionNum = (int) $_POST['q'];
    if($_SESSION['email']=='' || $_SESSION['name']=='')
    {
        $_SESSION['email']= (string) $_POST['email'];
        $_SESSION['name']= (string) $_POST['name'];
    }
    if (empty($questionNum)) {
        $questionNum = 0;
        $question="Введите email";
        $question1="Введите ФИО";
        $res = $db->query("SELECT count(*) AS count FROM questions WHERE test_id = {$testId}");
        $row = $res->fetch();
        $questionCount = $row['count'];
        $showForm = 1;
        $_SESSION['max_score']=0;
        $_SESSION['test_score']=0;
        $questionNum++;

    }
    else{
        if($_POST['timer']=='')
        {
            $res = $db->query("SELECT * FROM tests WHERE id = {$testId}");
            $row = $res->fetch();
            $_POST['timer_hid']=$row['time']*60;
        }

        $questionStart = $questionNum - 1;


        $res = $db->query("SELECT count(*) AS count FROM questions WHERE test_id = {$testId}");
        $row = $res->fetch();
        $questionCount = $row['count'];

        $answerId = (int) $_POST['answer_id'];
        if (!empty($answerId)) {
            $res = $db->query("SELECT * FROM answers WHERE id = {$answerId}");
            $row = $res->fetch();
            $score = $row['score'];
            $_SESSION['test_score'] += $score;
        }

        $showForm = 0;
        if ($questionCount >= $questionNum) {
            $showForm = 1;

            $res = $db->query("SELECT * FROM questions WHERE test_id = {$testId} LIMIT {$questionStart}, 1");
            $row = $res->fetch();
            $question = $row['question'];
            $questionId = $row['id'];

            $res = $db->query("SELECT * FROM answers WHERE question_id = {$questionId}");
            $answers = $res->fetchAll();
        } else {
            $score = $_SESSION['test_score'];

            $max_value=$_SESSION['max_score'];
            $_SESSION['max_score']=0;
            $_SESSION['test_score']=0;

        }
        $questionNum++;
    }
    if($_POST['timer_hid']=='0')
    {
        $showForm=0;
        $res = $db->query("SELECT * FROM questions WHERE test_id = {$testId}");
        $scoress = $res->fetchAll();
        $_SESSION['max_score']=0;
        foreach ($scoress as $scores)
        {
            $questionId = $row['id'];
            $res = $db->query("SELECT * FROM answers WHERE question_id = {$questionId}");
            $answers = $res->fetchAll();
            $max_value=0;
            foreach ($answers AS $answer) {
                $curr_answer_id=$answer['id'];
                $curr_answer=$answer['answer'];
                if($max_value<$answer['score'])
                {
                    $max_value=$answer['score'];
                }
            }
            $_SESSION['max_score']+=$max_value;
        }
    }
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
    <link rel="stylesheet" href="css/app.css">
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
<body>

    <div class="container">
        <?php if ($showForm) { ?>
            <form action="test.php?id=<?php echo $testId; ?>" method="post">
                <input type="hidden" name="q" value="<?php echo $questionNum; ?>">
                <?php $questionNum--; ?>

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <script>
                            timeMinut=<?php echo $_POST['timer_hid'];?>;
                            timer = setInterval(function () {
                                let timer_show = document.getElementById('timer');
                                let timer_hid = document.getElementById('timer_hid');
                                seconds = timeMinut%60 // Получаем секунды
                                minutes = timeMinut/60%60 // Получаем минуты
                                // Условие если время закончилось то...
                                if (timeMinut <= 0) {
                                    // Таймер удаляется
                                    timer_show.value="Время вышло!";
                                    timer_hid.value=0;
                                    clearInterval(timer);

                                    // Выводит сообщение что время закончилось
                                } else { // Иначе
                                    // Создаём строку с выводом времени
                                    let strTimer = `${Math.trunc(minutes)}:${seconds}`;
                                    // Выводим строку в блок для показа таймера
                                    timer_show.value=" "+strTimer;
                                    timer_hid.value=timeMinut;
                                }
                                --timeMinut; // Уменьшаем таймер
                            }, 1000)
                        </script>
                        <div class="text-center mt-5">
                            <p>Вопрос <?php echo $questionNum . ' из ' . $questionCount; ?></p>
                        </div>
                        <div class="card mt-3">
                            <div class="card-header text-center" >
                                <h3><?php echo $question; ?></h3>
                            </div>
                            <div class="card-body">
                                <?php if($questionNum!=0)
                                {
                                    $max_value=0;
                                    foreach ($answers AS $answer) {
                                        $curr_answer_id=$answer['id'];
                                        $curr_answer=$answer['answer'];
                                        if($max_value<$answer['score'])
                                        {
                                            $max_value=$answer['score'];
                                        }
                                        echo"
                                    <div>
                                        <input type='radio' name='answer_id' required value='";
                                        echo"$curr_answer_id";
                                        echo";'>";
                                        echo"$curr_answer";
                                        echo" 
                                    </div>";
                                }
                                $_SESSION['max_score']+=$max_value;

                                }
                                else{
                                    echo "
                                <div class='text-center'>
                                    <input type='email' name='email' placeholder='Введите почту' required >
                                </div>
                            </div>
                            <div class='card-header text-center'>
                                <h3> $question1 </h3>
                            </div>
                            <div class='card-body text-center'>
                                <div>
                                    <input type='text' name='name' placeholder='Введите ФИО' required >
                                </div>
                            </div>";
                                } ?>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <?php if ($questionCount == $questionNum) { ?>
                                <button class="btn btn-primary" onclick="history.go(-1);">Назад </button>
                                <button type="submit" class="btn btn-success">Получить результат</button>
                            <?php } else { ?>
                            <?php if ($questionNum==1) { ?>
                                <button class="btn btn-primary" onclick="history.go(-1);" disabled>Назад </button>
                                <button type="submit" class="btn btn-primary">Дальше</button>
                            <?php } else { ?>
                                <button class="btn btn-primary" onclick="history.go(-1);">Назад </button>
                                <button type="submit" class="btn btn-primary">Дальше</button>
                            <?php } ?>
                            <?php } ?>
                           <?php if (($questionCount == $questionNum && $questionNum!=0) || $questionNum!=0) { ?>
                                <h3><input type='text' readonly id="timer" name="timer" style="border:none;text-align:center;" size="20" value=" <?php echo floor($_POST['timer_hid']/60);?>:<?php echo $_POST['timer_hid']%60;?>"></h3>
                                <h3><input type='hidden' readonly id="timer_hid" name="timer_hid" style="border:none;text-align:center;" size="20" value="<?php echo $_POST['timer_hid'];?>"></h3>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </form>
        <?php } else { ?>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="text-center">Тест пройден!</h3>
                        </div>
                        <div class="card-body">
                            <div class="result-print">
                                <?php
                                    $res = $db->prepare("INSERT IGNORE INTO test_result (`email`, `name`, `test_id`, `score`, `max_score`) 
                                    VALUES (:email, :name, :test_id, :score, :max_score)");
                                    $res->execute([
                                        ':email' => $_SESSION['email'],
                                        ':name' => $_SESSION['name'],
                                        ':test_id' => $testId,
                                        ':score' => $score,
                                        ':max_score' => $max_value,
                                    ]);
                                    $_SESSION['email']= (string) '';
                                    $_SESSION['name']= (string) '';
                                ?>
                                Ваш результат
                                <?php echo $score/$max_value*100 ;?>
                                % (
                                <?php echo $score; ?>
                                из
                                <?php echo $max_value; ?>
                                )
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
