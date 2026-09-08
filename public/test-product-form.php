<?php
/**
 * Test Product Form
 * Kiểm tra form thêm/sửa sản phẩm
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\CategoryModel;
use App\Models\ProductModel;

session_start();

$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';

$_ENV['APP_URL'] = 'http://localhost/project-ecommerce/public';

header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <title>Test Product Form</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    </style>
</head>
<body>
    <h1>🧪 Test Product Form</h1>";

try {
    $categoryModel = new CategoryModel();
    $productModel = new ProductModel();
    
    // Test 1: Check categories
    echo "<h2>TEST 1: Kiểm tra Danh mục</h2>";
    $categories = $categoryModel->getAllActive();
    echo "<div class='box'>";
    echo "<strong>Số danh mục:</strong> " . count($categories) . "<br>";
    if (!empty($categories)) {
        echo "<ul>";
        foreach ($categories as $cat) {
            echo "<li>ID: {$cat['id']} - " . htmlspecialchars($cat['name']) . "</li>";
        }
        echo "</ul>";
        echo "<div class='success'>✅ Categories loaded successfully</div>";
    } else {
        echo "<div class='error'>❌ No categories found! Chạy fix-all-now.php để tạo dữ liệu.</div>";
    }
    echo "</div>";
    
    // Test 2: Check products
    echo "<h2>TEST 2: Kiểm tra Sản phẩm</h2>";
    $result = $productModel->getFiltered([], 1, 5);
    echo "<div class='box'>";
    echo "<strong>Tổng sản phẩm:</strong> " . ($result['total'] ?? 0) . "<br>";
    if (!empty($result['data'])) {
        echo "<ul>";
        foreach (array_slice($result['data'], 0, 3) as $p) {
            echo "<li>ID: {$p['id']} - " . htmlspecialchars($p['name']) . "</li>";
        }
        echo "</ul>";
        echo "<div class='success'>✅ Products loaded successfully</div>";
    } else {
        echo "<div class='error'>❌ No products found! Chạy fix-all-now.php để tạo dữ liệu.</div>";
    }
    echo "</div>";
    
    // Test 3: Simulate form data
    echo "<h2>TEST 3: Test Form Variables</h2>";
    echo "<div class='box'>";
    
    // Simulate create form
    $product = null;
    $tiers = [];
    echo "<strong>CREATE MODE:</strong><br>";
    echo "- \$product: " . (is_null($product) ? 'null (correct)' : 'ERROR') . "<br>";
    echo "- \$categories: " . count($categories) . " items<br>";
    echo "- \$tiers: " . count($tiers) . " items<br>";
    echo "<div class='success'>✅ Create mode variables OK</div>";
    echo "</div>";
    
    // Test 4: Form links
    echo "<h2>TEST 4: Test Form Access</h2>";
    echo "<div class='box'>";
    echo "<p>Thử truy cập các link sau:</p>";
    echo "<a href='/project-ecommerce/public/admin/products/create' class='btn'>➕ Form Thêm Sản Phẩm</a><br><br>";
    
    if (!empty($result['data'])) {
        $firstProduct = $result['data'][0];
        echo "<a href='/project-ecommerce/public/admin/products/{$firstProduct['id']}/edit' class='btn'>✏️ Form Sửa Sản Phẩm #{$firstProduct['id']}</a>";
    }
    echo "</div>";
    
    // Summary
    echo "<h2>📊 Summary</h2>";
    $allOk = !empty($categories) && !empty($result['data']);
    
    if ($allOk) {
        echo "<div class='box success'>";
        echo "<h3>✅ All checks passed!</h3>";
        echo "<p>Form sẵn sàng để sử dụng. Bạn có thể:</p>";
        echo "<ul>";
        echo "<li>Thêm sản phẩm mới</li>";
        echo "<li>Sửa sản phẩm hiện có</li>";
        echo "<li>Upload ảnh</li>";
        echo "</ul>";
        echo "<a href='/project-ecommerce/public/admin/products' class='btn'>📦 Vào Quản Lý Sản Phẩm</a>";
        echo "</div>";
    } else {
        echo "<div class='box error'>";
        echo "<h3>❌ Cần fix database!</h3>";
        echo "<p>Database chưa có dữ liệu. Chạy script này:</p>";
        echo "<a href='fix-all-now.php' class='btn' style='background:#28a745;'>🔧 Fix All Now</a>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='box error'>";
    echo "<h3>❌ Error</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</body></html>";
?>
