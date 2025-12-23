<?php
class Product {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all products with pagination
    public function getAllProducts($page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.status = 'available'
                  ORDER BY p.created_at DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get total products count
    public function getTotalProducts() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 'available'";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Get product by ID with images
    public function getProductById($product_id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.product_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if ($product) {
            // Get images
            $img_query = "SELECT image_url FROM product_images 
                         WHERE product_id = ? 
                         ORDER BY sort_order ASC";
            $img_stmt = $this->conn->prepare($img_query);
            $img_stmt->bind_param("i", $product_id);
            $img_stmt->execute();
            $product['images'] = $img_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        return $product;
    }

    // Get products by category
    public function getProductsByCategory($category_id, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE p.category_id = ? AND p.status = 'available'
                  ORDER BY p.created_at DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $category_id, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Search products
    public function searchProducts($keyword, $page = 1, $limit = ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        $search = "%$keyword%";
        
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p
                  LEFT JOIN categories c ON p.category_id = c.category_id
                  WHERE (p.name LIKE ? OR p.description LIKE ?) AND p.status = 'available'
                  ORDER BY p.created_at DESC
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssii", $search, $search, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Increment view count
    public function incrementViewCount($product_id) {
        $query = "UPDATE " . $this->table . " SET view_count = view_count + 1 WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        return $stmt->execute();
    }
}
?>
