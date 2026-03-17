<?php
include_once "./Models/Database.php";
class Category
{
  private $db;
  function __construct()
  {
    $this->db = new Database();
  }
  public function getAll()
{
    $sql = "SELECT 
                c.*,
                COALESCE(p.product_count, 0) AS product_count
            FROM category c
            LEFT JOIN (
                SELECT category_id, COUNT(*) AS product_count 
                FROM products 
                GROUP BY category_id
            ) p ON c.id = p.category_id
            ORDER BY c.id ASC";

    return $this->db->query($sql);
}
    

    // Lấy 1 danh mục theo id
    public function getById($id)
    {
        $sql = "SELECT * FROM category WHERE id = ?";
        return $this->db->queryOne($sql, [$id]);
    }

    public function insert($data)
    {
        $sql = "INSERT INTO category (name, status, sort_order) 
                VALUES (?, ?, ?)";
        $this->db->query($sql, [
            $data['name'],
            $data['status'] ?? 1,
            $data['sort_order'] ?? 0
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE category 
                SET name = ?, status = ?, sort_order = ?
                WHERE id = ?";
        $this->db->query($sql, [
            $data['name'],
            $data['status'],
            $data['sort_order'] ?? 0,
            $id
        ]);
    }

    // Xóa
    public function delete($id)
    {
        // Nên kiểm tra có sản phẩm hoặc danh mục con trước khi xóa thật tế
        $sql = "DELETE FROM category WHERE id = ?";
        $this->db->query($sql, [$id]);
    }

    // Toggle trạng thái (1 ↔ 0)
    public function toggleStatus($id)
    {
        $sql = "UPDATE category SET status = 1 - status WHERE id = ?";
        $this->db->query($sql, [$id]);
    }
    // Lấy danh mục đang hiển thị (status = 1)
    public function getVisibleCategories()
    {
        $all = $this->getAll();

        // Trả về danh mục đầy đủ thông tin, chỉ giữ những cái visible
        return array_filter($all, function($cat) {
            return $cat['status'] == 1;
        });
    }
  
}