<?php
session_start();

// Подключение файла с функциями авторизации
require_once __DIR__ . '/../php/auth_helpers.php';

// Проверка авторизации и прав
requireLogin();
requireAdmin();

include __DIR__ . '/../php/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение и валидация данных
    $title = trim($_POST['title'] ?? '');
    $release_year = intval($_POST['release_year'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $genre_id = intval($_POST['genre_id'] ?? 0);

    // Проверка обязательных полей
    if (empty($title) || !$release_year || !$description || !$genre_id) {
        $_SESSION['error_message'] = 'Пожалуйста, заполните все поля.';
        header('Location: ../add_movie.php');
        exit();
    }

    // Обработка файла постера
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['poster']['tmp_name'];
        $fileName = $_FILES['poster']['name'];
        $fileSize = $_FILES['poster']['size'];
        $fileType = $_FILES['poster']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            $_SESSION['error_message'] = 'Недопустимый формат изображения.';
            header('Location: ../add_movie.php');
            exit();
        }

        // Генерируем уникальное имя файла
        $newFileName = uniqid('poster_', true) . '.' . $fileExtension;
        $uploadFileDir = __DIR__ . '/../uploads/posters/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        $dest_path = $uploadFileDir . $newFileName;

        if (!move_uploaded_file($fileTmpPath, $dest_path)) {
            $_SESSION['error_message'] = 'Ошибка при загрузке файла.';
            header('Location: ../add_movie.php');
            exit();
        }
    } else {
        $_SESSION['error_message'] = 'Ошибка при загрузке файла.';
        header('Location: ../add_movie.php');
        exit();
    }

    // Вставка данных в базу
    try {
        $stmt = $pdo->prepare("INSERT INTO films (title, release_year, description, id, poster_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $release_year, $description, $genre_id, 'uploads/posters/' . $newFileName]);
        $_SESSION['success_message'] = 'Фильм успешно добавлен.';
        header('Location: ../add_movie.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Ошибка базы данных: ' . $e->getMessage();
        header('Location: ../add_movie.php');
        exit();
    }
} else {
    // Не POST-запрос
    header('Location: ../add_movie.php');
    exit();
}