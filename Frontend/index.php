<?php
$pageTitle = "Shaheen Enterprise - Premium Wellness";
$pageDescription = "Experience the essence of organic wellness with Shaheen Enterprise. Hand-crafted botanical treats for your skin and soul.";
require_once __DIR__ . '/../Backend/config/db.php';
include 'includes/header.php';
?>

<?php
// Fetch Settings safely using PDO
try {
    $stmt = $conn->query("SELECT * FROM site_settings");
    $settingsRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $settings = [];
    foreach ($settingsRaw as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Fallback if table doesn't exist yet
    $settings = [
        'hero_title' => 'Botanical Alchemy',
        'hero_subtitle' => 'The Ritual of Self Care',
        'hero_description' => 'Pure, potent formulations crafted with ancient wisdom and modern precision.'
    ];
}
?>

<main>
    <!-- Editorial Hero -->
    <section class="hero-wrapper reveal">
        <div class="hero-text">
            <h5 class="reveal" style="transition-delay: 0.2s;"><?= $settings['hero_subtitle'] ?></h5>
            <h1 class="reveal" style="transition-delay: 0.4s;"><?= $settings['hero_title'] ?></h1>
            <p class="reveal" style="margin-bottom: 40px; color: var(--text-muted); transition-delay: 0.6s;">
                <?= $settings['hero_description'] ?>
            </p>
            <div class="reveal" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; transition-delay: 0.8s;">
                <a href="shop.php" class="btn">Explore Collection</a>
                <?php if (isset($currentUser) && $currentUser): ?>
                    <a href="profile.php" class="btn-outline">My Account</a>
                <?php else: ?>
                    <a href="../Backend/auth/login.php" class="btn-outline">Login</a>
                    <a href="../Backend/auth/register.php" class="btn-outline">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Asymmetric Story Section -->
    <section class="container section-padding reveal">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <div style="padding-bottom: var(--spacing-lg);">
                <h2 style="font-size: 2.2rem; margin-bottom: 20px;">Conscious Formulations</h2>
                <p style="margin-bottom: var(--spacing-md); color: var(--text-muted); font-size: 1.1rem;">
                    Our philosophy is simple: nature knows best. We source distinct botanical ingredients from their
                    native regions to ensure potency, purity, and ethical integrity.
                </p>
                <ul style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; list-style: none; margin-top: 40px;"
                    class="reveal" style="transition-delay: 0.3s;">
                    <li><span style="display:block; border-top: 1px solid #000; padding: 15px 10px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">100% Vegan</span></li>
                    <li><span style="display:block; border-top: 1px solid #000; padding: 15px 10px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Cruelty Free</span></li>
                    <li><span style="display:block; border-top: 1px solid #000; padding: 15px 10px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Paraben Free</span></li>
                    <li><span style="display:block; border-top: 1px solid #000; padding: 15px 10px; font-size: 0.8rem; letter-spacing: 0.1em; text-transform: uppercase;">Ethically Sourced</span></li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Featured Product Carousel -->
    <section class="section-padding" style="background: white;">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: var(--spacing-lg);" class="reveal">
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 5px; letter-spacing: 0.1em;">CURATED SELECTION</h5>
                    <h2>Selected For You</h2>
                </div>
                <a href="shop.php" class="btn-outline" style="padding: 10px 30px;">View All</a>
            </div>

            <div class="product-grid" id="featured-list">
                <!-- Loaded via JS -->
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        apiCall('../Backend/api/getProducts.php?action=featured')
            .then(res => {
                const list = document.getElementById('featured-list');
                if (res.status === 'success') {
                    renderList(res.data, list);
                }
            });
    });

    function renderList(products, container) {
        container.innerHTML = products.map(p => `
            <div class="product-card">
                <div class="product-image-wrapper">
                    ${getBadgeHTML(p)}
                    <img src="${getImgPath(p.image)}" alt="${p.name}" loading="lazy">
                    <div class="quick-view-btn" onclick="openQuickView(${p.id})">Quick View</div>
                </div>
                <a href="product.php?id=${p.id}" class="product-info">
                    <h3>${p.name}</h3>
                    <p class="price">$${parseFloat(p.price).toFixed(2)}</p>
                </a>
            </div>
        `).join('');
    }
</script>
<?php include 'includes/footer.php'; ?>