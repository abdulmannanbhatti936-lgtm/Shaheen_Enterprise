<?php
/**
 * Wishlist Model - Handles User Saved Rituals
 */
class Wishlist
{
    private $conn;
    private $table = 'wishlist';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Add product to wishlist
     */
    public function add($user_id, $product_id)
    {
        $query = "INSERT IGNORE INTO " . $this->table . " (user_id, product_id) VALUES (:user_id, :product_id)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => (int)$user_id,
            ':product_id' => (int)$product_id
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function remove($user_id, $product_id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':user_id' => (int)$user_id,
            ':product_id' => (int)$product_id
        ]);
    }

    /**
     * List all products in user's wishlist
     */
    public function list($user_id)
    {
        $query = "SELECT p.* FROM " . $this->table . " w 
                  JOIN products p ON w.product_id = p.id 
                  WHERE w.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => (int)$user_id]);
        return $stmt->fetchAll();
    }

    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($user_id, $product_id)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => (int)$user_id,
            ':product_id' => (int)$product_id
        ]);
        return $stmt->rowCount() > 0;
    }
}
?>