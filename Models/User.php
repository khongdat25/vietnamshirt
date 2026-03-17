<?php
include_once "./Models/Database.php";

class User
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
        return $this->db->queryOne($sql, $email, md5($password));
    }

    function register($name, $email, $password)
    {
        $sql = "INSERT INTO users (`name`, `email`, `password`) VALUES (?, ?, ?)";
        return $this->db->insert($sql, $name, $email, md5($password));
    }

    function checkEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        return $this->db->queryOne($sql, $email);
    }
    function updateToken($email, $token) {
        // Token hết hạn sau 15 phút
        $sql = "UPDATE users 
                SET reset_token = ?, 
                    reset_token_exp = DATE_ADD(NOW(), INTERVAL 15 MINUTE) 
                WHERE email = ?";
        $this->db->query($sql, $token, $email);
    }

    // 2. Kiểm tra Token có hợp lệ không (Dùng cho trang Đổi mật khẩu)
    function checkToken($token) {
        $sql = "SELECT * FROM users 
                WHERE reset_token = ? 
                AND reset_token_exp > NOW()"; 
        return $this->db->queryOne($sql, $token);
    }

    // 3. Đổi mật khẩu mới
    function resetPassword($token, $newPassword) {
    $sql = "UPDATE users 
            SET password = ?, reset_token = NULL, reset_token_exp = NULL 
            WHERE reset_token = ?";
    
    // LỖI Ở ĐÂY: Bạn đang lưu pass thô (chưa mã hóa)
    $this->db->query($sql, $newPassword, $token); 
}

    function profile($user_id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->queryOne($sql, $user_id);
    }

    function myOrders($user_id)
    {
        $sql = "SELECT o.id, o.created_at, o.total_amount, o.status, o.address, o.phone,
                GROUP_CONCAT(CONCAT(p.name, ' (Size ', pv.size, ')') SEPARATOR ', ') as product_list 
                FROM orders o
                LEFT JOIN order_item oi ON o.id = oi.order_id
                LEFT JOIN product_variants pv ON oi.variant_id = pv.id
                LEFT JOIN products p ON pv.product_id = p.id
                WHERE o.user_id = ?   
                GROUP BY o.id
                ORDER BY o.created_at DESC";
        return $this->db->query($sql, $user_id);
    }

    function getOrderById($id)
    {
        $sql = "SELECT * FROM orders WHERE id = ?";
        return $this->db->queryOne($sql, $id);
    }
    function getOrderItems($order_id)
    {
        $sql = "SELECT oi.*, 
                    p.name,    
                    p.image,    
                    pv.size      
                FROM order_item oi 
                JOIN product_variants pv ON oi.variant_id = pv.id
                JOIN products p ON pv.product_id = p.id 
                WHERE oi.order_id = ?";
        return $this->db->query($sql, $order_id);
    }

    // Hàm cập nhật thông tin – ĐÃ HOÀN CHỈNH & AN TOÀN
    function updateUserProfile($user_id, $name, $email, $phone, $address, $birthday = null, $gender = 'khac')
    {
        $sql = "UPDATE users 
                SET name = ?, 
                    email = ?, 
                    phone = ?, 
                    address = ?, 
                    birthday = ?, 
                    gender = ? 
                WHERE id = ?";

        // Nếu ngày sinh rỗng → để NULL cho DB
        if (empty($birthday)) {
            $birthday = null;
        }

        return $this->db->update($sql, $name, $email, $phone, $address, $birthday, $gender, $user_id);
    }
   function updatePassword($user_id, $new_hashed_password)
    {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->db->update($sql, $new_hashed_password, $user_id);
    }
}