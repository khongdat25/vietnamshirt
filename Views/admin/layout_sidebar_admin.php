<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý sản phẩm - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --sidebar-w: 260px; }
        * { box-sizing: border-box; margin:0; padding:0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar - giống hệt như ảnh bạn gửi */
        .sidebar {
            width: var(--sidebar-w);
            background: #1e293b;
            color: #cbd5e1;
            position: fixed;
            top: 0; bottom: 0; left: 0;
            padding: 2rem 0;
            z-index: 1000;
        }
        .sidebar h4 {
            color: white;
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 600;
            font-size: 1.5rem;
        }
        .sidebar a {
            display: block;
            padding: 1rem 2rem;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 1.05rem;
            transition: all 0.3s;
        }
        .sidebar a:hover {
            background: #0d6efd;
            color: white !important;
        }
        .sidebar a.active {
            background: #0d6efd;
            color: white !important;
            font-weight: 500;
        }
        .sidebar a i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        /* Nội dung chính */
        .main-content {
            margin-left: var(--sidebar-w);
            width: calc(100% - var(--sidebar-w));
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .page-body {
            padding: 2rem;
            flex: 1;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .badge-low { background: #fff3cd; color: #856404; }
        .badge-out { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <!-- Sidebar - giống hệt như ảnh -->
    <nav class="sidebar">
        <h4>Admin Panel</h4>
        <a href="admin.php?ctrl=admin&act=page"><i class="bi bi-speedometer2"></i> Tổng quan</a>
        <a href="admin.php?ctrl=admin&act=product" ><i class="bi bi-box-seam"></i> Sản phẩm</a>
        <a href="admin.php?ctrl=admin&act=categories"><i class="bi bi-grid-3x3-gap"></i> Danh mục</a>
        <a href="admin.php?ctrl=admin&act=order"><i class="bi bi-cart-check"></i> Đơn hàng</a>
        <a href="?ctrl=user&act=logout"><i class="bi bi-box-arrow-right"></i> Trang chủ</a>
    </nav>

    <!-- Nội dung chính -->
    <div class="main-content">

        <!-- Topbar -->
        <div class="topbar">
            <strong class="fs-5">Admin Dashboard</strong>
            <div>
                <span class="me-3 fw-semibold">Chào mừng, Admin!</span>
                <img src="https://via.placeholder.com/40" class="rounded-circle" alt="avatar">
            </div>
        </div>