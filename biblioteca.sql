-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Ноя 29 2025 г., 21:37
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `biblioteca`
--

-- --------------------------------------------------------

--
-- Структура таблицы `carti`
--

CREATE TABLE `carti` (
  `CodCarte` int(11) NOT NULL,
  `Titlu` varchar(255) NOT NULL,
  `Autor` varchar(255) NOT NULL,
  `Editura` varchar(255) NOT NULL,
  `Pret` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `carti`
--

INSERT INTO `carti` (`CodCarte`, `Titlu`, `Autor`, `Editura`, `Pret`) VALUES
(1, 'Calatoriile lui Mircea', 'Vladimir Ionescu', 'newedit', 200.00),
(20, 'Calatoriile lui Mircea 2', 'Vladimir Ionescu', 'newedit', 250.00),
(22, 'The world after the fall', 'Jinh Hoo', 'koreanLine', 350.00);

-- --------------------------------------------------------

--
-- Структура таблицы `cititori`
--

CREATE TABLE `cititori` (
  `CodCititor` int(11) NOT NULL,
  `Nume` varchar(255) NOT NULL,
  `Prenume` varchar(255) NOT NULL,
  `Adresa` text DEFAULT NULL,
  `Telefon` varchar(15) DEFAULT NULL,
  `CodCarte` int(11) DEFAULT NULL,
  `DataImprumut` date DEFAULT NULL,
  `DataReturnare` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `cititori`
--

INSERT INTO `cititori` (`CodCititor`, `Nume`, `Prenume`, `Adresa`, `Telefon`, `CodCarte`, `DataImprumut`, `DataReturnare`) VALUES
(3, 'Cioban', 'Adrian', 'Str. Cuza Voda 4/1', '07837384', 1, '2025-10-12', NULL),
(11, 'Cioban', 'Adrian', 'Str. Cuza Voda 4/1', '07837384', 20, '2025-10-19', '2025-10-27');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `carti`
--
ALTER TABLE `carti`
  ADD PRIMARY KEY (`CodCarte`);

--
-- Индексы таблицы `cititori`
--
ALTER TABLE `cititori`
  ADD PRIMARY KEY (`CodCititor`),
  ADD KEY `CodCarte` (`CodCarte`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `carti`
--
ALTER TABLE `carti`
  MODIFY `CodCarte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT для таблицы `cititori`
--
ALTER TABLE `cititori`
  MODIFY `CodCititor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cititori`
--
ALTER TABLE `cititori`
  ADD CONSTRAINT `cititori_ibfk_1` FOREIGN KEY (`CodCarte`) REFERENCES `carti` (`CodCarte`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
