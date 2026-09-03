<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Báo Giá B2B Của Tôi - DecorNest';
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">

    <div class="flex items-center justify-between pb-6 border-b border-beige mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Báo giá B2B của tôi</h1>
            <p class="text-xs sm:text-sm text-muted mt-1">Danh sách các yêu cầu báo giá dự án homestay & khách sạn</p>
        </div>
        <a href="<?= $baseUrl ?>/quote" class="px-5 py-2.5 bg-charcoal text-white rounded-full text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
            + Tạo yêu cầu mới
        </a>
    </div>

    <?php if (empty($quotes)): ?>
    <div class="text-center py-20 px-4 bg-cream/40 rounded-3xl border border-dashed border-beige max-w-xl mx-auto">
        <div class="w-16 h-16 rounded-full bg-white shadow-warm flex items-center justify-center text-3xl mx-auto mb-4">
            💼
        </div>
        <h2 class="text-lg font-serif font-bold text-charcoal">Chưa có yêu cầu báo giá nào</h2>
        <p class="text-xs text-muted mt-1 max-w-xs mx-auto">Bạn có thể gửi yêu cầu báo giá số lượng lớn với mức chiết khấu ưu đãi lên đến 20% cho dự án của mình.</p>
        <a href="<?= $baseUrl ?>/quote" class="mt-6 inline-block px-7 py-3 bg-charcoal text-white rounded-full text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
            Yêu cầu báo giá ngay
        </a>
    </div>
    <?php else: ?>

    <div class="space-y-4">
        <?php foreach ($quotes as $quote): ?>
        <div class="bg-white rounded-3xl border border-beige p-6 sm:p-7 shadow-warm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-3">
                    <span class="font-serif font-bold text-base text-charcoal">Báo giá #<?= $quote['id'] ?></span>
                    <span class="px-3 py-0.5 text-[10px] font-semibold rounded-full border <?= 
                        ($quote['status'] ?? '') === 'approved' ? 'bg-sage-light text-sage border-sage/20' : 
                        (($quote['status'] ?? '') === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-amber-50 text-amber-800 border-amber-200')
                    ?>">
                        <?= ($quote['status'] ?? '') === 'approved' ? 'Đã duyệt báo giá' : (($quote['status'] ?? '') === 'rejected' ? 'Đã từ chối' : 'Đang xử lý') ?>
                    </span>
                </div>
                <p class="text-xs text-muted">
                    Gửi ngày: <?= date('d/m/Y H:i', strtotime($quote['created_at'])) ?>
                </p>
                <?php if (!empty($quote['customer_note'])): ?>
                <p class="text-xs text-charcoal mt-1">Ghi chú: "<?= htmlspecialchars($quote['customer_note']) ?>"</p>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <a href="<?= $baseUrl ?>/quote/<?= $quote['id'] ?>"
                   class="px-5 py-2.5 rounded-xl bg-cream hover:bg-charcoal text-charcoal hover:text-white border border-beige transition text-xs font-semibold">
                    Xem chi tiết →
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>

</div>
