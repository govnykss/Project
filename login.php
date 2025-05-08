<?php
// login.php

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

$email = ''; // Для сохранения введенного email при ошибке

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = 'Пожалуйста, заполните все поля.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Некорректный формат email.';
    } else {
        try {
            // Выбираем также никнейм из БД
            $stmt = $pdo->prepare("SELECT id, email, nickname, password, is_admin FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // *** ВЕРНИТЕСЬ К password_verify() ДЛЯ ПРОДАКШЕНА ***
            // Сравниваем введенный пароль с паролем из базы данных
            if ($user && $password === $user['password']) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nickname'] = $user['nickname']; // Сохраняем никнейм в сессии

                // Проверяем, куда пользователь пытался попасть до логина (если есть)
                $redirect_url = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']); // Удаляем URL после использования

                $_SESSION['success_message'] = 'Вы успешно вошли!';

                header('Location: ' . $redirect_url);
                exit;

            } else {
                $_SESSION['error_message'] = 'Неверный email или пароль.';
            }

        } catch (PDOException $e) {
            error_log("Database error during login: " . $e->getMessage());
            $_SESSION['error_message'] = 'Произошла ошибка при входе. Попробуйте позже.';
        }
    }

    // Перенаправление после POST для предотвращения повторной отправки формы
    // и отображения сообщений об ошибках
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-form-container">
        <h2>Вход</h2>

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

        <form id="loginForm" method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Войти</button>
        </form>
        <p>Еще нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</body>
</html>