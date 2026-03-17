<?php
include_once "./Models/Database.php";

class Order
{
    private $db;
    function __construct() {
        $this->db = new Database();
    }

    // --- CÁC HÀM THỐNG KÊ (Dùng cho Dashboard) ---
    
    function getAllCategory()
  {
    $sql = "SELECT * FROM categories";
    return $this->db->query($sql);
  }
  
    function getOrdersToday(){
        $sql = "SELECT COUNT(*) AS total FROM orders WHERE DATE(created_at) = CURDATE()";
        $row = $this->db->query($sql);
        return $row[0]['total'] ?? 0;
    }

   function getMonthlyRevenue(){
    $sql = "SELECT COALESCE(SUM(total_amount), 0) AS revenue 
            FROM orders 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
              AND status = 'completed'";  // ← Thêm điều kiện này
    
    $row = $this->db->query($sql);
    return $row[0]['revenue'] ?? 0;
}

    // MỚI: Thống kê số lượng theo trạng thái (Chuyển từ Controller sang)
    function countByStatus() {
        return $this->db->query("SELECT status, COUNT(*) AS count FROM orders GROUP BY status");
    }

    function getRevenueLast12Months() {
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%m/%Y') AS month,
                    COALESCE(SUM(total_amount), 0) AS revenue
                FROM orders 
                WHERE status = 'completed'
                AND created_at >= DATE_SUB(NOW(), INTERVAL 11 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%m/%Y') 
                ORDER BY MIN(created_at) DESC";

        $raw = $this->query($sql);
        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = new DateTime("-{$i} months");
            $key = $date->format('m/Y');
            $found = false;
            foreach ($raw as $row) {
                if ($row['month'] === $key) {
                    $result[] = ['month' => $key, 'revenue' => (int)$row['revenue']];
                    $found = true;
                    break;
                }
            }
            if (!$found) $result[] = ['month' => $key, 'revenue' => 0];
        }
        return $result;
    }

    function getAllOrders($keyword = '', $status = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM orders WHERE 1=1";
        $params = [];
        if ($keyword !== '') {
            $sql .= " AND (name LIKE ? OR phone LIKE ? OR id LIKE ?)";
            $keywordParam = "%$keyword%";
            $params[] = $keywordParam;
            $params[] = $keywordParam;
            $params[] = $keywordParam;
        }
        if ($status !== '' && $status !== 'all') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        return $this->db->query($sql, $params);
    }

    function countOrders($keyword = '', $status = '')
    {
        $sql = "SELECT COUNT(*) as total FROM orders WHERE 1=1";
        $params = [];
        if ($keyword !== '') {
            $sql .= " AND (name LIKE ? OR phone LIKE ? OR id LIKE ?)";
            $keywordParam = "%$keyword%";
            $params[] = $keywordParam;
            $params[] = $keywordParam;
            $params[] = $keywordParam;
        }
        if ($status !== '' && $status !== 'all') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        $row = $this->db->query($sql, $params);
        return isset($row[0]['total']) ? $row[0]['total'] : 0;
    }

    function updateStatus($id, $status)
    {
        $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->query($sql, [$status, $id]);
    }

    function getOrderById($id) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        return isset($result[0]) ? $result[0] : null;
    }

    function getOrderItems($order_id) {
        $sql = "SELECT oi.*, p.name as product_name, p.image, pv.size 
                FROM order_item oi 
                JOIN product_variants pv ON oi.variant_id = pv.id
                JOIN products p ON pv.product_id = p.id 
                WHERE oi.order_id = ?";
        return $this->db->query($sql, [$order_id]);
    }

    // Wrappers
    public function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }
    public function queryOne($sql, $params = []) {
        return $this->db->queryOne($sql, $params);
    }
}
?>