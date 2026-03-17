<main class="py-5 bg-light">
        <div class="container">
            <div class="row">
                
                <?php include_once "./Views/user/Layout_user_sidebar.php" ?>

                <div class="col-lg-9 col-md-8">
                    <div class="bg-white p-4 shadow-sm rounded mb-4">
                        <h4 class="mb-4 fw-bold border-bottom pb-2">
                            <i class="fas fa-heart text-danger me-2"></i>Sản phẩm yêu thích
                        </h4>

                        <?php if (!empty($favorites)): ?>
                            <div class="row g-4">
                                <?php foreach($favorites as $pro): ?>
                                    <div class="col-md-4 col-6">
                                        <div class="card h-100 border-0 shadow-sm rounded overflow-hidden position-relative group-action">
                                            
                                            <a href="?ctrl=product&act=toggle_favorite&id=<?= $pro['id'] ?>" 
                                               onclick="return confirm('Xóa khỏi yêu thích?')"
                                               class="position-absolute top-0 end-0 m-2 btn btn-sm btn-light rounded-circle shadow-sm text-danger"
                                               title="Xóa">
                                                <i class="fas fa-times"></i>
                                            </a>

                                            <a href="?ctrl=product&act=detail&id=<?= $pro['id'] ?>">
                                                <img src="Public/image/<?= htmlspecialchars($pro['image']) ?>" 
                                                     class="card-img-top" 
                                                     alt="<?= htmlspecialchars($pro['name']) ?>"
                                                     style="height: 200px; object-fit: contain;">
                                            </a>

                                            <div class="card-body text-center d-flex flex-column">
                                                <h6 class="card-title text-truncate">
                                                    <a href="?ctrl=product&act=detail&id=<?= $pro['id'] ?>" class="text-decoration-none text-dark fw-bold">
                                                        <?= htmlspecialchars($pro['name']) ?>
                                                    </a>
                                                </h6>
                                                <p class="card-text text-danger fw-bold mb-3">
                                                    <?= number_format($pro['price'], 0, ',', '.') ?> ₫
                                                </p>
                                                
                                                <a href="?ctrl=product&act=detail&id=<?= $pro['id'] ?>" class="btn btn-outline-dark btn-sm rounded-pill mt-auto">
                                                    Xem chi tiết
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-heart-broken text-muted fa-4x mb-3"></i>
                                <p class="text-muted">Bạn chưa có sản phẩm yêu thích nào.</p>
                                <a href="?ctrl=product&act=list" class="btn btn-dark px-4">Dạo cửa hàng ngay</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </div>
        </div>
    </main>