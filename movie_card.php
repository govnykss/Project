<div class="movie-card">
    <img src="<?= htmlspecialchars($movie['poster_url']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
    <h3><?= htmlspecialchars($movie['title']) ?></h3>
    <p>Год: <?= $movie['release_year'] ?></p>
    
    <?php if (isLoggedIn()): ?>
        <div class="movie-actions">
            <button class="btn-watched" data-movie="<?= $movie['id'] ?>">
                <?= $is_watched ? '✓ Просмотрено' : 'Отметить просмотренным' ?>
            </button>
            <button class="btn-wishlist" data-movie="<?= $movie['id'] ?>">
                <?= $in_wishlist ? '★ В списке' : 'В желаемое' ?>
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
// Обработка кликов по кнопкам
document.querySelectorAll('.btn-watched').forEach(btn => {
    btn.addEventListener('click', async () => {
        const response = await fetch('api/mark_watched.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ movie_id: btn.dataset.movie })
        });
        
        if (response.ok) {
            btn.textContent = '✓ Просмотрено';
        }
    });
});

document.querySelectorAll('.btn-wishlist').forEach(btn => {
    btn.addEventListener('click', async () => {
        const response = await fetch('api/toggle_wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ movie_id: btn.dataset.movie })
        });
        
        if (response.ok) {
            const data = await response.json();
            btn.textContent = data.in_wishlist ? '★ В списке' : 'В желаемое';
        }
    });
});
</script>