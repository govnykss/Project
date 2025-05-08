<?php
session_start();

// Подключение файла с функциями авторизации
require_once __DIR__ . '/php/auth_helpers.php';

// Проверка авторизации и прав администратора
requireLogin();
requireAdmin();

// Получение ошибок и сообщений из сессии
$error_message = $_SESSION['error_message'] ?? null;
$success_message = $_SESSION['success_message'] ?? null;

unset($_SESSION['error_message'], $_SESSION['success_message']);

// Получение списка жанров из базы
// Предполагается, что у вас есть подключение $pdo в config.php
include __DIR__ . '/php/config.php';

$stmt = $pdo->query("SELECT id, name FROM genres ORDER BY name");
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<title>Добавить фильм</title>
</head>
<body>
<h1>Добавить новый фильм</h1>

<?php if ($error_message): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error_message); ?></p>
<?php endif; ?>
<?php if ($success_message): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success_message); ?></p>
<?php endif; ?>

<form action="php/process_add_movie.php" method="post" enctype="multipart/form-data">
    <label>Название:<br>
        <input type="text" name="title" required>
    </label><br><br>

    <label>Год выпуска:<br>
        <input type="number" name="release_year" min="1800" max="<?php echo date('Y') + 5; ?>" required>
    </label><br><br>

    <label>Описание:<br>
        <textarea name="description" rows="5" cols="50" required></textarea>
    </label><br><br>

    <label>Жанр:<br>
        <select name="genre_id" required>
            <?php foreach ($genres as $genre): ?>
                <option value="<?php echo htmlspecialchars($genre['id']); ?>">
                    <?php echo htmlspecialchars($genre['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label><br><br>

    <label>Постер:<br>
        <input type="file" name="poster" accept="image/*" required>
    </label><br><br>

    <input type="submit" value="Добавить фильм">
</form>
</body>
</html>