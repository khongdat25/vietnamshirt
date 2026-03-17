<?php include_once "./Views/admin/layout_sidebar_admin.php" ?>

<div class="page-body">
    <div class="container-fluid py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white"> 
                <h4 class="mb-0">Sửa sản phẩm #<?=$product['id']?></h4>
            </div>
            <div class="card-body p-5">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Tên sản phẩm</label>
                                <input type="text" name="name" class="form-control" value="<?=htmlspecialchars($product['name'])?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Danh mục</label>
                                <select name="category_id" class="form-select" required>
                                    <?php foreach($categories as $c): ?>
                                        <option value="<?=$c['id']?>" <?=($c['id']==$product['category_id'])?'selected':''?>>
                                            <?=$c['name']?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Mô tả</label>
                                <textarea name="description" class="form-control" rows="3"><?=htmlspecialchars($product['description']??'')?></textarea>
                            </div>

                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold text-primary">Cấu hình Size & Giá</h5> 
                                    <table class="table table-bordered bg-white mt-3 align-middle">
                                        <thead class="table-primary"> 
                                            <tr>
                                                <th class="text-center">Size</th>
                                                <th>Giá (VNĐ)</th>
                                                <th>Kho (SL)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                            $map = [];
                                            if (!empty($variants)) { foreach($variants as $v) { $map[$v['size']] = $v; } }

                                            foreach($sizes as $size): 
                                                $hasData = isset($map[$size]);
                                                // Nếu có dữ liệu thì hiện, nếu là 0 hoặc không có thì để chuỗi rỗng
                                                $val_price = ($hasData && $map[$size]['price'] > 0) ? $map[$size]['price'] : '';
                                                $val_stock = ($hasData && $map[$size]['stock'] > 0) ? $map[$size]['stock'] : '';
                                                
                                                $rowClass = $hasData ? 'table-info' : ''; 
                                            ?>
                                            <tr class="<?=$rowClass?>">
                                                <td class="text-center fw-bold"><?=$size?></td>
                                                <td><input type="number" name="variants[<?=$size?>][price]" class="form-control" value="<?=$val_price?>" placeholder="Trống" min="0"></td>
                                                <td><input type="number" name="variants[<?=$size?>][stock]" class="form-control" value="<?=$val_stock?>" placeholder="Trống" min="0"></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="border rounded p-4 bg-light">
                                <label class="form-label fw-bold">Ảnh hiện tại</label>
                                <div class="text-center mb-3 p-3 bg-white border rounded">
                                    <?php 
                                        $dbVal = $product['image'];
                                        $imgUrl = "./Public/image/no-image.jpg";
                                        // Logic hiển thị ảnh giống trang danh sách
                                        if (!empty($dbVal) && file_exists("./Public/image/" . basename($dbVal))) {
                                            $imgUrl = "./Public/image/" . basename($dbVal);
                                        } elseif (!empty($dbVal) && file_exists("./Public/" . basename($dbVal))) {
                                            $imgUrl = "./Public/" . basename($dbVal);
                                        }
                                    ?>
                                    <img src="<?=$imgUrl?>" class="img-fluid" style="max-height: 250px; object-fit: contain;">
                                </div>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <a href="admin.php?ctrl=admin&act=product" class="btn btn-secondary px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary px-5">Cập nhật</button> 
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>