<?php
// Test voucher functionality
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

session_start();

use App\Models\VoucherModel;
use App\Models\ProductModel;
use App\Services\VoucherService;

$voucherModel = new VoucherModel();
$productModel = new ProductModel();
$voucherService = new VoucherService();

echo "<h1>Test Voucher System</h1>";
echo "<hr>";

// Test 1: Get all vouchers
echo "<h2>1. All Vouchers in Database</h2>";
$result = $voucherModel->getAll('', '', 1, 100);
echo "<p>Total vouchers: " . $result['total'] . "</p>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Active</th><th>Start Date</th><th>End Date</th><th>Used Count</th></tr>";
foreach ($result['data'] as $v) {
    echo "<tr>";
    echo "<td>{$v['id']}</td>";
    echo "<td><strong>{$v['code']}</strong></td>";
    echo "<td>{$v['discount_type']}</td>";
    echo "<td>" . number_format($v['discount_value']) . "</td>";
    echo "<td>" . number_format($v['min_order_value']) . "</td>";
    echo "<td>" . ($v['is_active'] ? '✅' : '❌') . "</td>";
    echo "<td>{$v['start_date']}</td>";
    echo "<td>{$v['end_date']}</td>";
    echo "<td>{$v['used_count']}</td>";
    echo "</tr>";
}
echo "</table>";

// Test 2: Find voucher by code
echo "<h2>2. Test Find By Code</h2>";
$testCode = '123'; // Thay bằng code từ database
$voucher = $voucherModel->findByCode($testCode);
if ($voucher) {
    echo "<p>✅ Found voucher: <strong>{$voucher['code']}</strong></p>";
    echo "<pre>" . print_r($voucher, true) . "</pre>";
} else {
    echo "<p>❌ Voucher with code '$testCode' not found</p>";
}

// Test 3: Validate voucher with cart items
echo "<h2>3. Test Validate Voucher</h2>";
if ($voucher) {
    // Get sample products
    $products = $productModel->findAll('id ASC', 5);
    if (!empty($products)) {
        $testItems = [];
        foreach ($products as $product) {
            $testItems[] = [
                'product_id' => $product['id'],
                'quantity' => 1,
                'unit_price' => $product['price']
            ];
        }
        
        echo "<p>Testing with " . count($testItems) . " products:</p>";
        echo "<ul>";
        foreach ($testItems as $item) {
            $product = $productModel->find($item['product_id']);
            echo "<li>Product #{$item['product_id']}: {$product['name']} - " . number_format($item['unit_price']) . "đ</li>";
        }
        echo "</ul>";
        
        $userId = 1; // Test with user ID 1
        $validation = $voucherService->validateVoucher($voucher, $testItems, $userId);
        
        if ($validation['success']) {
            echo "<p>✅ <strong style='color: green;'>Voucher is valid!</strong></p>";
            echo "<p>Discount: " . number_format($validation['discount']) . "đ</p>";
            echo "<p>Eligible Subtotal: " . number_format($validation['eligible_subtotal']) . "đ</p>";
        } else {
            echo "<p>❌ <strong style='color: red;'>Voucher validation failed</strong></p>";
            echo "<p>Reason: {$validation['message']}</p>";
        }
    }
}

// Test 4: Check voucher scope (products/categories)
echo "<h2>4. Voucher Scope</h2>";
if ($voucher) {
    $products = $voucherModel->getProducts($voucher['id']);
    $categories = $voucherModel->getCategories($voucher['id']);
    
    echo "<p>Applicable to:</p>";
    if (empty($products) && empty($categories)) {
        echo "<p>✅ All products (no restrictions)</p>";
    } else {
        if (!empty($products)) {
            echo "<p>Specific products: " . count($products) . "</p>";
            echo "<ul>";
            foreach ($products as $p) {
                $prod = $productModel->find($p['product_id']);
                echo "<li>Product #{$p['product_id']}: " . ($prod['name'] ?? 'Unknown') . "</li>";
            }
            echo "</ul>";
        }
        if (!empty($categories)) {
            echo "<p>Specific categories: " . count($categories) . "</p>";
        }
    }
}

echo "<hr>";
echo "<a href='/project-ecommerce12/'>← Back to Home</a> | ";
echo "<a href='/project-ecommerce12/cart'>Go to Cart</a>";
