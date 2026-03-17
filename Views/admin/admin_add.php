<?php include_once "./Views/admin/layout_sidebar_admin.php" ?>

<div class="page-body">
    <div class="container-fluid py-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Thêm sản phẩm mới</h4>
            </div>
            <div class="card-body p-5">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        
                        <div class="col-lg-7">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Tên sản phẩm</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Danh mục</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach($categories as $c): ?>
                                        <option value="<?=$c['id']?>"><?=$c['name']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Mô tả</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold text-success">Nhập chi tiết Size</h5>
                                    <table class="table table-bordered bg-white mt-3">
                                        <thead class="table-success">
                                            <tr>
                                                <th class="text-center">Size</th>
                                                <th>Giá (VNĐ)</th>
                                                <th>Kho (SL)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            // Danh sách size cố định
                                            $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                            foreach($sizes as $size): 
                                            ?>
                                            <tr>
                                                <td class="text-center align-middle fw-bold"><?=$size?></td>
                                                <td>
                                                    <input type="number" name="variants[<?=$size?>][price]" 
                                                           class="form-control" value="0" min="0">
                                                </td>
                                                <td>
                                                    <input type="number" name="variants[<?=$size?>][stock]" 
                                                           class="form-control" value="0" min="0">
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <small class="text-muted">Nhập số lượng 0 nếu không có size đó.</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="border rounded p-4 bg-light">
                                <label class="form-label fw-bold">Ảnh đại diện</label>
                                <input type="file" name="image" class="form-control mb-3" accept="image/*">
                                <div class="alert alert-info">
                                    <small>Chọn ảnh và bấm "Thêm mới" để lưu.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="admin.php?ctrl=admin&act=product" class="btn btn-secondary px-4">Quay lại</a>
                        <button type="submit" class="btn btn-success px-5 fw-bold">Thêm mới</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>