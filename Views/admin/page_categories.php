<?php include_once "./Views/admin/layout_sidebar_admin.php"; ?>

<div class="page-body">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-primary"><i class="bi bi-grid-3x3-gap"></i> Quản lý danh mục</h2>
        <a href="admin.php?ctrl=admin&act=category_add" class="btn btn-success btn-lg shadow">
            <i class="bi bi-plus-lg"></i> Thêm danh mục mới
        </a>
    </div>

    <!-- Tìm kiếm -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <input type="hidden" name="ctrl" value="admin">
                <input type="hidden" name="act" value="categories">
                <div class="col-lg-5">
                    <input type="text" name="keyword" class="form-control form-control-lg" 
                           placeholder="Tìm kiếm tên danh mục..." value="<?= htmlspecialchars($keyword ?? '') ?>">
                </div>
                <div class="col-lg-3">
                    <select name="status" class="form-select form-select-lg">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" <?= ($status_filter ?? '') === '1' ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= ($status_filter ?? '') === '0' ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
                <div class="col-lg-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-lg me-2">Tìm kiếm</button>
                    <a href="admin.php?ctrl=admin&act=categories" class="btn btn-outline-secondary btn-lg">Làm mới</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Danh sách danh mục (<?= $total ?>)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="80">#</th>
                        <th>Tên danh mục</th>
                        <th>Số sản phẩm</th>
                        <th>Trạng thái</th>
                        <th width="140" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $stt = $offset + 1;
                    foreach ($displayCategories as $cat):
                    ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                        <td>
                            <span class="badge <?= $cat['product_count'] > 0 ? 'bg-success' : 'bg-secondary' ?> text-white">
                                <?= $cat['product_count'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input toggle-status" type="checkbox" 
                                       <?= $cat['status'] ? 'checked' : '' ?> data-id="<?= $cat['id'] ?>">
                            </div>
                            <span class="badge <?= $cat['status'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $cat['status'] ? 'Hiển thị' : 'Ẩn' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="admin.php?ctrl=admin&act=category_edit&id=<?= $cat['id'] ?>" 
                               class="btn btn-sm btn-warning">Sửa</a>
                            <a href="admin.php?ctrl=admin&act=category_delete&id=<?= $cat['id'] ?>" 
                               onclick="return confirm('Bạn chắc chắn muốn xóa danh mục này?')" 
                               class="btn btn-sm btn-danger">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
            <small>Hiển thị <?= $offset + 1 ?>-<?= min($offset + $perPage, $total) ?> của <?= $total ?> danh mục</small>
            <nav>
                <ul class="pagination mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?ctrl=admin&act=categories&page=<?= $i ?>&keyword=<?= urlencode($keyword ?? '') ?>&status=<?= $status_filter ?? '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.toggle-status').change(function() {
        const id = $(this).data('id');
        $.post('admin.php?ctrl=admin&act=category_toggle_status', { id: id }, function(res) {
            if (res.success) {
                location.reload(); // Hoặc cập nhật badge mà không reload
            }
        });
    });
});
</script>