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
            echo "<a href='tests.php?subject_id=" . $row["id"] . "'>";
            echo "<h2 class='subject-title'>" . $row["name"] . "</h2>";
            echo "</a>";

            $sql2 = "SELECT * FROM tests WHERE subject_id = :subject_id";
            $stmt2 = $db->prepare($sql2);
            $stmt2->bindParam(':subject_id', $row["id"]);
            $stmt2->execute();
            $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
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

</div>
</body>
</html>
