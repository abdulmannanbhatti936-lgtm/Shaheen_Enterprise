<?php
$pageTitle = "Your Cart - Shaheen Enterprise";
include 'includes/header.php';
?>

<main class="container">
    <section class="cart-section" style="padding: 60px 0;">
        <h1>Shopping Cart</h1>
        <div id="cart-content" style="margin-top: 40px;">
            <!-- Loaded via JS -->
            <div class="spinner"></div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', loadCart);

    function loadCart() {
        apiCall('../Backend/api/getCart.php')
            .then(res => {
                const container = document.getElementById('cart-content');
                if (res.status !== 'success' || !res.data.items || res.data.items.length === 0) {
                    container.innerHTML = `
                            <div style="text-align: center; padding: 50px;">
                                <i class="fas fa-shopping-basket" style="font-size: 4rem; color: #eee; margin-bottom: 20px;"></i>
                                <h2>Your cart is empty</h2>
                                <p style="margin-bottom: 30px;">Looks like you haven't added anything yet.</p>
                                <a href="shop.php" class="btn">Go to Shop</a>
                            </div>
                        `;
                    return;
                }

                const data = res.data;
                let cartHtml = `
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 2px solid #eee; text-align: left;">
                                    <th style="padding: 15px;">Product</th>
                                    <th style="padding: 15px;">Price</th>
                                    <th style="padding: 15px;">Quantity</th>
                                    <th style="padding: 15px;">Subtotal</th>
                                    <th style="padding: 15px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                data.items.forEach(item => {
                    cartHtml += `
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px; display: flex; align-items: center; gap: 20px;">
                                    <img src="${getImgPath(item.image)}" width="60" style="border-radius: 5px;">
                                    <span>${item.name}</span>
                                </td>
                                <td style="padding: 15px;">$${parseFloat(item.price).toFixed(2)}</td>
                                <td style="padding: 15px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <button onclick="updateQty(${item.product_id}, ${item.quantity - 1})" class="btn-icon"><i class="fas fa-minus"></i></button>
                                        <span>${item.quantity}</span>
                                        <button onclick="updateQty(${item.product_id}, parseInt(${item.quantity}) + 1)" class="btn-icon"><i class="fas fa-plus"></i></button>
                                    </div>
                                </td>
                                <td style="padding: 15px; font-weight: 600;">$${(item.price * item.quantity).toFixed(2)}</td>
                                <td style="padding: 15px; text-align: right;">
                                    <button onclick="removeFromCart(${item.product_id})" class="text-danger" style="background: none; border: none; cursor: pointer;"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        `;
                });

                cartHtml += `
                            </tbody>
                        </table>
                        <div style="margin-top: 40px; text-align: right; background: var(--secondary); padding: 30px; border-radius: 10px;">
                            <h2 style="margin-bottom: 20px;">Total: $${data.total.toFixed(2)}</h2>
                            <a href="checkout.php" class="btn" style="padding: 15px 50px;">Proceed to Checkout</a>
                        </div>
                    `;

                container.innerHTML = cartHtml;
            });
    }

    function updateQty(productId, newQty) {
        if (newQty < 1) return removeFromCart(productId);
        apiCall('../Backend/api/updateCart.php', {
            method: 'POST',
            body: JSON.stringify({ id: productId, quantity: newQty })
        }).then(res => {
            if (res.status === 'success') {
                loadCart();
                updateCartCount();
            }
        });
    }

    function removeFromCart(productId) {
        apiCall('../Backend/api/updateCart.php', {
            method: 'POST',
            body: JSON.stringify({ id: productId, action: 'remove' })
        }).then(res => {
            if (res.status === 'success') {
                loadCart();
                updateCartCount();
                showToast("Item removed from cart");
            }
        });
    }
</script>
<?php include 'includes/footer.php'; ?>