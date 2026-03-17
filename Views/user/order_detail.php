<main class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Chi tiết đơn hàng #<?= $order['id'] ?></h3>
            <a href="?ctrl=user&act=myOrders" class="btn btn-outline-dark">
                <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4 rounded-3">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-primary">Danh sách sản phẩm</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Sản phẩm</th>
                                        <th class="text-center">Phân loại</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end pe-4">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_product_price = 0;
                                    foreach ($items as $item): 
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total_product_price += $subtotal;
                                        // Kiểm tra ảnh, nếu không có dùng ảnh mặc định
                                        $imgSrc = !empty($item['image']) ? "Public/image/" . $item['image'] : "Public/image/default.jpg";
                                    ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($imgSrc) ?>" 
                                                     style="width: 60px; height: 60px; object-fit: cover;" 
                                                     class="rounded me-3 border"
                                                     alt="Product Image">
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($item['name']) ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if(!empty($item['size'])): ?>
                                                <span class="badge bg-light text-dark border px-2 py-1">Size: <?= $item['size'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted small">Tiêu chuẩn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end"><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                                        <td class="text-center">x<?= $item['quantity'] ?></td>
                                        <td class="text-end fw-bold text-dark pe-4"><?= number_format($subtotal, 0, ',', '.') ?>₫</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 rounded-3">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Tóm tắt thanh toán</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tiền hàng:</span>
                            <span class="fw-medium"><?= number_format($total_product_price, 0, ',', '.') ?>₫</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="fw-medium"><?= number_format($order['Shipping_fee'], 0, ',', '.') ?>₫</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-6">Tổng thanh toán:</span>
                            <span class="fw-bold text-danger fs-4">
                                <?= number_format($order['total_amount'], 0, ',', '.') ?>₫
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Thông tin nhận hàng</h5>
                        <div class="mb-2">
                            <i class="bi bi-person text-secondary me-2"></i>
                            <strong><?= htmlspecialchars($order['name']) ?></strong>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-telephone text-secondary me-2"></i>
                            <span><?= htmlspecialchars($order['phone']) ?></span>
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-geo-alt text-secondary me-2"></i>
                            <span><?= htmlspecialchars($order['address']) ?></span>
                        </div>
                        
                        <div class="alert alert-light border small mb-3">
                            <strong>Ghi chú:</strong> 
                            <?= !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có ghi chú' ?>
                        </div>

                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Trạng thái:</span> 
                                <?php
                                    $statusMap = [
                                        'pending'   => ['bg-warning text-dark', '🕒 Chờ xác nhận'],
                                        'confirmed' => ['bg-info text-white', '📘 Đã xác nhận'],
                                        'shipping'  => ['bg-primary text-white', '🚚 Đang giao hàng'],
                                        'completed' => ['bg-success text-white', '✅ Hoàn thành'],
                                        'cancelled' => ['bg-danger text-white', '❌ Đã hủy']
                                    ];
                                    $st = $statusMap[$order['status']] ?? ['bg-secondary text-white', $order['status']];
                                ?>
                                <span class="badge rounded-pill <?= $st[0] ?> py-2 px-3"><?= $st[1] ?></span>
                            </div>
                            <div class="text-muted small mt-2 text-end">
                                Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>