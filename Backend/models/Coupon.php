<?php
/**
 * Coupon Model - Handles Discount Codes
 */
class Coupon
{
    private $conn;
    private $table = 'coupons';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Validate Coupon Code
     */
    public function validate($code, $total)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE code = :code AND is_active = TRUE AND (expiry_date IS NULL OR expiry_date >= CURDATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':code' => trim($code)]);
        $coupon = $stmt->fetch();

        if (!$coupon) {
            return ["status" => "error", "message" => "Invalid or expired coupon code", "data" => null];
        }

        if ($total < $coupon['min_purchase']) {
            return ["status" => "error", "message" => "Minimum purchase of $" . number_format($coupon['min_purchase'], 2) . " required", "data" => null];
        }

        $discount = 0;
        if ($coupon['discount_type'] === 'percentage') {
            $discount = ($total * $coupon['discount_value']) / 100;
        } else {
            $discount = $coupon['discount_value'];
        }

        return [
            "status" => "success",
            "message" => "Coupon applied successfully",
            "data" => [
                "discount" => (float)$discount,
                "code" => $code,
                "type" => $coupon['discount_type'],
                "value" => (float)$coupon['discount_value']
            ]
        ];
    }
}
?>