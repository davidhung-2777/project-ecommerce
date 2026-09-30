<?php
/**
 * Test Voucher in Checkout Feature
 * Demo tính năng nhập mã voucher và gợi ý voucher trong checkout
 */

define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

$baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Voucher in Checkout - DecorNest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-12">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-blue-500 text-white rounded-2xl shadow-lg p-8 mb-8">
            <h1 class="text-4xl font-bold mb-2">
                🎟️ Voucher in Checkout Feature
            </h1>
            <p class="text-green-100 text-lg">
                Tính năng nhập mã giảm giá và gợi ý voucher trực tiếp trong trang thanh toán
            </p>
        </div>

        <!-- Demo Video/Screenshot Placeholder -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">📸 Demo Giao Diện</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="border-2 border-gray-200 rounded-xl p-6 bg-gray-50">
                    <h3 class="font-semibold text-gray-900 mb-3">🆕 Chưa có voucher</h3>
                    <div class="bg-white border border-green-200 rounded-lg p-4 space-y-3">
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <span>🎟️</span>
                            <span>Mã giảm giá</span>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Nhập mã voucher" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" disabled>
                            <button class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold" disabled>
                                Áp dụng
                            </button>
                        </div>
                        <button class="text-sm text-green-600 font-semibold" disabled>
                            📋 Xem mã khả dụng (3)
                        </button>
                    </div>
                </div>
                
                <div class="border-2 border-green-500 rounded-xl p-6 bg-green-50">
                    <h3 class="font-semibold text-gray-900 mb-3">✅ Đã apply voucher</h3>
                    <div class="bg-white border border-green-300 rounded-lg p-4">
                        <div class="bg-green-100 border border-green-300 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">🎟️</span>
                                    <div>
                                        <p class="text-xs text-green-700">Mã giảm giá</p>
                                        <p class="font-mono font-bold text-green-600">GIAMGIA50K</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">-50.000đ</p>
                                    <button class="text-xs text-red-600 underline" disabled>Xóa</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features List -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">✨ Tính Năng Mới</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">✅</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Nhập mã trực tiếp</h3>
                            <p class="text-sm text-gray-600">Không cần quay lại giỏ hàng</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">🎯</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Gợi ý thông minh</h3>
                            <p class="text-sm text-gray-600">Chỉ hiển thị voucher khả dụng</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">⚡</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Real-time update</h3>
                            <p class="text-sm text-gray-600">Tổng tiền cập nhật ngay lập tức</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">🖱️</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Click để apply</h3>
                            <p class="text-sm text-gray-600">Apply nhanh từ gợi ý</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">❌</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Xóa dễ dàng</h3>
                            <p class="text-sm text-gray-600">Thử mã khác chỉ 1 click</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">🔔</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Thông báo lỗi</h3>
                            <p class="text-sm text-gray-600">Hiển thị lý do khi mã không hợp lệ</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">⌨️</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Nhấn Enter</h3>
                            <p class="text-sm text-gray-600">Apply nhanh bằng phím Enter</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">📱</span>
                        <div>
                            <h3 class="font-semibold text-gray-900">Responsive</h3>
                            <p class="text-sm text-gray-600">Hoạt động tốt trên mọi thiết bị</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Steps -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">🧪 Hướng Dẫn Test</h2>
            
            <div class="space-y-6">
                <!-- Step 1 -->
                <div class="border-l-4 border-blue-500 pl-4">
                    <h3 class="font-bold text-blue-900 mb-2">📝 Bước 1: Chuẩn bị dữ liệu</h3>
                    <ol class="list-decimal pl-6 space-y-1 text-sm text-gray-700">
                        <li>Đăng nhập vào hệ thống</li>
                        <li>Vào Admin → Vouchers để tạo mã test (hoặc dùng mã có sẵn)</li>
                        <li>Ví dụ: Tạo mã <code class="bg-gray-100 px-2 py-1 rounded">GIAMGIA10K</code> giảm 10.000đ cho đơn từ 500.000đ</li>
                    </ol>
                </div>

                <!-- Step 2 -->
                <div class="border-l-4 border-green-500 pl-4">
                    <h3 class="font-bold text-green-900 mb-2">🛒 Bước 2: Thêm sản phẩm vào giỏ</h3>
                    <ol class="list-decimal pl-6 space-y-1 text-sm text-gray-700">
                        <li>Vào trang sản phẩm</li>
                        <li>Thêm sản phẩm vào giỏ (tổng > 500K để đủ điều kiện voucher)</li>
                        <li>Click "Thanh toán"</li>
                    </ol>
                </div>

                <!-- Step 3 -->
                <div class="border-l-4 border-purple-500 pl-4">
                    <h3 class="font-bold text-purple-900 mb-2">🎟️ Bước 3: Test voucher trong checkout</h3>
                    <div class="space-y-3 text-sm text-gray-700">
                        <p><strong>Test A: Xem gợi ý voucher</strong></p>
                        <ol class="list-decimal pl-6 space-y-1">
                            <li>Scroll xuống phần "Mã giảm giá"</li>
                            <li>Click "📋 Xem mã khả dụng (X)"</li>
                            <li>Kiểm tra: Hiển thị danh sách voucher với code, mô tả, giá trị</li>
                            <li>Click vào 1 voucher → Tự động apply</li>
                        </ol>

                        <p><strong>Test B: Nhập mã thủ công</strong></p>
                        <ol class="list-decimal pl-6 space-y-1">
                            <li>Nhập mã: <code class="bg-gray-100 px-2 py-1 rounded">GIAMGIA10K</code></li>
                            <li>Click "Áp dụng" hoặc nhấn Enter</li>
                            <li>Kiểm tra: Hiển thị voucher đã apply với giá trị giảm</li>
                            <li>Kiểm tra: Tổng tiền đã trừ discount</li>
                        </ol>

                        <p><strong>Test C: Mã không hợp lệ</strong></p>
                        <ol class="list-decimal pl-6 space-y-1">
                            <li>Nhập mã: <code class="bg-gray-100 px-2 py-1 rounded">INVALIDCODE</code></li>
                            <li>Click "Áp dụng"</li>
                            <li>Kiểm tra: Hiển thị thông báo lỗi màu đỏ</li>
                            <li>Kiểm tra: Tổng tiền không thay đổi</li>
                        </ol>

                        <p><strong>Test D: Xóa voucher</strong></p>
                        <ol class="list-decimal pl-6 space-y-1">
                            <li>Sau khi đã apply voucher, click nút "Xóa"</li>
                            <li>Kiểm tra: Voucher bị xóa</li>
                            <li>Kiểm tra: Tổng tiền quay lại ban đầu</li>
                            <li>Kiểm tra: Hiển thị lại form nhập mã</li>
                        </ol>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="border-l-4 border-orange-500 pl-4">
                    <h3 class="font-bold text-orange-900 mb-2">✅ Bước 4: Hoàn tất đơn hàng</h3>
                    <ol class="list-decimal pl-6 space-y-1 text-sm text-gray-700">
                        <li>Điền đầy đủ thông tin giao hàng</li>
                        <li>Chọn phương thức thanh toán</li>
                        <li>Click "Hoàn tất đặt hàng"</li>
                        <li>Kiểm tra: Voucher được lưu vào đơn hàng</li>
                        <li>Kiểm tra: Database orders table có voucher_id và discount_amount</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- API Endpoints -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">🔌 API Endpoints</h2>
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">GET</span>
                        <code class="text-sm font-mono text-gray-800">/vouchers/available</code>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Lấy danh sách voucher khả dụng cho user</p>
                    <div class="bg-gray-50 rounded p-3 text-xs font-mono">
                        <pre>{ "success": true, "vouchers": [...] }</pre>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">POST</span>
                        <code class="text-sm font-mono text-gray-800">/cart/apply-voucher</code>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Validate và apply voucher</p>
                    <div class="bg-gray-50 rounded p-3 text-xs font-mono">
                        <pre>Body: { "code": "GIAMGIA10K", "cart_items": [...] }
Response: { "success": true, "discount": 10000, ... }</pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Files Changed -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📂 Files Đã Sửa</h2>
            <div class="space-y-2">
                <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <span class="text-green-600">✏️</span>
                    <code class="text-sm font-mono text-gray-800">app/views/frontend/pages/checkout.php</code>
                    <span class="ml-auto text-xs bg-green-600 text-white px-2 py-1 rounded">UPDATED</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <span class="text-gray-400">✓</span>
                    <code class="text-sm font-mono text-gray-600">app/controllers/VoucherController.php</code>
                    <span class="ml-auto text-xs bg-gray-400 text-white px-2 py-1 rounded">NO CHANGE</span>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <span class="text-gray-400">✓</span>
                    <code class="text-sm font-mono text-gray-600">app/services/VoucherService.php</code>
                    <span class="ml-auto text-xs bg-gray-400 text-white px-2 py-1 rounded">NO CHANGE</span>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-4">🚀 Quick Links</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <a href="<?= $baseUrl ?>/cart" 
                   class="block bg-white/20 hover:bg-white/30 backdrop-blur rounded-xl p-4 text-center transition">
                    <div class="text-3xl mb-2">🛒</div>
                    <div class="font-semibold">Giỏ Hàng</div>
                </a>
                <a href="<?= $baseUrl ?>/checkout" 
                   class="block bg-white/20 hover:bg-white/30 backdrop-blur rounded-xl p-4 text-center transition">
                    <div class="text-3xl mb-2">💳</div>
                    <div class="font-semibold">Checkout</div>
                </a>
                <a href="<?= $baseUrl ?>/admin/vouchers" 
                   class="block bg-white/20 hover:bg-white/30 backdrop-blur rounded-xl p-4 text-center transition">
                    <div class="text-3xl mb-2">⚙️</div>
                    <div class="font-semibold">Admin Vouchers</div>
                </a>
            </div>
        </div>

    </div>
</body>
</html>
""