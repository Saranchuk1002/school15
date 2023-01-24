<?php
error_reporting(0);
    include_once  'db.php';
    session_start();
    if($_SESSION['login_mail']=='')
    {
        header ('location: login.php');
    }
    else{

    $do = trim(strip_tags($_GET['do']));
    if ($do == 'save') {
        $title = trim($_POST['title']);
        $time_need = $_POST['time_score'];

        $res = $db->prepare("INSERT IGNORE INTO tests (`title`,`time`) VALUES (:title, :time_need)");
        $res->execute([
            ':title' => $title,
            ':time_need' => $time_need,
        ]);
        $testId = $db->lastInsertId();

        $questionNum = 1;
        while (isset($_POST['question_' . $questionNum])) {
            $question = trim($_POST['question_' . $questionNum]);
            if (empty($question)) {
                continue;
            }

            $res = $db->prepare("INSERT IGNORE INTO questions (`test_id`, `question`) VALUES (:test_id, :question)");
            $res->execute([
                ':test_id' => $testId,
                ':question' => $question,
            ]);
            $questionId = $db->lastInsertId();

            $answerNum = 1;
            while (isset($_POST['answer_text_' . $questionNum . '_' . $answerNum])) {
                $answer = trim($_POST['answer_text_' . $questionNum . '_' . $answerNum]);
                $score = trim($_POST['answer_score_' . $questionNum . '_' . $answerNum]);
                if (empty($answer)) {
                    continue;
                }

                $res = $db->prepare("INSERT IGNORE INTO answers (`question_id`, `answer`, `score`) 
                                    VALUES (:question_id, :answer, :score)");
                $res->execute([
                    ':question_id' => $questionId,
                    ':answer' => $answer,
                    ':score' => $score,
                ]);

                $answerNum++;
            }
            $questionNum++;
        }
        header ('Location: admin.php?do=main');
    }
    if ($do != 'constructor') {
        $do = 'main';
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
<div class="col-md-6">
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <?php include_once 'inc/' . $do . '.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>
<?php } ?>
