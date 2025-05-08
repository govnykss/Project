// js/auth.js

document.addEventListener('DOMContentLoaded', () => {

    // --- Клиентская валидация форм ---

    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Функция для отображения сообщения (можно улучшить, например, добавляя в DOM)
    function displayMessage(container, message, type = 'error') {
        // Найдем существующие сообщения и удалим их
        const existingAlerts = container.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

        const alertDiv = document.createElement('div');
        alertDiv.classList.add('alert', type);
        alertDiv.textContent = message;
        container.insertBefore(alertDiv, container.firstChild); // Вставляем перед формой
    }

    // Валидация формы входа
    if (loginForm) {
        loginForm.addEventListener('submit', (event) => {
            const emailInput = loginForm.querySelector('#email');
            const passwordInput = loginForm.querySelector('#password');
            const messageContainer = loginForm.closest('.auth-form-container'); // Контейнер для сообщений

            // Очистить предыдущие сообщения
            const existingAlerts = messageContainer.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            if (!emailInput.value || !passwordInput.value) {
                displayMessage(messageContainer, 'Пожалуйста, заполните все поля.', 'error');
                event.preventDefault(); // Отменить стандартную отправку формы
            } else if (!isValidEmail(emailInput.value)) {
                 displayMessage(messageContainer, 'Некорректный формат email.', 'error');
                 event.preventDefault();
            }
            // Дополнительные проверки, если нужны
        });
    }

    // Валидация формы регистрации
    if (registerForm) {
        registerForm.addEventListener('submit', (event) => {
            const emailInput = registerForm.querySelector('#email');
            const passwordInput = registerForm.querySelector('#password');
            const confirmPasswordInput = registerForm.querySelector('#confirm_password');
             const messageContainer = registerForm.closest('.auth-form-container'); // Контейнер для сообщений

            // Очистить предыдущие сообщения
             const existingAlerts = messageContainer.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());


            if (!emailInput.value || !passwordInput.value || !confirmPasswordInput.value) {
                 displayMessage(messageContainer, 'Пожалуйста, заполните все поля.', 'error');
                event.preventDefault();
            } else if (!isValidEmail(emailInput.value)) {
                 displayMessage(messageContainer, 'Некорректный формат email.', 'error');
                 event.preventDefault();
            } else if (passwordInput.value !== confirmPasswordInput.value) {
                 displayMessage(messageContainer, 'Пароли не совпадают.', 'error');
                 event.preventDefault();
            } else if (passwordInput.value.length < 6) { // Пример минимальной длины
                 displayMessage(messageContainer, 'Пароль должен быть не менее 6 символов.', 'error');
                 event.preventDefault();
            }
            // Дополнительные проверки
        });
    }

    // Вспомогательная функция для проверки формата email
    function isValidEmail(email) {
        // Простая проверка формата email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }



}); // Конец DOMContentLoaded