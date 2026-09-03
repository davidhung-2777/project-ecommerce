<?php
/**
 * Cron Job: Expire Orders
 * 
 * Chạy mỗi phút để tự động set các đơn hàng pending quá hạn thành expired
 * 
 * Cách cài đặt:
 * 
 * LINUX/MAC (crontab):
 * * * * * * php /path/to/project/cron/expire-orders.php >> /path/to/logs/cron.log 2>&1
 * 
 * WINDOWS (Task Scheduler):
 * Program: C:\xampp\php\php.exe
 * Arguments: C:\xampp\htdocs\project-ecommerce\cron\expire-orders.php
 * Trigger: Repeat task every 1 minute
 */

// Only allow CLI execution
if (PHP_SAPI !== 'cli') {
    die('This script can only be run from command line.');
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/core/Database.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$startTime = microtime(true);
echo "[" . date('Y-m-d H:i:s') . "] Starting expire orders cron job...\n";

try {
    $db = \App\Core\Database::getInstance();
    
    // Tìm các đơn hàng:
    // - payment_status = 'pending'
    // - expires_at < NOW()
    // - status != 'expired' (chưa bị expire)
    
    $sql = "SELECT id, order_code, order_number, expires_at 
            FROM orders 
            WHERE payment_status = 'pending' 
              AND expires_at IS NOT NULL
              AND expires_at < NOW()
              AND status != 'expired'
              AND status != 'cancelled'";
    
    $expiredOrders = $db->fetchAll($sql);
    
    $count = count($expiredOrders);
    
    if ($count === 0) {
        echo "No orders to expire.\n";
    } else {
        echo "Found {$count} expired order(s).\n";
        
        $db->beginTransaction();
        
        foreach ($expiredOrders as $order) {
            // Update status
            $db->update('orders', [
                'status' => 'expired',
                'admin_note' => 'Tự động hủy do quá hạn thanh toán (cron job)',
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$order['id']]);
            
            echo "  - Expired order: {$order['order_code']} ({$order['order_number']})\n";
            
            // TODO: Gửi email thông báo cho khách hàng về đơn hàng hết hạn
            // TODO: Log vào system log
        }
        
        $db->commit();
        echo "Successfully expired {$count} order(s).\n";
    }
    
    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000, 2);
    
    echo "[" . date('Y-m-d H:i:s') . "] Cron job completed in {$duration}ms.\n";
    echo str_repeat('-', 60) . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    error_log("Expire orders cron error: " . $e->getMessage());
    exit(1);
}
