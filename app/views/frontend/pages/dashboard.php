<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Bảng Điều Khiển - DecorNest';
?>

<div class="bg-cream/40 py-10 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Welcome Banner -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-beige shadow-warm mb-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sage-light text-sage text-xs font-semibold mb-2">
                    <span>🌿</span> Chào mừng trở lại
                </div>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">
                    Xin chào, <?= htmlspecialchars($user['name'] ?? 'Quý khách') ?>!
                </h1>
                <p class="text-xs sm:text-sm text-muted mt-1">Quản lý các đơn hàng và không gian phòng ngủ của bạn.</p>
            </div>
            <a href="<?= $baseUrl ?>/products" class="bg-charcoal text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                Khám phá sản phẩm →
            </a>
        </div>
        
        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-10">
            
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-beige shadow-warm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-muted font-medium mb-1">Tổng đơn hàng</p>
                        <p class="text-2xl sm:text-3xl font-serif font-bold text-charcoal"><?= $stats['total_orders'] ?? 0 ?></p>
                    </div>
                    <div class="w-12 h-12 bg-cream text-wood rounded-2xl flex items-center justify-center text-xl">
                        📦
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-beige shadow-warm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-muted font-medium mb-1">Đang xử lý</p>
                        <p class="text-2xl sm:text-3xl font-serif font-bold text-amber-warm"><?= $stats['pending_orders'] ?? 0 ?></p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-warm rounded-2xl flex items-center justify-center text-xl">
                        ⏳
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-beige shadow-warm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-muted font-medium mb-1">Đã hoàn thành</p>
                        <p class="text-2xl sm:text-3xl font-serif font-bold text-sage"><?= $stats['completed_orders'] ?? 0 ?></p>
                    </div>
                    <div class="w-12 h-12 bg-sage-light text-sage rounded-2xl flex items-center justify-center text-xl">
                        ✅
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-beige shadow-warm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-muted font-medium mb-1">Tổng chi tiêu</p>
                        <p class="text-xl sm:text-2xl font-serif font-bold text-wood"><?= number_format($stats['total_spent'] ?? 0) ?>đ</p>
                    </div>
                    <div class="w-12 h-12 bg-cream text-wood rounded-2xl flex items-center justify-center text-xl">
                        🕯️
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Recent Orders (Left) -->
            <div class="lg:col-span-8 bg-white rounded-3xl border border-beige shadow-warm p-6 sm:p-8">
                <div class="flex items-center justify-between pb-4 border-b border-beige mb-6">
                    <h2 class="font-serif font-bold text-lg text-charcoal">Đơn hàng gần đây</h2>
                    <a href="<?= $baseUrl ?>/user/orders" class="text-xs font-semibold text-wood hover:underline">
                        Xem tất cả →
                    </a>
                </div>
                
                <?php if (empty($recentOrders)): ?>
                    <div class="text-center py-12 bg-cream/30 rounded-2xl border border-dashed border-beige">
                        <span class="text-4xl mb-3 block">🛏️</span>
                        <p class="text-sm font-semibold text-charcoal">Bạn chưa có đơn hàng nào</p>
                        <p class="text-xs text-muted mt-1 max-w-xs mx-auto">Hãy bắt đầu lựa chọn những món đồ dịu êm cho phòng ngủ của mình nhé.</p>
                        <a href="<?= $baseUrl ?>/products" class="inline-block mt-4 px-6 py-2.5 bg-charcoal text-white rounded-full text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-sm">
                            Mua sắm ngay
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-4 divide-y divide-beige/60">
                        <?php foreach ($recentOrders as $order): ?>
                            <a href="<?= $baseUrl ?>/user/orders/<?= $order['id'] ?>" 
                               class="block pt-4 first:pt-0 hover:bg-cream/40 p-3 rounded-2xl transition border border-transparent hover:border-beige/80">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-1">
                                            <span class="font-serif font-bold text-sm text-charcoal">#<?= htmlspecialchars($order['order_number']) ?></span>
                                            <?php
                                            $statusColors = [
                                                'pending'    => 'bg-amber-50 text-amber-800 border-amber-200',
                                                'confirmed'  => 'bg-blue-50 text-blue-800 border-blue-200',
                                                'processing' => 'bg-purple-50 text-purple-800 border-purple-200',
                                                'shipped'    => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                                'delivered'  => 'bg-sage-light text-sage border-sage/20',
                                                'cancelled'  => 'bg-red-50 text-red-800 border-red-200',
                                            ];
                                            $statusLabels = [
                                                'pending'    => 'Chờ duyệt',
                                                'confirmed'  => 'Đã duyệt',
                                                'processing' => 'Đang đóng gói',
                                                'shipped'    => 'Đang vận chuyển',
                                                'delivered'  => 'Đã giao hàng',
                                                'cancelled'  => 'Đã hủy',
                                            ];
                                            ?>
                                            <span class="px-2.5 py-0.5 text-[10px] font-semibold rounded-full border <?= $statusColors[$order['status']] ?? 'bg-cream text-muted border-sand' ?>">
                                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                                            </span>
                                        </div>
                                        <p class="text-xs text-muted">
                                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm sm:text-base font-bold font-serif text-charcoal"><?= number_format($order['total_amount']) ?>đ</p>
                                        <span class="text-[11px] text-wood hover:underline">Chi tiết →</span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions (Right) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-3xl border border-beige shadow-warm p-6 sm:p-8">
                    <h3 class="font-serif font-bold text-base text-charcoal mb-4 pb-3 border-b border-beige">Thao tác nhanh</h3>
                    
                    <div class="space-y-2.5">
                        <a href="<?= $baseUrl ?>/user/orders" 
                           class="flex items-center gap-3 p-3 rounded-2xl hover:bg-cream transition border border-beige/60">
                            <span class="text-xl">📋</span>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-charcoal">Đơn hàng của tôi</p>
                                <p class="text-[10px] text-muted">Theo dõi lịch trình vận chuyển</p>
                            </div>
                            <span class="text-muted text-xs">→</span>
                        </a>
                        
                        <a href="<?= $baseUrl ?>/user/profile" 
                           class="flex items-center gap-3 p-3 rounded-2xl hover:bg-cream transition border border-beige/60">
                            <span class="text-xl">👤</span>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-charcoal">Thông tin tài khoản</p>
                                <p class="text-[10px] text-muted">Cập nhật hồ sơ & địa chỉ</p>
                            </div>
                            <span class="text-muted text-xs">→</span>
                        </a>
                        
                        <a href="<?= $baseUrl ?>/quote" 
                           class="flex items-center gap-3 p-3 rounded-2xl hover:bg-cream transition border border-beige/60">
                            <span class="text-xl">💼</span>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-charcoal">Yêu cầu báo giá B2B</p>
                                <p class="text-[10px] text-muted">Dự án homestay & khách sạn</p>
                            </div>
                            <span class="text-muted text-xs">→</span>
                        </a>
                    </div>
                    
                    <!-- Account Meta Summary -->
                    <div class="mt-6 pt-4 border-t border-beige/80 space-y-2 text-xs">
                        <div class="flex justify-between text-muted">
                            <span>Email:</span>
                            <span class="font-semibold text-charcoal truncate max-w-[140px]"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                        </div>
                        <div class="flex justify-between text-muted">
                            <span>Loại tài khoản:</span>
                            <span class="font-semibold text-charcoal">
                                <?= ($user['account_type'] ?? '') === 'business' ? '🏢 Doanh nghiệp (B2B)' : '👤 Cá nhân' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</div>
