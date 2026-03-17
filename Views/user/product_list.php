<?php
// Load model Category
$categoryModel = new Category();

// Lấy tất cả danh mục (bao gồm cả ẩn để xử lý logic)
$allCategoriesRaw = $categoryModel->getAll();

// Hàm lấy danh mục đang hiển thị (status = 1) và cha không bị ẩn
function getVisibleCategories($allCategories) {
    $visible = [];
    $hiddenIds = []; // Danh mục bị ẩn + tất cả con cháu của nó

    // Thu thập tất cả ID bị ẩn (bao gồm con cháu)
    foreach ($allCategories as $cat) {
        if ($cat['status'] == 0) {
            $hiddenIds[] = $cat['id'];
            // Thu thập con cháu đệ quy
            collectDescendants($allCategories, $cat['id'], $hiddenIds);
        }
    }

    // Lọc chỉ giữ danh mục status = 1 và không nằm trong hiddenIds
    foreach ($allCategories as $cat) {
        if ($cat['status'] == 1 && !in_array($cat['id'], $hiddenIds)) {
            $visible[] = $cat;
        }
    }

    return $visible;
}

function collectDescendants($allCategories, $parentId, &$hiddenIds) {
    foreach ($allCategories as $cat) {
        if ($cat['parent_id'] == $parentId) {
            $hiddenIds[] = $cat['id'];
            collectDescendants($allCategories, $cat['id'], $hiddenIds);
        }
    }
}

$visibleCategories = getVisibleCategories($allCategoriesRaw);

// Hàm render checkbox danh mục phân cấp (chỉ dùng visible categories)
function renderCategoryCheckbox($categories, $parent_id = null, $level = 0) {
    foreach ($categories as $cat) {
        if ($cat['parent_id'] == $parent_id) {
            $prefix = $level > 0 ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '└ ' : '';
            $checked = (isset($_GET['category']) && in_array($cat['id'], (array)$_GET['category'])) ? 'checked' : '';
            ?>
            <div class="form-check mb-2">
                <input 
                    type="checkbox" 
                    class="form-check-input"
                    name="category[]"
                    value="<?= $cat['id'] ?>"
                    id="cat_<?= $cat['id'] ?>"
                    <?= $checked ?>
                >
                <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                    <?= $prefix . htmlspecialchars($cat['name']) ?>
                </label>
            </div>
            <?php
            renderCategoryCheckbox($categories, $cat['id'], $level + 1);
        }
    }
}
?>

<section class="container my-5">
  <form id="filter-form" action="" method="get">
    <input type="hidden" name="ctrl" value="product">
    <input type="hidden" name="act" value="list">
    <?php if (isset($_GET['keyword'])): ?>
        <input type="hidden" name="keyword" value="<?= htmlspecialchars($_GET['keyword']) ?>">
    <?php endif; ?>

    <div class="row">
      <!-- BỘ LỌC BÊN TRÁI -->
      <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm p-4">
          <h5 class="mb-4 fw-bold"><i class="fas fa-filter me-2"></i>Lọc sản phẩm</h5>
          
          <!-- DANH MỤC (chỉ hiển thị những cái đang bật) -->
          <div class="mb-4">
            <h6 class="fw-bold mb-3">Danh mục</h6>
            <div class="category-filter-list" style="max-height: 400px; overflow-y: auto;">
              <?php if (!empty($visibleCategories)): ?>
                <?php renderCategoryCheckbox($visibleCategories); ?>
              <?php else: ?>
                <p class="text-muted small">Không có danh mục nào đang hiển thị.</p>
              <?php endif; ?>
            </div>
          </div>

          <!-- KHOẢNG GIÁ -->
          <div class="mb-4">
            <h6 class="fw-bold mb-3">Khoảng giá</h6>

            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="priceRange" value="duoi-200"
                     <?= (isset($_GET['priceRange']) && $_GET['priceRange'] == 'duoi-200') ? 'checked' : '' ?>>
              <label class="form-check-label">Dưới 200.000₫</label>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="priceRange" value="200-400"
                     <?= (isset($_GET['priceRange']) && $_GET['priceRange'] == '200-400') ? 'checked' : '' ?>>
              <label class="form-check-label">200.000₫ - 400.000₫</label>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="priceRange" value="400-600"
                     <?= (isset($_GET['priceRange']) && $_GET['priceRange'] == '400-600') ? 'checked' : '' ?>>
              <label class="form-check-label">400.000₫ - 600.000₫</label>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="priceRange" value="tren-600"
                     <?= (isset($_GET['priceRange']) && $_GET['priceRange'] == 'tren-600') ? 'checked' : '' ?>>
              <label class="form-check-label">Trên 600.000₫</label>
            </div>
          </div>

          <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">Áp dụng lọc</button>

          <!-- Nút xóa bộ lọc -->
          <?php 
          $hasFilter = !empty($_GET['category']) || !empty($_GET['priceRange']) || !empty($_GET['sort']);
          ?>
          <?php if ($hasFilter): ?>
            <a href="?ctrl=product&act=list<?= isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : '' ?>" 
               class="btn btn-outline-secondary w-100 mt-2">Xóa bộ lọc</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- DANH SÁCH SẢN PHẨM -->
      <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0 fw-bold">
            <?php 
              if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
                  echo 'Kết quả tìm kiếm: "' . htmlspecialchars($_GET['keyword']) . '"';
              } else {
                  echo 'Tất cả sản phẩm';
              }
            ?>
            <span class="text-muted ms-2">(<?= count($productList) ?> sản phẩm)</span>
          </h4>

          <div>
            <span class="me-2 text-muted">Sắp xếp:</span>
            <select name="sort" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
              <option value="newest" <?= (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
              <option value="price-asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price-asc') ? 'selected' : '' ?>>Giá: Thấp → Cao</option>
              <option value="price-desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price-desc') ? 'selected' : '' ?>>Giá: Cao → Thấp</option>
              <option value="name-asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name-asc') ? 'selected' : '' ?>>Tên: A → Z</option>
            </select>
          </div>
        </div>

        <!-- GRID SẢN PHẨM -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
          <?php if (!empty($productList)): ?>
            <?php foreach ($productList as $product): ?>
              <?php 
                $hinh = "Public/image/" . $product['image'];
                if (strpos($product['image'], 'image/') === 0) {
                    $hinh = "Public/" . $product['image'];
                }
              ?>
              <div class="col">
                <div class="card h-100 border-0 shadow-sm hover:shadow-lg transition-all duration-300">
                  <div class="position-relative overflow-hidden">
                    <img src="<?= htmlspecialchars($hinh) ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($product["name"]) ?>"
                         style="height: 250px; object-fit: cover;">
                    <?php if (!empty($product["sale"])): ?>
                      <span class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 small fw-bold">
                        -<?= $product["sale"] ?>%
                      </span>
                    <?php endif; ?>
                  </div>
                  
                  <div class="card-body d-flex flex-column p-3">
                    <h6 class="card-title text-truncate mb-2" title="<?= htmlspecialchars($product["name"]) ?>">
                      <?= htmlspecialchars($product["name"]) ?>
                    </h6>
                    
                    <div class="mt-auto">
                      <p class="text-danger fw-bold fs-5 mb-1">
                        <?= number_format($product["price"], 0, ',', '.') ?>₫
                      </p>
                      <?php if (!empty($product["original_price"]) && $product["original_price"] > $product["price"]): ?>
                        <del class="text-muted small">
                          <?= number_format($product["original_price"], 0, ',', '.') ?>₫
                        </del>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <div class="card-footer bg-white border-0 p-3 text-center d-flex gap-2">
                    <a href="?ctrl=product&act=detail&id=<?= $product["id"] ?>" 
                       class="btn btn-outline-secondary btn-sm flex-grow-1 fw-bold">
                      Xem chi tiết
                    </a>
                    <a href="?ctrl=cart&act=add&id=<?= $product['id'] ?>" 
                       class="btn btn-danger btn-sm fw-bold">
                      <i class="fas fa-cart-plus"></i>
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12">
              <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-4"></i>
                <p class="text-muted fs-4">Không tìm thấy sản phẩm nào phù hợp.</p>
                <a href="?ctrl=product&act=list" class="btn btn-primary">Xem tất cả sản phẩm</a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </form>
</section>