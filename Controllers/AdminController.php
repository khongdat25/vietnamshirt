<?php
include_once 'admin.php';
include_once './Models/Database.php';
include_once "./Models/Admin.php";
include_once "./Models/Order.php";
include_once './Models/Category.php';

class AdminController {
    
    function pageAdmin() {
        // Kiểm tra quyền
    }

    // --- HÀM DASHBOARD ĐÃ REFACTOR ---
    function page() {
        // 1. Khởi tạo model
        $productModel = new Admin();
        $orderModel   = new Order();

        // 2. Lấy dữ liệu thô từ Model (Không viết SQL ở đây nữa)
        $totalProducts       = $productModel->getTotalProducts();
        $ordersToday         = $orderModel->getOrdersToday();
        $monthlyRevenue      = $orderModel->getMonthlyRevenue();
        $revenueLast12Months = $orderModel->getRevenueLast12Months();
        
        // Gọi hàm mới vừa tạo bên Model
        $topProducts         = $productModel->getTopSellingProducts(10); 
        $orderStatusStats    = $orderModel->countByStatus();

        // 3. Xử lý Logic hiển thị (Màu sắc, Label) - Phần này giữ lại Controller là đúng
        $statusLabels = [];
        $statusData   = [];
        // Map màu sắc
        $statusColors = [
            'pending'    => '#ffc107',
            'confirmed'  => '#007bff',
            'shipping'   => '#17a2b8',
            'completed'  => '#28a745',
            'cancelled'  => '#dc3545',
            'returned'   => '#6c757d'
        ];

        // Format lại dữ liệu cho ChartJS
        foreach ($orderStatusStats as $row) {
            $st = $row['status'];
            $label = match($st) {
                'pending'    => 'Chờ xác nhận',
                'confirmed'  => 'Đã xác nhận',
                'shipping'   => 'Đang giao hàng',
                'completed'  => 'Hoàn thành',
                'cancelled'  => 'Đã hủy',
                'returned'   => 'Trả hàng',
                default      => ucfirst($st)
            };
            $statusLabels[] = $label;
            $statusData[]   = (int)$row['count'];
        }

        // 4. Gọi View
        include_once "./Views/admin/page_admin.php";
    }

    // --- CÁC HÀM KHÁC GIỮ NGUYÊN ---
    function product() {
        $productModel = new Admin();
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        $perPage = 10;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $perPage;

        $keyword = trim($_GET['keyword'] ?? '');
        $cat_id = $_GET['cat_id'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $where = "WHERE 1=1";
        $params = [];

        if ($keyword) { $where .= " AND p.name LIKE ?"; $params[] = "%$keyword%"; }
        if ($cat_id) { $where .= " AND p.category_id = ?"; $params[] = $cat_id; }
        if ($status === 'instock') $where .= " AND p.quantity > 10";
        if ($status === 'low') $where .= " AND p.quantity > 0 AND p.quantity <= 10";
        if ($status === 'out') $where .= " AND p.quantity = 0";

        $sql = "SELECT p.*, COALESCE(c.name, 'Chưa chọn') as cat_name,
               (SELECT MIN(price) FROM product_variants WHERE product_id = p.id) as display_price,
               (SELECT GROUP_CONCAT(CONCAT('<b>', size, '</b>: ', stock) ORDER BY FIELD(size, 'S','M','L','XL','XXL') SEPARATOR '<br>') 
                FROM product_variants WHERE product_id = p.id) as stock_detail
               FROM products p LEFT JOIN category c ON p.category_id = c.id 
               $where ORDER BY p.id DESC LIMIT $perPage OFFSET $offset";

        $products = $productModel->query($sql, $params);
        $totalRow = $productModel->queryOne("SELECT COUNT(*) FROM products p $where", $params);
        $total = $totalRow['COUNT(*)'] ?? 0;
        $totalPages = max(1, ceil($total / $perPage));

        include_once "./Views/admin/page_product.php";
    }

    function updateQuantity($id) {
        $productModel = new Admin();
        $product = $productModel->getById($id);
        if (!$product) { header("Location: admin.php?ctrl=admin&act=product"); exit(); }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['action'] === 'increase') {
                $productModel->updateQuantityDirect($id, 1);
            } elseif ($_POST['action'] === 'decrease' && $product['quantity'] > 0) {
                $productModel->updateQuantityDirect($id, -1);
            }
        }
        header("Location: admin.php?ctrl=admin&act=product"); exit();
    }

    function add() {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productModel = new Admin();
            $variants = $_POST['variants'] ?? [];
            
            $totalQty = 0;
            foreach ($variants as $v) {
                if(isset($v['stock']) && $v['stock'] !== '') $totalQty += (int)$v['stock'];
            }

            $target = "./Public/image/";
            if (!is_dir($target)) mkdir($target, 0777, true);
            
            $dbImage = 'no-image.jpg';
            if (!empty($_FILES['image']['name'])) {
                $fname = 'prod_' . time() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES['image']['tmp_name'], $target . $fname);
                $dbImage = "image/" . $fname; 
            }

            $productModel->insert([
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'quantity' => $totalQty,
                'category_id' => $_POST['category_id']
            ], $dbImage);

            $newId = $productModel->queryOne("SELECT id FROM products ORDER BY id DESC LIMIT 1", [])['id'];
            
            foreach ($variants as $size => $d) {
                if((isset($d['price']) && $d['price'] !== '') || (isset($d['stock']) && $d['stock'] !== '')) {
                    $price = (int)($d['price'] ?? 0);
                    $stock = (int)($d['stock'] ?? 0);
                    $productModel->query("INSERT INTO product_variants (product_id, size, price, stock) VALUES (?, ?, ?, ?)", 
                    [$newId, $size, $price, $stock]);
                }
            }
            header('Location: admin.php?ctrl=admin&act=product'); exit();
        }
        include_once './Views/admin/admin_add.php';
    }

    function edit($id) {
        $productModel = new Admin();
        $categoryModel = new Category();
        $product = $productModel->getById($id);
        $categories = $categoryModel->getAll();
        $variants = $productModel->query("SELECT * FROM product_variants WHERE product_id = ?", [$id]);

        if (!$product) { header('Location: admin.php?ctrl=admin&act=product'); exit(); }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $variantsPost = $_POST['variants'] ?? [];
            $totalQty = 0;
            foreach ($variantsPost as $v) {
                if(isset($v['stock']) && $v['stock'] !== '') $totalQty += (int)$v['stock'];
            }

            $dbImage = "";
            if (!empty($_FILES['image']['name'])) {
                $target = "./Public/image/";
                if (!is_dir($target)) mkdir($target, 0777, true);
                $fname = 'prod_' . time() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES['image']['tmp_name'], $target . $fname);
                $dbImage = "image/" . $fname;
            }

            $productModel->update($id, [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'quantity' => $totalQty,
                'category_id' => $_POST['category_id'],
                'current_image' => $product['image']
            ], $dbImage);

            foreach ($variantsPost as $size => $d) {
                if ((isset($d['price']) && $d['price'] !== '') || (isset($d['stock']) && $d['stock'] !== '')) {
                    $price = (int)($d['price'] ?? 0);
                    $stock = (int)($d['stock'] ?? 0);
                    $check = $productModel->queryOne("SELECT id FROM product_variants WHERE product_id = ? AND size = ?", [$id, $size]);

                    if ($check) {
                        $productModel->query("UPDATE product_variants SET price = ?, stock = ? WHERE id = ?", [$price, $stock, $check['id']]);
                    } else {
                        $productModel->query("INSERT INTO product_variants (product_id, size, price, stock) VALUES (?, ?, ?, ?)", [$id, $size, $price, $stock]);
                    }
                }
            }
            header('Location: admin.php?ctrl=admin&act=product'); exit();
        }
        include './Views/admin/admin_edit.php';
    }

    function delete($id) {
        (new Admin())->delete($id);
        header('Location: admin.php?ctrl=admin&act=product'); exit();
    }
    
    function order() {
        $orderModel = new Order();
        $keyword = trim($_GET['keyword'] ?? ''); $status = $_GET['status'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1)); $perPage = 10;
        $orders = $orderModel->getAllOrders($keyword, $status, $perPage, ($page - 1) * $perPage);
        $totalOrders = $orderModel->countOrders($keyword, $status);
        $totalPages = max(1, ceil($totalOrders / $perPage));
        $ordersToday = $orderModel->getOrdersToday();
        $revenueMonth = $orderModel->getMonthlyRevenue();
        include_once "./Views/admin/page_order.php";
    }

    function update_order_status() {
        if (isset($_POST['order_id'], $_POST['status'])) (new Order())->updateStatus($_POST['order_id'], $_POST['status']);
        header("Location: admin.php?ctrl=admin&act=order"); exit();
    }

    function order_detail() {
        if (!isset($_GET['order_id'])) { header("Location: admin.php?ctrl=admin&act=order"); exit(); }
        $orderModel = new Order();
        $order = $orderModel->getOrderById($_GET['order_id']);
        $items = $orderModel->getOrderItems($_GET['order_id']);
        include_once "./Views/admin/page_order_detail.php";
    }
    //danh mục nè kui 
    public function category(){
        include_once './Views/admin/page_categories.php';
    }
    public function categories()
{
    $categoryModel = new Category();
    $allCategories = $categoryModel->getAll();

    // Tìm kiếm và lọc
    $keyword = trim($_GET['keyword'] ?? '');
    $status_filter = $_GET['status'] ?? ''; // '' hoặc '0' hoặc '1'

    $filtered = $allCategories;

    if ($keyword !== '') {
        $filtered = array_filter($filtered, function($cat) use ($keyword) {
            return stripos($cat['name'], $keyword) !== false;
        });
    }

    if ($status_filter !== '') {
        $filtered = array_filter($filtered, function($cat) use ($status_filter) {
            return $cat['status'] == $status_filter;
        });
    }

    // Phân trang
    $perPage = 20;
    $page = max(1, (int)($_GET['page'] ?? 1));
    $total = count($filtered);
    $offset = ($page - 1) * $perPage;
    $displayCategories = array_slice($filtered, $offset, $perPage);
    $totalPages = max(1, ceil($total / $perPage));

    include_once './Views/admin/page_categories.php';
}

// Thêm danh mục
public function category_add()
{
    $categoryModel = new Category();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'name'       => trim($_POST['name']),
            'status'     => $_POST['status'] ?? 1
        ];
        $categoryModel->insert($data);
        header('Location: admin.php?ctrl=admin&act=categories');
        exit;
    }

    $allCategories = $categoryModel->getAll();
    include_once './Views/admin/category_add.php';
}

// Sửa danh mục
public function category_edit($id = null)
{
    if (!$id) {
        header('Location: admin.php?ctrl=admin&act=categories');
        exit;
    }

    $categoryModel = new Category();
    $cat = $categoryModel->getById($id);

    if (!$cat) {
        header('Location: admin.php?ctrl=admin&act=categories');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'name'       => trim($_POST['name']),
            'status'     => $_POST['status'] ?? 1
        ];
        $categoryModel->update($id, $data);
        header('Location: admin.php?ctrl=admin&act=categories');
        exit;
    }

    $allCategories = $categoryModel->getAll();
    include_once './Views/admin/category_edit.php';
}

// Xóa danh mục
public function category_delete($id = null)
{
    if ($id) {
        (new Category())->delete($id);
    }
    header('Location: admin.php?ctrl=admin&act=categories');
    exit;
}

// AJAX toggle status
public function category_toggle_status()
{
    if (isset($_POST['id'])) {
        (new Category())->toggleStatus($_POST['id']);
        echo json_encode(['success' => true]);
        exit;
    }
    echo json_encode(['success' => false]);
    exit;
}
}
?>