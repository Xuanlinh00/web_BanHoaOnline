<?php
class Category {
    private $conn;
    private $table = 'categories';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy tất cả các danh mục đang hoạt động
    public function getAllCategories() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'active' ORDER BY name ASC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get category by ID
    public function getCategoryById($category_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE category_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
