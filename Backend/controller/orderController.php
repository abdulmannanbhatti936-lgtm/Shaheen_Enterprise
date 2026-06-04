<?php
/**
 * OrderController - Standardized Order & Transaction Management
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController
{
    private $db;
    private $order;

    public function __construct($db)
    {
        $this->db = $db;
        $this->order = new Order($db);
    }

    /**
     * Place a new order
     */
    public function place($user_id, $data)
    {
        $this->order->user_id = (int)$user_id;
        $this->order->total = (float)$data['total'];
        $this->order->address = htmlspecialchars(strip_tags($data['address']));

        $items = $data['items'] ?? []; 

        if (empty($items)) {
            return ["status" => "error", "message" => "Order must contain at least one item", "data" => null];
        }

        $result = $this->order->create($items);
        
        if ($result['success']) {
            return [
                "status" => "success", 
                "message" => "Order placed successfully", 
                "data" => ["order_id" => $result['order_id']]
            ];
        }
        
        return [
            "status" => "error", 
            "message" => $result['message'] ?? "Failed to place order", 
            "data" => null
        ];
    }

    /**
     * List all orders (Admin)
     */
    public function listAll()
    {
        $data = $this->order->getAll();
        return [
            "status" => "success",
            "message" => "All orders retrieved",
            "data" => $data
        ];
    }

    /**
     * List orders for specific user
     */
    public function listByUser($user_id)
    {
        $data = $this->order->getByUserId((int)$user_id);
        return [
            "status" => "success",
            "message" => "User orders retrieved",
            "data" => $data
        ];
    }

    /**
     * Get order details
     */
    public function details($order_id)
    {
        $data = $this->order->getItems((int)$order_id);
        return [
            "status" => "success",
            "message" => "Order items retrieved",
            "data" => $data
        ];
    }

    /**
     * Update order status (Admin)
     */
    public function update($order_id, $status)
    {
        if ($this->order->updateStatus((int)$order_id, $status)) {
            return [
                "status" => "success", 
                "message" => "Order status updated to: " . $status,
                "data" => null
            ];
        }
        return ["status" => "error", "message" => "Failed to update order status", "data" => null];
    }
}

// Global initialization
$orderController = new OrderController($conn);
?>