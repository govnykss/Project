<?php
// logout.php

// Начинаем сессию
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Удаляем все переменные сессии
$_SESSION = array();

// Если используется сессионная cookie, удаляем ее.
// Замечание: Это уничтожит сессию, а не только данные сессии!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Уничтожаем сессию
session_destroy();

// Устанавливаем сообщение об успехе (опционально)
// Сообщение об успехе будет доступно на странице, куда произойдет перенаправление
$_SESSION['success_message'] = 'Вы успешно вышли из аккаунта.';

// Перенаправляем на страницу входа или главную
// Логичнее перенаправлять на главную страницу после выхода
header('Location: index.php'); // Изменено с login.php на index.php
exit; // Важно: exit после header

?>