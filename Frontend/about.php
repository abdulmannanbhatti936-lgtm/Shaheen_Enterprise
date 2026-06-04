<?php
$pageTitle = "About Us - Shaheen Enterprise";
$pageDescription = "Discover our journey and philosophy. Shaheen Enterprise is dedicated to restoring the harmony between nature and daily care.";
include 'includes/header.php';
?>

<main>
    <section class="about-hero reveal" style="background: var(--secondary); padding: 80px 0; text-align: center;">
        <div class="container">
            <h1 class="reveal reveal-delay-1">Our Story</h1>
            <p class="reveal reveal-delay-2" style="max-width: 800px; margin: 20px auto; font-size: 1.2rem;">Inspired by
                the deep wisdom of ancient
                herbalism, Shaheen Enterprise was founded to bring pure, organic, and effective care products to the
                modern
                world.</p>
        </div>
    </section>

    <section class="about-content reveal">
        <div class="container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
            <img src="images/about.jpg" alt="Botanical Garden"
                style="width: 100%; border-radius: 20px; box-shadow: var(--shadow);" class="reveal reveal-delay-1">
            <div class="reveal reveal-delay-2">
                <h2>Why Nature?</h2>
                <p>We believe that the earth provides everything we need to nourish our bodies. Chemicals and synthetics
                    only mask problems; nature heals them. That's why every product in our collection is hand-crafted
                    using only the finest organic ingredients.</p>
                <p style="margin-top: 20px;">Our mission is to create a sustainable future where beauty and health are
                    synonymous with environmental responsibility.</p>
                <div style="margin-top: 30px;">
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 10px;"><i class="fas fa-check-circle"
                                style="color: var(--primary);"></i> 100% Certified Organic</li>
                        <li style="margin-bottom: 10px;"><i class="fas fa-check-circle"
                                style="color: var(--primary);"></i> Sustainably Sourced</li>
                        <li style="margin-bottom: 10px;"><i class="fas fa-check-circle"
                                style="color: var(--primary);"></i> Small-batch Handcrafted</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>