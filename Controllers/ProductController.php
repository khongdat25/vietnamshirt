<?php
include_once "Models/Product.php";
include_once "Models/Category.php";

class ProductController {
    private $model;

    public function __construct() {
        $this->model = new Product();
    }

    // --- CHI TIẾT SẢN PHẨM ---
    function detail($id) {
        // 1. Lấy thông tin chung sản phẩm
        $product = $this->model->getById($id);
        if (!$product) { echo "Sản phẩm không tồn tại"; return; }

        // 2. Lấy danh sách biến thể
        $variants = $this->model->getVariants($id);
        
        // Nếu chưa có biến thể nào, tạo dữ liệu ảo để không bị lỗi
        if (empty($variants)) {
            $variants = [['size'=>'S', 'price'=>0, 'stock'=>0]];
        }

        // 3. Xử lý danh sách Size (Sắp xếp theo thứ tự S, M, L...)
        $thu_tu = ['S','M','L','XL','XXL'];
        $ds_size = [];
        
        // Lọc ra các size thực tế đang có trong database
        foreach ($thu_tu as $s) {
            foreach ($variants as $v) {
                if ($v['size'] == $s) { 
                    $ds_size[] = $s; 
                    break; 
                }
            }
        }
        // Thêm các size lạ (nếu có) mà không nằm trong danh sách chuẩn S,M,L...
        foreach ($variants as $v) {
            if (!in_array($v['size'], $ds_size)) {
                $ds_size[] = $v['size'];
            }
        }
        $ds_size = array_unique($ds_size); // Đảm bảo không trùng lặp

        // 4. Xác định Size đang chọn (Ưu tiên: Size khách bấm > Size cũ > Size S > Size đầu tiên)
        $size_default = in_array('S', $ds_size) ? 'S' : ($ds_size[0] ?? '');
        $size = $_POST['size'] ?? ($_POST['old_size'] ?? $size_default);

        // 5. Xác định Giá và Kho theo Size đã chọn
        $gia   = 0;
        $stock = 0;
        
        // Tìm biến thể khớp với Size
        $found = false;
        foreach ($variants as $v) {
            if ($v['size'] == $size) {
                $gia = $v['price'];
                $stock = $v['stock'];
                $found = true;
                break;
            }
        }
        // Nếu không tìm thấy (trường hợp lỗi), lấy giá của biến thể đầu tiên
        if (!$found && !empty($variants)) {
            $gia = $variants[0]['price'];
            $stock = $variants[0]['stock'];
            $size = $variants[0]['size'];
        }

        // 6. Xử lý số lượng (Tăng/Giảm)
        $sl = 1;
        if (isset($_POST['sl'])) $sl = (int)$_POST['sl'];
        if (isset($_POST['tang'])) $sl++;
        if (isset($_POST['giam'])) $sl--;
        
        // Validate số lượng
        if ($sl < 1) $sl = 1;
        if ($stock > 0 && $sl > $stock) $sl = $stock;

        // --- [GIỮ NGUYÊN] CÁC CHỨC NĂNG PHỤ (TIM, COMMENT, QA) ---
        $isFavorite = false;
        if (isset($_SESSION['user'])) {
            $isFavorite = $this->model->checkFavorite($_SESSION['user']['id'], $id);
        }

        // Xử lý Comment & Q&A
        if (isset($_POST['submit_comment']) && isset($_SESSION['user'])) {
            $this->model->addComment($_SESSION['user']['id'], $id, $_POST['content'], $_POST['rating']);
            header("Location: ?ctrl=product&act=detail&id=$id"); exit;
        }
        if (isset($_POST['submit_question']) && isset($_SESSION['user'])) {
            $this->model->addQuestion($_SESSION['user']['id'], $id, $_POST['question_content']);
            header("Location: ?ctrl=product&act=detail&id=$id"); exit;
        }

        $list_comments    = $this->model->getComments($id);
        $list_questions   = $this->model->getQuestions($id);
        
        // Lấy sản phẩm tương tự
        $related_products = [];
        if (isset($product['category_id'])) {
            $related_products = $this->model->getRelatedProducts($product['category_id'], $id);
        }

        include_once "./Views/user/product_detail.php";
    }

    // --- [GIỮ NGUYÊN] XỬ LÝ BẤM TIM ---
    function toggle_favorite() {
        if (!isset($_SESSION['user'])) {
            header("Location: ?ctrl=user&act=login"); exit;
        }
        $product_id = $_GET['id'];
        $this->model->toggleFavorite($_SESSION['user']['id'], $product_id);
        
        if(isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: ?ctrl=product&act=detail&id=$product_id");
        }
        exit;
    }

    // --- [GIỮ NGUYÊN] DANH SÁCH YÊU THÍCH ---
    function favorite_list() {
        if (!isset($_SESSION['user'])) {
            header("Location: ?ctrl=user&act=login"); exit;
        }
        $favorites = $this->model->getFavoritesByUser($_SESSION['user']['id']);
        include_once "./Views/user/favorite_list.php";
    }

    // --- [GIỮ NGUYÊN] DANH SÁCH SẢN PHẨM ---
    function list() {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();
        $options = [ 'keyword' => $_GET['keyword'] ?? null, 'categories' => $_GET['category'] ?? [], 'sort' => $_GET['sort'] ?? 'newest', 'min_price' => null, 'max_price' => null ];
        
        // Sửa lại logic lọc giá một chút cho an toàn
        if (!empty($_GET['priceRange'])) {
             // Logic lọc giá của bạn (nếu có)
        }
        
        $productList = $this->model->getFilteredProducts($options);
        include_once "./Views/user/product_list.php";
    }

    function cart() {
        if (isset($_GET['action']) && $_GET['action'] == 'add') {
            $id = intval($_GET['id']);
            $size = $_POST['size'] ?? '';
            $qty = intval($_POST['sl'] ?? 1);

            if ($id > 0 && !empty($size)) {
                $key = $id . '_' . $size;

                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                if (isset($_SESSION['cart'][$key])) {
                    $_SESSION['cart'][$key] += $qty;
                } else {
                    $_SESSION['cart'][$key] = $qty;
                }
                header("Location: ?ctrl=product&act=cart");
                exit;
            }
        }
        
        $cartItems = [];
        $totalPrice = 0;
        $totalItems = 0;

        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $qty) {
                $parts = explode('_', $key);
                if(count($parts) == 2) {
                    $pro_id = $parts[0];
                    $pro_size = $parts[1];

                    $product = $this->model->getById($pro_id);
                    $variant = $this->model->getVariant($pro_id, $pro_size);

                    if ($product && $variant && isset($variant['price'])) {
                        $cartItems[] = [
                            'key'      => $key,
                            'id'       => $product['id'],
                            'name'     => $product['name'],
                            'image'    => $product['image'],
                            'size'     => $pro_size,
                            'price'    => $variant['price'],
                            'qty' => $qty,
                            'total'    => $variant['price'] * $qty
                        ];
                        $totalPrice += ($variant['price'] * $qty);
                        $totalItems += $qty;
                    }
                }
            }
        }
        
        $shippingFee = 30000;
        
        include_once "./Views/user/cart.php";
    }
}
?>