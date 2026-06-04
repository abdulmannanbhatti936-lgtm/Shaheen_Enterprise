/**
 * Centralized API Communication Helper
 * Standardizes all Fetch calls to handle the {status, message, data} format
 */
async function apiCall(endpoint, options = {}) {
    const defaultHeaders = { 'Content-Type': 'application/json' };
    const config = {
        ...options,
        headers: { ...defaultHeaders, ...options.headers }
    };

    try {
        const response = await fetch(endpoint, config);
        const result = await response.json();

        // Check if response is structurally valid
        if (result.status === "error") {
            showToast(result.message || "An error occurred", "error");
            return result;
        }

        return result;
    } catch (error) {
        showToast("Connection error. Please try again.", "error");
        return { status: "error", message: error.message, data: null };
    }
}

// Utility to resolve image paths (images/ vs uploads/)
function getImgPath(path) {
    if (!path) return 'images/placeholder.jpg';
    if (path.indexOf('http') === 0) return path;
    if (path.indexOf('uploads/') === 0 || path.indexOf('images/') === 0) return path;
    return 'images/' + path;
}

// Utility to update cart count in header
function updateCartCount() {
    apiCall("../Backend/api/getCart.php")
        .then((res) => {
            if (res.status === "success") {
                const countEl = document.getElementById("cart-count");
                if (countEl && res.data.count !== undefined) countEl.innerText = res.data.count;
                renderDrawerCart(res.data);
            }
        });
}

function toggleCartDrawer() {
    const drawer = document.getElementById('cart-drawer');
    if (drawer) drawer.classList.toggle('active');
}

function renderDrawerCart(data) {
    const container = document.getElementById('drawer-cart-items');
    const totalEl = document.getElementById('drawer-total');

    if (!container || !data.items) return;

    if (data.items.length === 0) {
        container.innerHTML = '<div style="text-align: center; padding: 40px; color: var(--text-muted);">Your cart is currently empty.</div>';
        totalEl.innerText = '$0.00';
        return;
    }

    container.innerHTML = data.items.map(item => `
    <div style="display: flex; gap: 15px; margin-bottom: 20px; align-items: center;">
      <img src="${getImgPath(item.image)}" width="60" style="border-radius: 5px;">
      <div style="flex: 1;">
        <h4 style="font-size: 0.9rem; margin-bottom: 5px;">${item.name}</h4>
        <p style="font-size: 0.8rem; color: var(--text-muted);">${item.quantity} x $${item.price}</p>
      </div>
      <i class="fas fa-times" style="cursor: pointer; font-size: 0.8rem; color: #ccc;" onclick="removeFromCart(${item.id})"></i>
    </div>
  `).join('');

    totalEl.innerText = `$${(data.total || 0).toFixed(2)}`;
}

function addToCart(productId, quantity = 1) {
    apiCall("../Backend/api/addToCart.php", {
        method: "POST",
        body: JSON.stringify({ id: productId, quantity: parseInt(quantity) }),
    }).then((res) => {
        if (res.status === "success") {
            showToast(res.message);
            updateCartCount();
            toggleCartDrawer();
        }
    });
}

function removeFromCart(productId) {
    apiCall("../Backend/api/updateCart.php", {
        method: "POST",
        body: JSON.stringify({ id: productId, action: 'remove' }),
    }).then((res) => {
        if (res.status === "success") {
            updateCartCount();
        }
    });
}

// Utility to show toast notifications
function showToast(message, type = "success") {
    let container = document.querySelector(".toast-container");
    if (!container) {
        container = document.createElement("div");
        container.className = "toast-container";
        document.body.appendChild(container);
    }

    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <i class="fas ${type === "success" ? "fa-check-circle" : "fa-exclamation-circle"}"></i>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = "fadeOut 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards";
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Product Quick View Logic
function openQuickView(id) {
    const modal = document.getElementById('quick-view-modal');
    const body = document.getElementById('modal-body-content');

    body.innerHTML = '<div class="spinner"></div>';
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    apiCall(`../Backend/api/getProducts.php?id=${id}`)
        .then(res => {
            if (res.status === "success") {
                const product = res.data;
                body.innerHTML = `
                <div class="modal-img-side">
                    <img src="${getImgPath(product.image)}" alt="${product.name}">
                </div>
                <div class="modal-info-side">
                    <h2>${product.name}</h2>
                    <p class="price">$${product.price}</p>
                    <p class="description">${product.description || 'Experience the essence of organic wellness with our premium botanical formulation.'}</p>
                    
                    <div style="display: flex; gap: 15px; margin-top: auto;">
                        <input type="number" id="quick-qty" value="1" min="1" style="width: 70px; padding: 10px; border: 1px solid var(--border-light);">
                        <button onclick="addToCart(${product.id}, document.getElementById('quick-qty').value)" class="btn btn-full">Add to Cart</button>
                    </div>
                    <a href="product.php?id=${product.id}" style="margin-top: 20px; color: var(--text-muted); font-size: 0.8rem; text-decoration: underline;">View Full Details</a>
                </div>
              `;
            } else {
                body.innerHTML = `<p style="padding: 40px; text-align: center;">${res.message}</p>`;
            }
        });
}

function closeQuickView() {
    const modal = document.getElementById('quick-view-modal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Wishlist Logic
function toggleWishlist(id, el) {
    const isActive = el.classList.contains('active');
    const action = isActive ? 'remove' : 'add';

    apiCall(`../Backend/api/wishlist.php?action=${action}`, {
        method: 'POST',
        body: JSON.stringify({ product_id: id })
    }).then(res => {
        if (res.status === "success") {
            el.classList.toggle('active');
            showToast(res.message, 'success');
        }
    });
}

// Global initialization
document.addEventListener("DOMContentLoaded", () => {
    updateCartCount();

    // Dark Mode Toggle Logic
    const themeToggle = document.createElement("div");
    themeToggle.id = "theme-toggle";
    themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    themeToggle.style.cssText = "position: fixed; bottom: 90px; right: 30px; width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 1000; box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: all 0.3s ease;";
    document.body.appendChild(themeToggle);

    const currentTheme = localStorage.getItem("theme");
    if (currentTheme) {
        document.documentElement.setAttribute("data-theme", currentTheme);
        if (currentTheme === "dark") themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }

    themeToggle.addEventListener("click", () => {
        let theme = document.documentElement.getAttribute("data-theme");
        if (theme === "dark") {
            document.documentElement.removeAttribute("data-theme");
            localStorage.setItem("theme", "light");
            themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        } else {
            document.documentElement.setAttribute("data-theme", "dark");
            localStorage.setItem("theme", "dark");
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        }
    });

    // Mobile Menu Toggle
    const menuToggle = document.getElementById("mobile-menu");
    const navLinks = document.querySelector(".nav-links");

    if (menuToggle) {
        menuToggle.addEventListener("click", () => {
            navLinks.classList.toggle("active");
            const icon = menuToggle.querySelector("i");
            icon.classList.toggle("fa-bars");
            icon.classList.toggle("fa-times");
        });
    }

    // Header Scroll Effect
    const header = document.querySelector("header");
    window.addEventListener("scroll", () => {
        if (window.scrollY > 50) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    });

    // Add Scroll to Top Button
    const scrollTopBtn = document.createElement("div");
    scrollTopBtn.id = "scroll-top";
    scrollTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    document.body.appendChild(scrollTopBtn);

    window.addEventListener("scroll", () => {
        if (window.scrollY > 300) {
            scrollTopBtn.classList.add("visible");
        } else {
            scrollTopBtn.classList.remove("visible");
        }
    });

    scrollTopBtn.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    });

    // Intersection Observer for Reveal Animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                revealObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.reveal, .product-card').forEach(el => {
        revealObserver.observe(el);
    });

    // Smart Search Logic (Live Suggestions)
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        let searchDropdown = document.createElement('div');
        searchDropdown.className = 'search-suggestions';
        searchInput.parentElement.appendChild(searchDropdown);

        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.trim();
            if (term.length < 2) {
                searchDropdown.classList.remove('active');
                return;
            }

            apiCall(`../Backend/api/getProducts.php?action=search&search=${term}`)
                .then(res => {
                    const products = res.data || [];
                    if (products.length === 0) {
                        searchDropdown.innerHTML = '<div style="padding: 15px; font-size: 0.8rem; color: var(--text-muted);">No products found</div>';
                    } else {
                        searchDropdown.innerHTML = products.slice(0, 5).map(p => `
              <div class="suggestion-item" onclick="window.location.href='product.php?id=${p.id}'">
                <img src="${getImgPath(p.image)}" alt="${p.name}">
                <div class="info">
                  <h4>${p.name}</h4>
                  <p>$${p.price}</p>
                </div>
              </div>
            `).join('') + (products.length > 5 ? `<div style="padding: 10px; text-align: center; font-size: 0.75rem; background: #f9f9f9; border-top: 1px solid #eee;"><a href="shop.php?search=${term}">View all results</a></div>` : '');
                    }
                    searchDropdown.classList.add('active');
                });
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.remove('active');
            }
        });
    }
});

/**
 * Generates badge HTML based on product metrics
 * @param {Object} product 
 * @returns {String} HTML string
 */
function getBadgeHTML(product) {
    let badges = '';
    const createdDate = new Date(product.created_at);
    const now = new Date();
    const diffDays = Math.ceil((now - createdDate) / (1000 * 60 * 60 * 24));

    if (diffDays <= 14) {
        badges += `<span class="badge badge-new">New</span>`;
    }
    if (product.is_featured == 1) {
        badges += `<span class="badge badge-featured">Best Seller</span>`;
    }
    return badges;
}

// Newsletter Popup Logic
function openNewsletter() {
    if (localStorage.getItem('newsletter_shown')) return;
    const modal = document.getElementById('newsletter-modal');
    if (modal) modal.classList.add('active');
}

function closeNewsletter() {
    const modal = document.getElementById('newsletter-modal');
    if (modal) modal.classList.remove('active');
    localStorage.setItem('newsletter_shown', 'true');
}

function subscribeNewsletter(e) {
    e.preventDefault();
    showToast('Welcome to the inner circle! Check your email for your 10% code.', 'success');
    closeNewsletter();
}

document.addEventListener("DOMContentLoaded", () => {
    // Custom Cursor Logic
    const cursor = document.getElementById('custom-cursor');
    const follower = document.getElementById('cursor-follower');

    if (cursor && follower) {
        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;
        let followerX = 0, followerY = 0;

        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        function animate() {
            cursorX += (mouseX - cursorX) * 0.2;
            cursorY += (mouseY - cursorY) * 0.2;
            followerX += (mouseX - followerX) * 0.1;
            followerY += (mouseY - followerY) * 0.1;

            cursor.style.transform = `translate3d(${cursorX}px, ${cursorY}px, 0)`;
            follower.style.transform = `translate3d(${followerX}px, ${followerY}px, 0)`;

            requestAnimationFrame(animate);
        }
        animate();

        const interactiveElements = document.querySelectorAll('a, button, .wishlist-btn, .quick-view-btn, .modal-close, #theme-toggle, .suggestion-item');
        interactiveElements.forEach(el => {
            el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hover'));
            el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hover'));
        });
    }
});

