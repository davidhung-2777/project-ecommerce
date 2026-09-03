<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Admin - Thêm Sản Phẩm</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-2xl font-bold mb-6">✅ Hướng Dẫn Test Admin CRUD</h1>
            
            <div class="space-y-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4">
                    <h2 class="font-bold text-lg mb-2">🔐 Đăng Nhập Admin</h2>
                    <ol class="list-decimal ml-5 space-y-2 text-sm">
                        <li>Mở: <a href="/project-ecommerce/public/user/login" class="text-blue-600 underline" target="_blank">/user/login</a></li>
                        <li>Email: <code class="bg-gray-100 px-2 py-1 rounded">admin@decornest.com</code></li>
                        <li>Password: <code class="bg-gray-100 px-2 py-1 rounded">admin123</code></li>
                    </ol>
                </div>

                <div class="bg-green-50 border-l-4 border-green-500 p-4">
                    <h2 class="font-bold text-lg mb-2">📦 Quản Lý Sản Phẩm</h2>
                    <ul class="space-y-2 text-sm">
                        <li>• <a href="/project-ecommerce/public/admin/products" class="text-blue-600 underline" target="_blank">Danh sách sản phẩm</a></li>
                        <li>• <a href="/project-ecommerce/public/admin/products/create" class="text-blue-600 underline" target="_blank">Thêm sản phẩm mới</a></li>
                    </ul>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                    <h2 class="font-bold text-lg mb-2">🎯 Test Cases</h2>
                    <ol class="list-decimal ml-5 space-y-2 text-sm">
                        <li><strong>Thêm sản phẩm:</strong>
                            <ul class="ml-4 mt-1 space-y-1">
                                <li>→ Điền thông tin đầy đủ</li>
                                <li>→ Upload ảnh (tùy chọn)</li>
                                <li>→ Bấm "Tạo sản phẩm"</li>
                            </ul>
                        </li>
                        <li><strong>Sửa sản phẩm:</strong>
                            <ul class="ml-4 mt-1 space-y-1">
                                <li>→ Click "Sửa" ở dòng sản phẩm</li>
                                <li>→ Thay đổi thông tin</li>
                                <li>→ Bấm "Cập nhật sản phẩm"</li>
                            </ul>
                        </li>
                        <li><strong>Xóa sản phẩm:</strong>
                            <ul class="ml-4 mt-1 space-y-1">
                                <li>→ Click "Xóa" ở dòng sản phẩm</li>
                                <li>→ Xác nhận xóa</li>
                                <li>→ Sản phẩm sẽ ẩn (is_active=0)</li>
                            </ul>
                        </li>
                    </ol>
                </div>

                <div class="bg-purple-50 border-l-4 border-purple-500 p-4">
                    <h2 class="font-bold text-lg mb-2">📂 Thư Mục Upload</h2>
                    <p class="text-sm mb-2">Ảnh sẽ được lưu vào:</p>
                    <code class="bg-gray-100 px-2 py-1 rounded text-xs block">
                        /public/uploads/products/
                    </code>
                    <p class="text-xs text-gray-600 mt-2">Đảm bảo thư mục này có quyền ghi (chmod 777)</p>
                </div>

                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                    <h2 class="font-bold text-lg mb-2">⚠️ Lưu Ý</h2>
                    <ul class="text-sm space-y-1">
                        <li>• <strong>Database:</strong> Chạy <a href="/project-ecommerce/public/fix-all-now.php" class="text-blue-600 underline" target="_blank">fix-all-now.php</a> trước</li>
                        <li>• <strong>Upload ảnh:</strong> Tối đa 5MB, định dạng JPG/PNG/WebP</li>
                        <li>• <strong>Xóa:</strong> Soft delete (is_active=0), không xóa hẳn khỏi DB</li>
                    </ul>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <a href="/project-ecommerce/public/admin/products" 
                       class="bg-blue-600 text-white py-3 px-4 rounded-lg text-center font-semibold hover:bg-blue-700 transition">
                        🛍️ Quản Lý Sản Phẩm
                    </a>
                    <a href="/project-ecommerce/public/admin/dashboard" 
                       class="bg-gray-800 text-white py-3 px-4 rounded-lg text-center font-semibold hover:bg-gray-900 transition">
                        📊 Dashboard Admin
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
            <h2 class="font-bold text-lg mb-4">🐛 Kiểm Tra Lỗi</h2>
            <div class="space-y-3 text-sm">
                <?php
                $checks = [
                    'PHP Version' => PHP_VERSION,
                    'Upload Folder Exists' => file_exists(__DIR__ . '/uploads/products') ? '✅ Có' : '❌ Không',
                    'Upload Folder Writable' => is_writable(__DIR__ . '/uploads/products') ? '✅ Có' : '❌ Không',
                    'Session Started' => session_status() === PHP_SESSION_ACTIVE ? '✅ Đã khởi động' : '⚠️ Chưa khởi động',
                ];
                
                foreach ($checks as $label => $value) {
                    echo "<div class='flex justify-between border-b pb-2'>";
                    echo "<span class='font-medium'>{$label}:</span>";
                    echo "<span>{$value}</span>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
