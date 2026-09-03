<?php
/**
 * Test Permissions System
 * http://localhost/project-ecommerce/public/test-permissions.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Auth.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

session_start();

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Test Permissions</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .test-section { background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .test-section h2 { color: #007bff; margin-bottom: 15px; font-size: 18px; }
        .status { padding: 4px 12px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .pass { background: #d4edda; color: #155724; }
        .fail { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 6px; margin: 10px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        code { background: #e9ecef; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Test Permissions System</h1>
        
        <?php
        use App\Core\Auth;
        
        // Current auth status
        echo "<div class='info'>";
        echo "<strong>Current Session Status:</strong><br>";
        
        if (Auth::check()) {
            echo "✅ <strong>Logged in</strong><br>";
            echo "User ID: <code>" . Auth::id() . "</code><br>";
            echo "Name: <code>" . Auth::name() . "</code><br>";
            echo "Email: <code>" . Auth::email() . "</code><br>";
            echo "Role: <code>" . Auth::role() . "</code><br>";
            
            if (Auth::isAdmin()) {
                echo "<br>👨‍💼 <strong>You are an ADMIN</strong> - Full access granted!";
            } else if (Auth::isCustomer()) {
                echo "<br>👤 <strong>You are a CUSTOMER</strong> - Limited access.";
            }
            
            echo "<br><br><a href='?logout=1' class='btn btn-danger'>Đăng xuất</a>";
            
        } else {
            echo "❌ <strong>Not logged in</strong><br>";
            echo "Please login to test permissions.";
        }
        echo "</div>";
        
        // Handle logout
        if (isset($_GET['logout'])) {
            Auth::logout();
            header('Location: test-permissions.php');
            exit;
        }
        ?>
        
        <div class="test-section">
            <h2>🧪 Quick Login</h2>
            
            <form method="POST" action="test-permissions.php" style="margin-bottom: 15px;">
                <input type="hidden" name="test_login" value="admin">
                <button type="submit" class="btn btn-success">Login as ADMIN</button>
                <small style="display: block; margin-top: 5px; color: #666;">
                    admin@decornest.com / admin123
                </small>
            </form>
            
            <form method="POST" action="test-permissions.php">
                <input type="hidden" name="test_login" value="customer">
                <button type="submit" class="btn btn-success">Login as CUSTOMER</button>
                <small style="display: block; margin-top: 5px; color: #666;">
                    tienhungtrinh59@gmail.com / 12345678
                </small>
            </form>
        </div>
        
        <?php
        // Handle test login
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
            $db = \App\Core\Database::getInstance();
            
            if ($_POST['test_login'] === 'admin') {
                $user = $db->fetch("SELECT * FROM users WHERE email = 'admin@decornest.com'");
            } else {
                $user = $db->fetch("SELECT * FROM users WHERE email = 'tienhungtrinh59@gmail.com'");
            }
            
            if ($user) {
                Auth::login($user);
                header('Location: test-permissions.php');
                exit;
            }
        }
        
        // Test permissions
        if (Auth::check()) {
            echo "<div class='test-section'>";
            echo "<h2>✅ Permission Tests</h2>";
            
            $tests = [
                ['name' => 'Auth::check()', 'result' => Auth::check()],
                ['name' => 'Auth::isAdmin()', 'result' => Auth::isAdmin()],
                ['name' => 'Auth::isCustomer()', 'result' => Auth::isCustomer()],
                ['name' => 'Auth::can("orders.view_own")', 'result' => Auth::can('orders.view_own')],
                ['name' => 'Auth::can("products.create")', 'result' => Auth::can('products.create')],
                ['name' => 'Auth::can("users.delete")', 'result' => Auth::can('users.delete')],
            ];
            
            echo "<table style='width: 100%; border-collapse: collapse;'>";
            echo "<tr style='background: #f0f0f0;'>";
            echo "<th style='text-align: left; padding: 10px; border: 1px solid #ddd;'>Method</th>";
            echo "<th style='text-align: center; padding: 10px; border: 1px solid #ddd;'>Result</th>";
            echo "</tr>";
            
            foreach ($tests as $test) {
                $status = $test['result'] ? 'pass' : 'fail';
                $result = $test['result'] ? '✅ TRUE' : '❌ FALSE';
                
                echo "<tr>";
                echo "<td style='padding: 10px; border: 1px solid #ddd;'><code>{$test['name']}</code></td>";
                echo "<td style='text-align: center; padding: 10px; border: 1px solid #ddd;'><span class='status {$status}'>{$result}</span></td>";
                echo "</tr>";
            }
            
            echo "</table>";
            echo "</div>";
        }
        ?>
        
        <div class="test-section">
            <h2>🔗 Test Links</h2>
            
            <p style="margin-bottom: 15px;">Click để test redirect và access control:</p>
            
            <a href="/project-ecommerce/public/admin" class="btn">
                👨‍💼 /admin
            </a>
            <small style="display: block; margin: 5px 0 15px 0; color: #666;">
                Admin only - Customer sẽ bị redirect
            </small>
            
            <a href="/project-ecommerce/public/dashboard" class="btn">
                👤 /dashboard
            </a>
            <small style="display: block; margin: 5px 0 15px 0; color: #666;">
                Customer dashboard - Guest sẽ redirect login
            </small>
            
            <a href="/project-ecommerce/public/products" class="btn btn-success">
                🛍️ /products
            </a>
            <small style="display: block; margin: 5px 0; color: #666;">
                Public - Tất cả đều truy cập được
            </small>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0; color: #666; font-size: 14px;">
            <strong>📚 Documentation:</strong> 
            <a href="../PERMISSIONS_GUIDE.md" style="color: #007bff;">PERMISSIONS_GUIDE.md</a>
        </div>
        
    </div>
</body>
</html>
