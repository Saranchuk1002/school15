<?php
include_once 'db.php';
include('templates/header.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class = $_POST['class'];
    $subject_id = $_POST['subject_id'];
    $topic = $_POST['topic'];
    $question_text = $_POST['question_text'];

    $sql = "INSERT INTO questions (class_id, subject_id, topic, question_text) VALUES ('$class', '$subject_id', '$topic', '$question_text')";
    $result = $db->exec($sql);

    if ($result) {
        echo "Вопрос успешно добавлен";
    } else {
        echo "Ошибка при добавлении вопроса";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Добавить вопрос</title>
</head>
<body>
<h1>Добавить вопрос</h1>
<form action="add_questions.php" method="post">
    <label for="class">Класс:</label>
    <select name="class" id="class">

        <?php
        $sql = "SELECT * FROM classes ORDER BY class_number";
        $stmt = $db->query($sql);
        $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Вывод опций для каждого класса
        foreach ($classes as $class) {
            $class_number = $class['class_number'];
            echo "<option value='$class_number'>$class_number класс</option>";
        }
        ?>

    </select>

    <label for="subject">Предмет:</label>
    <select name="subject_id" id="subject_id">

        <?php
        $sql = "SELECT * FROM subjects ORDER BY name";
        $stmt = $db->query($sql);
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Вывод опций для каждого предмета
        foreach ($subjects as $subject) {
            $id = $subject['id'];
            $name = $subject['name'];
            echo "<option value='$id'>$name</option>";
        }
        ?>

    </select>

    <label for="topic">Тема:</label>
    <input type="text" name="topic" id="topic">
    <label for="question_text">Вопрос:</label>
    <textarea name="question_text" id="question" cols="30" rows="10"></textarea>
    <button type="submit">Добавить</button>
</form>
</body>
</html>
