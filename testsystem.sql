-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 13 2023 г., 08:13
-- Версия сервера: 5.7.40
-- Версия PHP: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `testsystem`
--

-- --------------------------------------------------------

--
-- Структура таблицы `admin_logins`
--

DROP TABLE IF EXISTS `admin_logins`;
CREATE TABLE IF NOT EXISTS `admin_logins` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `admin_logins`
--

INSERT INTO `admin_logins` (`id`, `email`, `password`) VALUES
    (1, 'saranchuk_1002@mail.ru', '1234');

-- --------------------------------------------------------

--
-- Структура таблицы `answers`
--

DROP TABLE IF EXISTS `answers`;
CREATE TABLE IF NOT EXISTS `answers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `question_id` int(11) NOT NULL,
    `answer_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `score` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `fk_answers_questions` (`question_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_text`, `score`) VALUES
                                                                        (42, 46, 'Наличие крупных торговых путей', 1),
                                                                        (43, 47, 'Наличие крупных торговых путей', 1),
                                                                        (44, 47, 'верно', 1),
                                                                        (45, 48, 'Наличие крупных торговых путей', 1),
                                                                        (46, 48, 'верно', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
    `class_number` int(11) DEFAULT NULL,
    `subject_id` int(11) DEFAULT NULL,
    `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`),
    KEY `subject_id` (`subject_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `classes`
--

INSERT INTO `classes` (`class_number`, `subject_id`, `id`) VALUES
                                                               (1, 1, 12),
                                                               (2, 2, 13);

-- --------------------------------------------------------

--
-- Структура таблицы `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `question_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `test_id` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `test_id` (`test_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `questions`
--

INSERT INTO `questions` (`id`, `question_text`, `test_id`) VALUES
                                                               (46, 'Наличие крупных торговых путей', 55),
                                                               (47, 'Наличие крупных торговых путей', 56),
                                                               (48, 'верно', 56);

-- --------------------------------------------------------

--
-- Структура таблицы `results`
--

DROP TABLE IF EXISTS `results`;
CREATE TABLE IF NOT EXISTS `results` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `test_id` int(11) NOT NULL,
    `score_min` int(11) NOT NULL,
    `score_max` int(11) NOT NULL,
    `result` text COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
                                          (1, 'Математика'),
                                          (2, 'Русский язык'),
                                          (3, 'История'),
                                          (4, 'Физика'),
                                          (5, 'Химия'),
                                          (6, 'Биология'),
                                          (7, 'География');

-- --------------------------------------------------------

--
-- Структура таблицы `tests`
--

DROP TABLE IF EXISTS `tests`;
CREATE TABLE IF NOT EXISTS `tests` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_swedish_ci NOT NULL,
    `time` int(11) NOT NULL,
    `subject_id` int(11) NOT NULL,
    `class_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `subject_id` (`subject_id`),
    KEY `class_id` (`class_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Дамп данных таблицы `tests`
--

INSERT INTO `tests` (`id`, `name`, `time`, `subject_id`, `class_id`) VALUES
                                                                         (55, 'История Древнего Египта', 3, 6, 12),
                                                                         (56, 'История Древнего Египта', 3, 3, 12);

-- --------------------------------------------------------

--
-- Структура таблицы `test_result`
--

DROP TABLE IF EXISTS `test_result`;
CREATE TABLE IF NOT EXISTS `test_result` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `test_id` int(11) NOT NULL,
    `score` int(11) NOT NULL,
    `max_score` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `test_result`
--

INSERT INTO `test_result` (`id`, `email`, `name`, `test_id`, `score`, `max_score`) VALUES
                                                                                       (49, 'saranchuk_1002@mail.ru', 'Саранчук М.В', 48, 0, 0),
                                                                                       (50, 'saranchuk_1002@mail.ru', 'Саранчук М.В', 50, 2, 2),
                                                                                       (51, 'saranchuk_1002@mail.ru', 'Саранчук М.В', 54, 0, 1),
                                                                                       (52, 'saranchuk_1002@mail.ru', 'Саранчук М.В', 55, 1, 1),
                                                                                       (53, 'saranchuk_1002@mail.ru', 'Саранчук М.В', 56, 2, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `type` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `type`) VALUES
                                                               (1, 'saranchuk_1002@mail.ru', '1234', 'admin'),
                                                               (2, '1002@mail.ru', '4321', 'user');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `answers`
--
ALTER TABLE `answers`
    ADD CONSTRAINT `fk_answers_questions` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `questions`
--
ALTER TABLE `questions`
    ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
