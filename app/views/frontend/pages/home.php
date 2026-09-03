<?php
$baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'DecorNest - Trang Trí Phòng Ngủ An Yên & Sang Trọng';
$pageDesc  = 'Bộ sưu tập đồ decor phòng ngủ Japandi & Scandinavian tối giản, chất liệu tự nhiên, ánh sáng ấm áp vỗ về giấc ngủ sau ngày dài làm việc.';
?>

<!-- Hero Section: The Healing Bedroom Sanctuary -->
<section class="relative bg-gradient-to-b from-cream via-cream/80 to-warmwhite overflow-hidden py-12 lg:py-20 border-b border-beige/60">
    <!-- Decorative background glow -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-sand/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -right-32 w-96 h-96 bg-sage-light/50 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">

            <!-- Left Text Content -->
            <div class="lg:col-span-6 space-y-6 max-w-xl">
                <!-- Tagline Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-sand/80 shadow-warm text-xs font-semibold text-wood">
                    <span class="w-2 h-2 rounded-full bg-sage animate-pulse"></span>
                    <span class="tracking-wide">PHONG CÁCH JAPANDI & SCANDINAVIAN</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-charcoal leading-[1.2] tracking-tight">
                    Biến phòng ngủ thành<br>
                    <span class="font-editorial italic font-normal text-wood">chốn về an yên</span><br>
                    sau ngày dài mỏi mệt
                </h1>

                <!-- Subtitle -->
                <p class="text-sm sm:text-base text-muted leading-relaxed">
                    Nơi ánh sáng ấm dịu, chất liệu gỗ sồi và vải lanh tự nhiên xoa dịu mọi giác quan, vỗ về tâm trí bạn vào giấc ngủ sâu và tái tạo trọn vẹn năng lượng.
                </p>

                <!-- Action CTAs -->
                <div class="pt-2 flex flex-wrap items-center gap-3.5">
                    <a href="<?= $baseUrl ?>/products"
                       class="inline-flex items-center justify-center gap-2.5 bg-charcoal text-white px-7 py-3.5 rounded-full text-xs sm:text-sm font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm group">
                        <span>Khám phá bộ sưu tập</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="<?= $baseUrl ?>/quote"
                       class="inline-flex items-center justify-center gap-2 border border-sand hover:border-charcoal bg-white/80 text-charcoal px-6 py-3.5 rounded-full text-xs sm:text-sm font-semibold tracking-wide hover:bg-white transition shadow-sm">
                        <span>Tư vấn B2B / Dự án</span>
                    </a>
                </div>

                <!-- Micro Values List -->
                <div class="pt-6 border-t border-beige/80 grid grid-cols-3 gap-4 text-center sm:text-left">
                    <div>
                        <p class="text-base sm:text-lg font-bold text-charcoal font-serif">100%</p>
                        <p class="text-[11px] text-muted mt-0.5">Chất liệu tự nhiên</p>
                    </div>
                    <div>
                        <p class="text-base sm:text-lg font-bold text-charcoal font-serif">2.700K</p>
                        <p class="text-[11px] text-muted mt-0.5">Ánh sáng ấm thư giãn</p>
                    </div>
                    <div>
                        <p class="text-base sm:text-lg font-bold text-charcoal font-serif">30 Ngày</p>
                        <p class="text-[11px] text-muted mt-0.5">Đổi trả thảnh thơi</p>
                    </div>
                </div>
            </div>

            <!-- Right Hero Visual -->
            <div class="lg:col-span-6 relative">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <!-- Main Bedroom Image Card -->
                    <div class="aspect-[4/5] sm:aspect-[4/4.5] rounded-3xl overflow-hidden shadow-warm-xl border-4 border-white bg-cream">
                        <img src="<?= $baseUrl ?>/assets/images/hero-bedroom.jpg"
                             onerror="this.src='<?= $baseUrl ?>/assets/images/product-placeholder.jpg'"
                             alt="Phòng ngủ Japandi Scandinavian chữa lành" 
                             class="w-full h-full object-cover object-center hover:scale-105 transition-transform duration-1000 ease-out">
                    </div>

                    <!-- Floating Badge 1: Free Delivery & Assembly -->
                    <div class="absolute -bottom-6 -left-4 sm:left-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-warm-lg p-4 border border-beige max-w-[210px] flex items-center gap-3 animate-fade-in">
                        <div class="w-10 h-10 rounded-xl bg-sage-light text-sage flex items-center justify-center text-xl flex-shrink-0">
                            🌿
                        </div>
                        <div>
                            <p class="text-xs font-bold text-charcoal leading-snug">Giao & Lắp tận phòng</p>
                            <p class="text-[10px] text-muted mt-0.5">Đơn hàng từ 5.000.000đ</p>
                        </div>
                    </div>

                    <!-- Floating Badge 2: Amber Ambient Light -->
                    <div class="hidden sm:flex absolute -top-4 -right-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-warm-lg p-3.5 border border-beige items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-amber-warm shadow-glow"></span>
                        <p class="text-xs font-medium text-charcoal">Ánh sáng êm dịu cho mắt</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Category Mood Collections (Danh mục cảm xúc nghỉ ngơi) -->
<section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="text-xs font-semibold uppercase tracking-widest text-wood block mb-2">Bộ Sưu Tập Cảm Xúc</span>
        <h2 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">
            Không gian vỗ về từng giác quan
        </h2>
        <p class="text-xs sm:text-sm text-muted mt-2">
            Mỗi món đồ decor được chọn lọc tỉ mỉ để hòa quyện thành tổng thể êm ả, dịu dàng.
        </p>
    </div>

    <!-- Category Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6">
        
        <!-- 1. Đèn Ánh Sáng -->
        <a href="<?= $baseUrl ?>/category/den-trang-tri" 
           class="group bg-cream/60 hover:bg-white rounded-2xl p-5 border border-beige hover:border-wood/40 transition-all duration-300 shadow-sm hover:shadow-warm text-center flex flex-col items-center justify-between">
            <div class="w-16 h-16 rounded-2xl bg-white group-hover:bg-cream border border-sand/40 flex items-center justify-center text-3xl shadow-sm group-hover:scale-110 transition-all duration-300">
                💡
            </div>
            <div class="mt-4">
                <h3 class="font-serif font-bold text-xs sm:text-sm text-charcoal group-hover:text-wood transition-colors">Đèn & Ánh Sáng</h3>
                <p class="text-[11px] text-muted mt-0.5">Ánh sáng êm dịu</p>
            </div>
        </a>

        <!-- 2. Gối & Nệm -->
        <a href="<?= $baseUrl ?>/category/goi-nem" 
           class="group bg-cream/60 hover:bg-white rounded-2xl p-5 border border-beige hover:border-wood/40 transition-all duration-300 shadow-sm hover:shadow-warm text-center flex flex-col items-center justify-between">
            <div class="w-16 h-16 rounded-2xl bg-white group-hover:bg-cream border border-sand/40 flex items-center justify-center text-3xl shadow-sm group-hover:scale-110 transition-all duration-300">
                🛋️
            </div>
            <div class="mt-4">
                <h3 class="font-serif font-bold text-xs sm:text-sm text-charcoal group-hover:text-wood transition-colors">Gối & Nệm Lanh</h3>
                <p class="text-[11px] text-muted mt-0.5">Chạm vào êm ái</p>
            </div>
        </a>

        <!-- 3. Tranh & Khung -->
        <a href="<?= $baseUrl ?>/category/tranh-khung" 
           class="group bg-cream/60 hover:bg-white rounded-2xl p-5 border border-beige hover:border-wood/40 transition-all duration-300 shadow-sm hover:shadow-warm text-center flex flex-col items-center justify-between">
            <div class="w-16 h-16 rounded-2xl bg-white group-hover:bg-cream border border-sand/40 flex items-center justify-center text-3xl shadow-sm group-hover:scale-110 transition-all duration-300">
                🖼️
            </div>
            <div class="mt-4">
                <h3 class="font-serif font-bold text-xs sm:text-sm text-charcoal group-hover:text-wood transition-colors">Tranh Tĩnh Lặng</h3>
                <p class="text-[11px] text-muted mt-0.5">Nghệ thuật thư thái</p>
            </div>
        </a>

        <!-- 4. Đồ Gốm Sứ -->
        <a href="<?= $baseUrl ?>/category/do-gom-su" 
           class="group bg-cream/60 hover:bg-white rounded-2xl p-5 border border-beige hover:border-wood/40 transition-all duration-300 shadow-sm hover:shadow-warm text-center flex flex-col items-center justify-between">
            <div class="w-16 h-16 rounded-2xl bg-white group-hover:bg-cream border border-sand/40 flex items-center justify-center text-3xl shadow-sm group-hover:scale-110 transition-all duration-300">
                🏺
            </div>
            <div class="mt-4">
                <h3 class="font-serif font-bold text-xs sm:text-sm text-charcoal group-hover:text-wood transition-colors">Gốm Sứ & Chậu</h3>
                <p class="text-[11px] text-muted mt-0.5">Hơi thở tự nhiên</p>
            </div>
        </a>

        <!-- 5. Đồng Hồ -->
        <a href="<?= $baseUrl ?>/category/dong-ho" 
           class="group bg-cream/60 hover:bg-white rounded-2xl p-5 border border-beige hover:border-wood/40 transition-all duration-300 shadow-sm hover:shadow-warm text-center flex flex-col items-center justify-between">
            <div class="w-16 h-16 rounded-2xl bg-white group-hover:bg-cream border border-sand/40 flex items-center justify-center text-3xl shadow-sm group-hover:scale-110 transition-all duration-300">
                🕐
            </div>
            <div class="mt-4">
                <h3 class="font-serif font-bold text-xs sm:text-sm text-charcoal group-hover:text-wood transition-colors">Đồng Hồ Tối Giản</h3>
                <p class="text-[11px] text-muted mt-0.5">Không tiếng tích tắc</p>
            </div>
        </a>

        <!-- 6. Kệ & Giá -->
        <a href="<?= $baseUrl ?>/category/ke-gia" 
           class="group bg-cream/60 hover:bg-white rounded-2xl p-5 border border-beige hover:border-wood/40 transition-all duration-300 shadow-sm hover:shadow-warm text-center flex flex-col items-center justify-between">
            <div class="w-16 h-16 rounded-2xl bg-white group-hover:bg-cream border border-sand/40 flex items-center justify-center text-3xl shadow-sm group-hover:scale-110 transition-all duration-300">
                📦
            </div>
            <div class="mt-4">
                <h3 class="font-serif font-bold text-xs sm:text-sm text-charcoal group-hover:text-wood transition-colors">Kệ Sách Gỗ Sồi</h3>
                <p class="text-[11px] text-muted mt-0.5">Gọn gàng, tinh tế</p>
            </div>
        </a>

    </div>
</section>

<!-- Featured Sanctuary Products (Sản phẩm nổi bật) -->
<?php if (!empty($featured)): ?>
<section class="py-16 bg-cream/50 border-y border-beige/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-wood block mb-1">Gợi Ý Chữa Lành</span>
                <h2 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Sản phẩm được yêu thích nhất</h2>
                <p class="text-xs sm:text-sm text-muted mt-1">Những món đồ được khách hàng lựa chọn nhiều nhất để làm mới phòng ngủ</p>
            </div>
            <a href="<?= $baseUrl ?>/products?is_featured=1" 
               class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-wood hover:text-wooddk transition">
                <span>Xem trọn bộ sưu tập</span>
                <span>→</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            <?php $products = $featured; require ROOT_PATH . '/app/views/frontend/partials/product-grid.php'; ?>
        </div>

    </div>
</section>
<?php endif; ?>

<!-- The 3 Pillars of Healing Sleep (Bí quyết tạo không gian ngủ chữa lành) -->
<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid lg:grid-cols-12 gap-12 items-center">

        <!-- Visual Side -->
        <div class="lg:col-span-5 relative">
            <div class="aspect-[4/5] rounded-3xl overflow-hidden shadow-warm-xl border-4 border-white bg-cream">
                <img src="<?= $baseUrl ?>/assets/images/hero-healing.jpg"
                     onerror="this.src='<?= $baseUrl ?>/assets/images/product-placeholder.jpg'"
                     alt="Góc phòng ngủ healing tĩnh lặng"
                     class="w-full h-full object-cover">
            </div>
            <div class="absolute -bottom-5 -right-5 bg-warmwhite rounded-2xl p-5 shadow-warm border border-beige max-w-[220px] text-center hidden sm:block">
                <p class="text-xl">🕯️</p>
                <p class="text-xs font-bold text-charcoal font-serif mt-1">Nơi nạp lại năng lượng</p>
                <p class="text-[10px] text-muted mt-0.5">Cho 8 tiếng ngủ sâu trọn vẹn</p>
            </div>
        </div>

        <!-- Content Side -->
        <div class="lg:col-span-7 space-y-8">
            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-wood block mb-2">Triết Lý Thiết Kế</span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-bold text-charcoal leading-tight">
                    3 yếu tố cốt lõi cho một<br>
                    <span class="font-editorial italic font-normal text-wood">giấc ngủ ngon lành</span>
                </h2>
                <p class="text-sm text-muted mt-3 leading-relaxed">
                    Một phòng ngủ lý tưởng không chỉ đẹp mắt, mà còn là một liệu pháp tự nhiên giúp hệ thần kinh thả lỏng sau những căng thẳng thường nhật.
                </p>
            </div>

            <div class="space-y-6">
                
                <!-- Pillar 1 -->
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-cream/50 border border-beige/70">
                    <div class="w-12 h-12 rounded-2xl bg-white text-wood flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                        💡
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-sm text-charcoal">Ánh sáng ấm sắc độ 2.700K</h4>
                        <p class="text-xs text-muted mt-1 leading-relaxed">
                            Ánh sáng vàng dịu kích thích cơ thể sản sinh melatonin tự nhiên, xua tan căng thẳng từ màn hình máy tính và đưa bạn vào trạng thái buồn ngủ tự nhiên.
                        </p>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-cream/50 border border-beige/70">
                    <div class="w-12 h-12 rounded-2xl bg-white text-sage flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                        🌿
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-sm text-charcoal">Chất liệu tự nhiên thuần khiết</h4>
                        <p class="text-xs text-muted mt-1 leading-relaxed">
                            Vải lanh tự nhiên, cotton hữu cơ và gỗ sồi nguyên khối giúp không khí lưu thông, thoáng mát vào mùa hè và ấm áp vào mùa đông.
                        </p>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="flex items-start gap-4 p-4 rounded-2xl bg-cream/50 border border-beige/70">
                    <div class="w-12 h-12 rounded-2xl bg-white text-amber-warm flex items-center justify-center text-xl flex-shrink-0 shadow-sm">
                        ✨
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-sm text-charcoal">Tối giản thị giác & Yên tĩnh tuyệt đối</h4>
                        <p class="text-xs text-muted mt-1 leading-relaxed">
                            Bố cục tinh gọn, không chi tiết rườm rà, cùng đồng hồ hoạt động êm ái mang đến sự tĩnh tâm tuyệt đối cho giấc ngủ trọn vẹn.
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- B2B & Homestay / Hotel Sanctuary Project Banner -->
<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-charcoaldk rounded-3xl p-8 sm:p-12 lg:p-14 text-white relative overflow-hidden shadow-warm-xl">
        <!-- Ambient background circle -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-wood/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="grid lg:grid-cols-12 gap-8 items-center relative">
            <div class="lg:col-span-7 space-y-4">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-warm text-[11px] font-semibold tracking-widest uppercase">
                    DÀNH CHO DOANH NGHIỆP & HOMESTAY
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-bold leading-tight">
                    Giải pháp decor phòng ngủ<br>
                    <span class="font-editorial italic font-normal text-wood">giá sỉ & chiết khấu đến 20%</span>
                </h2>
                <p class="text-xs sm:text-sm text-cream/70 leading-relaxed max-w-lg">
                    Cung cấp trọn gói đồ decor, chăn gối lanh, đèn ngủ và tranh treo tường cho các dự án khách sạn boutique, homestay, căn hộ dịch vụ và văn phòng. Hỗ trợ xuất hóa đơn VAT và tư vấn thiết kế.
                </p>
                <div class="pt-3 flex flex-wrap gap-3">
                    <a href="<?= $baseUrl ?>/quote"
                       class="px-6 py-3 bg-wood hover:bg-wooddk text-white rounded-full text-xs font-bold uppercase tracking-wider transition shadow-warm">
                        Yêu cầu báo giá B2B
                    </a>
                    <a href="<?= $baseUrl ?>/user/register"
                       class="px-6 py-3 border border-white/30 hover:border-white text-white rounded-full text-xs font-semibold transition hover:bg-white/10">
                        Đăng ký tài khoản đối tác
                    </a>
                </div>
            </div>

            <div class="lg:col-span-5 grid grid-cols-3 gap-3 text-center">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 sm:p-5">
                    <span class="text-2xl sm:text-3xl font-bold font-serif text-amber-warm block">20%</span>
                    <span class="text-[11px] text-cream/60 mt-1 block">Chiết khấu tối đa</span>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 sm:p-5">
                    <span class="text-2xl sm:text-3xl font-bold font-serif text-amber-warm block">24h</span>
                    <span class="text-[11px] text-cream/60 mt-1 block">Phản hồi báo giá</span>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 sm:p-5">
                    <span class="text-2xl sm:text-3xl font-bold font-serif text-amber-warm block">VAT</span>
                    <span class="text-[11px] text-cream/60 mt-1 block">Xuất hóa đơn đỏ</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals (Hàng mới về) -->
<?php if (!empty($newArrivals)): ?>
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
        <div>
            <span class="text-xs font-semibold uppercase tracking-widest text-wood block mb-1">Xu Hướng Mới</span>
            <h2 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">Hàng mới về trong tháng</h2>
            <p class="text-xs sm:text-sm text-muted mt-1">Cập nhật những mẫu thiết kế mới nhất cho không gian phòng ngủ</p>
        </div>
        <a href="<?= $baseUrl ?>/products?is_new=1" 
           class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-wood hover:text-wooddk transition">
            <span>Xem tất cả</span>
            <span>→</span>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        <?php $products = $newArrivals; require ROOT_PATH . '/app/views/frontend/partials/product-grid.php'; ?>
    </div>
</section>
<?php endif; ?>

<!-- Healing Sanctuary Testimonials -->
<section class="py-16 bg-cream/40 border-t border-beige/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-xl mx-auto mb-12">
            <span class="text-xs font-semibold uppercase tracking-widest text-wood block mb-2">Cảm Nhận Khách Hàng</span>
            <h2 class="text-2xl sm:text-3xl font-serif font-bold text-charcoal">
                Niềm an yên được gửi gắm
            </h2>
        </div>

        <div class="grid sm:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-beige shadow-warm flex flex-col justify-between">
                <div>
                    <div class="flex text-amber-warm text-sm mb-3">★★★★★</div>
                    <p class="text-xs text-charcoal leading-relaxed italic font-serif">
                        "Chiếc đèn ngủ gỗ sồi và bộ gối lanh DecorNest thực sự thay đổi cảm xúc của mình mỗi khi bước chân vào phòng sau giờ làm việc. Ánh sáng vàng dịu cực kỳ thư giãn!"
                    </p>
                </div>
                <div class="mt-5 pt-4 border-t border-beige/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-cream font-bold text-xs text-wood flex items-center justify-center">
                        M
                    </div>
                    <div>
                        <p class="text-xs font-bold text-charcoal">Minh Anh</p>
                        <p class="text-[10px] text-muted">Kiến trúc sư, Hà Nội</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-beige shadow-warm flex flex-col justify-between">
                <div>
                    <div class="flex text-amber-warm text-sm mb-3">★★★★★</div>
                    <p class="text-xs text-charcoal leading-relaxed italic font-serif">
                        "Mình đặt trọn bộ decor cho homestay ở Đà Lạt. Khách lưu trú khen nức nở góc phòng ngủ vì chụp ảnh rất thơ và ngủ cực kỳ sâu giấc."
                    </p>
                </div>
                <div class="mt-5 pt-4 border-t border-beige/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-cream font-bold text-xs text-wood flex items-center justify-center">
                        H
                    </div>
                    <div>
                        <p class="text-xs font-bold text-charcoal">Hoàng Long</p>
                        <p class="text-[10px] text-muted">Chủ chuỗi Homestay Mây</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-beige shadow-warm flex flex-col justify-between">
                <div>
                    <div class="flex text-amber-warm text-sm mb-3">★★★★★</div>
                    <p class="text-xs text-charcoal leading-relaxed italic font-serif">
                        "Đóng gói vô cùng cẩn thận, sản phẩm gốm mộc và đồng hồ tối giản sắc nét hơn cả hình ảnh trên web. Sẽ tiếp tục ủng hộ DecorNest!"
                    </p>
                </div>
                <div class="mt-5 pt-4 border-t border-beige/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-cream font-bold text-xs text-wood flex items-center justify-center">
                        T
                    </div>
                    <div>
                        <p class="text-xs font-bold text-charcoal">Thanh Thảo</p>
                        <p class="text-[10px] text-muted">Content Creator, TP. HCM</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
