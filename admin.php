<?php
// admin.php

session_start();

include __DIR__ . '/config.php';
include __DIR__ . '/auth_helpers.php';

requireLogin();
requireAdmin();

// Получение списка фильмов из базы
$stmt = $pdo->query("SELECT id, title, release_year FROM movies ORDER BY release_year DESC");
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<title>Админка - управление фильмами</title>
</head>
<body>
<h1>Управление фильмами</h1>

<?php if (isset($_SESSION['success_message'])): ?>
    <p style="color: green;"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></p>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <p style="color: red;"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></p>
<?php endif; ?>

<a href="add_movie.php">Добавить новый фильм</a>

<table border="1" cellpadding="5" cellspacing="0" style="margin-top:20px;">
<tr>
    <th>ID</th>
    <th>Название</th>
    <th>Год</th>
</tr>
<?php foreach ($movies as $movie): ?>
<tr>
    <td><?php echo htmlspecialchars($movie['id']); ?></td>
    <td><?php echo htmlspecialchars($movie['title']); ?></td>
    <td><?php echo htmlspecialchars($movie['release_year']); ?></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>