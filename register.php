<?php
// register.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/php/config.php';
include __DIR__ . '/php/auth_helpers.php';

if (isLoggedIn()) {
    // Перенаправляем на главную страницу, если пользователь уже авторизован
    header('Location: index.php');
    exit;
}

$email = '';
$nickname = ''; // Новая переменная для сохранения введенного никнейма

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    // Используем FILTER_UNSAFE_RAW для никнейма, т.к. FILTER_SANITIZE_STRING устарел
    // Важно: при отображении никнейма всегда используйте htmlspecialchars()
    $nickname = filter_input(INPUT_POST, 'nickname', FILTER_UNSAFE_RAW);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Валидация данных (добавлена проверка никнейма)
    if (empty($email) || empty($nickname) || empty($password) || empty($confirm_password)) {
        $_SESSION['error_message'] = 'Пожалуйста, заполните все поля.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Некорректный формат email.';
    } elseif ($password !== $confirm_password) {
        $_SESSION['error_message'] = 'Пароли не совпадают.';
    } elseif (strlen($password) < 6) {
         $_SESSION['error_message'] = 'Пароль должен быть не менее 6 символов.';
    } elseif (strlen($nickname) < 3 || strlen($nickname) > 50) { // Пример проверки длины никнейма
         $_SESSION['error_message'] = 'Никнейм должен быть от 3 до 50 символов.';
    }
    else {
        // Проверка, существует ли пользователь с таким email или никнеймом
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR nickname = :nickname");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':nickname', $nickname);
            $stmt->execute();

            if ($stmt->fetch()) {
                $_SESSION['error_message'] = 'Пользователь с таким email или никнеймом уже существует.';
            } else {
                // Пользователь не существует, можно регистрировать

                // *** ВЕРНИТЕСЬ К password_hash() ДЛЯ ПРОДАКШЕНА ***
                // В продакшене используйте: $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $hashed_password = $password; // Временное решение для тестирования

                // Вставка нового пользователя в базу данных (включая никнейм)
                $stmt = $pdo->prepare("INSERT INTO users (email, nickname, password, is_admin) VALUES (:email, :nickname, :password, 0)");
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':nickname', $nickname);
                $stmt->bindParam(':password', $hashed_password);

                if ($stmt->execute()) {
                    $_SESSION['success_message'] = 'Регистрация прошла успешно! Теперь вы можете войти.';
                    header('Location: login.php');
                    exit;
                } else {
                    // Логируем ошибку выполнения запроса, если она не PDOException
                     error_log("Error executing user insert statement.");
                    $_SESSION['error_message'] = 'Ошибка при регистрации пользователя. Попробуйте позже.';
                }
            }

        } catch (PDOException $e) {
            error_log("Database error during registration: " . $e->getMessage());
            $_SESSION['error_message'] = 'Произошла ошибка при регистрации. Попробуйте позже.';
        }
    }

    // Перенаправление после POST для предотвращения повторной отправки формы
    // и отображения сообщений об ошибках
    header('Location: register.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-form-container">
        <h2>Регистрация</h2>

        <?php
        // Отображение сообщений об успехе или ошибке
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert error">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <form id="registerForm" method="POST" action="register.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">
            </div>
             <div class="form-group">
                <label for="nickname">Никнейм:</label>
                <!-- Важно: при получении данных никнейма используем FILTER_UNSAFE_RAW,
                     но при отображении в поле формы используем htmlspecialchars() -->
                <input type="text" id="nickname" name="nickname" required value="<?= htmlspecialchars($nickname) ?>">
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
             <div class="form-group">
                <label for="confirm_password">Подтвердите пароль:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</body>
</html>