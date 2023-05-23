<?php
include_once 'db.php';
include('templates/header.php');

if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];
    $sql = "SELECT * FROM tests WHERE subject_id = $subject_id";
    $result = $db->query($sql);
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            // Выводим список тестов для данного предмета
        }
    } else {
        echo "Тесты отсутствуют.";
    }
} else {
    echo "Предмет не выбран.";
}
// Получаем список тестов из базы данных
$query = $db->query("SELECT * FROM tests");
$tests = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<h1>Список тестов</h1>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Time</th>
        <th>Subject ID</th>
        <th>Class ID</th>
        <th>Question IDs</th>
        <th>Correct Answers</th>
        <th>Points</th>
    </tr>
    </thead>
    <tbody>
    <?php
    // Подключаемся к базе данных

    // Запрашиваем тесты из базы данных
    $sql = "SELECT * FROM tests WHERE subject_id = :subject_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':subject_id', $subject_id);
    $stmt->execute();
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Обрабатываем результат запроса
    foreach ($tests as $row) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['time'] . '</td>';
        echo '<td>' . $row['subject_id'] . '</td>';
        echo '<td>' . $row['class_id'] . '</td>';
        echo '</tr>';
    }

    ?>
        </tbody>
    </table>

    </div>
    </body>
    </html>
