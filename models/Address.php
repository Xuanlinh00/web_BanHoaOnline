<?php
class Address {
    private $conn;
    private $table = 'user_addresses';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get user addresses
    public function getUserAddresses($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = ? ORDER BY is_default DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get address by ID
    public function getAddressById($address_id, $user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE address_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $address_id, $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Add address
    public function addAddress($user_id, $data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, recipient_name, recipient_phone, address_line, ward, district, city, is_default)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $is_default = isset($data['is_default']) ? 1 : 0;
        
        $stmt->bind_param(
            "issssssi",
            $user_id,
            $data['recipient_name'],
            $data['recipient_phone'],
            $data['address_line'],
            $data['ward'],
            $data['district'],
            $data['city'],
            $is_default
        );
        
        return $stmt->execute();
    }

    // Update address
    public function updateAddress($address_id, $user_id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET recipient_name = ?, recipient_phone = ?, address_line = ?, 
                      ward = ?, district = ?, city = ?, is_default = ?
                  WHERE address_id = ? AND user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $is_default = isset($data['is_default']) ? 1 : 0;
        
        $stmt->bind_param(
            "sssssssii",
            $data['recipient_name'],
            $data['recipient_phone'],
            $data['address_line'],
            $data['ward'],
            $data['district'],
            $data['city'],
            $is_default,
            $address_id,
            $user_id
        );
        
        return $stmt->execute();
    }

    // Delete address
    public function deleteAddress($address_id, $user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE address_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $address_id, $user_id);
        return $stmt->execute();
    }
}
?>
