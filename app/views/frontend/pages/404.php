<?php $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public'; ?>
<div class="min-h-[70vh] bg-cream/40 flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-md mx-auto">
        <div class="w-24 h-24 rounded-full bg-cream border border-sand flex items-center justify-center text-4xl mx-auto mb-6 shadow-warm">
            🌿
        </div>
        <p class="text-6xl font-serif font-bold text-sand mb-2">404</p>
        <h1 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal mb-3">Không tìm thấy trang</h1>
        <p class="text-muted text-xs sm:text-sm mb-8 leading-relaxed">
            Trang bạn đang tìm kiếm có thể đã được chuyển dời hoặc không tồn tại. Hãy quay về chốn bình yên của DecorNest nhé.
        </p>
        <a href="<?= $baseUrl ?>" class="inline-flex items-center gap-2 bg-charcoal text-white px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
            <span>Trở về trang chủ</span>
            <span>→</span>
        </a>
    </div>
</div>
