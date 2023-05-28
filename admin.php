<?php
include_once 'db.php';
include('templates/header.php');

if (!isset($_SESSION['login_mail']) || empty($_SESSION['login_mail'])) {
    header('location: login.php');
    exit;
}

// получить тип пользователя из базы данных
$login_mail = $_SESSION['login_mail'];
$res = $db->query("SELECT * FROM users WHERE username = '{$login_mail}'");
$row = $res->fetch(PDO::FETCH_ASSOC);
$user_type = $row['type'];
// Получение информации о классе пользователя
$userClassId = $row['class_id'];


$classFilter = isset($_GET['class_filter']) ? $_GET['class_filter'] : $userClassId;

// Проверяем выбранный фильтр класса и формируем соответствующий запрос
if ($classFilter === 'all') {
    $sql = "SELECT s.* FROM subjects s";
} else {
    $sql = "SELECT s.* FROM subjects s
            JOIN subject_class sc ON s.id = sc.subject_id
            WHERE sc.class_id = :class_id";
}

// Подготавливаем запрос и выполняем его
$stmt = $db->prepare($sql);
if ($classFilter !== 'all') {
    $stmt->bindParam(':class_id', $classFilter);
}
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" href="css/style.css">
<body>
<div class="container">
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
            echo "<a href='tests.php?subject_id=" . $row["id"] . "'>";
            echo "<h2>" . $row["name"] . "</h2>";
            echo "</a>";

            $sql2 = "SELECT * FROM tests WHERE subject_id = :subject_id";
            $stmt2 = $db->prepare($sql2);
            $stmt2->bindParam(':subject_id', $row["id"]);
            $stmt2->execute();
            $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

//вывод тестов
//            if (count($result2) > 0) {
//                echo "<ul>";
//                foreach ($result2 as $row2) {
//                    echo "<li><a href='test.php?id=" . $row2["id"] . "'>" . $row2["name"] . "</a></li>";
//                }
//                echo "</ul>";
//            } else {
//                echo "<p>Тесты отсутствуют.</p>";
//            }
        }

    } else {
        echo "<p>Тесты отсутствуют.</p>";
    }

    // отобразить кнопку только для администраторов
    if ($user_type == 'admin') {
        echo "<a href='constructor.php' class='btn btn-primary'>Добавить тест</a>";
        echo "<a href='add_questions.php' class='btn btn-primary' style='margin-left: 10px;'>Добавить вопрос</a>";
    }
    ?>

</div>
</body>
</html>

