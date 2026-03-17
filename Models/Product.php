<?php
include_once "./Models/Database.php";

class Product
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }
    
    // Hàm query chung
    function query($sql, ...$args)
    {
        return $this->db->query($sql, ...$args);
    }

    // --- LOGIC LẤY GIÁ MỚI ---
    // Hàm này giúp tạo câu SQL lấy giá ưu tiên Size S, nếu không có thì lấy giá thấp nhất
    private function getPriceQuery() {
        return "COALESCE(
            (SELECT price FROM product_variants WHERE product_id = p.id AND size = 'S' LIMIT 1),
            (SELECT MIN(price) FROM product_variants WHERE product_id = p.id)
        ) as price";
    }

    // 1. Lấy chi tiết sản phẩm
    function getById($id)
    {
        // Lấy thông tin sản phẩm + giá (ưu tiên S)
        $sql = "SELECT p.*, " . $this->getPriceQuery() . " FROM products p WHERE p.id = ?";
        return $this->db->queryOne($sql, $id);
    }

    // 2. Lấy biến thể (giữ nguyên)
    function getVariants($product_id)
    {
        return $this->db->query("SELECT * FROM product_variants WHERE product_id = ?", $product_id);
    }

    // 3. Lấy bình luận (giữ nguyên)
    public function getComments($product_id) {
        $sql = "SELECT c.*, u.name as user_name 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.product_id = ? 
                ORDER BY c.created_at DESC";
        return $this->query($sql, $product_id);
    }

    // 4. Thêm bình luận (giữ nguyên)
    public function addComment($user_id, $product_id, $content, $rating) {
        $sql = "INSERT INTO comments (user_id, product_id, content, rating, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        return $this->query($sql, $user_id, $product_id, $content, $rating);
    }

    // 5. Lấy câu hỏi Q&A (giữ nguyên)
    function getQuestions($product_id)
    {
        $sql = "SELECT q.*, u.name as user_name 
                FROM questions q 
                JOIN users u ON q.user_id = u.id 
                WHERE q.product_id = ? 
                ORDER BY q.created_at DESC";
        return $this->query($sql, $product_id);
    }

    // 6. Thêm câu hỏi (giữ nguyên)
    function addQuestion($user_id, $product_id, $content)
    {
        $sql = "INSERT INTO questions (user_id, product_id, content, created_at) 
                VALUES (?, ?, ?, NOW())";
        return $this->query($sql, $user_id, $product_id, $content);
    }

    // 7. Lấy tất cả sản phẩm
    function getAllProducts()
    {
        $sql = "SELECT p.*, " . $this->getPriceQuery() . " FROM products p ORDER BY p.id DESC";
        return $this->db->query($sql);
    }

    // --- CÁC HÀM CHO TRANG CHỦ (ĐÃ CẬP NHẬT QUERY) ---
    function BestSelling($limit = 4)
    {
        $sql = "SELECT p.*, " . $this->getPriceQuery() . " 
                FROM products p 
                WHERE p.status = 1 
                ORDER BY p.sold DESC 
                LIMIT $limit";
        return $this->db->query($sql);
    }

    function NewProducts($limit = 4)
    {
        $sql = "SELECT p.*, " . $this->getPriceQuery() . " 
                FROM products p 
                WHERE p.status = 1
                ORDER BY p.created_at DESC 
                LIMIT $limit";
        return $this->db->query($sql);
    }

    function RecommendedProducts($limit = 4)
    {
        $sql = "SELECT p.*, " . $this->getPriceQuery() . " 
                FROM products p 
                WHERE p.status = 1 
                ORDER BY RAND() 
                LIMIT $limit";
        return $this->db->query($sql);
    }

    // 8. Filter tìm kiếm (Cập nhật logic giá vì cột price đã mất)
    function getFilteredProducts($options = [])
    {
        // Ta cần JOIN bảng biến thể để lọc theo giá min/max
        $sql = "SELECT p.*, MIN(pv.price) as price 
                FROM products p 
                JOIN product_variants pv ON p.id = pv.product_id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($options['keyword'])) {
            $sql .= " AND p.name LIKE ?"; 
            $params[] = '%' . $options['keyword'] . '%';
        }

        if (!empty($options['categories']) && is_array($options['categories'])) {
            $placeholders = implode(',', array_fill(0, count($options['categories']), '?'));
            $sql .= " AND p.category_id IN ($placeholders)";
            $params = array_merge($params, $options['categories']);
        }

        // Gom nhóm để tính toán giá
        $sql .= " GROUP BY p.id ";

        // HAVING dùng để lọc sau khi đã gom nhóm (xử lý giá min/max)
        $havingClause = [];
        if ($options['min_price'] !== null) { 
            $havingClause[] = "MIN(pv.price) >= ?"; 
            $params[] = intval($options['min_price']); 
        }
        if ($options['max_price'] !== null) { 
            $havingClause[] = "MIN(pv.price) <= ?"; 
            $params[] = intval($options['max_price']); 
        }

        if (!empty($havingClause)) {
            $sql .= " HAVING " . implode(' AND ', $havingClause);
        }
        
        // Sắp xếp
        $sort = $options['sort'] ?? 'newest';
        switch ($sort) {
            case 'price-asc': $sql .= " ORDER BY MIN(pv.price) ASC"; break;
            case 'price-desc': $sql .= " ORDER BY MIN(pv.price) DESC"; break;
            case 'name-asc': $sql .= " ORDER BY p.name ASC"; break;
            default: $sql .= " ORDER BY p.id DESC"; break;
        }

        return $this->db->query($sql, ...$params);
    }

    // 9. Lấy sản phẩm tương tự
    function getRelatedProducts($category_id, $current_product_id, $limit = 4)
    {
        if (empty($category_id)) return [];
        $limit = (int)$limit;
        $sql = "SELECT p.*, " . $this->getPriceQuery() . " 
                FROM products p 
                WHERE p.category_id = ? AND p.id != ? AND p.status = 1 
                ORDER BY RAND() LIMIT $limit";
        return $this->db->query($sql, $category_id, $current_product_id);
    }

    // --- CHỨC NĂNG YÊU THÍCH (WISHLIST) ---
    function checkFavorite($user_id, $product_id)
    {
        $sql = "SELECT count(*) as count FROM favorites WHERE user_id = ? AND product_id = ?";
        $result = $this->db->queryOne($sql, $user_id, $product_id);
        return $result['count'] > 0;
    }

    function toggleFavorite($user_id, $product_id)
    {
        if ($this->checkFavorite($user_id, $product_id)) {
            $sql = "DELETE FROM favorites WHERE user_id = ? AND product_id = ?";
            $this->query($sql, $user_id, $product_id);
        } else {
            $sql = "INSERT INTO favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())";
            $this->query($sql, $user_id, $product_id);
        }
    }

    function getFavoritesByUser($user_id)
    {
        // Lấy danh sách yêu thích kèm giá
        $sql = "SELECT p.*, f.created_at as liked_at, " . $this->getPriceQuery() . "
                FROM products p 
                JOIN favorites f ON p.id = f.product_id 
                WHERE f.user_id = ? 
                ORDER BY f.created_at DESC";
        return $this->query($sql, $user_id);
    }
    function getVariant($product_id, $size)
    {
        $sql = "SELECT * FROM product_variants WHERE product_id = ? AND size = ?";
        $result = $this->db->query($sql, $product_id, $size); 
        return isset($result[0]) ? $result[0] : null;
    }
    
    function decreaseVariantStock($variant_id, $qty) {
        $sql = "UPDATE product_variants SET stock = stock - ? WHERE id = ?";
        $this->db->query($sql, $qty, $variant_id); 
    }
}
?>