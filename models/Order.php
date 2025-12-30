<?php
class Order {
    private $conn;
    private $table = 'orders';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create order from cart
    public function createOrder($user_id, $data) {
        $order_code = 'ORD' . date('YmdHis') . rand(1000, 9999);
        
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, order_code, delivery_date, delivery_time_slot, message_card, 
                   is_anonymous, subtotal, shipping_fee, discount_amount, total_amount, 
                   recipient_name, recipient_phone, shipping_address, shipping_ward, 
                   shipping_district, shipping_city, shipping_method_id, payment_method_id, notes)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Prepare failed'];
        }

        $is_anonymous = isset($data['is_anonymous']) ? 1 : 0;
        $shipping_fee = $data['shipping_fee'] ?? 0;
        $discount_amount = $data['discount_amount'] ?? 0;
        $total = $data['subtotal'] + $shipping_fee - $discount_amount;

        $stmt->bind_param(
            "isssiddddsssssssiis",
            $user_id,
            $order_code,
            $data['delivery_date'],
            $data['delivery_time_slot'],
            $data['message_card'],
            $is_anonymous,
            $data['subtotal'],
            $shipping_fee,
            $discount_amount,
            $total,
            $data['recipient_name'],
            $data['recipient_phone'],
            $data['shipping_address'],
            $data['shipping_ward'],
            $data['shipping_district'],
            $data['shipping_city'],
            $data['shipping_method_id'],
            $data['payment_method_id'],
            $data['notes']
        );

        if ($stmt->execute()) {
            return ['success' => true, 'order_id' => $this->conn->insert_id, 'order_code' => $order_code];
        } else {
            return ['success' => false, 'message' => $stmt->error];
        }
    }

    // Add items to order
    public function addOrderItems($order_id, $items) {
        $query = "INSERT INTO order_items 
                  (order_id, product_id, product_name, product_price, quantity, subtotal)
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->bind_param(
                "iisidi",
                $order_id,
                $item['product_id'],
                $item['name'],
                $item['price'],
                $item['quantity'],
                $subtotal
            );
            $stmt->execute();
        }
        
        return true;
    }

    // Get order by ID
    public function getOrderById($order_id) {
        $query = "SELECT o.*, 
                         sm.name as shipping_method_name,
                         pm.name as payment_method_name
                  FROM " . $this->table . " o
                  LEFT JOIN shipping_methods sm ON o.shipping_method_id = sm.shipping_method_id
                  LEFT JOIN payment_methods pm ON o.payment_method_id = pm.payment_method_id
                  WHERE o.order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get user orders
    public function getUserOrders($user_id, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT o.* FROM " . $this->table . " o
                  WHERE o.user_id = ?
                  ORDER BY o.order_date DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get all orders (admin)
    public function getAllOrders($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT o.*, u.full_name, u.email
                  FROM " . $this->table . " o
                  JOIN users u ON o.user_id = u.user_id
                  ORDER BY o.order_date DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update order status
    public function updateOrderStatus($order_id, $status) {
        $query = "UPDATE " . $this->table . " SET status = ? WHERE order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }
}
?>
