<?php
/**
 * Order Model - Handles Atomic Order Creation & Stock Management
 */
class Order
{
    private $conn;
    private $table = 'orders';

    public $id;
    public $user_id;
    public $total;
    public $address;
    public $status;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Create Order with Atomic Stock Validation & Deduction
     */
    public function create($items)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Validate Stock for all items first
            foreach ($items as $item) {
                $stmt = $this->conn->prepare("SELECT stock, name FROM products WHERE id = :id FOR UPDATE");
                $stmt->execute([':id' => $item['product_id']]);
                $product = $stmt->fetch();

                if (!$product || $product['stock'] < $item['quantity']) {
                    throw new Exception("Sorry, " . ($product['name'] ?? 'an item') . " is out of stock or insufficient.");
                }
            }

            // 2. Insert Order Main Record
            $query = "INSERT INTO " . $this->table . " (user_id, total, address, status) VALUES (:user_id, :total, :address, 'Pending')";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':user_id' => $this->user_id,
                ':total'   => $this->total,
                ':address' => $this->address
            ]);
            $order_id = $this->conn->lastInsertId();

            // 3. Insert Items and Deduct Stock
            $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
            $item_stmt = $this->conn->prepare($item_query);

            $update_stock_query = "UPDATE products SET stock = stock - :qty WHERE id = :id";
            $stock_stmt = $this->conn->prepare($update_stock_query);

            foreach ($items as $item) {
                // Insert item
                $item_stmt->execute([
                    ':order_id'   => $order_id,
                    ':product_id' => $item['product_id'],
                    ':quantity'   => $item['quantity'],
                    ':price'      => $item['price']
                ]);

                // Deduct stock
                $stock_stmt->execute([
                    ':qty' => $item['quantity'],
                    ':id'  => $item['product_id']
                ]);
            }

            $this->conn->commit();
            return ["success" => true, "order_id" => $order_id];

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll()
    {
        $query = "SELECT o.*, u.username FROM " . $this->table . " o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    public function getByUserId($user_id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    public function getItems($order_id)
    {
        $query = "SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':order_id' => $order_id]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status)
    {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id
        ]);
    }
}
?>