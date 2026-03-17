<?php include_once "./Views/admin/layout_sidebar_admin.php"?>
<div class="page-body">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-primary"><i class="bi bi-cart-check"></i> Quản lý đơn hàng</h2>
        <div>
            <span class="me-3">Đơn hôm nay: <strong class="text-success"><?= $ordersToday ?? 0 ?></strong></span>
            <span>Doanh thu tháng: <strong class="text-danger"><?= number_format($revenueMonth ?? 0) ?> ₫</strong></span>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET">
                <input type="hidden" name="ctrl" value="admin">
                <input type="hidden" name="act" value="order">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3">
                        <select name="status" class="form-select form-select-lg">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" <?= ($status??'') == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                            <option value="confirmed" <?= ($status??'') == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="cancelled" <?= ($status??'') == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                            <option value="shipping" <?= ($status??'') == 'shipping' ? 'selected' : '' ?>>Đang giao</option>
                            <option value="completed" <?= ($status??'') == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                            
                        </select>
                    </div>
                    <div class="col-lg-5">
                        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword??'') ?>" class="form-control form-control-lg" placeholder="Tìm mã đơn, tên khách, SĐT...">
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100">Tìm kiếm</button>
                    </div>
                    <div class="col-lg-2">
                         <a href="admin.php?ctrl=admin&act=order" class="btn btn-outline-secondary btn-lg w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách đơn hàng</h5>
            <small>Tổng số: <?= $totalOrders ?? 0 ?> đơn hàng</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái & Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($orders) && count($orders) > 0): ?>
                        <?php 
                        // Định nghĩa thứ tự trạng thái tuyến tính và nhãn
                        $statusOrder = ['pending', 'confirmed', 'shipping', 'completed'];
                        $statusLabels = [
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Đã xác nhận',
                            'shipping' => 'Đang giao',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Hủy đơn'
                        ];
                        ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?= $order['id'] ?></strong></td>
                                <td>
                                    <?= htmlspecialchars($order['name']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($order['phone']) ?></small>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($order['created_at'])) ?><br>
                                    <small><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                </td>
                                <td class="text-danger fw-bold">
                                    <?= number_format($order['total_amount'], 0, ',', '.') ?> ₫
                                </td>
                                <td>
                                    <?php if ($order['payment_status'] == 'paid'): ?>
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Chưa thanh toán</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="admin.php?ctrl=admin&act=order_detail&order_id=<?= $order['id'] ?>" class="btn btn-info btn-sm text-white" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <?php
                                        $currentStatus = $order['status'];
                                        if ($currentStatus === 'completed' || $currentStatus === 'cancelled') {
                                            // Nếu đã hoàn thành hoặc hủy, chỉ hiển thị badge trạng thái, không cho thay đổi
                                            $badgeClass = ($currentStatus === 'completed') ? 'bg-success' : 'bg-danger';
                                            echo '<span class="badge ' . $badgeClass . '">' . $statusLabels[$currentStatus] . '</span>';
                                        } else {
                                            // Tìm vị trí hiện tại trong thứ tự tuyến tính
                                            $currentIndex = array_search($currentStatus, $statusOrder);
                                            // Lấy các trạng thái từ hiện tại trở đi
                                            $availableStatuses = array_slice($statusOrder, $currentIndex);
                                            // Thêm 'cancelled' nếu chưa hủy
                                            if ($currentStatus !== 'cancelled') {
                                                $availableStatuses[] = 'cancelled';
                                            }
                                        ?>
                                            <form action="admin.php?ctrl=admin&act=update_order_status" method="POST" class="d-flex mb-0">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <input type="hidden" name="current_filter_status" value="<?= htmlspecialchars($status ?? '') ?>">
                                                <input type="hidden" name="current_keyword" value="<?= htmlspecialchars($keyword ?? '') ?>">
                                                <input type="hidden" name="current_page" value="<?= $page ?? 1 ?>">
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 130px;">
                                                    <?php foreach ($availableStatuses as $stat): ?>
                                                        <option value="<?= $stat ?>" <?= $currentStatus == $stat ? 'selected' : '' ?>><?= $statusLabels[$stat] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </form>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">Không tìm thấy đơn hàng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light d-flex justify-content-center">
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="admin.php?ctrl=admin&act=order&page=<?= $page - 1 ?>&keyword=<?= $keyword ?>&status=<?= $status ?>">Trước</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="admin.php?ctrl=admin&act=order&page=<?= $i ?>&keyword=<?= $keyword ?>&status=<?= $status ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < ($totalPages ?? 1)): ?>
                        <li class="page-item">
                            <a class="page-link" href="admin.php?ctrl=admin&act=order&page=<?= $page + 1 ?>&keyword=<?= $keyword ?>&status=<?= $status ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>