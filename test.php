<?php
include_once 'db.php';
include('templates/header.php');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
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
$testType = $row['type'];
$correctPassword = $row['password'];

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
        $imagePath= $row['image_path'];


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
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Система тестирования</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Стили модального окна */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            position: relative;
            box-sizing: border-box;
        }

        /* Стили для разных типов сообщений */
        .modal-content.correct {
            color: green;
        }

        .modal-content.incorrect {
            color: red;
        }

        /* Стили для кнопки "Закрыть" */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #888;
            cursor: pointer;
        }
        .question-image {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }

        .text-center {
            text-align: center;
        }

        .mt-5 {
            margin-top: 5px;
        }

        .mt-3 {
            margin-top: 3px;
        }

        .result-print {
            font-size: 20px;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f8f8f8;
            border-radius: 5px;
            color: #222222;
        }
        body {
            background-color: #222;
            color: #fff;
        }
        .container {
            margin: 20px auto;
            max-width: 1600px;
            padding: 20px;
            background-color: #333;
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            text-align: left;
            margin-bottom: 20px;
            color: #222222;
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


        .test-list li {
            margin-bottom: 5px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-left: 30px;
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
        .timer-container {
            background-color: #333;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: auto;
        }

        .timer-input {
            border: none;
            text-align: center;
            background-color: transparent;
            color: #fefefe;
            font-size: 25px;
            margin-left: 0px; /* Уменьшите значение для более близкого расстояния */
        }

        .timer-label {
            color: #fefefe;
            font-size: 25px;
            margin-right: 5px; /* Уменьшите значение для более близкого расстояния */
        }

        .timer-hidden {
            background-color: #222;
            text-align: center;
            margin-top: 5px;
            visibility: hidden;
        }
        .card-body {
            display: flex;
            flex-direction: column;
        }

        .card-body > div {
            margin-bottom: 10px;
        }

        .radio-group {
            display: flex;
            align-items: center;
        }

        .radio-group input[type="radio"] {
            margin-right: 10px;
        }

        .radio-group label {
            margin-bottom: 0;
        }

        input[type="text"] {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<!-- Модальное окно -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div id="message"></div>
    </div>
</div>
<div class="container">
    <?php if ($showForm) { ?>
        <div class="question-links">
            <ul class="question-nav">
                <?php if ($questionNum != 1) { ?>
                <h4>Номера вопросов:</h4>
                <?php } ?>
                <?php for ($i = 1; $i <= $questionCount; $i++) {
                    if ($questionNum != 1) { ?>
                        <li><a href="javascript:void(0);" onclick="changeQuestion(<?php echo $i; ?>);"><?php echo $i; ?></a></li>
                    <?php }
                } ?>
            </ul>
        </div>
        <form id="question-form" action="test.php?id=<?php echo $testId; ?>" method="post">
            <input id="question-input" type="hidden" name="q" value="<?php echo $questionNum; ?>">
            <?php $questionNum--; ?>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="text-center mt-5" style="color: #fefefe">
                        <p>Вопрос <?php echo $questionNum . ' из ' . $questionCount; ?></p>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header text-center">
                            <h3><?php echo $question_text; ?></h3>
                            <?php if (!empty($imagePath)) { ?>
                                <img src="<?php echo $imagePath; ?>" alt="Изображение вопроса" class="question-image">
                            <?php } ?>
                        </div>
                        <div class="card-body">
                            <?php if ($questionNum !== 0) {
                                $max_value = 0;
                                foreach ($answer_text as $answer) {
                                    $curr_answer_id = $answer['id'];
                                    $curr_answer = $answer['answer_text'];
                                    if ($max_value < $answer['score']) {
                                        $max_value = $answer['score'];
                                    } ?>
                                    <div>
                                        <div class="radio-group" id="radio-group-<?php echo $questionId; ?>">
                                            <input type="radio" name="answer_id" required value="<?php echo $curr_answer_id; ?>">
                                            <?php echo $curr_answer; ?>
                                        </div>
                                    </div>
                                <?php }
                                $_SESSION['max_score'] += $max_value;
                            } else { ?>
                                <input type="text" name="name" placeholder="Введите ФИО" value="<?php echo $fullName; ?>" readonly required>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <?php if ($questionCount == $questionNum) { ?>
                            <button class="btn btn-primary" onclick="history.go(-1);">Назад</button>
                            <button type="submit" class="btn btn-success">Получить результат</button>
                            <?php } else if ($questionNum==1 || $questionNum==0 ){ ?>
                                <button class="btn btn-primary" onclick="history.go(-1);" disabled>Назад</button>
                                <button type="submit" class="btn btn-primary">Дальше</button>
                            <?php } else if($questionNum!=0){ ?>
                                <button class="btn btn-primary" onclick="history.go(-1);">Назад</button>
                                <button type="submit" class="btn btn-primary">Дальше</button>
                            <?php } ?>
                            <?php if ($questionNum != 0 || ($questionCount == $questionNum && $questionNum != 0)) { ?>
                                <div class="timer-container">
                                    <h3>
                                        <span class="timer-label">Время до окончания теста:</span>
                                        <input type="text" readonly id="timer" name="timer" class="timer-input" size="1" value="<?php echo floor($_POST['timer_hid'] / 60); ?>:<?php echo $_POST['timer_hid'] % 60; ?>">
                                    </h3>
                                    <h3>
                                        <input type="hidden" readonly id="timer_hid" name="timer_hid" class="timer-hidden" size="1" value="<?php echo $_POST['timer_hid']; ?>">
                                    </h3>
                                    <script>
                                        timeMinut = <?php echo $_POST['timer_hid']; ?>;
                                        timer = setInterval(function () {
                                            let timer_show = document.getElementById('timer');
                                            let timer_hid = document.getElementById('timer_hid');
                                            seconds = timeMinut % 60; // Получаем секунды
                                            minutes = timeMinut / 60 % 60; // Получаем минуты
                                            // Условие если время закончилось то...
                                            if (timeMinut <= 0) {
                                                // Таймер удаляется
                                                timer_show.value = "Время вышло!";
                                                timer_hid.value = 0;
                                                clearInterval(timer);

                                                // Выводит сообщение что время закончилось
                                            } else { // Иначе
                                                // Создаём строку с выводом времени
                                                let strTimer = `${Math.trunc(minutes)}:${seconds}`;
                                                // Выводим строку в блок для показа таймера
                                                timer_show.value = " " + strTimer;
                                                timer_hid.value = timeMinut;
                                            }
                                            --timeMinut; // Уменьшаем таймер
                                        }, 1000)
                                    </script>
                                </div>
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
                        <h3 class="text-center" style="color: #222222">Тест пройден!</h3>
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
                            Ваш результат <?php echo $score / $max_value * 100; ?>% (<?php echo $score; ?> из <?php echo $max_value; ?>)
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
        <script>
            // Отображение модального окна
            function showModal(message, type) {
                var modal = document.getElementById("myModal");
                var modalContent = modal.getElementsByClassName("modal-content")[0];
                var messageElement = document.getElementById("message");

                messageElement.innerHTML = message;
                modalContent.classList.add(type);
                modal.style.display = "block";
            }

            // Закрытие модального окна
            function closeModal() {
                var modal = document.getElementById("myModal");
                var modalContent = modal.getElementsByClassName("modal-content")[0];
                var messageElement = document.getElementById("message");

                messageElement.innerHTML = "";
                modalContent.classList.remove("correct", "incorrect");
                modal.style.display = "none";
            }

            // Проверка и отображение сообщения в модальном окне
            var score = <?php echo $score; ?>; // Значение переменной $score
            var testType = "<?php echo $testType; ?>"; // Значение переменной $testType

            if (testType === "self_check") {
                if (score > 0) {
                    var message = "Ответ верный!";
                    var type = "correct";
                } else {
                    var message = "Ответ неверный!";
                    var type = "incorrect";
                }

                showModal(message, type);
            }

            // Закрытие модального окна при щелчке вне окна
            window.onclick = function(event) {
                var modal = document.getElementById("myModal");
                if (event.target == modal) {
                    closeModal();
                }
            }
        </script>

</body>
</html>
