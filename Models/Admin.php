<?php
include_once "./Models/Database.php";

class Admin
{
    private $db;
    function __construct() {
        $this->db = new Database();
    }

    function getAllCategory() {
        return $this->db->query("SELECT * FROM category");
    }

    function getById($id) {
        return $this->db->queryOne("SELECT * FROM products WHERE id = ?", [$id]);
    }

    function getTotalProducts() {
        $row = $this->db->queryOne("SELECT COUNT(*) as total FROM products");
        return $row['total'];
    }

    function search($keyword) {
        $keyword = trim($keyword);
        if ($keyword == "") return [];
        return $this->db->query("SELECT * FROM products WHERE name LIKE ?", ["%$keyword%"]);
    }
    //khang làm
  function updateQuantity($id, $quantity) {
    $sql = "UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?";
    return $this->db->query($sql, $quantity, $id, $quantity);
  }
//khang làm
  function updateQuantityDirect($id, $change) {
    $sql = "UPDATE products SET quantity = GREATEST(quantity + ?, 0) WHERE id = ?";
    return $this->db->query($sql, $change, $id);
  }
    // --- SỬA: Xóa cột price và truyền mảng tham số ---
    function update($id, $data, $imageString) {
        $finalImage = ($imageString) ? $imageString : $data['current_image'];
        
        $sql = "UPDATE products SET name=?, description=?, quantity=?, image=?, category_id=? WHERE id=?";
        
        $params = [
            $data['name'], 
            $data['description'], 
            $data['quantity'], 
            $finalImage, 
            $data['category_id'], 
            $id
        ];
        return $this->db->query($sql, $params);
    }

    // --- SỬA: Xóa cột price và truyền mảng tham số ---
    function insert($data, $imgString) {
        $slug = strtolower(str_replace(' ', '-', $data['name']));
        
        // Đã xóa 'price' trong câu lệnh này
        $sql = "INSERT INTO products (name, category_slug, description, image, category_id, quantity, sold, created_at, status) 
                VALUES (?, ?, ?, ?, ?, ?, 0, NOW(), 1)"; 
        
        $params = [
            $data['name'], 
            $slug,
            $data['description'] ?? '', 
            $imgString, 
            $data['category_id'], 
            $data['quantity']
        ];
        return $this->db->query($sql, $params);
    }

    function delete($id) {
        try {
            $this->db->delete("DELETE FROM product_variants WHERE product_id = ?", [$id]);
            return $this->db->delete("DELETE FROM products WHERE id = ?", [$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    // --- SỬA: Xử lý tham số để tránh lỗi Unpacked ---
    function query($sql, ...$params) {
        if (isset($params[0]) && is_array($params[0])) {
            $params = $params[0];
        }
        return $this->db->query($sql, $params);
    }
    
    function queryOne($sql, ...$params) {
        if (isset($params[0]) && is_array($params[0])) {
            $params = $params[0];
        }
        return $this->db->queryOne($sql, $params);
    }
    function getTopSellingProducts($limit = 10) {
        $sql = "SELECT 
                    p.name, 
                    COALESCE(SUM(oi.quantity), 0) AS total_sold
                FROM order_item oi
                JOIN product_variants v ON oi.variant_id = v.id
                JOIN products p ON v.product_id = p.id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status = 'completed'
                GROUP BY p.id, p.name
                ORDER BY total_sold DESC
                LIMIT $limit"; // limit là số nguyên do ta truyền, nối chuỗi an toàn
        return $this->db->query($sql);
    }
}
?>