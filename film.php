<?php
$film_id = $_GET['id'] ?? 0;

try {
    // Получаем данные фильма
    $stmt = $pdo->prepare("SELECT f.*, GROUP_CONCAT(g.name SEPARATOR ', ') AS genres 
                           FROM films f
                           LEFT JOIN film_genres fg ON f.id = fg.film_id
                           LEFT JOIN genres g ON fg.genre_id = g.id
                           WHERE f.id = ?");
    $stmt->execute([$film_id]);
    $film = $stmt->fetch();
    
    if (!$film) {
        throw new Exception('Фильм не найден');
    }
    
    // Получаем отзывы
    $reviews = $pdo->prepare("SELECT r.*, u.nickname 
                              FROM reviews r
                              JOIN users u ON r.user_id = u.id
                              WHERE r.film_id = ?
                              ORDER BY r.created_at DESC");
    $reviews->execute([$film_id]);
    $reviews = $reviews->fetchAll();
    
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: index.php');
    exit;
}
?>

<!-- Детальная страница фильма -->
<div class="film-details">
    <img src="<?= htmlspecialchars($film['poster_url']) ?>" class="film-poster">
    <div class="film-info">
        <h1><?= htmlspecialchars($film['title']) ?></h1>
        <p>Год: <?= $film['release_year'] ?></p>
        <p>Режиссер: <?= htmlspecialchars($film['director']) ?></p>
        <p>Жанры: <?= htmlspecialchars($film['genres']) ?></p>
        <div class="description"><?= nl2br(htmlspecialchars($film['description'])) ?></div>
    </div>
</div>

<!-- Форма отзыва -->
<?php if (isLoggedIn()): ?>
<form method="post" action="add_review.php" class="review-form">
    <input type="hidden" name="film_id" value="<?= $film['id'] ?>">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    
    <select name="rating" required>
        <option value="">Оцените фильм</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?> звезд</option>
        <?php endfor; ?>
    </select>
    
    <textarea name="comment" placeholder="Ваш отзыв..."></textarea>
    <button type="submit">Отправить отзыв</button>
</form>
<?php endif; ?>

<!-- Список отзывов -->
<div class="reviews">
    <?php foreach ($reviews as $review): ?>
        <div class="review">
            <h4><?= htmlspecialchars($review['nickname']) ?></h4>
            <div class="rating"><?= str_repeat('★', $review['rating']) ?></div>
            <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
        </div>
    <?php endforeach; ?>
</div>