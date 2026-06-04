<?php
/**
 * Wellness Journal - Shaheen Enterprise
 * Phase 4.1: PDO Refactor & Elite UI
 */
require_once __DIR__ . '/../Backend/config/db.php';
require_once __DIR__ . '/../Backend/helpers/image_helper.php';

$pageTitle = "Wellness Journal - Shaheen Enterprise";
$pageDescription = "Deeper insights into botanical wisdom, self-care rituals, and the art of organic living.";

try {
    $stmt = $conn->query("SELECT * FROM blog_posts WHERE status = 'Published' ORDER BY created_at DESC");
    $posts = $stmt->fetchAll();
} catch (Exception $e) {
    $posts = [];
}

include 'includes/header.php';
?>

<main class="container" style="padding-top: 50px;">
    <section class="reveal" style="text-align: center; margin-bottom: 80px;">
        <h5 style="letter-spacing: 3px; text-transform: uppercase; color: var(--primary); margin-bottom: 10px;">Knowledge & Rituals</h5>
        <h1 style="font-size: clamp(2.5rem, 5vw, 3.5rem); margin: 0; font-family: var(--font-heading); line-height: 1.1;">The Wellness Journal</h1>
        <p style="color: var(--text-muted); max-width: 600px; margin: 20px auto 0; line-height: 1.6;">
            Explore the wisdom of nature, ingredient insights, and the art of self-care ritualization.
        </p>
    </section>

    <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 40px; margin-bottom: 100px;">
        <?php if (empty($posts)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px; background: #fafafa; border-radius: var(--radius-sm);">
                <p style="color: var(--text-muted);">Our first stories are being woven. Check back soon.</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $index => $post): ?>
                <article class="reveal" style="transition-delay: <?= ($index * 0.1) ?>s;">
                    <div style="overflow: hidden; border-radius: var(--radius-sm); margin-bottom: 25px; aspect-ratio: 16/10; background: #f0f0f0;">
                        <img src="<?= formatImgPath($post['image'] ?? 'journal-placeholder.jpg') ?>" 
                             alt="<?= htmlspecialchars($post['title']) ?>" 
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s cubic-bezier(0.19, 1, 0.22, 1);"
                             onmouseover="this.style.transform='scale(1.05)'"
                             onmouseout="this.style.transform='scale(1)'"
                             loading="lazy">
                    </div>
                    <div class="blog-meta" style="margin-bottom: 15px;">
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 2px; color: var(--primary); font-weight: 600;">
                            <?= date('F d, Y', strtotime($post['created_at'])) ?>
                        </span>
                    </div>
                    <h2 style="font-size: 1.8rem; line-height: 1.2; margin-bottom: 15px; font-family: var(--font-heading);">
                        <a href="post.php?slug=<?= $post['slug'] ?>" style="color: inherit;"><?= htmlspecialchars($post['title']) ?></a>
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 25px;">
                        <?= substr(strip_tags($post['content']), 0, 160) ?>...
                    </p>
                    <a href="post.php?slug=<?= $post['slug'] ?>" 
                       style="font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1.5px; border-bottom: 1px solid var(--primary); padding-bottom: 4px; display: inline-block;">
                        Read Ritual
                    </a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>