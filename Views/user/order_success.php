<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt hàng thành công!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container text-center py-5 my-5">
    <div class="py-5">
        <i class="fas fa-check-circle text-success" style="font-size: 100px;"></i>
        <h1 class="mt-4 text-success">Đặt hàng thành công!</h1>
        <h4 class="mt-3">Mã đơn hàng: <strong class="text-danger">#<?= $order_id ?></strong></h4>
        <p class="lead mt-4">Cảm ơn bạn đã mua sắm tại shop!</p>
        <p>Chúng tôi sẽ liên hệ xác nhận trong vòng 30 phút - 2 giờ.</p>
        <p>Bạn sẽ <strong>thanh toán bằng tiền mặt khi nhận hàng</strong>.</p>

        <div class="mt-5">
            <a href="?ctrl=page&act=home" class="btn btn-primary btn-lg">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
            <a href="?ctrl=user&act=updateProfile" class="btn btn-outline-secondary btn-lg ms-3">
                <i class="fas fa-user"></i> Xem đơn hàng của tôi
            </a>
        </div>
    </div>
</div>
</body>
</html>