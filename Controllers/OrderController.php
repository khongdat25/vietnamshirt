<?php
include_once "./Models/Product.php";
include_once "./Models/Database.php";

class OrderController
{
    private $db;
    private $productModel;
    
    
    public function __construct()
    {
        $this->db = new Database();
        $this->productModel = new Product();
    }

    // Trang checkout
public function checkout()
    {
        if (empty($_SESSION) || !isset($_SESSION['user'])) {
            $_SESSION['error_login'] = "Vui lòng đăng nhập để thanh toán!";
            header("Location: ?ctrl=user&act=login"); exit;
        }

        if (empty($_SESSION['cart'])) {
            header("Location: ?ctrl=cart&act=index"); exit;
        }

        $total = 0;
        $items = [];
        
        foreach ($_SESSION['cart'] as $key => $qty) {
            $parts = explode('_', $key);
            if(count($parts) == 2) {
                $pro_id = $parts[0];
                $pro_size = $parts[1];

                $p = $this->productModel->getById($pro_id);
                $v = $this->productModel->getVariant($pro_id, $pro_size);

                if ($p && $v) {
                    $subtotal = $v['price'] * $qty;
                    $total += $subtotal;
                    $items[] = [
                        'product'  => $p,
                        'variant'  => $v,
                        'size'     => $pro_size,
                        'quantity' => $qty,
                        'subtotal' => $subtotal
                    ];
                }
            }
        }

        $shipping_fee = 30000;
        $final_total = $total + $shipping_fee;

        include_once "./Views/user/order_checkout.php";
    }

    // Xử lý đặt hàng COD
public function placeOrder()
    {
        if (!isset($_SESSION['user']) || empty($_SESSION['cart'])) {
            header("Location: ?ctrl=page&act=home"); exit;
        }
        $name    = $_SESSION['user']['name'];
        $email   = $_SESSION['user']['email'] ?? '';
        $phone   = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $note    = trim($_POST['note'] ?? '');

        if (empty($phone) || empty($address)) {
            $_SESSION['error'] = "Vui lòng nhập thông tin!";
            header("Location: ?ctrl=order&act=checkout"); exit;
        }

        $total = 0;
        foreach ($_SESSION['cart'] as $key => $qty) {
            
            $parts = explode('_', $key);
            if(count($parts) == 2) {
                $v = $this->productModel->getVariant($parts[0], $parts[1]);
                if($v) $total += $v['price'] * $qty;
            }
        }
        $shipping_fee = 30000;
        $total_amount = $total + $shipping_fee;

        $sql = "INSERT INTO orders (user_id, name, email, phone, address, note, total_amount, Shipping_fee, pay_method_id, payment_status, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, 'unpaid', 'pending', NOW())";
        $order_id = $this->db->insert($sql, $_SESSION['user']['id'], $name, $email, $phone, $address, $note, $total_amount, $shipping_fee);

        if ($order_id) {
                foreach ($_SESSION['cart'] as $key => $quantity) {
                    $parts = explode('_', $key);
                    if(count($parts) == 2) {
                        $pro_id = $parts[0];
                        $pro_size = $parts[1];
                        
                        $variant = $this->productModel->getVariant($pro_id, $pro_size);
                        
                        if ($variant) {
                            $price = $variant['price'];
                            $variant_id = $variant['id'];
                            $product_id = $parts[0]; 

                            $sql_item = "INSERT INTO order_item (order_id, variant_id, quantity, price, product_id) 
                                        VALUES (?, ?, ?, ?, ?)";
                                        
                            $this->db->insert($sql_item, $order_id, $variant_id, $quantity, $price, $product_id);
                        }
                    }
                }

            unset($_SESSION['cart']);
            $_SESSION['success'] = "Đặt hàng thành công! Mã đơn: #$order_id";
            header("Location: ?ctrl=order&act=success&id=$order_id");
            exit;
        } else {
            $_SESSION['error'] = "Đặt hàng thất bại!";
            header("Location: ?ctrl=order&act=checkout");
            exit;
        }
    }
    public function detail() {
    if (!isset($_SESSION['user'])) {
        header("Location: ?ctrl=user&act=login");
        exit;
    }

    if (!isset($_GET['id'])) {
        $_SESSION['error'] = "Không tìm thấy đơn hàng!";
        header("Location: ?ctrl=user&act=myOrders");
        exit;
    }
    
    $id = $_GET['id'];
    $user_id = $_SESSION['user']['id'];

    include_once "./Models/User.php"; 
    $userModel = new User();
    
    $order = $userModel->getOrderById($id);

    if (!$order || $order['user_id'] != $user_id) {
        $_SESSION['error'] = "Bạn không có quyền xem đơn hàng này.";
        header("Location: ?ctrl=user&act=myOrders");
        exit;
    }

    $items = $userModel->getOrderItems($id);

    include_once "./Views/user/order_detail.php";
    }
    // Trang cảm ơn
    public function success()
    {
        $order_id = $_GET['id'] ?? 0;
        include_once "./Views/user/order_success.php";
    }

    // // lịch sử đơn hàng
    // function getHistory()
    // {
    // $user_id = $_SESSION['user_id'];
    // $order = $this->model->getHistory($user_id);
    // include_once "./Views/";
    // }
}
?>