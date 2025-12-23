<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register new user
    public function register($username, $email, $password, $full_name, $phone) {
        $query = "INSERT INTO " . $this->table . " 
                  (username, email, password_hash, full_name, phone, role) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Prepare failed: ' . $this->conn->error];
        }

        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $role = ROLE_CUSTOMER;

        $stmt->bind_param("ssssss", $username, $email, $password_hash, $full_name, $phone, $role);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Đăng ký thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi: ' . $stmt->error];
        }
    }

    // Login user
    public function login($username, $password) {
        $query = "SELECT user_id, username, email, full_name, role, status 
                  FROM " . $this->table . " 
                  WHERE username = ? AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Prepare failed'];
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Get password hash from database
            $query_pass = "SELECT password_hash FROM " . $this->table . " WHERE user_id = ?";
            $stmt_pass = $this->conn->prepare($query_pass);
            $stmt_pass->bind_param("i", $user['user_id']);
            $stmt_pass->execute();
            $pass_result = $stmt_pass->get_result();
            $pass_data = $pass_result->fetch_assoc();

            if (password_verify($password, $pass_data['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                return ['success' => true, 'message' => 'Đăng nhập thành công'];
            }
        }

        return ['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'];
    }

    // Get user by ID
    public function getUserById($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Check if username exists
    public function usernameExists($username) {
        $query = "SELECT user_id FROM " . $this->table . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Check if email exists
    public function emailExists($email) {
        $query = "SELECT user_id FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
?>
