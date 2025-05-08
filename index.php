<?php
// index.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/php/config.php';
include __DIR__ . '/php/auth_helpers.php';

$user_nickname = 'Гость'; // Значение по умолчанию для неавторизованных

// Инициализация переменной для фильмов
$movies = [];

if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $is_admin = $_SESSION['is_admin'];
    $user_nickname = $_SESSION['user_nickname'] ?? 'Пользователь';

    // --- Загрузка фильмов из базы данных (таблица films) ---
    try {
        $stmt = $pdo->query("
            SELECT 
                id, 
                title, 
                description, 
                release_year, 
                director, 
                poster_url
            FROM films
            ORDER BY title ASC
        ");
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error loading movies: " . $e->getMessage());
        $movies = [];
        $_SESSION['error_message'] = 'Не удалось загрузить список фильмов.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Movie Tracker</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<header>
    <div class="header-container">
        <div class="site-title">
            <h1>Movie Tracker</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="add_movie.php">Добавить фильм</a></li>
                    <li><a href="admin_panel.php">Панель администратора</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php">Профиль</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="auth-info">
            <?php if (isLoggedIn()): ?>
                <p>Привет, <?php echo htmlspecialchars($user_nickname); ?>!</p>
                <p><a href="logout.php">Выход</a></p>
            <?php else: ?>
                <p><a href="login.php">Вход</a> | <a href="register.php">Регистрация</a></p>
            <?php endif; ?>
        </div>
    </div>
</header>

<main>
    <h2>Список фильмов</h2>
    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>

    <div class="movies-grid">
        <?php if (isLoggedIn()): ?>
            <?php if (!empty($movies)): ?>
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-item">
                        <?php if (!empty($movie['poster_url'])): ?>
                            <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" style="max-width: 100%; height: auto; border-radius: 4px;">
                        <?php else: ?>
                            <img src="images/placeholder_poster.png" alt="Обложка отсутствует" style="max-width: 100%; height: auto; border-radius: 4px;">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($movie['title']) ?></h3>
                        <p>Год: <?= htmlspecialchars($movie['release_year']) ?></p>
                        <p>Режиссер: <?= htmlspecialchars($movie['director']) ?></p>
                        <p><?= nl2br(htmlspecialchars($movie['description'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Список фильмов пуст или не удалось загрузить.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Для просмотра списка фильмов, пожалуйста, <a href="login.php">войдите</a> или <a href="register.php">зарегистрируйтесь</a>.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>© 2025 Movie Tracker</p>
</footer>
</body>
</html>