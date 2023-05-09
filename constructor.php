<?php
include_once('templates/header.php');
include_once 'db.php';

// Получение списка всех предметов из базы данных
$sql = "SELECT * FROM subjects";
$statement = $db->prepare($sql);
$statement->execute();
$subjects = $statement->fetchAll(PDO::FETCH_ASSOC);

// Получение списка всех классов из базы данных
$sql = "SELECT * FROM classes";
$statement = $db->prepare($sql);
$statement->execute();
$classes = $statement->fetchAll(PDO::FETCH_ASSOC);

// Проверка наличия сохраненного выбранного предмета, класса и темы в сессии
if(isset($_SESSION['selected_subject'])) {
$selected_subject = $_SESSION['selected_subject'];
} else {
$selected_subject = null;
}
if(isset($_SESSION['selected_class'])) {
$selected_class = $_SESSION['selected_class'];
} else {
$selected_class = null;
}

// Обработка формы для выбора предмета, класса и темы
if(isset($_POST['subject_id']) && isset($_POST['class_id'])) {
    $subject_id = $_POST['subject_id']; // получаем ID выбранного предмета
    $class_id = $_POST['class_id']; // получаем ID выбранного класса

// сохраняем выбранный предмет, класс и тему в сессии
    $_SESSION['selected_subject'] = $subject_id;
    $_SESSION['selected_class'] = $class_id;
}
?>
<form method="post" action="create_test.php">
    <div class="form-group">
        <label for="subject_id">Выберите предмет:</label>
        <select type="number" name="subject_id" id="subject_id" required>
            <?php foreach($subjects as $subject):
                ?>
                <option value="<?php echo $subject['id']; ?>" <?php echo ($selected_subject == $subject['id']) ? 'selected' : ''; ?>><?php echo $subject['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="class_id">Выберите класс:</label>
        <select  type="number" name="class_id" id="class" required>
            <?php foreach($classes as $class_number): ?>
                <option value="<?php echo $class_number['id']; ?>" <?php echo ($selected_class == $class_number['id']) ? 'selected' : ''; ?>><?php echo $class_number['class_number']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="name">Название теста:</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="time">Время на тест (в минутах):</label>
        <input type="number" name="time" id="time" class="form-control" required>
    </div>

    <div id="questions-container">
        <!-- Контейнер для вопросов -->
    </div>

    <button type="button" class="btn btn-primary mt-3" onclick="addQuestion()">Добавить вопрос</button>

    <input type="submit" class="btn btn-success mt-3" value="Создать тест">
</form>
<script>
         let questionCounter = 1;

        function addAnswer(answersContainer) {
        const answerIndex = answersContainer.children.length ;
        const answerInput = document.createElement('div');

        // Создаем поле для ввода ответа
        const answerTextInput = document.createElement('input');
        answerTextInput.type = 'text';
        answerTextInput.name = `question-${questionCounter}-answer-${answerIndex}-text`;
        answerTextInput.required = true;
        answerTextInput.placeholder = `Введите ответ ${answerIndex}...`;
        answerTextInput.classList.add('form-control');
        answerInput.appendChild(answerTextInput);

        // Создаем поле для ввода количества баллов за ответ
        const answerPointsInput = document.createElement('input');
        answerPointsInput.type = 'number';
        answerPointsInput.name = `question-${questionCounter}-answer-${answerIndex}-points`;
        answerPointsInput.required = true;
        answerPointsInput.placeholder = `Введите количество баллов за ответ ${answerIndex}...`;
        answerPointsInput.classList.add('form-control');
        answerInput.appendChild(answerPointsInput);

        answersContainer.appendChild(answerInput);
    }
         function addQuestion() {
             const container = document.getElementById('questions-container');

             // Создаем новую форму вопроса
             const questionForm = document.createElement('div');
             questionForm.classList.add('question-form');

             // Создаем заголовок для формы вопроса
             const questionHeader = document.createElement('h2');
             questionHeader.textContent = `Вопрос ${questionCounter}`;

             // Создаем поле для ввода вопроса
             const questionInput = document.createElement('input');
             questionInput.type = 'text';
             questionInput.name = `question-${questionCounter}`;
             questionInput.required = true;
             questionInput.placeholder = 'Введите вопрос...';
             questionInput.classList.add('form-control');

             // Добавляем поле для первого ответа
             const firstAnswerInput = document.createElement('input');
             firstAnswerInput.type = 'text';
             firstAnswerInput.name = `question-${questionCounter}-answer-1`;
             firstAnswerInput.required = true;
             firstAnswerInput.placeholder = 'Введите первый ответ...';
             firstAnswerInput.classList.add('form-control');

             // Создаем поле для ввода количества баллов за первый ответ
             const firstAnswerPointsInput = document.createElement('input');
             firstAnswerPointsInput.type = 'number';
             firstAnswerPointsInput.name = `question-${questionCounter}-answer-1-points`;
             firstAnswerPointsInput.required = true;
             firstAnswerPointsInput.placeholder = `Введите количество баллов за ответ 1...`;
             firstAnswerPointsInput.classList.add('form-control');

             // Создаем контейнер для ответов и добавляем в него элементы
             const answersContainer = document.createElement('div');
             answersContainer.classList.add('answers-container');
             answersContainer.appendChild(firstAnswerInput);
             answersContainer.appendChild(firstAnswerPointsInput);
             questionForm.appendChild(questionHeader);
             questionForm.appendChild(questionInput);
             questionForm.appendChild(answersContainer);

             // Добавляем кнопку для добавления нового поля ответа
             const addAnswerButton = document.createElement('button');
             addAnswerButton.type = 'button';
             addAnswerButton.textContent = 'Добавить ответ';
             addAnswerButton.classList.add('btn', 'btn-secondary', 'mt-2');
             addAnswerButton.addEventListener('click', () => {
                 addAnswer(answersContainer);
                 questionForm.appendChild(addAnswerButton); // перемещаем кнопку вниз формы
             });
             questionForm.appendChild(addAnswerButton); // добавляем кнопку в конец формы

             // Добавляем форму вопроса в контейнер
             container.appendChild(questionForm);

             // Увеличиваем значение questionCounter для следующего вопроса
             questionCounter++;
         }


</script>
<style>
    .question-form {
        margin-bottom: 20px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .question-form h2 {
        font-size: 18px;
        margin-top: 0;
    }

    .answers-container {
        margin-top: 10px;
    }

    .answers-container input[type="text"] {
        margin-top: 5px;
    }
</style>
