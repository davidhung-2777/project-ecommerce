<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use App\Services\GhnShippingService;

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test GHN API - DecorNest</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f5f5; 
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #8B7355; margin-bottom: 20px; }
        .box { 
            background: white; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 4px; 
            overflow-x: auto;
            font-size: 12px;
        }
        h3 { color: #333; margin: 15px 0 10px 0; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0;
        }
        th, td { 
            padding: 10px; 
            text-align: left; 
            border-bottom: 1px solid #ddd;
        }
        th { background: #8B7355; color: white; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #8B7355;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
        .btn:hover { background: #6d5942; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚚 Test Giao Hàng Nhanh (GHN) API</h1>
        
        <?php
        $ghn = new GhnShippingService();
        
        // Kiểm tra config
        echo '<div class="box">';
        echo '<h3>📋 Cấu hình hiện tại</h3>';
        
        $hasToken = !empty($_ENV['GHN_API_TOKEN']) && $_ENV['GHN_API_TOKEN'] !== 'your_ghn_token_here';
        $hasShopId = !empty($_ENV['GHN_SHOP_ID']) && $_ENV['GHN_SHOP_ID'] !== 'your_shop_id_here';
        
        if ($hasToken && $hasShopId) {
            echo '<div class="success">✅ API Token và Shop ID đã được cấu hình</div>';
            echo '<div class="info">';
            echo '<strong>API URL:</strong> ' . htmlspecialchars($_ENV['GHN_API_URL']) . '<br>';
            echo '<strong>Shop ID:</strong> ' . htmlspecialchars($_ENV['GHN_SHOP_ID']) . '<br>';
            echo '<strong>Token:</strong> ' . substr($_ENV['GHN_API_TOKEN'], 0, 20) . '...<br>';
            echo '<strong>From District ID:</strong> ' . htmlspecialchars($_ENV['GHN_FROM_DISTRICT_ID'] ?? 'Chưa có') . '<br>';
            echo '<strong>From Ward Code:</strong> ' . htmlspecialchars($_ENV['GHN_FROM_WARD_CODE'] ?? 'Chưa có');
            echo '</div>';
        } else {
            echo '<div class="error">❌ Chưa cấu hình đầy đủ. Vui lòng cập nhật file .env:</div>';
            if (!$hasToken) echo '<div class="error">- Thiếu GHN_API_TOKEN</div>';
            if (!$hasShopId) echo '<div class="error">- Thiếu GHN_SHOP_ID</div>';
            echo '<div class="info">Xem file FEATURE_CHECKLIST.md để biết cách lấy API Token</div>';
        }
        echo '</div>';
        
        // Test 1: Lấy danh sách tỉnh/thành
        if ($hasToken && $hasShopId) {
            echo '<div class="box">';
            echo '<h3>🗺️ Test 1: Lấy danh sách Tỉnh/Thành phố</h3>';
            
            $provinces = $ghn->getProvinces();
            
            if ($provinces['success']) {
                echo '<div class="success">✅ Lấy danh sách tỉnh/thành thành công (' . count($provinces['data']) . ' tỉnh)</div>';
                echo '<table>';
                echo '<tr><th>Province ID</th><th>Province Name</th></tr>';
                foreach (array_slice($provinces['data'], 0, 10) as $province) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($province['ProvinceID']) . '</td>';
                    echo '<td>' . htmlspecialchars($province['ProvinceName']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '<small>Hiển thị 10/' . count($provinces['data']) . ' tỉnh</small>';
            } else {
                echo '<div class="error">❌ Lỗi: ' . htmlspecialchars($provinces['message']) . '</div>';
            }
            echo '</div>';
            
            // Test 2: Lấy danh sách quận/huyện Hà Nội (ProvinceID = 201)
            echo '<div class="box">';
            echo '<h3>🏙️ Test 2: Lấy danh sách Quận/Huyện của Hà Nội</h3>';
            
            $districts = $ghn->getDistricts(201); // 201 = Hà Nội
            
            if ($districts['success']) {
                echo '<div class="success">✅ Lấy danh sách quận/huyện thành công (' . count($districts['data']) . ' quận)</div>';
                echo '<table>';
                echo '<tr><th>District ID</th><th>District Name</th></tr>';
                foreach (array_slice($districts['data'], 0, 10) as $district) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($district['DistrictID']) . '</td>';
                    echo '<td>' . htmlspecialchars($district['DistrictName']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '<small>Hiển thị 10/' . count($districts['data']) . ' quận</small>';
            } else {
                echo '<div class="error">❌ Lỗi: ' . htmlspecialchars($districts['message']) . '</div>';
            }
            echo '</div>';
            
            // Test 3: Tính phí vận chuyển (Ví dụ: từ Ba Đình đến Đống Đa)
            echo '<div class="box">';
            echo '<h3>💰 Test 3: Tính phí vận chuyển</h3>';
            echo '<div class="info">Ví dụ: Gửi từ Quận Ba Đình đến Quận Đống Đa, Hà Nội<br>Cân nặng: 5kg, Giá trị đơn: 5.000.000đ</div>';
            
            $shippingFee = $ghn->calculateShippingFee([
                'to_district_id' => 1451, // Quận Đống Đa
                'to_ward_code' => '20311', // Phường Văn Chương
                'weight' => 5000, // 5kg
                'order_value' => 5000000, // 5 triệu
            ]);
            
            if ($shippingFee['success']) {
                echo '<div class="success">✅ Tính phí thành công</div>';
                echo '<table>';
                echo '<tr><th>Loại phí</th><th>Số tiền</th></tr>';
                echo '<tr><td>Phí dịch vụ</td><td>' . number_format($shippingFee['service_fee'] ?? 0) . 'đ</td></tr>';
                echo '<tr><td><strong>Tổng phí vận chuyển</strong></td><td><strong>' . number_format($shippingFee['fee']) . 'đ</strong></td></tr>';
                echo '<tr><td>Thời gian dự kiến</td><td>' . htmlspecialchars($shippingFee['expected_delivery_time'] ?? 'N/A') . '</td></tr>';
                echo '</table>';
            } else {
                echo '<div class="error">❌ Lỗi: ' . htmlspecialchars($shippingFee['message']) . '</div>';
            }
            echo '</div>';
            
            // Test 4: Lấy dịch vụ khả dụng
            echo '<div class="box">';
            echo '<h3>📦 Test 4: Lấy dịch vụ vận chuyển khả dụng</h3>';
            
            $services = $ghn->getAvailableServices(1451); // Quận Đống Đa
            
            if ($services['success']) {
                echo '<div class="success">✅ Lấy dịch vụ thành công (' . count($services['data']) . ' dịch vụ)</div>';
                if (!empty($services['data'])) {
                    echo '<table>';
                    echo '<tr><th>Service ID</th><th>Service Name</th></tr>';
                    foreach ($services['data'] as $service) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($service['service_id'] ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($service['short_name'] ?? '') . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<div class="info">Không có dịch vụ khả dụng</div>';
                }
            } else {
                echo '<div class="error">❌ Lỗi: ' . htmlspecialchars($services['message']) . '</div>';
            }
            echo '</div>';
            
            // Hướng dẫn tiếp theo
            echo '<div class="box">';
            echo '<h3>✅ API hoạt động tốt!</h3>';
            echo '<div class="success">';
            echo '<p><strong>Bước tiếp theo:</strong></p>';
            echo '<ol style="margin-left: 20px; margin-top: 10px;">';
            echo '<li>Tích hợp vào form checkout để tính phí ship tự động</li>';
            echo '<li>Tạo đơn vận chuyển khi khách đặt hàng</li>';
            echo '<li>Thêm trang tracking để khách theo dõi đơn hàng</li>';
            echo '</ol>';
            echo '</div>';
            echo '<a href="/project-ecommerce/public/" class="btn">← Về trang chủ</a>';
            echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" class="btn">🔄 Test lại</a>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
