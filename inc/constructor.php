<div class="col-md-6">
        <form action="admin.php?do=save" method="post">
            <div class="card mt-4">
                <h2 class="text-center">Конструктор тестов</h2>
                <div class="card-body">
                    <div>
                        <label for="title" class="form-label">Введите название теста</label>
                        <input type="text" name="title" id="title" class="form-control">
                    </div>
                </div>
            </div>
            <div class="card mt-4 ">
                <div class="mt-5 text-center">
                    <h5>Ввод вопросов</h5>
                </div>
                <div class="questions">
                    <div class="question-items">
                        <div class="card-body">
                            <label for="question_1" class="form-label">Вопрос #1</label>
                            <input type="text" name="question_1" id="question_1" class="form-control">
                            <div class="answers">
                                <div  class="answer-items">
                                    <div>
                                        <label for="answer_text_1_1" class="form-label">Ответ #1</label>
                                        <input type="text" name="answer_text_1_1" id="answer_text_1_1" class="form-control">
                                    </div>
                                    <div class="mt-2">
                                        <label for="answer_score_1_1" class="form-label">Балл за ответ #1</label>
                                        <input type="text" name="answer_score_1_1" id="answer_score_1_1" class="form-control">
                                    </div>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="button" class="btn btn-light border addAnswer" data-question="1" data-answer="1">Добавить вариант ответа</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary addQuestion">Добавить вопрос</button>
                    </div>
                </div>
            </div>
            <div class="card mt-4 mb-4 ">
                <div class="mt-5 text-center">
                    <h5> <label for="time_score" class="form-label">Время на прохождение</label></h5>
                </div >
                <div class="card-body">
                    <input type="text" name="time_score" id="time_score" class="form-control" required>
                </div>
            </div>
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-success">Сохранить</button>
        </div>
    </form>
</div>
