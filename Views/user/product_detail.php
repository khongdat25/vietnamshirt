<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .btn-size { min-width: 50px; margin: 5px 3px; }
        .btn-size.active { background:#000; color:#fff; border-color:#000; }
        .quantity-wrapper { border:1px solid #ddd; border-radius:8px; overflow:hidden; display:inline-flex; }
        .quantity-btn { width:40px; height:40px; background:#f8f9fa; border:none; font-size:1.2rem; }
        
        /* Style cho nút tim */
        .btn-love { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; transition: 0.3s; }
        .btn-love:hover { background-color: #f8d7da; border-color: #dc3545; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    
    <div class="row g-5">
        <div class="col-md-5 text-center">
            <?php 
                $hinh = "Public/image/" . $product['image'];
                if (strpos($product['image'], 'image/') === 0) {
                    $hinh = "Public/" . $product['image'];
                }
            ?>
            <img src="<?= htmlspecialchars($hinh) ?>" 
                 class="img-fluid rounded shadow" style="max-height:520px; object-fit:cover;">
        </div>

        <div class="col-md-7">
            <h1 class="fw-bold"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-muted">Đã bán: <?= $product['sold'] ?? 0 ?> sản phẩm</p>
            
            <h2 class="text-danger fw-bold"><?= number_format($gia,0,',','.') ?> ₫</h2>

            <form method="post" class="mt-4">
                <input type="hidden" name="old_size" value="<?= $size ?>">

                <div class="my-4">
                    <strong>Kích thước:</strong><br>
                    <?php if(!empty($ds_size)): ?>
                        <?php foreach($ds_size as $s): ?>
                            <button type="submit" name="size" value="<?= $s ?>"
                                    class="btn btn-outline-dark btn-size <?= ($s==$size)?'active':'' ?>">
                                <?= $s ?>
                            </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-danger">Sản phẩm đang tạm hết hàng các biến thể.</p>
                    <?php endif; ?>
                </div>

                <div class="my-4">
                    <strong>Số lượng:</strong>
                    <div class="quantity-wrapper">
                        <button type="submit" name="giam" class="quantity-btn" <?= $sl<=1?'disabled':'' ?>>–</button>
                        
                        <input type="text" name="sl" value="<?= $sl ?>" class="form-control text-center fw-bold" style="width:70px;border:none;" readonly>
                        
                        <button type="submit" name="tang" class="quantity-btn" <?= $sl>=$stock?'disabled':'' ?>>+</button>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Kho: <strong class="text-danger"><?= $stock ?></strong> sản phẩm (Size <?= $size ?>)
                    </small>
                </div>
            </form>

            <form method="post" action="?ctrl=cart&act=add&id=<?=$product['id']?> class="mt-4">
                <input type="hidden" name="size"  value="<?= $size ?>">
                <input type="hidden" name="price" value="<?= $gia ?>">
                <input type="hidden" name="sl"    value="<?= $sl ?>">

                <div class="mt-5 d-flex align-items-center gap-3">
                    <form action="?ctrl=cart&act=add&id=<?= $product['id'] ?>" method="POST" class="d-flex flex-column gap-3">
    
                    <div class="d-flex gap-2">
                        <button type="submit" name="add_to_cart" value="1" class="btn btn-outline-primary btn-lg flex-grow-1" <?= ($stock <= 0) ? 'disabled' : '' ?>>
                            <i class="bi bi-cart-plus me-2"></i> Thêm giỏ hàng
                        </button>
                        
                        <button type="submit" name="buy_now" value="1" class="btn btn-danger btn-lg flex-grow-1 shadow-sm" <?= ($stock <= 0) ? 'disabled' : '' ?>>
                            <i class="bi bi-lightning-charge-fill me-1"></i> Mua ngay
                        </button>
                    </div>
                    
                </form>

                    <a href="?ctrl=product&act=toggle_favorite&id=<?= $product['id'] ?>" 
                       class="btn btn-outline-danger btn-love rounded-3" 
                       title="Yêu thích">
                        <?php if(isset($isFavorite) && $isFavorite): ?>
                            <i class="bi bi-heart-fill text-danger"></i>
                        <?php else: ?>
                            <i class="bi bi-heart"></i>
                        <?php endif; ?>
                    </a>
                </div>
            </form>
        </div>
    </div> 

    <div class="row mt-5 pt-4 border-top">
        <div class="col-12 mb-4">
            <h3 class="fw-bold text-uppercase">Đánh giá sản phẩm</h3>
        </div>
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card shadow-sm border-0 bg-white">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">Viết đánh giá của bạn</h5>
                    <?php if(isset($_SESSION['user'])): ?>
                        <?php if(isset($error)) echo "<div class='alert alert-danger py-2'>$error</div>"; ?>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Chọn mức độ hài lòng:</label>
                                <select name="rating" class="form-select form-select-lg">
                                    <option value="5">⭐⭐⭐⭐⭐ - Tuyệt vời</option>
                                    <option value="4">⭐⭐⭐⭐ - Tốt</option>
                                    <option value="3">⭐⭐⭐ - Bình thường</option>
                                    <option value="2">⭐⭐ - Tệ</option>
                                    <option value="1">⭐ - Rất tệ</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <textarea name="content" class="form-control" rows="5" placeholder="Chia sẻ cảm nhận..." required></textarea>
                            </div>
                            <button type="submit" name="submit_comment" class="btn btn-primary w-100 btn-lg">Gửi đánh giá</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">Vui lòng đăng nhập để đánh giá.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-7">
            <div class="bg-white p-4 rounded shadow-sm">
                <h5 class="fw-bold mb-4">Các bài đánh giá gần đây</h5>
                <?php if (!empty($list_comments)): ?>
                    <?php foreach($list_comments as $cmt): ?>
                        <div class="d-flex mb-4 border-bottom pb-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 20px;"><?= strtoupper(substr($cmt['user_name'], 0, 1)) ?></div>
                            </div>
                            <div class="ms-3 w-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0"><?= htmlspecialchars($cmt['user_name']) ?></h6>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($cmt['created_at'])) ?></small>
                                </div>
                                <div class="text-warning mb-1">
                                    <?php for($i=1; $i<=5; $i++) echo ($i <= $cmt['rating']) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>'; ?>
                                </div>
                                <p class="mb-0 text-dark"><?= nl2br(htmlspecialchars($cmt['content'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Chưa có đánh giá nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12 mb-4"><h3 class="fw-bold text-uppercase border-bottom pb-2">Sản phẩm tương tự</h3></div>
        <?php if (!empty($related_products)): ?>
            <?php foreach($related_products as $rp): ?>
                <?php 
                    // XỬ LÝ ẢNH TƯƠNG TỰ
                    $hinhRP = "Public/image/" . $rp['image'];
                    if (strpos($rp['image'], 'image/') === 0) {
                        $hinhRP = "Public/" . $rp['image'];
                    }
                ?>
                <div class="col-md-3 col-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="?ctrl=product&act=detail&id=<?= $rp['id'] ?>">
                            <img src="<?= htmlspecialchars($hinhRP) ?>" class="card-img-top" alt="<?= htmlspecialchars($rp['name']) ?>">
                        </a>
                        <div class="card-body text-center">
                            <h6 class="card-title"><a href="?ctrl=product&act=detail&id=<?= $rp['id'] ?>" class="text-decoration-none text-dark fw-bold"><?= htmlspecialchars($rp['name']) ?></a></h6>
                            <p class="card-text text-danger fw-bold">
                                <?= !empty($rp['price']) ? number_format($rp['price'],0,',','.') . ' ₫' : 'Liên hệ' ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">Không có sản phẩm tương tự.</p>
        <?php endif; ?>
    </div>

    <div class="row mt-4 pt-4 border-top">
        <div class="col-12 mb-4"><h3 class="fw-bold text-uppercase">Hỏi đáp về sản phẩm</h3></div>
        <div class="col-lg-4 mb-4">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Đặt câu hỏi</h5>
                    <?php if(isset($_SESSION['user'])): ?>
                        <?php if(isset($error_qa)) echo "<div class='alert alert-danger py-2'>$error_qa</div>"; ?>
                        <form method="post">
                            <div class="mb-3"><textarea name="question_content" class="form-control" rows="4" placeholder="Bạn cần tư vấn gì?" required></textarea></div>
                            <button type="submit" name="submit_question" class="btn btn-dark w-100">Gửi câu hỏi</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">Vui lòng đăng nhập để đặt câu hỏi.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <?php if (!empty($list_questions)): ?>
                <?php foreach($list_questions as $qa): ?>
                    <div class="mb-4 p-3 bg-white rounded shadow-sm border">
                        <div class="d-flex justify-content-between">
                            <strong class="text-primary"><i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($qa['user_name']) ?></strong>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($qa['created_at'])) ?></small>
                        </div>
                        <p class="mt-2 mb-2 fw-medium">Q: <?= nl2br(htmlspecialchars($qa['content'])) ?></p>
                        <?php if (!empty($qa['reply'])): ?>
                            <div class="ms-4 p-3 bg-light rounded border-start border-4 border-success">
                                <strong class="text-success">Shop phản hồi:</strong>
                                <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($qa['reply'])) ?></p>
                            </div>
                        <?php else: ?>
                            <small class="text-muted ms-2 fst-italic">- Chờ shop trả lời -</small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4 border rounded bg-white"><p class="text-muted mb-0">Chưa có câu hỏi nào.</p></div>
            <?php endif; ?>
        </div>
    </div>
</div> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>