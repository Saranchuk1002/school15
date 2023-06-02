<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Подключаемся к базе данных
include_once 'db.php';

// Получаем данные из формы
$name = isset($_POST['name']) ? $_POST['name'] : '';
$time = isset($_POST['time']) ? $_POST['time'] : '';
$subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : '';
$class_id = isset($_POST['class_id']) ? $_POST['class_id'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';


if (!empty($name) && !empty($time) && !empty($subject_id) && !empty($class_id)) {
    $query = $db->prepare("INSERT INTO tests (name, time, subject_id, class_id, type, password) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    $query->execute([$name, $time, $subject_id, $class_id, $type, $password]);

    // Получаем ID добавленного теста
    $test_id = $db->lastInsertId();

    $questionCounter = 1;
    while (isset($_POST['question-' . $questionCounter])) {
        $question_text = trim($_POST['question-' . $questionCounter]);
        if (empty($question_text)) {
            continue;
        }

        $imagePath = '';
        if (isset($_FILES['question-' . $questionCounter . '-image']) && $_FILES['question-' . $questionCounter . '-image']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['question-' . $questionCounter . '-image']['tmp_name'];
            $imageFileName = $_FILES['question-' . $questionCounter . '-image']['name'];
            $imagePath = 'img/' . $imageFileName;

            // Переместите загруженный файл в желаемую директорию
            move_uploaded_file($imageTmpPath, $imagePath);
        }

        $res = $db->prepare("INSERT IGNORE INTO questions (`test_id`, `question_text`, `image_path`) VALUES (:test_id, :question_text, :image_path)");
        $res->execute([
            ':test_id' => $test_id,
            ':question_text' => $question_text,
            ':image_path' => $imagePath, // Путь к сохраненному изображению
        ]);

        $question_id = $db->lastInsertId();

        $answerIndex = 1;
        // Добавляем каждый ответ к этому вопросу в базу данных
        while (isset($_POST['question-' . $questionCounter. '-answer-' . $answerIndex])) {
            $answer_text = isset($_POST['question-' . $questionCounter . '-answer-' . $answerIndex]) ? $_POST['question-' . $questionCounter . '-answer-' . $answerIndex] : '';
            $score = isset($_POST['question-' . $questionCounter . '-answer-' . $answerIndex . '-score']) ? $_POST['question-' . $questionCounter . '-answer-' . $answerIndex . '-score'] : '';;
            if (empty($answer_text)) {
                continue;
            }

            $res = $db->prepare("INSERT IGNORE INTO answers (`question_id`, `answer_text`, `score`) 
                                VALUES (:question_id, :answer_text, :score)");
            $res->execute([
                ':question_id' => $question_id,
                ':answer_text' => $answer_text,
                ':score' => $score,
            ]);

            $answerIndex++;
        }
        $questionCounter++;
    }

    // Перенаправляем пользователя на страницу со списком тестов
    header("Location: admin.php");
    exit(); // рекомендуется использовать exit() после перенаправления, чтобы код после него не выполнялся.
}
