<?php
include_once "./Models/Product.php";

class CartController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index()
    {
        $cartItems = [];
        $totalPrice = 0;
        $totalItems = 0;

        if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $quantity) {
                $parts = explode('_', $key);
                
                if (count($parts) == 2) {
                    $pro_id = $parts[0];
                    $pro_size = $parts[1];

                    $product = $this->productModel->getById($pro_id);
                    $variant = $this->productModel->getVariant($pro_id, $pro_size);

                    if ($product && $variant) {
                        $item = [
                            'key'      => $key,         
                            'id'       => $product['id'],
                            'name'     => $product['name'],
                            'image'    => $product['image'],
                            'size'     => $pro_size,
                            'price'    => $variant['price'], 
                            'qty'      => $quantity,
                            'subtotal' => $variant['price'] * $quantity
                        ];
                        
                        $cartItems[] = $item;
                        $totalPrice += $item['subtotal'];
                        $totalItems += $quantity;
                    }
                }
            }
        }
        
        $shippingFee = 30000;
        
        include_once "./Views/user/cart.php";
    }

    public function add()
    {
        if (isset($_GET['id'])) {
            $pro_id = (int)$_GET['id'];
            $size = $_POST['size'] ?? 'S';
            $qty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            $newKey = $pro_id . '_' . $size;
            
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            if (isset($_SESSION['cart'][$newKey])) {
                $_SESSION['cart'][$newKey] += $qty;
            } else {
                $_SESSION['cart'][$newKey] = $qty;
            }
            
            if (isset($_POST['buy_now'])) {
                header("Location: ?ctrl=order&act=checkout");
                exit;
            } 
            $_SESSION['success'] = "Đã thêm sản phẩm vào giỏ!";
            header("Location: ?ctrl=cart&act=index");
            exit;
        }
        
        header("Location: ?ctrl=page&act=home");
        exit;
    }

    public function increase()
    {
        if (isset($_GET['id'])) {
            $key = $_GET['id'];
            if (isset($_SESSION['cart'][$key])) {
                $_SESSION['cart'][$key]++;
            }
        }
        header("Location: ?ctrl=cart&act=index");
        exit;
    }

    public function decrease()
    {
        if (isset($_GET['id'])) {
            $key = $_GET['id'];
            if (isset($_SESSION['cart'][$key])) {
                $_SESSION['cart'][$key]--;
                if ($_SESSION['cart'][$key] <= 0) {
                    unset($_SESSION['cart'][$key]);
                }
            }
        }
        header("Location: ?ctrl=cart&act=index");
        exit;
    }

    public function remove()
    {
        if (isset($_GET['id'])) {
            $key = $_GET['id'];
            if (isset($_SESSION['cart'][$key])) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['success'] = "Đã xóa sản phẩm!";
            }
        }
        header("Location: ?ctrl=cart&act=index");
        exit;
    }

    public function clear()
    {
        $_SESSION['cart'] = [];
        $_SESSION['success'] = "Đã làm trống giỏ hàng!";
        header("Location: ?ctrl=cart&act=index");
        exit;
    }
}
?>