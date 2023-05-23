<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система тестирования</title>
    <link rel="stylesheet" href="css/results.css">
</head>
<body>
<?php
include_once 'db.php';
include('templates/header.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION['login_mail'] == '') {
    header('location: login.php');
} else {

$resClass = $db->query("SELECT DISTINCT class_id FROM test_result");
$classIds = $resClass->fetchAll(PDO::FETCH_COLUMN);

$selectedClass = isset($_POST['class_id']) ? $_POST['class_id'] : null;
if ($selectedClass === 'all') {
    $selectedClass = 'all';
}


$resSubjects = $db->query("SELECT DISTINCT subject_id FROM test_result");
$subjectIds = $resSubjects->fetchAll(PDO::FETCH_COLUMN);
$selectedSubject = isset($_POST['subject_id']) ? $_POST['subject_id'] : null;
if ($selectedSubject === 'all') {
    $selectedSubject = 'all';
}


$sql = "SELECT * FROM subjects";
$statement = $db->prepare($sql);
$statement->execute();
$subjects = $statement->fetchAll(PDO::FETCH_ASSOC);


if (!empty($_POST['subject_id'])) {
    if ($_POST['subject_id'] == '') {
        $selectedSubject = null; // Значение "Выберите все предметы"
    } else {
        $selectedSubject = $_POST['subject_id'];
    }
} else {
    $selectedSubject = null;
}
$res = $db->query("SELECT * FROM test_result");
$results = $res->fetchAll();

$selectedStudent = isset($_POST['user_id']) ? $_POST['user_id'] : null;
if ($selectedStudent === 'all') {
    $selectedStudent = 'all';
}


$filteredResults = [];

if ($selectedSubject === 'all' && $selectedClass === 'all' && $selectedStudent === 'all') {
    // Вывести все результаты без фильтрации
    $sql = "SELECT * FROM test_result";
    $statement = $db->prepare($sql);
    $statement->execute();
    $filteredResults = $statement->fetchAll();
} elseif ($selectedSubject !== 'all' && $selectedClass !== 'all' && $selectedStudent !== 'all') {
    // Фильтровать результаты по выбранному предмету, классу и ученику
    $sql = "SELECT * FROM test_result WHERE subject_id = ? AND class_id = ? AND user_id = ?";
    $statement = $db->prepare($sql);
    $statement->execute([$selectedSubject, $selectedClass, $selectedStudent]);
    $filteredResults = $statement->fetchAll();
} else {
    // Фильтровать результаты по выбранным предмету, классу или ученику
    $conditions = [];
    $params = [];

    if ($selectedSubject !== 'all') {
        $conditions[] = "subject_id = ?";
        $params[] = $selectedSubject;
    }

    if ($selectedClass !== 'all') {
        $conditions[] = "class_id = ?";
        $params[] = $selectedClass;
    }

    if ($selectedStudent !== 'all') {
        $conditions[] = "user_id = ?";
        $params[] = $selectedStudent;
    }

    $conditionStr = implode(" AND ", $conditions);
    $sql = "SELECT * FROM test_result WHERE $conditionStr";
    $statement = $db->prepare($sql);
    $statement->execute($params);
    $filteredResults = $statement->fetchAll();
}




// Проверка наличия сохраненного выбранного предмета, класса и темы в сессии
if(isset($_SESSION['selected_subject'])) {
    $selected_subject = $_SESSION['selected_subject'];
} else {
    $selected_subject = null;
}
$login_mail = $_SESSION['login_mail'];
$res = $db->query("SELECT * FROM users WHERE username = '{$login_mail}'");
$userData = $res->fetch(PDO::FETCH_ASSOC);
$fullName = $userData['full_name'];
$userId = $userData['id'];
// Запрос к базе данных для получения списка классов
$resClasses = $db->query("SELECT * FROM classes");
$classes = $resClasses->fetchAll(PDO::FETCH_ASSOC);


?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="mt-4">
                <div class="text-center">
                    <form action="results.php" method="POST">
                        <div class="form-group">
                            <label for="subject_id">Выберите предмет:</label>
                            <select name="subject_id" id="subject_id" required>
                                <option value="all" <?php echo ($selectedSubject == 'all') ? 'selected' : ''; ?>>Все предметы</option>
                                <?php foreach($subjects as $subject): ?>
                                    <option value="<?php echo $subject['id']; ?>" <?php echo ($selectedSubject == $subject['id']) ? 'selected' : ''; ?>>
                                        <?php echo $subject['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-group">
                                <label for="class_id">Выберите класс:</label>
                                <select name="class_id" id="class_id" required>
                                    <option value="all" <?php echo ($selectedClass == 'all') ? 'selected' : ''; ?>>Все классы</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo $class['id']; ?>" <?php echo ($selectedClass == $class['id']) ? 'selected' : ''; ?>>
                                            <?php echo $class['class_number']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="user_id">Выберите ученика:</label>
                                <select name="user_id" id="user_id" required class="user-select">
                                    <?php
                                    if ($selectedClass !== null && $selectedClass !== 'all') {
                                        // Запрос к базе данных для получения списка учеников выбранного класса
                                        $usersQuery = $db->prepare("SELECT * FROM users WHERE class_id = ?");
                                        $usersQuery->execute([$selectedClass]);
                                        $users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);
                                    } else {
                                        // Получить всех учеников
                                        $usersQuery = $db->prepare("SELECT * FROM users");
                                        $usersQuery->execute();
                                        $users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);
                                    }
                                    foreach ($users as $user) {
                                        $selected = ($user['id'] == $selectedStudent) ? 'selected' : ''; // Проверяем, соответствует ли текущий ученик переменной $selectedStudent
                                        echo "<option value=\"" . $user['id'] . "\" $selected>" . $user['full_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>


                            <button type="submit" class="btn btn-primary">Выбрать</button>
                    </form>
                </div>
                <table class="table table-bordered" style="background-color: grey">
                    <tr>
                        <th>ФИО</th>
                        <th>Класс</th>
                        <th>Название теста</th>
                        <th>Баллы</th>
                        <th>Всего баллов в тесте</th>
                        <th>Процент</th>
                        <th>Оценка</th>
                        <th>Предмет</th>
                    </tr>
                    <?php
                    if (empty($results)) {
                        echo "</table>";
                        echo "<h3>Этот тест еще не проходился!</h3>";
                    } else {
                        foreach ($filteredResults as $result) {

                            $classId = $result['class_id'];

                            // Получите название класса по его id
                            $resClass = $db->query("SELECT class_number FROM classes WHERE id = '{$classId}'");
                            $classData = $resClass->fetch(PDO::FETCH_ASSOC);
                            $className = $classData['class_number'];

                            $testId = $result['test_id'];

                            $resTest = $db->query("SELECT * FROM tests WHERE id = {$testId}");
                            $row = $resTest->fetch();
                            $testTitle = $row['name'];

                            ?>
                            <tr class='other-rows text-center'>
                                <th class='other-th'><?php echo $result['name']; ?></th>
                                <th class='other-th'><?php echo $className; ?></th>
                                <th class='other-th'><?php echo $testTitle; ?></th>
                                <th class='other-th'><?php echo $result['score']; ?></th>
                                <th class='other-th'><?php echo $result['max_score']; ?></th>
                                <th class='other-th'><?php echo $result['score'] / $result['max_score'] * 100; ?></th>
                                <th class='other-th'>
                                    <?php
                                    if ($result['score'] / $result['max_score'] * 100 <= 60) {
                                        echo "2";
                                    } elseif ($result['score'] / $result['max_score'] * 100 > 60 && $result['score'] / $result['max_score'] * 100 <= 80) {
                                        echo "3";
                                    } else {
                                        echo "5";
                                    }
                                    ?>
                                </th>
                                <th class='other-th'>
                                    <?php
                                    $subjectId = $result['subject_id'];
                                    $subjectName = '';

                                    // Найти буквенное обозначение предмета по его идентификатору
                                    foreach ($subjects as $subject) {
                                        if ($subject['id'] == $subjectId) {
                                            $subjectName = $subject['name'];
                                            break;
                                        }
                                    }

                                    echo $subjectName;
                                    ?>
                                </th>

                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
            </div>
            <p>
            <form action="delete.php" method="post">
                <button class="btn btn-danger">Удалить тест</button>
            </form>
            </p>
        </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        // Функция для обновления списка учеников на основе выбранного класса
        function updateStudents() {
            var classId = document.getElementById('class_id').value; // Получаем выбранный class_id

            // Отправляем AJAX-запрос на сервер
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Получаем ответ сервера
                        var response = JSON.parse(xhr.responseText);

                        // Обновляем список учеников
                        var select = document.getElementById('user_id');
                        select.innerHTML = ''; // Очищаем текущие опции


                            var allOption = document.createElement('option');
                            allOption.value = 'all';
                            allOption.text = 'Все ученики';
                            select.appendChild(allOption);


                        // Добавляем опции на основе полученного списка учеников
                        response.forEach(function(user) {
                            var option = document.createElement('option');
                            option.value = user.id;
                            option.text = user.full_name;
                            select.appendChild(option);
                        });
                    } else {
                        // Обработка ошибки AJAX-запроса
                        console.error('Ошибка при получении списка учеников');
                    }
                }
            };

            // Отправляем запрос на получение списка учеников выбранного класса
            xhr.open('GET', 'get_students.php?class_id=' + classId, true);
            xhr.send();
        }

        // Обработчик события изменения выбранного класса
        document.getElementById('class_id').addEventListener('change', function() {
            updateStudents(); // Вызываем функцию обновления списка учеников
        });

        // Вызываем функцию обновления списка учеников при загрузке страницы (для инициализации)
        updateStudents();
    </script>



</body>
</html>
<?php } ?>

