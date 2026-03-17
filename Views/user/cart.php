<div class="bg-white border-bottom py-3 mb-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="?ctrl=page&act=home" class="text-decoration-none text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-0 mb-4">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5 bg-white shadow-sm" style="min-height: 400px; display: flex; flex-direction: column; justify-content: center;">
            <i class="fas fa-shopping-bag fa-4x text-muted mb-4" style="opacity: 0.3;"></i>
            <h4 class="fw-light">Giỏ hàng của bạn đang trống</h4>
            <p class="text-muted mb-4">Hãy chọn những món đồ yêu thích để lấp đầy giỏ hàng nhé!</p>
            <div>
                <a href="?ctrl=page&act=home" class="btn btn-dark btn-lg rounded-0 px-5">TIẾP TỤC MUA SẮM</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold">Sản phẩm (<?= $totalItems ?>)</h5>
                    <a href="?ctrl=cart&act=clear" class="text-muted text-decoration-none small" onclick="return confirm('Bạn chắc chắn muốn xóa hết giỏ hàng?')">
                        <i class="fas fa-trash-alt me-1"></i> Xóa tất cả
                    </a>
                </div>

                <?php foreach ($cartItems as $item): ?>
                <div class="card cart-item mb-3 p-3">
                    <div class="row align-items-center">
                        <div class="col-3 col-md-2">
                            <img src="Public/image/<?= htmlspecialchars($item['image'] ?? 'default.jpg') ?>" 
                                    class="product-image" 
                                    onerror="this.src='Public/image/default.jpg'">
                        </div>
                        
                        <div class="col-9 col-md-4">
                            <h6 class="mb-1 text-dark fw-bold"><?= htmlspecialchars($item['name']) ?></h6>
                            <p class="text-muted small mb-0">Mã SP: #<?= $item['id'] ?></p>
                            <p class="mb-0 mt-1">
                                Size: <span class="badge bg-light text-dark border"><?= $item['size'] ?></span>
                            </p>
                        </div>

                        <div class="col-6 col-md-2 mt-3 mt-md-0 text-md-center">
                            <span class="text-danger fw-bold d-block"><?= number_format($item['price'], 0, ',', '.') ?>đ</span>
                        </div>

                        <div class="col-6 col-md-3 mt-3 mt-md-0">
                            <div class="quantity-group mx-auto mx-md-0">
                                <a href="?ctrl=cart&act=decrease&id=<?= $item['key'] ?>" class="quantity-btn">
                                    <i class="fas fa-minus small"></i>
                                </a>
                                
                                <input type="text" value="<?= $item['qty'] ?>" class="quantity-input" readonly>
                                
                                <a href="?ctrl=cart&act=increase&id=<?= $item['key'] ?>" class="quantity-btn">
                                    <i class="fas fa-plus small"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-12 col-md-1 mt-2 mt-md-0 text-end text-md-center">
                            <a href="?ctrl=cart&act=remove&id=<?= $item['key'] ?>" 
                                class="text-secondary opacity-50 hover-danger" 
                                onclick="return confirm('Xóa sản phẩm này?')"
                                title="Xóa sản phẩm">
                                <i class="fas fa-times fa-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="col-lg-4">
                <div class="cart-summary p-4">
                    <h5 class="fw-bold mb-4">Tổng giỏ hàng</h5>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-bold"><?= number_format($totalPrice, 0, ',', '.') ?>đ</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span><?= number_format($shippingFee, 0, ',', '.') ?>đ</span>
                    </div>
                    
                    <hr style="border-style: dashed; opacity: 0.3;">
                    
                    <div class="d-flex justify-content-between mb-4 align-items-center">
                        <span class="fw-bold fs-5">Tổng cộng:</span>
                        <span class="text-danger fw-bold fs-4"><?= number_format($totalPrice + $shippingFee, 0, ',', '.') ?>đ</span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted text-uppercase fw-bold">Mã giảm giá</label>
                        <div class="input-group">
                            <input type="text" class="form-control rounded-0" placeholder="Nhập mã voucher">
                            <button class="btn btn-dark rounded-0" type="button">Áp dụng</button>
                        </div>
                    </div>

                    <a href="?ctrl=order&act=checkout" class="btn btn-danger w-100 py-3 fw-bold rounded-0 mb-3 shadow-sm text-uppercase">
                        Tiến hành thanh toán
                    </a>
                    
                    <a href="?ctrl=page&act=home" class="btn btn-outline-secondary w-100 rounded-0">
                        <i class="fas fa-arrow-left me-2"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    /* CSS Giữ nguyên như cũ nhưng bỏ phần body background vì layout chính đã có */
    .cart-item { transition: all 0.3s ease; border: 1px solid #eee; border-radius: 0; }
    .cart-item:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.05); background-color: #fff; border-color: #ddd; }
    .product-image { width: 100px; height: 100px; object-fit: cover; border-radius: 4px; }
    .quantity-group { display: flex; align-items: center; border: 1px solid #dee2e6; width: fit-content; }
    .quantity-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: #fff; color: #333; text-decoration: none; transition: 0.2s; }
    .quantity-btn:hover { background: #f1f1f1; color: #000; }
    .quantity-input { width: 40px; text-align: center; border: none; border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6; height: 32px; outline: none; background: #fff; }
    .cart-summary { background: #fff; position: sticky; top: 20px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-radius: 0; }
</style>