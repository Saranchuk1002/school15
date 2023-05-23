<?php
include_once 'db.php';
include('templates/header.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


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
    $subject_id = $row['subject_id'];
    $classId = $row['class_id'];

    $res = $db->query("SELECT * FROM tests WHERE id = {$testId}");
    $row = $res->fetch();
    $name = $row['name'];

    $questionNum = isset($_POST['q']) ? (int) $_POST['q'] : 0;

if (empty($questionNum)) {
        $questionNum = 0;
        $question="email";
        $question1="ФИО";
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
// Проверяем, если ключи 'email' и 'name' не установлены, то устанавливаем их в значения из $_POST
        if (!isset($_SESSION['email']) || $_SESSION['email'] === '') {
            $_SESSION['email'] = (string) $_POST['email'];
        }

        if (!isset($_SESSION['name']) || $_SESSION['name'] === '') {
            $_SESSION['name'] = (string) $_POST['name'];
        }


        $showForm = 0;
        if ($questionCount >= $questionNum) {
            $showForm = 1;

            $res = $db->query("SELECT * FROM questions WHERE test_id = {$testId} LIMIT {$questionStart}, 1");
            $row = $res->fetch();
            $question_text = $row['question_text'];
            $questionId = $row['id'];

            $res = $db->query("SELECT * FROM answers WHERE question_id = {$questionId}");
            $answer_text = $res->fetchAll();
        }

             else {
            $score = $_SESSION['test_score'];
            $max_value = $_SESSION['max_score'];
            $_SESSION['max_score'] = 0;
            $_SESSION['test_score'] = 0;
        }


        $questionNum++;
    }


        if (isset($_POST['timer_hid']) && $_POST['timer_hid'] == '0') {
            $showForm = 0;
            $res = $db->query("SELECT * FROM questions WHERE test_id = {$testId}");
            $questions = $res->fetchAll();
            $_SESSION['max_score'] = 0;

            foreach ($questions as $question) {
                $questionId = $question['id'];
                $res = $db->query("SELECT * FROM answers WHERE question_id = {$questionId}");
                $answers = $res->fetchAll();
                $max_value = 0;

                foreach ($answers as $answer) {
                    $curr_answer_id = $answer['id'];
                    $curr_answer = $answer['answer'];

                    if ($max_value < $answer['score']) {
                        $max_value = $answer['score'];
                    }
                }

                $_SESSION['max_score'] += $max_value;
            }
        }

        $login_mail = $_SESSION['login_mail'];
        $res = $db->query("SELECT * FROM users WHERE username = '{$login_mail}'");
        $userData = $res->fetch(PDO::FETCH_ASSOC);
        $email = $userData['username']; // Используем поле 'username' для хранения почты пользователя
        $fullName = $userData['full_name'];
        $userId = $userData['id'];




?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Система тестирования</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container">

        <?php if ($showForm){
            $question_text = $row['question_text'];
        ?>
        <div class="container">
            <div class="container">
                <div class="question-links">
                    <h4>Номера вопросов:</h4>
                    <ul class="question-nav">

                        <?php

                        for ($i = 1; $i <= $questionCount; $i++) {
                            if ($questionNum != 1) {
                                echo "<li><a href='javascript:void(0);' onclick='changeQuestion({$i});'>{$i}</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </div>

                <form id="question-form" action="test.php?id=<?php echo $testId; ?>" method="post">
                        <input id="question-input" type="hidden" name="q" value="<?php echo $questionNum; ?>">


                    <?php $questionNum--;
                    ?>
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
                                            <h3><?php echo $question_text; ?></h3>
                                        </div>
                                        <div class="card-body">

                                            <?php if($questionNum!==0)
                                            {

                                                $max_value=0;
                                                foreach ($answer_text AS $answer) {
                                                    $curr_answer_id=$answer['id'];
                                                    $curr_answer=$answer['answer_text'];
                                                    if($max_value<$answer['score'])
                                                    {
                                                        $max_value=$answer['score'];
                                                    }

                                                    echo"
                                    <div>
                                    <div class='radio-group' id='radio-group-" . $questionId . "'>
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
        <h3>$question</h3>
        <input type='email' name='email' placeholder='Введите почту' value='$email' readonly required>
    </div>
    <div class='card-header text-center'>
        <h3>$question1</h3>
    </div>
    <div class='card-body text-center'>
        <div>
            <input type='text' name='name' placeholder='Введите ФИО' value='$fullName' readonly required>
        </div>
    </div>";


                                            }
                                            ?>
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
                                        $_SESSION['email']= $email;
                                        $_SESSION['name']= $fullName;
                                        $_SESSION['id'] = $userId;
                                        $res = $db->prepare("INSERT IGNORE INTO test_result (`subject_id`, `email`, `name`, `test_id`, `score`, `max_score`, `class_id`,`user_id`) 
                    VALUES (:subject_id, :email, :name, :test_id, :score, :max_score, :class_id,:user_id)");
                                        $res->execute([
                                            ':subject_id' => $subject_id,
                                            ':email' => $_SESSION['email'],
                                            ':name' => $_SESSION['name'],
                                            ':test_id' => $testId,
                                            ':score' => $score,
                                            ':max_score' => $max_value,
                                            ':class_id' => $classId,
                                            ':user_id' => $_SESSION['id'],
                                        ]);
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
<script>
    function changeQuestion($questionNum) {
        if($questionNum!=0) {
            document.getElementById("question-input").value = $questionNum;
            document.getElementById("question-form").submit();
        }
    }
    // Получаем все группы радиокнопок на странице
    var radioGroups = document.querySelectorAll('.radio-group');

    // Функция для сохранения выбранного значения в заданной группе
    function saveRadioButtonValue(group) {
        var radioButtons = group.querySelectorAll('input[type="radio"]');
        var selectedValue = null;

        // Ищем выбранную радиокнопку в заданной группе
        for (var i = 0; i < radioButtons.length; i++) {
            if (radioButtons[i].checked) {
                selectedValue = radioButtons[i].value;
                break;
            }
        }

        // Сохраняем значение в localStorage
        localStorage.setItem(group.id, selectedValue);
    }

    // Функция для восстановления выбранного значения в заданной группе
    function restoreRadioButtonValue(group) {
        var radioButtons = group.querySelectorAll('input[type="radio"]');
        var selectedValue = localStorage.getItem(group.id);

        // Устанавливаем выбранное значение радиокнопки в заданной группе
        if (selectedValue) {
            for (var i = 0; i < radioButtons.length; i++) {
                if (radioButtons[i].value === selectedValue) {
                    radioButtons[i].checked = true;
                    break;
                }
            }
        }
    }

    // Вызываем функцию восстановления значения для каждой группы при загрузке страницы
    for (var i = 0; i < radioGroups.length; i++) {
        restoreRadioButtonValue(radioGroups[i]);
    }

    // Сохраняем значение при изменении выбора радиокнопки в каждой группе
    for (var i = 0; i < radioGroups.length; i++) {
        var radioButtons = radioGroups[i].querySelectorAll('input[type="radio"]');
        for (var j = 0; j < radioButtons.length; j++) {
            radioButtons[j].addEventListener('change', function() {
                saveRadioButtonValue(this.parentNode);
            });
        }
    }
</script>
</body>
</html>
