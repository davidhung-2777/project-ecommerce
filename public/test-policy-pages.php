<?php
/**
 * Test Policy Pages
 * Kiểm tra xem tất cả trang chính sách có hoạt động không
 */

define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

$baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';

$policyPages = [
    'return' => [
        'url' => '/policy/return',
        'title' => 'Chính Sách Đổi Trả',
        'controller' => 'PolicyController',
        'action' => 'returnPolicy'
    ],
    'warranty' => [
        'url' => '/policy/warranty',
        'title' => 'Chính Sách Bảo Hành',
        'controller' => 'PolicyController',
        'action' => 'warrantyPolicy'
    ],
    'shipping' => [
        'url' => '/policy/shipping',
        'title' => 'Chính Sách Giao Hàng',
        'controller' => 'PolicyController',
        'action' => 'shippingPolicy'
    ],
    'privacy' => [
        'url' => '/policy/privacy',
        'title' => 'Chính Sách Bảo Mật',
        'controller' => 'PolicyController',
        'action' => 'privacyPolicy'
    ],
    'terms' => [
        'url' => '/policy/terms',
        'title' => 'Điều Khoản Dịch Vụ',
        'controller' => 'PolicyController',
        'action' => 'termsOfService'
    ]
];

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Policy Pages - DecorNest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 py-12">
        
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                🧪 Test Policy Pages
            </h1>
            <p class="text-gray-600 mb-6">
                Kiểm tra tất cả trang chính sách đã được tạo và routes đã hoạt động
            </p>
            
            <!-- Controller Check -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h2 class="text-lg font-semibold text-blue-900 mb-2">📋 Controller Check</h2>
                <?php
                $controllerPath = ROOT_PATH . '/app/controllers/PolicyController.php';
                if (file_exists($controllerPath)) {
                    echo '<p class="text-green-700">✅ PolicyController.php exists</p>';
                    
                    require_once $controllerPath;
                    
                    if (class_exists('App\\Controllers\\PolicyController')) {
                        echo '<p class="text-green-700">✅ PolicyController class loaded</p>';
                        
                        $controller = new App\Controllers\PolicyController();
                        $methods = get_class_methods($controller);
                        
                        echo '<p class="text-sm text-gray-600 mt-2">Methods: ' . implode(', ', $methods) . '</p>';
                    } else {
                        echo '<p class="text-red-700">❌ PolicyController class not found</p>';
                    }
                } else {
                    echo '<p class="text-red-700">❌ PolicyController.php not found</p>';
                }
                ?>
            </div>
            
            <!-- View Files Check -->
            <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                <h2 class="text-lg font-semibold text-purple-900 mb-3">📄 View Files Check</h2>
                <div class="space-y-2">
                    <?php foreach ($policyPages as $key => $page): ?>
                        <?php
                        $viewPath = ROOT_PATH . '/app/views/frontend/pages/policy/' . $key . '-policy.php';
                        $exists = file_exists($viewPath);
                        $fileSize = $exists ? filesize($viewPath) : 0;
                        ?>
                        <div class="flex items-center justify-between py-2 border-b border-purple-100">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl"><?= $exists ? '✅' : '❌' ?></span>
                                <div>
                                    <p class="font-semibold text-gray-900"><?= $page['title'] ?></p>
                                    <p class="text-xs text-gray-500"><?= $key ?>-policy.php</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php if ($exists): ?>
                                    <span class="text-sm text-green-600 font-medium"><?= number_format($fileSize) ?> bytes</span>
                                <?php else: ?>
                                    <span class="text-sm text-red-600 font-medium">Not found</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Routes Check -->
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <h2 class="text-lg font-semibold text-green-900 mb-3">🔗 Routes Check</h2>
                <p class="text-sm text-gray-600 mb-3">
                    Click vào các link dưới đây để test từng trang:
                </p>
                <div class="grid gap-3">
                    <?php foreach ($policyPages as $key => $page): ?>
                        <a href="<?= $baseUrl . $page['url'] ?>" 
                           target="_blank"
                           class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:border-green-500 hover:shadow-md transition group">
                            <div>
                                <p class="font-semibold text-gray-900 group-hover:text-green-600">
                                    <?= $page['title'] ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?= $page['controller'] ?>::<?= $page['action'] ?>()
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-blue-600 font-mono"><?= $page['url'] ?></span>
                                <span class="ml-2 text-gray-400">→</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Footer Links Check -->
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <h2 class="text-lg font-semibold text-amber-900 mb-3">🔗 Footer Links Check</h2>
                <p class="text-sm text-gray-600 mb-3">
                    Kiểm tra footer đã cập nhật links chưa:
                </p>
                <?php
                $footerPath = ROOT_PATH . '/app/views/frontend/partials/footer.php';
                if (file_exists($footerPath)) {
                    $footerContent = file_get_contents($footerPath);
                    
                    $checks = [
                        '/policy/return' => 'Chính sách đổi trả',
                        '/policy/warranty' => 'Bảo hành',
                        '/policy/shipping' => 'Chính sách giao hàng',
                        '/policy/privacy' => 'Chính sách bảo mật',
                        '/policy/terms' => 'Điều khoản dịch vụ'
                    ];
                    
                    echo '<div class="space-y-2">';
                    foreach ($checks as $url => $name) {
                        $found = strpos($footerContent, $url) !== false;
                        $icon = $found ? '✅' : '❌';
                        $color = $found ? 'text-green-700' : 'text-red-700';
                        echo "<p class='$color'>$icon Footer contains: $url ($name)</p>";
                    }
                    echo '</div>';
                } else {
                    echo '<p class="text-red-700">❌ Footer file not found</p>';
                }
                ?>
            </div>
            
            <!-- App.php Routes Check -->
            <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                <h2 class="text-lg font-semibold text-indigo-900 mb-3">⚙️ App.php Routes Check</h2>
                <?php
                $appPath = ROOT_PATH . '/app/core/App.php';
                if (file_exists($appPath)) {
                    $appContent = file_get_contents($appPath);
                    
                    echo '<div class="space-y-2">';
                    
                    // Check if PolicyController routes exist
                    $hasRoutes = strpos($appContent, 'PolicyController') !== false;
                    if ($hasRoutes) {
                        echo '<p class="text-green-700">✅ PolicyController routes found in App.php</p>';
                        
                        // Count how many routes
                        preg_match_all('/PolicyController/', $appContent, $matches);
                        $count = count($matches[0]);
                        echo "<p class='text-sm text-gray-600'>Found $count PolicyController route references</p>";
                        
                        // Check each specific route
                        foreach ($policyPages as $key => $page) {
                            $found = strpos($appContent, "'/policy/$key'") !== false || 
                                     strpos($appContent, '"/policy/' . $key . '"') !== false;
                            $icon = $found ? '✅' : '❌';
                            $color = $found ? 'text-green-700' : 'text-red-700';
                            echo "<p class='$color text-sm'>$icon Route: /policy/$key</p>";
                        }
                    } else {
                        echo '<p class="text-red-700">❌ No PolicyController routes found in App.php</p>';
                        echo '<p class="text-sm text-gray-600 mt-2">You need to add routes manually!</p>';
                    }
                    
                    echo '</div>';
                } else {
                    echo '<p class="text-red-700">❌ App.php not found</p>';
                }
                ?>
            </div>
            
        </div>
        
        <!-- Summary -->
        <div class="bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-200 rounded-2xl p-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">✅ Test Summary</h2>
            <p class="text-gray-700 mb-4">
                Nếu tất cả check đều pass (✅), hệ thống policy pages đã hoàn thành!
            </p>
            <div class="flex gap-3 justify-center">
                <a href="<?= $baseUrl ?>" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition">
                    🏠 Về Trang Chủ
                </a>
                <a href="<?= $baseUrl ?>/policy/return" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                    🧪 Test Policy Page
                </a>
            </div>
        </div>
        
    </div>
</body>
</html>
