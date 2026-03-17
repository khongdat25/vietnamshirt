<?php include_once "./Views/admin/layout_sidebar_admin.php" ?>

<div class="page-body container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Chi tiết đơn hàng #<?= $order['id'] ?></h3>
            <span class="text-muted">Xem thông tin chi tiết và xử lý đơn hàng</span>
        </div>
        <a href="admin.php?ctrl=admin&act=order" class="btn btn-light border shadow-sm text-secondary hover-shadow">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row g-4"> <div class="col-lg-4">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 text-primary">
                        <i class="bi bi-person-circle fs-4"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">Khách hàng</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                            <span class="text-muted">Họ tên:</span>
                            <span class="fw-medium"><?= htmlspecialchars($order['name']) ?></span>
                        </li>
                        <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                            <span class="text-muted">Email:</span>
                            <span class="fw-medium"><?= htmlspecialchars($order['email']) ?></span>
                        </li>
                        <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                            <span class="text-muted">SĐT:</span>
                            <span class="fw-medium"><?= htmlspecialchars($order['phone']) ?></span>
                        </li>
                        <li class="list-group-item px-0 border-0">
                            <span class="text-muted d-block mb-1">Địa chỉ:</span>
                            <span class="fw-medium"><?= htmlspecialchars($order['address']) ?></span>
                        </li>
                        <li class="list-group-item px-0 border-0 d-flex justify-content-between">
                            <span class="text-muted">Ngày đặt:</span>
                            <span><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></span>
                        </li>
                    </ul>
                    
                    <div class="mt-3 p-3 bg-light rounded-3 border border-light">
                        <small class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Ghi chú:</small>
                        <p class="mb-0 fst-italic text-dark mt-1 small">
                            <?= !empty($order['note']) ? htmlspecialchars($order['note']) : 'Không có ghi chú nào.' ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3 text-warning">
                        <i class="bi bi-gear-fill fs-4"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">Xử lý đơn hàng</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="admin.php?ctrl=admin&act=update_order_status" method="POST">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        
                        <label class="form-label text-muted small fw-bold">TRẠNG THÁI HIỆN TẠI</label>
                        <select name="status" class="form-select form-select-lg mb-3 shadow-none border-secondary-subtle">
                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>🕒 Chờ xử lý</option>
                            <option value="confirmed" <?= $order['status'] == 'confirmed' ? 'selected' : '' ?>>📘 Đã xác nhận</option>
                            <option value="shipping" <?= $order['status'] == 'shipping' ? 'selected' : '' ?>>🚚 Đang giao</option>
                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>✅ Hoàn thành</option>
                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>❌ Đã hủy</option>
                        </select>
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">
                            Cập nhật trạng thái
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                    <h5 class="card-title mb-0 fw-bold text-primary">Danh sách sản phẩm</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th class="ps-4 py-3 fw-semibold text-uppercase small border-0">Sản phẩm</th>
                                    <th class="text-center py-3 fw-semibold text-uppercase small border-0">Số lượng</th>
                                    <th class="text-center py-3 fw-semibold text-uppercase small border-0">Phân loại</th>
                                    <th class="text-end py-3 fw-semibold text-uppercase small border-0">Đơn giá</th>
                                    <th class="text-end pe-4 py-3 fw-semibold text-uppercase small border-0">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $tam_tinh = 0;
                                foreach ($items as $item): 
                                    $total = $item['price'] * $item['quantity'];
                                    $tam_tinh += $total;
                                ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="./public/image/<?= $item['image'] ?>" 
                                                 alt="" 
                                                 class="rounded-3 shadow-sm border" 
                                                 style="width: 64px; height: 64px; object-fit: cover;">
                                            <div class="ms-3">
                                                <p class="mb-0 fw-bold text-dark"><?= htmlspecialchars($item['product_name']) ?></p>
                                                <small class="text-muted">ID: #<?= $item['product_id'] ?? 'N/A' ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><?= $item['quantity'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if(!empty($item['size'])): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">Size: <?= $item['size'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted small">Mặc định</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end text-muted"><?= number_format($item['price'], 0, ',', '.') ?> ₫</td>
                                    <td class="text-end fw-bold text-dark pe-4"><?= number_format($total, 0, ',', '.') ?> ₫</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="border-top">
                                <tr class="bg-light bg-opacity-50">
                                    <td colspan="3"></td>
                                    <td class="text-end text-muted py-2">Tạm tính:</td>
                                    <td class="text-end fw-bold py-2 pe-4"><?= number_format($tam_tinh, 0, ',', '.') ?> ₫</td>
                                </tr>
                                <tr class="bg-light bg-opacity-50">
                                    <td colspan="3"></td>
                                    <td class="text-end text-muted py-2">Phí vận chuyển:</td>
                                    <td class="text-end fw-bold py-2 pe-4"><?= number_format($order['Shipping_fee'] ?? 30000, 0, ',', '.') ?> ₫</td>
                                </tr>
                                <tr class="bg-primary bg-opacity-10">
                                    <td colspan="3"></td>
                                    <td class="text-end text-primary fw-bold fs-5 py-3">TỔNG CỘNG:</td>
                                    <td class="text-end text-primary fw-bold fs-4 py-3 pe-4">
                                        <?= number_format($order['total_amount'], 0, ',', '.') ?> ₫
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>