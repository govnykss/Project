<?php
// profile.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/php/config.php';
include __DIR__ . '/php/auth_helpers.php';
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
// Требуем авторизации для доступа к этой странице
requireLogin();

// Если пользователь авторизован, получаем его данные из сессии
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];
$user_nickname = $_SESSION['user_nickname'];
$is_admin = $_SESSION['is_admin'];

// Можно также загрузить актуальные данные пользователя из БД, если они могли измениться
// (например, если добавили возможность редактирования профиля)
// try {
//     $stmt = $pdo->prepare("SELECT email, nickname, is_admin FROM users WHERE id = :id");
//     $stmt->bindParam(':id', $user_id);
//     $stmt->execute();
//     $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
//     if ($user_data) {
//         $user_email = $user_data['email'];
//         $user_nickname = $user_data['nickname'];
//         $is_admin = (bool)$user_data['is_admin'];
//     } else {
//         // Если пользователь не найден в БД, возможно, что-то не так с сессией
//         // Лучше перенаправить на выход
//         header('Location: logout.php');
//         exit;
//     }
// } catch (PDOException $e) {
//     error_log("Database error loading user profile: " . $e->getMessage());
//     // Обработка ошибки, возможно, показ сообщения пользователю
//     $_SESSION['error_message'] = 'Не удалось загрузить данные профиля.';
//     // Или просто продолжаем с данными из сессии
// }

// $pdo = null; // Закрываем соединение, если оно было открыто для запроса

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .profile-info p {
            margin-bottom: 10px;
            font-size: 1.1em;
            color: #555;
        }
        .profile-info span {
            font-weight: bold;
            color: #333;
        }
        .profile-links {
            margin-top: 20px;
            text-align: center;
        }
        .profile-links a {
            margin: 0 10px;
            color: #5cb85c;
            text-decoration: none;
        }
        .profile-links a:hover {
            text-decoration: underline;
        }
    </style>
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
                    <li><a href="profile.php">Профиль</a></li> <!-- Ссылка на профиль -->
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
        <div class="profile-container">
            <h2>Профиль пользователя</h2>

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

            <div class="profile-info">
                <p><span>Никнейм:</span> <?php echo htmlspecialchars($user_nickname); ?></p>
                <p><span>Email:</span> <?php echo htmlspecialchars($user_email); ?></p>
                <p><span>Статус:</span> <?php echo $is_admin ? 'Администратор' : 'Пользователь'; ?></p>
            </div>

            <div class="profile-links">
                 <!-- Ссылка для редактирования профиля (если реализуете) -->
                 <!-- <a href="edit_profile.php">Редактировать профиль</a> -->
                 <a href="index.php">Вернуться на главную</a>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2023 Movie Tracker</p>
    </footer>
</body>
</html>