<?php
/**
 * SePay API Test & Debug Tool
 * 
 * Công cụ để test kết nối SePay API và xem lịch sử giao dịch
 * 
 * URL: http://localhost/project-ecommerce/public/test-sepay.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/services/payment/SepayService.php';

// Load environment
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$sepay = new SepayService();
$action = $_GET['action'] ?? 'dashboard';

// Handle actions
$testResult = null;
$transactions = [];
$balance = null;
$accounts = [];

switch ($action) {
    case 'test':
        $testResult = $sepay->testConnection();
        break;
        
    case 'transactions':
        $fromDate = $_GET['from'] ?? date('Y-m-d', strtotime('-7 days'));
        $toDate = $_GET['to'] ?? date('Y-m-d');
        $limit = (int)($_GET['limit'] ?? 100);
        
        $result = $sepay->getTransactionHistory($fromDate, $toDate, $limit);
        if ($result['success']) {
            $transactions = $result['data'];
        }
        break;
        
    case 'balance':
        $balance = $sepay->getBalance();
        break;
        
    case 'accounts':
        $accounts = $sepay->getLinkedAccounts();
        break;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SePay API Test Tool - DecorNest</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .result-success { background: #d1fae5; border-left: 4px solid #10b981; }
        .result-error { background: #fee2e2; border-left: 4px solid #ef4444; }
        .result-info { background: #dbeafe; border-left: 4px solid #3b82f6; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto p-8">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-xl mb-6 shadow-lg">
            <h1 class="text-3xl font-bold mb-2">🏦 SePay API Test Tool</h1>
            <p class="text-blue-100">Test và debug SePay API integration</p>
            <p class="text-sm text-blue-200 mt-2">
                <strong>Webhook URL:</strong> 
                <?= $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public' ?>/webhook/sepay
            </p>
        </div>

        <!-- Config Status -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-600 mb-1">API Key</div>
                <div class="font-bold <?= !empty($_ENV['SEPAY_API_KEY']) && $_ENV['SEPAY_API_KEY'] !== 'your_sepay_api_key_here' ? 'text-green-600' : 'text-red-600' ?>">
                    <?= !empty($_ENV['SEPAY_API_KEY']) && $_ENV['SEPAY_API_KEY'] !== 'your_sepay_api_key_here' ? '✓ Configured' : '✗ Not Set' ?>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-600 mb-1">Account Number</div>
                <div class="font-bold text-blue-600">
                    <?= $_ENV['SEPAY_ACCOUNT_NUMBER'] ?? 'Not Set' ?>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-600 mb-1">Bank Code</div>
                <div class="font-bold text-purple-600">
                    <?= $_ENV['SEPAY_BANK_CODE'] ?? 'Not Set' ?>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-600 mb-1">Webhook Secret</div>
                <div class="font-bold <?= !empty($_ENV['SEPAY_WEBHOOK_SECRET']) && $_ENV['SEPAY_WEBHOOK_SECRET'] !== 'your_random_webhook_secret_change_this' ? 'text-green-600' : 'text-yellow-600' ?>">
                    <?= !empty($_ENV['SEPAY_WEBHOOK_SECRET']) && $_ENV['SEPAY_WEBHOOK_SECRET'] !== 'your_random_webhook_secret_change_this' ? '✓ Set' : '⚠ Default' ?>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-bold mb-4">🔧 Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="?action=test" class="bg-blue-600 text-white px-4 py-3 rounded-lg text-center hover:bg-blue-700 transition">
                    🔌 Test Connection
                </a>
                <a href="?action=transactions" class="bg-green-600 text-white px-4 py-3 rounded-lg text-center hover:bg-green-700 transition">
                    📜 Transactions
                </a>
                <a href="?action=balance" class="bg-purple-600 text-white px-4 py-3 rounded-lg text-center hover:bg-purple-700 transition">
                    💰 Check Balance
                </a>
                <a href="?action=accounts" class="bg-indigo-600 text-white px-4 py-3 rounded-lg text-center hover:bg-indigo-700 transition">
                    🏦 Linked Accounts
                </a>
            </div>
        </div>

        <!-- Test Result -->
        <?php if ($testResult): ?>
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-bold mb-4">✅ Connection Test Result</h2>
            <div class="<?= $testResult['success'] ? 'result-success' : 'result-error' ?> p-4 rounded-lg">
                <div class="font-bold mb-2">
                    <?= $testResult['success'] ? '✓ Success' : '✗ Failed' ?>
                </div>
                <div class="text-sm"><?= htmlspecialchars($testResult['message']) ?></div>
                
                <?php if ($testResult['success'] && !empty($testResult['accounts'])): ?>
                <div class="mt-4">
                    <strong>Connected Accounts:</strong>
                    <ul class="list-disc list-inside mt-2">
                        <?php foreach ($testResult['accounts'] as $account): ?>
                        <li><?= htmlspecialchars($account['bank_name'] ?? 'N/A') ?> - <?= htmlspecialchars($account['account_number'] ?? 'N/A') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Balance -->
        <?php if ($balance): ?>
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-bold mb-4">💰 Account Balance</h2>
            <?php if ($balance['success']): ?>
            <div class="result-success p-4 rounded-lg">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-600">Balance</div>
                        <div class="text-2xl font-bold text-green-600">
                            <?= number_format($balance['balance'], 0, ',', '.') ?>đ
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Account</div>
                        <div class="font-bold"><?= htmlspecialchars($balance['account_number']) ?></div>
                        <div class="text-sm"><?= htmlspecialchars($balance['account_name']) ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($balance['bank_name']) ?></div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="result-error p-4 rounded-lg">
                <?= htmlspecialchars($balance['message']) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Transactions -->
        <?php if ($action === 'transactions'): ?>
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-bold mb-4">📜 Transaction History</h2>
            
            <!-- Filter Form -->
            <form method="GET" class="mb-4 p-4 bg-gray-50 rounded-lg">
                <input type="hidden" name="action" value="transactions">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="text-sm text-gray-600">From Date</label>
                        <input type="date" name="from" value="<?= $_GET['from'] ?? date('Y-m-d', strtotime('-7 days')) ?>" 
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">To Date</label>
                        <input type="date" name="to" value="<?= $_GET['to'] ?? date('Y-m-d') ?>" 
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Limit</label>
                        <input type="number" name="limit" value="<?= $_GET['limit'] ?? 100 ?>" 
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            🔍 Filter
                        </button>
                    </div>
                </div>
            </form>

            <?php if (empty($transactions)): ?>
            <div class="result-info p-4 rounded-lg text-center">
                <div class="text-gray-600">Không có giao dịch nào trong khoảng thời gian này</div>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Reference</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-left">Bank</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($transactions as $txn): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3"><?= htmlspecialchars($txn['transaction_date'] ?? '') ?></td>
                            <td class="px-4 py-3 font-mono text-xs"><?= htmlspecialchars($txn['reference_number'] ?? '') ?></td>
                            <td class="px-4 py-3 text-right font-bold text-green-600">
                                +<?= number_format($txn['amount_in'] ?? 0, 0, ',', '.') ?>đ
                            </td>
                            <td class="px-4 py-3"><?= htmlspecialchars($txn['transaction_content'] ?? '') ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($txn['bank_brand_name'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                Total: <strong><?= count($transactions) ?></strong> transactions
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Linked Accounts -->
        <?php if (!empty($accounts)): ?>
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-bold mb-4">🏦 Linked Bank Accounts</h2>
            <?php if ($accounts['success']): ?>
            <div class="grid gap-4">
                <?php foreach ($accounts['accounts'] as $account): ?>
                <div class="result-success p-4 rounded-lg">
                    <div class="font-bold text-lg"><?= htmlspecialchars($account['bank_name'] ?? 'N/A') ?></div>
                    <div class="text-sm mt-2">
                        <strong>Account:</strong> <?= htmlspecialchars($account['account_number'] ?? 'N/A') ?><br>
                        <strong>Name:</strong> <?= htmlspecialchars($account['account_name'] ?? 'N/A') ?><br>
                        <strong>Status:</strong> <span class="text-green-600">● Active</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="result-error p-4 rounded-lg">
                <?= htmlspecialchars($accounts['message']) ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Documentation -->
        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 p-6 rounded-lg">
            <h2 class="text-xl font-bold mb-4">📚 Hướng Dẫn Setup SePay</h2>
            
            <div class="space-y-4">
                <div>
                    <h3 class="font-bold text-lg mb-2">1️⃣ Đăng ký tài khoản SePay</h3>
                    <p class="text-sm text-gray-700">
                        Truy cập: <a href="https://my.sepay.vn/" target="_blank" class="text-blue-600 underline">https://my.sepay.vn/</a> 
                        và đăng ký tài khoản
                    </p>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-2">2️⃣ Liên kết tài khoản ngân hàng</h3>
                    <p class="text-sm text-gray-700">
                        - Vào menu "Tài khoản ngân hàng"<br>
                        - Thêm tài khoản Vietcombank: <strong><?= $_ENV['SEPAY_ACCOUNT_NUMBER'] ?? 'XXXXXXXXXX' ?></strong><br>
                        - Xác thực bằng cách chuyển khoản số tiền nhỏ
                    </p>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-2">3️⃣ Lấy API Key</h3>
                    <p class="text-sm text-gray-700">
                        - Vào menu "API & Webhook" → "API Key"<br>
                        - Copy API Key và paste vào file <code class="bg-gray-200 px-2 py-1 rounded">.env</code>:<br>
                        <code class="bg-gray-800 text-green-400 px-3 py-2 rounded block mt-2">
                            SEPAY_API_KEY=your_actual_api_key_here
                        </code>
                    </p>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-2">4️⃣ Cấu hình Webhook</h3>
                    <p class="text-sm text-gray-700">
                        - Vào menu "API & Webhook" → "Webhook"<br>
                        - Thêm Webhook URL: <br>
                        <code class="bg-gray-800 text-blue-400 px-3 py-2 rounded block mt-2">
                            <?= $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public' ?>/webhook/sepay
                        </code>
                        <br>
                        <span class="text-orange-600 font-bold">⚠️ Lưu ý:</span> Webhook chỉ hoạt động khi server public (có domain/IP public).<br>
                        Để test localhost, dùng <a href="https://ngrok.com/" target="_blank" class="text-blue-600 underline">ngrok</a> hoặc 
                        <a href="https://localtunnel.github.io/www/" target="_blank" class="text-blue-600 underline">localtunnel</a>.
                    </p>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-2">5️⃣ Test kết nối</h3>
                    <p class="text-sm text-gray-700">
                        Click button "🔌 Test Connection" ở trên để kiểm tra API hoạt động
                    </p>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
