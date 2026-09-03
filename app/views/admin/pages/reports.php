<?php $pageTitle = 'Báo cáo doanh thu'; ?>
<div class="space-y-5">
    <h1 class="text-xl font-bold">Báo cáo doanh thu</h1>
    <div class="grid sm:grid-cols-3 gap-5">
        <?php foreach (['today' => 'Hôm nay', 'week' => 'Tuần này', 'month' => 'Tháng này'] as $k => $l): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
            <p class="text-xs font-medium text-muted uppercase tracking-wide mb-2"><?= $l ?></p>
            <p class="text-2xl font-bold text-charcoal"><?= number_format($stats[$k] ?? 0) ?>đ</p>
            <p class="text-xs text-muted mt-1">Doanh thu đã thanh toán</p>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <p class="text-sm text-muted text-center">Biểu đồ doanh thu theo thời gian (tích hợp Chart.js ở phiên bản tiếp theo)</p>
    </div>
</div>
