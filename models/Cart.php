<?php
class Cart {
    private $conn;
    private $carts_table = 'carts';
    private $cart_items_table = 'cart_items';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get or create cart for user
    private function getOrCreateCart($user_id) {
        $query = "SELECT cart_id FROM " . $this->carts_table . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['cart_id'];
        } else {
            $insert_query = "INSERT INTO " . $this->carts_table . " (user_id) VALUES (?)";
            $insert_stmt = $this->conn->prepare($insert_query);
            $insert_stmt->bind_param("i", $user_id);
            $insert_stmt->execute();
            return $this->conn->insert_id;
        }
    }

    // Add item to cart
    public function addItem($user_id, $product_id, $quantity = 1) {
        $cart_id = $this->getOrCreateCart($user_id);

        $query = "INSERT INTO " . $this->cart_items_table . " (cart_id, product_id, quantity) 
                  VALUES (?, ?, ?)
                  ON DUPLICATE KEY UPDATE quantity = quantity + ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiii", $cart_id, $product_id, $quantity, $quantity);
        return $stmt->execute();
    }

    // Get cart items
    public function getCartItems($user_id) {
        $cart_id = $this->getOrCreateCart($user_id);

        $query = "SELECT ci.cart_item_id, ci.product_id, ci.quantity, 
                         p.name, p.price, p.image_url, p.stock
                  FROM " . $this->cart_items_table . " ci
                  JOIN products p ON ci.product_id = p.product_id
                  WHERE ci.cart_id = ?
                  ORDER BY ci.cart_item_id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update item quantity
    public function updateItemQuantity($cart_item_id, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($cart_item_id);
        }

        $query = "UPDATE " . $this->cart_items_table . " SET quantity = ? WHERE cart_item_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $quantity, $cart_item_id);
        return $stmt->execute();
    }

    // Remove item from cart
    public function removeItem($cart_item_id) {
        $query = "DELETE FROM " . $this->cart_items_table . " WHERE cart_item_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $cart_item_id);
        return $stmt->execute();
    }

    // Clear cart
    public function clearCart($user_id) {
        $cart_id = $this->getOrCreateCart($user_id);
        $query = "DELETE FROM " . $this->cart_items_table . " WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $cart_id);
        return $stmt->execute();
    }

    // Get cart total
    public function getCartTotal($user_id) {
        $items = $this->getCartItems($user_id);
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}
?>
