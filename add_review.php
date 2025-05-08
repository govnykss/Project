<?php
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/auth_helpers.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Некорректный запрос');
}

$film_id = filter_input(INPUT_POST, 'film_id', FILTER_VALIDATE_INT);
$rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 5]
]);
$comment = filter_input(INPUT_POST, 'comment', FILTER_UNSAFE_RAW);

if (!$film_id || !$rating) {
    $_SESSION['error_message'] = 'Заполните обязательные поля';
    header("Location: film.php?id=$film_id");
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO reviews (film_id, user_id, rating, comment) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$film_id, $_SESSION['user_id'], $rating, $comment]);
    
    $_SESSION['success_message'] = 'Ваш отзыв добавлен';
} catch (PDOException $e) {
    error_log("Review add error: " . $e->getMessage());
    $_SESSION['error_message'] = 'Ошибка добавления отзыва';
}

header("Location: film.php?id=$film_id");