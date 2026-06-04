<?php
$pageTitle = "Contact Us - Shaheen Enterprise";
$pageDescription = "Get in touch with the Shaheen Enterprise team for any inquiries about our organic wellness products or your orders.";
include 'includes/header.php';
?>

<main class="container">
    <section class="contact-section reveal"
        style="padding: 60px 0; max-width: 800px; margin: 0 auto; text-align: center;">
        <h1 class="reveal reveal-delay-1">Get in Touch</h1>
        <p class="reveal reveal-delay-2" style="margin-bottom: 30px;">Have questions about our products or your order?
            We're here to help.</p>

        <div class="contact-info reveal reveal-delay-3"
            style="margin-bottom: 40px; padding: 30px; background: #f9f9f9; border-radius: 10px;">
            <p><strong>Owner:</strong> Abdul Mannan Bhatti</p>
            <p><strong>Email:</strong> abdulmannanbhatti936@gmail.com</p>
            <p><strong>Phone:</strong> +92 301 2343633</p>
        </div>

        <form id="contact-form" class="auth-container reveal reveal-delay-3"
            style="max-width: none; text-align: left; margin-top: 40px;">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-full">Send Message</button>
        </form>
    </section>
</main>

<script>
    document.getElementById('contact-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = {
            name: e.target.name.value,
            email: e.target.email.value,
            message: e.target.message.value
        };

        apiCall('../Backend/api/contact.php', {
            method: 'POST',
            body: JSON.stringify(formData)
        }).then(res => {
            if (res.status === 'success') {
                showToast(res.message);
                e.target.reset();
            }
        });
    });
</script>
<?php include 'includes/footer.php'; ?>