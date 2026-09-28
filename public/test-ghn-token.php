<?php
/**
 * Test GHN API Token mới
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

use App\Services\GhnShippingService;

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test GHN Token</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .success { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 10px 0; }
        .error { background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0; }
        .info { background: #d1ecf1; padding: 15px; border-left: 4px solid #0c5460; margin: 10px 0; }
        pre { background: #f8f8f8; padding: 15px; border: 1px solid #ddd; overflow-x: auto; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
    </style>
</head>
<body>

<h1>🔍 Test GHN API Token</h1>

<?php

echo "<div class='info'>";
echo "<h3>Cấu hình hiện tại:</h3>";
echo "<p><strong>Token:</strong> " . ($_ENV['GHN_API_TOKEN'] ?? '<span style="color:red">CHƯA CÓ</span>') . "</p>";
echo "<p><strong>Shop ID:</strong> " . ($_ENV['GHN_SHOP_ID'] ?? '<span style="color:red">CHƯA CÓ</span>') . "</p>";
echo "<p><strong>API URL:</strong> " . ($_ENV['GHN_API_URL'] ?? '<span style="color:red">CHƯA CÓ</span>') . "</p>";
echo "</div>";

try {
    $ghn = new GhnShippingService();
    
    echo "<h2>📍 Test 1: Lấy danh sách Tỉnh/Thành phố</h2>";
    $provinces = $ghn->getProvinces();
    
    if ($provinces['success']) {
        $count = count($provinces['data']);
        echo "<div class='success'>";
        echo "✅ <strong>THÀNH CÔNG!</strong> Lấy được {$count} tỉnh/thành phố.";
        echo "</div>";
        
        echo "<h3>5 tỉnh/thành đầu tiên:</h3>";
        echo "<pre>";
        foreach (array_slice($provinces['data'], 0, 5) as $province) {
            echo "ID: {$province['ProvinceID']} - {$province['ProvinceName']}\n";
        }
        echo "</pre>";
    } else {
        echo "<div class='error'>";
        echo "❌ <strong>LỖI:</strong> " . ($provinces['message'] ?? 'Không lấy được dữ liệu');
        echo "</div>";
        echo "<pre>" . print_r($provinces, true) . "</pre>";
    }
    
    echo "<h2>🏙️ Test 2: Lấy danh sách Quận/Huyện (Hà Nội - ID: 201)</h2>";
    $districts = $ghn->getDistricts(201);
    
    if ($districts['success']) {
        $count = count($districts['data']);
        echo "<div class='success'>";
        echo "✅ <strong>THÀNH CÔNG!</strong> Lấy được {$count} quận/huyện.";
        echo "</div>";
        
        echo "<h3>5 quận/huyện đầu tiên:</h3>";
        echo "<pre>";
        foreach (array_slice($districts['data'], 0, 5) as $district) {
            echo "ID: {$district['DistrictID']} - {$district['DistrictName']}\n";
        }
        echo "</pre>";
    } else {
        echo "<div class='error'>";
        echo "❌ <strong>LỖI:</strong> " . ($districts['message'] ?? 'Không lấy được dữ liệu');
        echo "</div>";
        echo "<pre>" . print_r($districts, true) . "</pre>";
    }
    
    echo "<h2>📮 Test 3: Lấy danh sách Phường/Xã (Ba Đình - ID: 1542)</h2>";
    $wards = $ghn->getWards(1542);
    
    if ($wards['success']) {
        $count = count($wards['data']);
        echo "<div class='success'>";
        echo "✅ <strong>THÀNH CÔNG!</strong> Lấy được {$count} phường/xã.";
        echo "</div>";
        
        echo "<h3>5 phường/xã đầu tiên:</h3>";
        echo "<pre>";
        foreach (array_slice($wards['data'], 0, 5) as $ward) {
            echo "Code: {$ward['WardCode']} - {$ward['WardName']}\n";
        }
        echo "</pre>";
    } else {
        echo "<div class='error'>";
        echo "❌ <strong>LỖI:</strong> " . ($wards['message'] ?? 'Không lấy được dữ liệu');
        echo "</div>";
        echo "<pre>" . print_r($wards, true) . "</pre>";
    }
    
    echo "<h2>💰 Test 4: Tính phí vận chuyển (Hà Nội → Ba Đình)</h2>";
    $fee = $ghn->calculateShippingFee([
        'to_district_id' => 1542,
        'to_ward_code' => '20308',
        'weight' => 5000,
        'order_value' => 1000000,
    ]);
    
    if ($fee['success']) {
        echo "<div class='success'>";
        echo "✅ <strong>THÀNH CÔNG!</strong> Phí vận chuyển: <strong>" . number_format($fee['fee']) . "đ</strong>";
        echo "</div>";
        echo "<pre>" . print_r($fee, true) . "</pre>";
    } else {
        echo "<div class='error'>";
        echo "❌ <strong>LỖI:</strong> " . ($fee['message'] ?? 'Không tính được phí');
        echo "</div>";
        echo "<pre>" . print_r($fee, true) . "</pre>";
    }
    
    echo "<hr>";
    echo "<h2>📋 Tổng kết</h2>";
    
    $allSuccess = $provinces['success'] && $districts['success'] && $wards['success'] && $fee['success'];
    
    if ($allSuccess) {
        echo "<div class='success'>";
        echo "<h3>✅ TẤT CẢ API HOẠT ĐỘNG BÌNH THƯỜNG!</h3>";
        echo "<p>Token GHN mới đã hoạt động. Dropdown trên trang checkout sẽ hiển thị dữ liệu.</p>";
        echo "<p><strong>Bước tiếp theo:</strong></p>";
        echo "<ol>";
        echo "<li>Vào trang checkout: <a href='/checkout'>/checkout</a></li>";
        echo "<li>Chọn Tỉnh/Quận/Phường từ dropdown</li>";
        echo "<li>Xem phí ship tự động tính</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ CÓ LỖI XẢY RA</h3>";
        echo "<p>Kiểm tra lại:</p>";
        echo "<ul>";
        echo "<li>Token GHN có đúng không?</li>";
        echo "<li>Shop ID có đúng không?</li>";
        echo "<li>Đã kích hoạt shop trên GHN chưa?</li>";
        echo "</ul>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ EXCEPTION:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

?>

<hr>
<p><small>Kiểm tra lúc: <?= date('Y-m-d H:i:s') ?></small></p>

</body>
</html>
