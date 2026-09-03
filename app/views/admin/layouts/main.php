<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> - DecorNest Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cream': '#F5F0E8', 'beige': '#E8DFD0', 'sand': '#D4C4A8',
                        'wood': '#A0856A', 'wooddk': '#6B5744', 'charcoal': '#2C2C2C', 'muted': '#6B6B6B',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 text-charcoal antialiased" x-data="{ sidebarOpen: true }">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-60' : 'w-0 overflow-hidden'" class="bg-charcoal text-white flex-shrink-0 transition-all duration-300 flex flex-col">
        <!-- Logo -->
        <div class="px-5 py-5 border-b border-white/10">
            <a href="<?= $_ENV['APP_URL'] ?? '' ?>/admin" class="text-lg font-bold">
                Decor<span class="text-wood">Nest</span>
                <span class="text-xs font-normal text-white/50 ml-1">Admin</span>
            </a>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto py-4 px-3">
            <?php
            $navItems = [
                ['icon' => '📊', 'label' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['icon' => '📦', 'label' => 'Sản phẩm', 'url' => '/admin/products'],
                ['icon' => '🏷️', 'label' => 'Danh mục', 'url' => '/admin/categories'],
                ['icon' => '🛒', 'label' => 'Đơn hàng', 'url' => '/admin/orders'],
                ['icon' => '📋', 'label' => 'Báo giá', 'url' => '/admin/quotes'],
                ['icon' => '💳', 'label' => 'Thanh toán', 'url' => '/admin/payments'],
                ['icon' => '👥', 'label' => 'Khách hàng', 'url' => '/admin/users'],
                ['icon' => '📈', 'label' => 'Báo cáo', 'url' => '/admin/reports'],
            ];
            $base    = $_ENV['APP_URL'] ?? '';
            $current = $_SERVER['REQUEST_URI'] ?? '';
            ?>
            <?php foreach ($navItems as $item): ?>
            <a href="<?= $base . $item['url'] ?>"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 text-sm transition
                      <?= str_contains($current, $item['url']) ? 'bg-wood text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?>">
                <span class="text-base"><?= $item['icon'] ?></span>
                <?= $item['label'] ?>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Bottom -->
        <div class="p-4 border-t border-white/10">
            <a href="<?= $base ?>" target="_blank" class="block text-xs text-white/50 hover:text-white mb-2">← Xem website</a>
            <a href="<?= $base ?>/user/logout" class="block text-xs text-red-400 hover:text-red-300">Đăng xuất</a>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="p-1 text-gray-500 hover:text-charcoal">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-3 text-sm">
                <span class="text-muted">👋 Xin chào, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong></span>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto p-6">
            <?php if (!empty($_SESSION['flash'])): ?>
            <?php $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
            <div class="mb-4 p-3 rounded-lg text-sm <?= $f['type'] === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
                <?= htmlspecialchars($f['message']) ?>
            </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<script>const APP_URL = '<?= $_ENV['APP_URL'] ?? '' ?>';</script>
<script src="<?= $_ENV['APP_URL'] ?? '' ?>/assets/js/admin.js"></script>
</body>
</html>
