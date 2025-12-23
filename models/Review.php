<?php
class Review {
    private $conn;
    private $table = 'reviews';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add review
    public function addReview($user_id, $product_id, $order_id, $rating, $comment, $images = null) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, product_id, order_id, rating, comment, images, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $status = REVIEW_PENDING;
        $images_json = $images ? json_encode($images) : null;
        
        $stmt->bind_param(
            "iiisss",
            $user_id,
            $product_id,
            $order_id,
            $rating,
            $comment,
            $images_json,
            $status
        );
        
        return $stmt->execute();
    }

    // Get product reviews (approved only)
    public function getProductReviews($product_id) {
        $query = "SELECT r.*, u.full_name, u.username
                  FROM " . $this->table . " r
                  JOIN users u ON r.user_id = u.user_id
                  WHERE r.product_id = ? AND r.status = 'approved'
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get pending reviews (admin)
    public function getPendingReviews($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT r.*, u.full_name, p.name as product_name
                  FROM " . $this->table . " r
                  JOIN users u ON r.user_id = u.user_id
                  JOIN products p ON r.product_id = p.product_id
                  WHERE r.status = 'pending'
                  ORDER BY r.created_at DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Approve review
    public function approveReview($review_id) {
        $query = "UPDATE " . $this->table . " SET status = 'approved' WHERE review_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $review_id);
        return $stmt->execute();
    }

    // Reject review
    public function rejectReview($review_id) {
        $query = "UPDATE " . $this->table . " SET status = 'rejected' WHERE review_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $review_id);
        return $stmt->execute();
    }

    // Check if user can review
    public function canUserReview($user_id, $product_id, $order_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE user_id = ? AND product_id = ? AND order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $product_id, $order_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return $result['count'] == 0;
    }
}
?>
