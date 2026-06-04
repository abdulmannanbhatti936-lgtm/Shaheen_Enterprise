<?php
$pageTitle = "Ingredient Glossary - Shaheen Enterprise";
include 'includes/header.php';
?>

<main class="container" style="padding-top: 50px;">
    <div style="text-align: center; margin-bottom: 80px;">
        <h5 style="letter-spacing: 3px; text-transform: uppercase; color: var(--primary);">Botanical Wisdom</h5>
        <h1 style="font-size: 3.5rem; margin: 10px 0;">Ingredient Glossary</h1>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">A transparent guide to the powerful,
            hand-picked botanicals that define our formulations.</p>
    </div>

    <div
        style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; margin-bottom: 100px;">
        <?php
        $ingredients = [
            ['name' => 'Saffron', 'benefit' => 'Brightens & Evens Tone', 'desc' => 'Known as "Red Gold," it contains antioxidants that repair cells and lighten dark spots.'],
            ['name' => 'Rosehip Oil', 'benefit' => 'Regenerative', 'desc' => 'Rich in vitamins A and C, it helps reduce signs of aging and promotes skin elasticity.'],
            ['name' => 'Neem', 'benefit' => 'Antibacterial', 'desc' => 'A powerful purifier that treats acne and calms irritated skin with its medicinal properties.'],
            ['name' => 'Amla', 'benefit' => 'Hair Growth', 'desc' => 'A potent source of Vitamin C that strengthens hair follicles and prevents premature greying.'],
            ['name' => 'Aloe Vera', 'benefit' => 'Deep Hydration', 'desc' => 'Soothes inflammation and provides a cooling burst of moisture to dehydrated skin.'],
            ['name' => 'Jojoba', 'benefit' => 'Balance', 'desc' => 'Closely mimics skins natural sebum, providing hydration without clogging pores.'],
        ];
        foreach ($ingredients as $ing):
            ?>
            <div class="stat-card" style="padding: 30px; text-align: left; border-left: 4px solid var(--primary);">
                <h3 style="font-size: 1.4rem; margin-bottom: 10px;">
                    <?= $ing['name'] ?>
                </h3>
                <span
                    style="display: block; font-size: 0.8rem; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">
                    <?= $ing['benefit'] ?>
                </span>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
                    <?= $ing['desc'] ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>