<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-about">
                <div class="brand-name" style="margin-bottom: 20px;">
                    <span class="primary" style="color: white; font-size: 2rem;">Shaheen</span>
                    <span class="secondary" style="color: rgba(255,255,255,0.6); letter-spacing: 7px;">Enterprise</span>
                </div>
                <p>Bringing the wisdom of nature to your daily care routine with sustainable, organic products.</p>
            </div>
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <p><strong>Abdul Mannan Bhatti</strong></p>
                <p>Email: abdulmannanbhatti936@gmail.com</p>
                <p>Phone: +92 301 2343633</p>
            </div>
        </div>

        <div class="newsletter-section"
            style="margin-top: 50px; padding: 40px; background: rgba(255,255,255,0.05); border-radius: 15px; text-align: center;">
            <h3>Join Our Green Community</h3>
            <p>Subscribe for exclusive offers and wellness tips.</p>
            <form id="newsletter-form"
                style="margin-top: 20px; display: flex; gap: 10px; max-width: 500px; margin-left: auto; margin-right: auto;">
                <input type="email" placeholder="Enter your email" required
                    style="flex: 1; padding: 12px 20px; border-radius: 25px; border: none; outline: none;">
                <button type="submit" class="btn">Subscribe</button>
            </form>
        </div>

        <div class="footer-bottom"
            style="margin-top: 50px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px;">
            <p>&copy; 2026 <span class="elegant-text" style="color: white;">Shaheen Enterprise</span>. All Rights
                Reserved.</p>
        </div>
    </div>
</footer>

<div id="cookie-banner"
    style="position: fixed; bottom: -100px; left: 0; width: 100%; background: var(--primary); color: white; padding: 20px; text-align: center; z-index: 3000; transition: bottom 0.5s ease-in-out; display: flex; justify-content: center; align-items: center; gap: 20px; box-shadow: 0 -5px 20px rgba(0,0,0,0.1);">
    <p style="margin: 0; font-size: 0.9rem;">We use cookies to improve your experience. By continuing, you agree to our
        <a href="privacy.php" style="color: var(--accent-light); text-decoration: underline;">Privacy Policy</a>.
    </p>
    <button id="accept-cookies" class="btn"
        style="padding: 8px 20px; font-size: 0.8rem; background: var(--white); color: var(--primary);">Accept</button>
</div>

<script src="js/main.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!localStorage.getItem('cookiesAccepted')) {
            setTimeout(() => {
                document.getElementById('cookie-banner').style.bottom = '0';
            }, 2000);
        }

        document.getElementById('accept-cookies').addEventListener('click', () => {
            localStorage.setItem('cookiesAccepted', 'true');
            document.getElementById('cookie-banner').style.bottom = '-100px';
        });
    });
</script>
<script>
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = e.target.querySelector('input').value;
            fetch('../Backend/api/newsletter.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            })
                .then(res => res.json())
                .then(data => {
                    showToast(data.message, data.success ? 'success' : 'error');
                    if (data.success) e.target.reset();
                });
        });
    }
</script>
</body>

</html>