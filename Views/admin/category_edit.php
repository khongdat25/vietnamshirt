<?php include_once "./Views/admin/layout_sidebar_admin.php"; ?>

<div class="page-body">
    <h2 class="mb-4 text-primary">Sửa danh mục: <?= htmlspecialchars($cat['name']) ?></h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control form-control-lg" 
                           value="<?= htmlspecialchars($cat['name']) ?>" required>
                </div>



                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select form-select-lg">
                        <option value="1" <?= $cat['status'] ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= !$cat['status'] ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-warning btn-lg">Cập nhật</button>
                    <a href="admin.php?ctrl=admin&act=categories" class="btn btn-secondary btn-lg">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>