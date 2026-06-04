<?php
/**
 * CartController - Session-Based Shopping Cart Logic
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CartController
{
    public function __construct()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Add item to cart
     */
    public function add($product)
    {
        if (!isset($product['id']) || !isset($product['price'])) {
            return ["status" => "error", "message" => "Invalid product data", "data" => null];
        }

        $id = $product['id'];
        $quantity = isset($product['quantity']) ? (int)$product['quantity'] : 1;

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$id] = [
                "id" => $product['id'],
                "name" => $product['name'] ?? 'Unknown Product',
                "price" => (float)$product['price'],
                "image" => $product['image'] ?? '',
                "quantity" => $quantity
            ];
        }

        return [
            "status" => "success",
            "message" => "Added to cart",
            "data" => [
                "cart_count" => $this->getCountValue(),
                "total" => $this->getTotalValue()
            ]
        ];
    }

    /**
     * Update item quantity
     */
    public function update($id, $quantity)
    {
        $id = (int)$id;
        $quantity = (int)$quantity;

        if (isset($_SESSION['cart'][$id])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$id]);
                $msg = "Item removed from cart";
            } else {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
                $msg = "Cart updated";
            }
            return [
                "status" => "success",
                "message" => $msg,
                "data" => [
                    "cart_count" => $this->getCountValue(),
                    "total" => $this->getTotalValue()
                ]
            ];
        }
        return ["status" => "error", "message" => "Item not found in cart", "data" => null];
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $id = (int)$id;
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            return [
                "status" => "success",
                "message" => "Removed from cart",
                "data" => [
                    "cart_count" => $this->getCountValue(),
                    "total" => $this->getTotalValue()
                ]
            ];
        }
        return ["status" => "error", "message" => "Item not found in cart", "data" => null];
    }

    /**
     * Empty the cart
     */
    public function clear()
    {
        $_SESSION['cart'] = [];
        return ["status" => "success", "message" => "Cart cleared", "data" => null];
    }

    /**
     * Get all cart items
     */
    public function get()
    {
        return [
            "status" => "success",
            "message" => "Cart data retrieved",
            "data" => [
                "items" => array_values($_SESSION['cart']),
                "total" => $this->getTotalValue(),
                "count" => $this->getCountValue()
            ]
        ];
    }

    /**
     * Internal: Get total count
     */
    private function getCountValue()
    {
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    /**
     * Internal: Get total price
     */
    private function getTotalValue()
    {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    /**
     * Compatibility wrapper for count
     */
    public function getCount()
    {
        return $this->getCountValue();
    }

    /**
     * Compatibility wrapper for total
     */
    public function getTotal()
    {
        return $this->getTotalValue();
    }
}

// Global initialization
$cartController = new CartController();
?>