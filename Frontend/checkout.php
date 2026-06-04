<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Backend/controller/authController.php';
$currentUser = $authController->getCurrentUser();

if (!$currentUser) {
    header("Location: ../Backend/auth/login.php");
    exit;
}

$pageTitle = "Checkout - Shaheen Enterprise";
include 'includes/header.php';
?>

<main class="container">
    <section class="checkout-section" style="padding: 60px 0;">
        <h1 style="margin-bottom: 40px;">Finalize Your Order</h1>
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px;">
            <div class="shipping-info">
                <form id="checkout-form" class="auth-container" style="max-width: none; text-align: left;">
                    <h3>Shipping & Payment</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($currentUser['username']) ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($currentUser['email']) ?>"
                                readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Shipping Address</label>
                        <textarea name="address" rows="3" required
                            placeholder="Enter your full street address, city, and zip code."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <div style="display: flex; gap: 20px; margin-top: 10px;">
                            <label
                                style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal;">
                                <input type="radio" name="payment" value="cod" checked onchange="togglePayment('cod')">
                                Cash on Delivery
                            </label>
                            <label
                                style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: normal;">
                                <input type="radio" name="payment" value="card" onchange="togglePayment('card')"> Credit
                                Card
                            </label>
                        </div>
                    </div>

                    <div id="card-details"
                        style="display: none; background: #f9f9f9; padding: 20px; border-radius: 10px; margin-top: 20px;">
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" name="card_expiry" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" name="card_cvc" placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="place-order-btn" class="btn btn-full" style="padding: 15px; margin-top: 20px;">Place Order</button>
                </form>
            </div>
            <div class="order-summary" id="checkout-summary"
                style="background: #fafafa; padding: 30px; border-radius: 10px; height: fit-content;">
                <h3>Order Summary</h3>
                <!-- Loaded via JS -->
            </div>
        </div>
    </section>
</main>

<script>
    function togglePayment(method) {
        const cardDetails = document.getElementById('card-details');
        cardDetails.style.display = method === 'card' ? 'block' : 'none';
        
        // Toggle required attributes for card details
        const inputs = cardDetails.querySelectorAll('input');
        inputs.forEach(input => {
            input.required = (method === 'card');
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Load Cart Summary
        apiCall('../Backend/api/getCart.php')
            .then(res => {
                if (res.status === 'success') {
                    const data = res.data;
                    const summary = document.getElementById('checkout-summary');
                    
                    if (!data.items || data.items.length === 0) {
                        window.location.href = 'shop.php';
                        return;
                    }

                    let summaryHtml = `
                        <div style="margin-top: 20px;">
                            ${data.items.map(item => `
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem;">
                                    <span>${item.name} x ${item.quantity}</span>
                                    <span>$${(item.price * item.quantity).toFixed(2)}</span>
                                </div>
                            `).join('')}
                            <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
                            <div id="applied-coupon-row" style="display: none; justify-content: space-between; color: #e74c3c; margin-bottom: 10px;">
                                <span>Discount (<span id="coupon-display"></span>)</span>
                                <span>-$<span id="discount-amount">0.00</span></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.2rem; color: var(--primary);">
                                <span>Total</span>
                                <span>$<span id="final-total">${data.total.toFixed(2)}</span></span>
                            </div>
                            <div style="margin-top: 30px;">
                                <div style="display: flex; gap: 10px;">
                                    <input type="text" id="coupon-code" placeholder="Promo Code" style="flex: 1; padding: 10px; border: 1px solid #ddd; outline: none;">
                                    <button type="button" onclick="applyCoupon()" class="btn-outline" style="padding: 10px 20px;">Apply</button>
                                </div>
                                <p id="coupon-msg" style="font-size: 0.8rem; margin-top: 10px;"></p>
                            </div>
                        </div>
                    `;
                    summary.innerHTML += summaryHtml;
                }
            });

        let appliedCoupon = null;

        window.applyCoupon = () => {
            const code = document.getElementById('coupon-code').value.trim();
            const msg = document.getElementById('coupon-msg');
            if (!code) return;

            apiCall('../Backend/api/validateCoupon.php', {
                method: 'POST',
                body: JSON.stringify({ code })
            }).then(res => {
                const info = res.data;
                if (res.status === 'success') {
                    appliedCoupon = info.code;
                    document.getElementById('applied-coupon-row').style.display = 'flex';
                    document.getElementById('coupon-display').innerText = info.code;
                    document.getElementById('discount-amount').innerText = info.discount.toFixed(2);

                    const baseTotal = parseFloat(document.getElementById('final-total').innerText);
                    const final = Math.max(0, baseTotal - info.discount);
                    document.getElementById('final-total').innerText = final.toFixed(2);

                    msg.style.color = 'var(--primary)';
                    msg.innerText = res.message;
                } else {
                    msg.style.color = '#e74c3c';
                    msg.innerText = res.message;
                }
            });
        };

        document.getElementById('checkout-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('place-order-btn');
            const address = e.target.address.value;
            const payment = e.target.payment.value;

            // Loading state
            submitBtn.disabled = true;
            submitBtn.innerText = 'Processing Order...';

            apiCall('../Backend/api/placeOrder.php', {
                method: 'POST',
                body: JSON.stringify({
                    address: address,
                    payment: payment,
                    coupon: appliedCoupon
                })
            }).then(res => {
                if (res.status === 'success') {
                    showToast(res.message);
                    window.location.href = `order-success.php?id=${res.data.order_id}`;
                } else {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Place Order';
                    // Error message is already shown by apiCall if it's a structural 'error' status
                }
            }).catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Place Order';
            });
        });
    });
</script>
<?php include 'includes/footer.php'; ?>