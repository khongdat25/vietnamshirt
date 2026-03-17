<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Hỏi đáp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="fw-bold mb-4 text-primary text-uppercase">Quản lý Hỏi đáp (Q&A)</h2>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-3 ps-4">Sản phẩm</th>
                            <th class="py-3">Người hỏi</th>
                            <th class="py-3" style="width: 30%;">Câu hỏi</th>
                            <th class="py-3" style="width: 30%;">Trả lời của Shop</th>
                            <th class="py-3 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($questions as $q): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="Public/image/<?= $q['product_image'] ?>" width="50" height="50" class="rounded me-2 object-fit-cover">
                                    <span class="fw-bold small"><?= htmlspecialchars($q['product_name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($q['user_name']) ?></div>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($q['created_at'])) ?></small>
                            </td>
                            <td>
                                <div class="bg-light p-2 rounded small text-secondary">
                                    <?= nl2br(htmlspecialchars($q['content'])) ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($q['reply'])): ?>
                                    <div class="text-success small fw-bold">
                                        <i class="bi bi-check-circle-fill"></i> Đã trả lời:
                                    </div>
                                    <span class="small"><?= nl2br(htmlspecialchars($q['reply'])) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Chưa trả lời</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#replyModal<?= $q['id'] ?>">
                                    <i class="bi bi-pencil-square"></i> Trả lời
                                </button>
                                
                                <a href="?ctrl=admin_question&act=delete&id=<?= $q['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Bạn có chắc muốn xóa câu hỏi này?')">
                                    <i class="bi bi-trash"></i>
                                </a>

                                <div class="modal fade" id="replyModal<?= $q['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="?ctrl=admin_question&act=reply" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Trả lời khách hàng: <?= htmlspecialchars($q['user_name']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label">Câu hỏi:</label>
                                                        <p class="bg-light p-2 rounded"><?= htmlspecialchars($q['content']) ?></p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="fw-bold form-label text-success">Câu trả lời của bạn:</label>
                                                        <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                                                        <textarea name="reply_content" class="form-control" rows="5" placeholder="Nhập câu trả lời..." required><?= $q['reply'] ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" class="btn btn-primary">Lưu câu trả lời</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>