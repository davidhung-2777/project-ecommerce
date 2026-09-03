<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Product CRUD - DecorNest</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            padding: 30px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #333; margin-bottom: 15px; font-size: 32px; }
        h2 { color: #667eea; margin: 30px 0 20px; font-size: 20px; border-bottom: 3px solid #e0e0e0; padding-bottom: 10px; }
        .status { 
            padding: 15px 20px; 
            margin: 12px 0; 
            border-radius: 8px; 
            border-left: 5px solid;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .success { background: #d4edda; color: #155724; border-left-color: #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; border-left-color: #17a2b8; }
        .warning { background: #fff3cd; color: #856404; border-left-color: #ffc107; }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px 8px;
            transition: all 0.2s;
        }
        .btn:hover { background: #218838; transform: translateY(-2px); }
        .btn-blue { background: #007bff; }
        .btn-blue:hover { background: #0056b3; }
        .btn-purple { background: #6f42c1; }
        .btn-purple:hover { background: #5a32a3; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
        code { background: #f4f4f4; padding: 3px 8px; border-radius: 4px; font-family: monospace; }
        .icon { font-size: 24px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0; }
        .card { background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px solid #e9ecef; }
        .card strong { display: block; margin-bottom: 8px; color: #495057; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Product CRUD - DecorNest</h1>
        <p style="color: #666; margin-bottom: 40px;">Hướng dẫn test đầy đủ chức năng Thêm/Sửa/Xóa sản phẩm</p>

        <?php
        // Check system requirements
        $checks = [
            'PHP Version >= 8.0' => version_compare(PHP_VERSION, '8.0.0', '>='),
            'PDO Extension' => extension_loaded('pdo'),
            'PDO MySQL' => extension_loaded('pdo_mysql'),
            'GD Extension (images)' => extension_loaded('gd'),
            'FileInfo Extension' => extension_loaded('fileinfo'),
            'Upload Directory' => is_dir(__DIR__ . '/uploads/products'),
            'Writable Upload' => is_writable(__DIR__ . '/uploads/products'),
        ];

        $allGood = !in_array(false, $checks, true);
        ?>

        <h2><span class="icon">🔍</span> Kiểm Tra Hệ Thống</h2>
        <table>
            <thead>
                <tr>
                    <th>Yêu Cầu</th>
                    <th style="text-align: center;">Trạng Thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checks as $label => $passed): ?>
                <tr>
                    <td><?= htmlspecialchars($label) ?></td>
                    <td style="text-align: center;">
                        <?php if ($passed): ?>
                            <span style="color: #28a745; font-weight: bold;">✅ OK</span>
                        <?php else: ?>
                            <span style="color: #dc3545; font-weight: bold;">❌ Lỗi</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($allGood): ?>
            <div class="status success">
                <span class="icon">✅</span>
                <strong>Hệ thống sẵn sàng!</strong> Tất cả yêu cầu đã được đáp ứng.
            </div>
        <?php else: ?>
            <div class="status error">
                <span class="icon">❌</span>
                <strong>Có lỗi!</strong> Vui lòng sửa các vấn đề trên trước khi tiếp tục.
            </div>
        <?php endif; ?>

        <h2><span class="icon">📋</span> Hướng Dẫn Test CRUD</h2>

        <div class="grid">
            <div class="card">
                <strong>1️⃣ Chuẩn Bị Database</strong>
                <p style="font-size: 14px; color: #666; margin-top: 8px;">
                    Chạy script tạo dữ liệu mẫu trước khi test
                </p>
                <a href="/project-ecommerce/public/fix-all-now.php" class="btn" style="margin: 10px 0 0 0;">🔧 Fix Database</a>
            </div>

            <div class="card">
                <strong>2️⃣ Đăng Nhập Admin</strong>
                <p style="font-size: 14px; color: #666; margin-top: 8px;">
                    Email: <code>admin@decornest.com</code><br>
                    Password: <code>admin123</code>
                </p>
                <a href="/project-ecommerce/public/user/login" class="btn btn-blue" style="margin: 10px 0 0 0;">🔐 Đăng Nhập</a>
            </div>

            <div class="card">
                <strong>3️⃣ Quản Lý Sản Phẩm</strong>
                <p style="font-size: 14px; color: #666; margin-top: 8px;">
                    Thêm, sửa, xóa sản phẩm từ admin panel
                </p>
                <a href="/project-ecommerce/public/admin/products" class="btn btn-purple" style="margin: 10px 0 0 0;">📦 Products</a>
            </div>
        </div>

        <h2><span class="icon">🎯</span> Test Cases Chi Tiết</h2>

        <div class="status info">
            <span class="icon">➕</span>
            <div>
                <strong>TEST 1: Thêm Sản Phẩm Mới</strong>
                <ol style="margin-top: 10px; margin-left: 20px; font-size: 14px;">
                    <li>Vào <code>/admin/products/create</code></li>
                    <li>Điền: Tên SP, SKU, Giá, Số lượng tồn kho</li>
                    <li>Chọn danh mục</li>
                    <li>Upload ảnh (tùy chọn)</li>
                    <li>Bấm "Tạo sản phẩm"</li>
                    <li>✅ Kiểm tra: Sản phẩm xuất hiện trong danh sách</li>
                </ol>
            </div>
        </div>

        <div class="status warning">
            <span class="icon">✏️</span>
            <div>
                <strong>TEST 2: Sửa Sản Phẩm</strong>
                <ol style="margin-top: 10px; margin-left: 20px; font-size: 14px;">
                    <li>Trong danh sách sản phẩm, click nút "Sửa"</li>
                    <li>Thay đổi: Tên, giá, hoặc mô tả</li>
                    <li>Upload ảnh mới (nếu muốn thay đổi)</li>
                    <li>Bấm "Cập nhật sản phẩm"</li>
                    <li>✅ Kiểm tra: Thông tin đã được cập nhật</li>
                </ol>
            </div>
        </div>

        <div class="status error">
            <span class="icon">🗑️</span>
            <div>
                <strong>TEST 3: Xóa Sản Phẩm</strong>
                <ol style="margin-top: 10px; margin-left: 20px; font-size: 14px;">
                    <li>Trong danh sách, click nút "Xóa"</li>
                    <li>Xác nhận xóa trong popup</li>
                    <li>✅ Sản phẩm biến mất khỏi danh sách</li>
                    <li>💡 Lưu ý: Soft delete (is_active=0), không xóa hoàn toàn</li>
                </ol>
            </div>
        </div>

        <h2><span class="icon">📂</span> Thông Tin Kỹ Thuật</h2>

        <table>
            <tr>
                <td><strong>Upload Path:</strong></td>
                <td><code>/public/uploads/products/</code></td>
            </tr>
            <tr>
                <td><strong>Max File Size:</strong></td>
                <td>5 MB</td>
            </tr>
            <tr>
                <td><strong>Allowed Formats:</strong></td>
                <td>JPG, JPEG, PNG, WebP</td>
            </tr>
            <tr>
                <td><strong>Delete Type:</strong></td>
                <td>Soft Delete (is_active = 0)</td>
            </tr>
            <tr>
                <td><strong>Form Method:</strong></td>
                <td>POST with enctype="multipart/form-data"</td>
            </tr>
        </table>

        <h2><span class="icon">🔗</span> Quick Links</h2>

        <div style="text-align: center; margin-top: 30px;">
            <a href="/project-ecommerce/public" class="btn">🏠 Trang Chủ</a>
            <a href="/project-ecommerce/public/admin/dashboard" class="btn btn-blue">📊 Admin Dashboard</a>
            <a href="/project-ecommerce/public/admin/products" class="btn btn-purple">📦 Quản Lý SP</a>
            <a href="/project-ecommerce/public/admin/products/create" class="btn" style="background: #fd7e14;">➕ Thêm SP Mới</a>
        </div>

        <div class="status info" style="margin-top: 40px;">
            <span class="icon">💡</span>
            <div>
                <strong>Tip:</strong> Nếu gặp lỗi 500, hãy check:
                <ul style="margin-top: 8px; margin-left: 20px; font-size: 14px;">
                    <li>Apache error log: <code>xampp/apache/logs/error.log</code></li>
                    <li>PHP error log: kiểm tra <code>php.ini</code></li>
                    <li>Database connection: check <code>.env</code></li>
                    <li>Upload permissions: <code>chmod 777 public/uploads</code></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
