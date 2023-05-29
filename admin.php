<?php
include_once 'db.php';
include('templates/header.php');

if (!isset($_SESSION['login_mail']) || empty($_SESSION['login_mail'])) {
    header('location: login.php');
    exit;
}

// Получение информации о пользователе и его классе
$login_mail = $_SESSION['login_mail'];
$res = $db->query("SELECT * FROM users WHERE username = '{$login_mail}'");
$row = $res->fetch(PDO::FETCH_ASSOC);
$user_type = $row['type'];
$userClassId = $row['class_id'];

$classFilter = isset($_GET['class_filter']) ? $_GET['class_filter'] : $userClassId;

// Формирование запроса в зависимости от выбранного фильтра класса
if ($classFilter === 'all') {
    $sql = "SELECT s.* FROM subjects s";
} else {
    $sql = "SELECT s.* FROM subjects s
            JOIN subject_class sc ON s.id = sc.subject_id
            WHERE sc.class_id = :class_id";
}

// Подготовка и выполнение запроса
$stmt = $db->prepare($sql);
if ($classFilter !== 'all') {
    $stmt->bindParam(':class_id', $classFilter);
}
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ваши предметы</title>
    <link rel="stylesheet" href="css/style.css">
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

        .admin-buttons {
            text-align: center;
            margin-top: 30px;
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
    </style>
</head>
<body>
<div class="container">
    <h1>Ваши предметы</h1>

    <form action="" method="get">
        <label for="class_filter">Фильтр по классу:</label>
        <select name="class_filter" id="class_filter" onchange="this.form.submit()">
            <option value="all">Все классы</option>
            <?php
            // Запрос к базе данных для получения списка классов
            $classQuery = $db->prepare("SELECT * FROM classes");
            $classQuery->execute();
            $classes = $classQuery->fetchAll(PDO::FETCH_ASSOC);

            // Вывод опций для каждого класса с предварительным выбором класса пользователя
            foreach ($classes as $class) {
                $selected = ($class['id'] == $classFilter) ? 'selected' : '';
                echo "<option value='" . $class['id'] . "' $selected>" . $class['class_number'] . "</option>";
            }
            ?>
        </select>
    </form>

    <?php
    // Вывод предметов
    if (count($result) > 0) {
        foreach ($result as $row) {
            echo "<div class='subject'>";
            echo "<a href='#' class='subject-link' data-subject-id='" . $row["id"] . "'>"; // Открываем тег <a> с указанием data-subject-id
            echo "<h2 class='subject-title'>" . $row["name"] . "</h2>";
            echo "</a>"; // Закрываем тег <a>
            echo "</div>";
        }

        // Отображение кнопок только для администраторов
        if ($user_type == 'admin') {
            echo "<div class='admin-buttons'>";
            echo "<a href='constructor.php' class='btn btn-primary'>Добавить тест</a>";
            echo "<a href='add_questions.php' class='btn btn-primary'>Добавить вопрос</a>";
            echo "</div>";
        }
    } else {
        echo "<p class='no-subjects'>Предметы отсутствуют.</p>";
    }
    ?>

    <!-- Модальное окно -->
    <div id="test-type-modal" class="modal">
        <div class="modal-content">
            <h2>Выберите тип теста</h2>
            <button id="control-test-btn">Контрольный тест</button>
            <button id="self-assessment-test-btn">Тест для самоконтроля</button>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Получаем ссылки на предметы
        var subjectLinks = document.querySelectorAll('.subject-link');
        var modal = document.getElementById('test-type-modal');
        var controlTestBtn = document.getElementById('control-test-btn');
        var selfAssessmentTestBtn = document.getElementById('self-assessment-test-btn');

        // Обработчик клика по ссылке на предмет
        subjectLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var subjectId = this.getAttribute('data-subject-id');
                openModal(subjectId);
            });
        });

        // Открытие модального окна и передача идентификатора предмета
        function openModal(subjectId) {
            modal.style.display = 'block';

            // Обработчик клика по кнопке "Контрольный тест"
            controlTestBtn.addEventListener('click', function() {
                // Перенаправление на страницу контрольных тестов с передачей идентификатора предмета и типа теста
                window.location.href = 'tests.php?subject_id=' + subjectId + '&test_type=exam';
            });

            // Обработчик клика по кнопке "Тест для самоконтроля"
            selfAssessmentTestBtn.addEventListener('click', function() {
                // Перенаправление на страницу тестов для самоконтроля с передачей идентификатора предмета и типа теста
                window.location.href = 'tests.php?subject_id=' + subjectId + '&test_type=self_check';
            });
        }

    </script>

    <!-- CSS -->
    <style>
        /* Стили для модального окна */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            text-align: center;
        }

        .modal-content h2 {
            margin-bottom: 20px;
            color: #222;
        }

        .modal-content button {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #ffca28;
            color: #222;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
