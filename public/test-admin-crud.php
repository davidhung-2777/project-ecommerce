<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Admin CRUD - DecorNest</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #333; margin-bottom: 10px; font-size: 32px; }
        h2 { color: #667eea; margin: 30px 0 20px; font-size: 22px; border-bottom: 3px solid #e0e0e0; padding-bottom: 10px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .module {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #dee2e6;
            position: relative;
            overflow: hidden;
        }
        .module::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        .module-icon { font-size: 32px; margin-bottom: 10px; }
        .module-title { font-size: 18px; font-weight: bold; margin-bottom: 8px; color: #333; }
        .module-desc { font-size: 13px; color: #666; margin-bottom: 15px; }
        .features { list-style: none; margin: 15px 0; padding: 0; }
        .features li { 
            padding: 8px 0; 
            border-bottom: 1px solid #e9ecef; 
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .features li:last-child { border-bottom: none; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 5px;
            transition: all 0.2s;
            font-size: 14px;
        }
        .btn:hover { background: #5568d3; transform: translateY(-2px); }
        .btn-green { background: #28a745; }
        .btn-green:hover { background: #218838; }
        .btn-orange { background: #fd7e14; }
        .btn-orange:hover { background: #e67700; }
        .status { padding: 12px; border-radius: 8px; margin: 15px 0; display: flex; align-items: center; gap: 10px; }
        .status-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .status-info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background: #f8f9fa; font-weight: 600; }
        .icon-check { color: #28a745; font-weight: bold; }
        .icon-cross { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Test Admin CRUD - DecorNest</h1>
        <p style="color: #666; margin-bottom: 30px;">Kiểm tra toàn bộ chức năng Thêm/Sửa/Xóa của Admin Panel</p>

        <div class="status status-info">
            <span style="font-size: 24px;">ℹ️</span>
            <div>
                <strong>Đăng nhập Admin trước:</strong><br>
                Email: <code>admin@decornest.com</code> | Password: <code>admin123</code>
                <a href="/project-ecommerce/public/user/login" class="btn" style="margin-left: 10px; padding: 6px 16px; font-size: 12px;">Đăng nhập</a>
            </div>
        </div>

        <h2>📊 Tổng Quan Chức Năng</h2>
        <table>
            <thead>
                <tr>
                    <th>Module</th>
                    <th style="text-align: center;">Thêm</th>
                    <th style="text-align: center;">Sửa</th>
                    <th style="text-align: center;">Xóa</th>
                    <th style="text-align: center;">Xem</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>📊 Dashboard</td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td>Chỉ xem thống kê</td>
                </tr>
                <tr>
                    <td><strong>📦 Sản phẩm</strong></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td><strong>CRUD đầy đủ</strong></td>
                </tr>
                <tr>
                    <td><strong>🏷️ Danh mục</strong></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td><strong>CRUD đầy đủ</strong></td>
                </tr>
                <tr>
                    <td>🛒 Đơn hàng</td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td>Xác nhận/Hủy nhanh</td>
                </tr>
                <tr>
                    <td>📋 Báo giá</td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td>Phản hồi/Từ chối</td>
                </tr>
                <tr>
                    <td>💳 Thanh toán</td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td>Xác nhận TT</td>
                </tr>
                <tr>
                    <td><strong>👥 Khách hàng</strong></td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td>Khóa/Mở khóa</td>
                </tr>
                <tr>
                    <td>📈 Báo cáo</td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-cross">✗</span></td>
                    <td style="text-align: center;"><span class="icon-check">✓</span></td>
                    <td>Chỉ xem báo cáo</td>
                </tr>
            </tbody>
        </table>

        <h2>🧪 Chi Tiết Modules</h2>
        
        <div class="grid">
            <!-- Products -->
            <div class="module">
                <div class="module-icon">📦</div>
                <div class="module-title">Sản Phẩm</div>
                <div class="module-desc">Quản lý toàn bộ sản phẩm, giá, tồn kho</div>
                <ul class="features">
                    <li><span class="icon-check">✓</span> Thêm sản phẩm + upload ảnh</li>
                    <li><span class="icon-check">✓</span> Sửa thông tin, giá, tồn kho</li>
                    <li><span class="icon-check">✓</span> Xóa (soft delete)</li>
                    <li><span class="icon-check">✓</span> Thêm giá sỉ theo số lượng</li>
                </ul>
                <span class="badge badge-success">CRUD Đầy Đủ</span>
                <div style="margin-top: 15px;">
                    <a href="/project-ecommerce/public/admin/products" class="btn">Quản Lý</a>
                    <a href="/project-ecommerce/public/admin/products/create" class="btn btn-orange">+ Thêm Mới</a>
                </div>
            </div>

            <!-- Categories -->
            <div class="module">
                <div class="module-icon">🏷️</div>
                <div class="module-title">Danh Mục</div>
                <div class="module-desc">Quản lý cây danh mục sản phẩm</div>
                <ul class="features">
                    <li><span class="icon-check">✓</span> Thêm danh mục cha/con</li>
                    <li><span class="icon-check">✓</span> Sửa inline (tên, thứ tự)</li>
                    <li><span class="icon-check">✓</span> Xóa (soft delete)</li>
                    <li><span class="icon-check">✓</span> Sắp xếp thứ tự</li>
                </ul>
                <span class="badge badge-success">CRUD Đầy Đủ</span>
                <div style="margin-top: 15px;">
                    <a href="/project-ecommerce/public/admin/categories" class="btn">Quản Lý</a>
                </div>
            </div>

            <!-- Orders -->
            <div class="module">
                <div class="module-icon">🛒</div>
                <div class="module-title">Đơn Hàng</div>
                <div class="module-desc">Xử lý và theo dõi đơn hàng</div>
                <ul class="features">
                    <li><span class="icon-check">✓</span> Xác nhận đơn nhanh</li>
                    <li><span class="icon-check">✓</span> Hủy đơn nhanh</li>
                    <li><span class="icon-check">✓</span> Cập nhật trạng thái</li>
                    <li><span class="icon-check">✓</span> Lọc theo PTTT, trạng thái</li>
                </ul>
                <span class="badge badge-info">Chỉ Sửa</span>
                <div style="margin-top: 15px;">
                    <a href="/project-ecommerce/public/admin/orders" class="btn">Quản Lý</a>
                </div>
            </div>

            <!-- Quotes -->
            <div class="module">
                <div class="module-icon">📋</div>
                <div class="module-title">Báo Giá B2B</div>
                <div class="module-desc">Xử lý yêu cầu báo giá sỉ</div>
                <ul class="features">
                    <li><span class="icon-check">✓</span> Phản hồi + set giá</li>
                    <li><span class="icon-check">✓</span> Từ chối nhanh</li>
                    <li><span class="icon-check">✓</span> Chuyển thành đơn hàng</li>
                    <li><span class="icon-check">✓</span> Lọc theo trạng thái</li>
                </ul>
                <span class="badge badge-info">Sửa/Xóa</span>
                <div style="margin-top: 15px;">
                    <a href="/project-ecommerce/public/admin/quotes" class="btn">Quản Lý</a>
                </div>
            </div>

            <!-- Payments -->
            <div class="module">
                <div class="module-icon">💳</div>
                <div class="module-title">Thanh Toán</div>
                <div class="module-desc">Xác nhận chuyển khoản thủ công</div>
                <ul class="features">
                    <li><span class="icon-check">✓</span> Xác nhận bank transfer</li>
                    <li><span class="icon-check">✓</span> VietQR auto (webhook)</li>
                    <li><span class="icon-check">✓</span> MoMo/VNPay tự động</li>
                    <li><span class="icon-check">✓</span> Xem lịch sử thanh toán</li>
                </ul>
                <span class="badge badge-warning">Chỉ Xác Nhận</span>
                <div style="margin-top: 15px;">
                    <a href="/project-ecommerce/public/admin/payments" class="btn">Quản Lý</a>
                </div>
            </div>

            <!-- Users -->
            <div class="module">
                <div class="module-icon">👥</div>
                <div class="module-title">Khách Hàng</div>
                <div class="module-desc">Quản lý tài khoản khách hàng</div>
                <ul class="features">
                    <li><span class="icon-check">✓</span> Khóa tài khoản</li>
                    <li><span class="icon-check">✓</span> Mở khóa tài khoản</li>
                    <li><span class="icon-check">✓</span> Xem lịch sử mua hàng</li>
                    <li><span class="icon-check">✓</span> Phân loại cá nhân/DN</li>
                </ul>
                <span class="badge badge-success">Sửa/Xóa</span>
                <div style="margin-top: 15px;">
                    <a href="/project-ecommerce/public/admin/users" class="btn">Quản Lý</a>
                </div>
            </div>

            <!-- Dashboard -->
            <div class="module">
                <div class="module-icon">📊</div>
                <div class="module-title">Dashboard</div>
                <div class="module-desc">Tổng quan hệ thống</div>
                <ul class="features">
                    <li><span class="icon-check">✓</span> Đơn hàng hôm nay</li>
                    <li><span class="icon-check">✓</span> Báo giá chờ xử lý</li>
                    <li><span class="icon-check">✓</span> Doanh thu</li>
                    <li><span class="icon-check">✓</span> 10 đơn mới nhất</li>
                </ul>
                <span class="badge badge-warning">Chỉ Xem</span>
                <div style="margin-top: 15px;">
                    <a href="/project-ecommerce/public/admin/dashboard" class="btn">Xem</a>
                </div>
            </div>

            <!-- Reports -->
            <div class="module">
                <div class="module-icon">📈</div>
                <div class="module-title">Báo Cáo</div>
                <div class="module-desc">Thống kê chi tiết</div>
                <ul class="features">
                    <li><span class="icon-check">✓</span> Doanh thu theo tháng</li>
                    <li><span class="icon-check">✓</span> Sản phẩm bán chạy</li>
                    <li><span class="icon-check">✓</span> Khách hàng mới</li>
                    <li><span class="icon-check">✓</span> Tỷ lệ chuyển đổi</li>
                </ul>
                <span class="badge badge-warning">Chỉ Xem</span>
                <div style="margin-top: 15px;">
                    <a href="/project-ecommerce/public/admin/reports" class="btn">Xem</a>
                </div>
            </div>
        </div>

        <div class="status status-success" style="margin-top: 40px;">
            <span style="font-size: 28px;">🎉</span>
            <div>
                <strong>Hệ thống Admin CRUD hoàn chỉnh!</strong><br>
                Tất cả 8 modules đã có đầy đủ chức năng cần thiết.<br>
                <a href="/project-ecommerce/ADMIN_CRUD_COMPLETE.md" style="color: #155724; text-decoration: underline;">Xem tài liệu chi tiết →</a>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px; padding-top: 30px; border-top: 2px solid #e9ecef;">
            <a href="/project-ecommerce/public" class="btn">🏠 Trang Chủ</a>
            <a href="/project-ecommerce/public/admin" class="btn btn-green">🎯 Admin Panel</a>
            <a href="/project-ecommerce/public/fix-all-now.php" class="btn btn-orange">🔧 Fix Database</a>
        </div>
    </div>
</body>
</html>
