<?php include_once "./Views/admin/layout_sidebar_admin.php" ?>

<div class="page-body">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-primary">Quản lý sản phẩm</h2>
        <a href="admin.php?ctrl=admin&act=add" class="btn btn-success btn-lg shadow">
            <i class="bi bi-plus-circle"></i> Thêm sản phẩm mới
        </a>
    </div>

    <form method="GET" class="card shadow-sm mb-4">
        <input type="hidden" name="ctrl" value="admin">
        <input type="hidden" name="act" value="product">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" name="keyword" class="form-control form-control-lg" placeholder="Tên sản phẩm..." value="<?=htmlspecialchars($_GET['keyword']??'')?>">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Danh mục</label>
                    <select name="cat_id" class="form-select form-select-lg">
                        <option value="">Tất cả danh mục</option>
                        <?php if(!empty($categories)): foreach($categories as $c): ?>
                            <option value="<?=$c['id']?>" <?=($c['id']==($_GET['cat_id']??''))?'selected':''?>><?=htmlspecialchars($c['name'])?></option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Trạng thái kho</label>
                    <select name="status" class="form-select form-select-lg">
                        <option value="">Tất cả trạng thái</option>
                        <option value="instock" <?=($_GET['status']??'')=='instock'?'selected':''?>>Còn hàng (>10)</option>
                        <option value="low" <?=($_GET['status']??'')=='low'?'selected':''?>>Sắp hết (<=10)</option>
                        <option value="out" <?=($_GET['status']??'')=='out'?'selected':''?>>Hết hàng (0)</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Lọc</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="100">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá bán </th> 
                        <th>Chi tiết Kho</th>
                        <th>Trạng thái</th>
                        <th class="text-center" width="150">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($products) || !is_array($products)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">Không tìm thấy sản phẩm nào</td></tr>
                    <?php else: ?>
                        <?php foreach($products as $p): 
                            $status_class = $p['quantity'] > 10 ? 'bg-success' : ($p['quantity'] > 0 ? 'bg-warning text-dark' : 'bg-danger');
                            $status_text  = $p['quantity'] > 10 ? 'Còn hàng' : ($p['quantity'] > 0 ? 'Sắp hết' : 'Hết hàng');
                            
                            $dbVal = $p['image'];
                            $imgUrl = "./Public/image/no-image.jpg";
                            if (!empty($dbVal)) {
                                if (strpos($dbVal, 'image/') === 0) $imgUrl = "./Public/" . $dbVal;
                                else $imgUrl = "./Public/image/" . $dbVal;
                            }

                            $displayPrice = isset($p['display_price']) && $p['display_price'] > 0 
                                            ? number_format($p['display_price']) . " ₫" 
                                            : "<span class='text-muted small'>Chưa có giá</span>";
                        ?>
                            <tr <?= $p['quantity'] == 0 ? 'class="table-danger"' : '' ?>>
                                <td>
                                    <img src="<?=$imgUrl?>" class="rounded border bg-white" style="width:70px; height:70px; object-fit:contain;">
                                </td>
                                <td>
                                    <strong class="text-primary"><?=htmlspecialchars($p['name'])?></strong><br>
                                    <small class="text-muted">Mã SP: #<?=$p['id']?></small>
                                </td>
                                <td><span class="badge bg-info text-dark"><?=htmlspecialchars($p['cat_name']??'Chưa chọn')?></span></td>
                                <td class="text-danger fw-bold"><?=$displayPrice?></td>
                                
                                <td>
                                    <div class="mb-1">Tổng: <b><?=$p['quantity']?></b></div>
                                    <div class="text-muted border-top pt-1 mt-1" style="font-size: 0.85rem; line-height: 1.4;">
                                        <?= $p['stock_detail'] ?? '<span class="text-danger">Chưa có biến thể</span>' ?>
                                    </div>
                                </td>

                                <td><span class="badge <?=$status_class?>"><?=$status_text?></span></td>

                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="admin.php?ctrl=admin&act=edit&id=<?=$p['id']?>" 
                                           class="btn btn-warning btn-sm text-white shadow-sm fw-bold" 
                                           title="Sửa sản phẩm">
                                            <i class="bi bi-pencil-square"></i> Sửa
                                        </a>

                                        <a href="admin.php?ctrl=admin&act=delete&id=<?=$p['id']?>" 
                                           class="btn btn-danger btn-sm text-white shadow-sm fw-bold" 
                                           title="Xóa sản phẩm"
                                           onclick="return confirm('Xóa sản phẩm này sẽ xóa cả các biến thể giá. Bạn chắc chắn chứ?');">
                                           <i class="bi bi-trash"></i> Xóa
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
             <small class="text-muted">Hiển thị <?=count($products ?? [])?> sản phẩm</small>
             <?php if(isset($totalPages) && $totalPages > 1): ?>
                <nav><ul class="pagination pagination-sm mb-0">
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($page??1) == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?<?=http_build_query(array_merge($_GET, ['page' => $i]))?>"><?=$i?></a>
                        </li>
                    <?php endfor; ?>
                </ul></nav>
             <?php endif; ?>
        </div>
    </div>
</div>