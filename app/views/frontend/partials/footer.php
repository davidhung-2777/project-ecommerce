<?php $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public'; ?>
<footer class="bg-charcoaldk text-cream mt-24 border-t border-white/10 relative overflow-hidden">

    <!-- Trust Badges & Healing Values -->
    <div class="border-b border-white/10 bg-black/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center sm:text-left">
                
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-xl text-amber-warm flex-shrink-0">
                        🌿
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Chất liệu tự nhiên</h4>
                        <p class="text-xs text-cream/60 mt-1 leading-relaxed">Gỗ sồi, vải lanh, gốm mộc & cotton hữu cơ lành tính cho giấc ngủ.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-xl text-amber-warm flex-shrink-0">
                        🚚
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Giao & Lắp tận phòng</h4>
                        <p class="text-xs text-cream/60 mt-1 leading-relaxed">Miễn phí vận chuyển từ 5.000.000đ. Đội ngũ lắp đặt tỉ mỉ, chu đáo.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-xl text-amber-warm flex-shrink-0">
                        🛡️
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Bảo hành 2 năm</h4>
                        <p class="text-xs text-cream/60 mt-1 leading-relaxed">Đổi trả thảnh thơi 30 ngày. Cam kết bền bỉ theo năm tháng.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-xl text-amber-warm flex-shrink-0">
                        🤝
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-white">Tư vấn không gian</h4>
                        <p class="text-xs text-cream/60 mt-1 leading-relaxed">Hỗ trợ phối màu & ánh sáng phòng ngủ theo nhu cầu thư giãn.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Main Footer Links -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12">

            <!-- Brand Column -->
            <div class="lg:col-span-2 space-y-4">
                <a href="<?= $baseUrl ?>" class="inline-block">
                    <span class="text-2xl font-bold font-serif text-white tracking-tight">Decor<span class="text-wood font-editorial italic">Nest</span></span>
                </a>
                <p class="text-xs text-cream/70 leading-relaxed max-w-sm">
                    Thương hiệu decor phòng ngủ phong cách Japandi & Scandinavian tối giản. Chúng tôi kiến tạo những không gian êm ái, nơi bạn tìm lại sự cân bằng, xoa dịu giác quan sau những giờ làm việc mỏi mệt.
                </p>
                <div class="pt-2 space-y-2 text-xs text-cream/80">
                    <p class="flex items-center gap-2">
                        <span class="text-amber-warm">📍</span> Showroom: 128 Nguyễn Trãi, Thanh Xuân, Hà Nội
                    </p>
                    <p class="flex items-center gap-2">
                        <span class="text-amber-warm">📞</span> Hotline: <a href="tel:19001234" class="text-white hover:text-wood font-semibold">1900 1234</a> (8:00 - 21:30)
                    </p>
                    <p class="flex items-center gap-2">
                        <span class="text-amber-warm">✉️</span> Email: <a href="mailto:hello@decornest.vn" class="text-white hover:text-wood">hello@decornest.vn</a>
                    </p>
                </div>
            </div>

            <!-- Categories -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-white mb-4 border-b border-white/10 pb-2">
                    Danh mục decor
                </h4>
                <ul class="space-y-2.5 text-xs text-cream/70">
                    <li><a href="<?= $baseUrl ?>/category/den-trang-tri" class="hover:text-wood transition">Đèn Ngủ & Ánh Sáng Ấm</a></li>
                    <li><a href="<?= $baseUrl ?>/category/goi-nem" class="hover:text-wood transition">Gối Lanh & Nệm Êm</a></li>
                    <li><a href="<?= $baseUrl ?>/category/tranh-khung" class="hover:text-wood transition">Tranh Tĩnh Lặng & Khung Gỗ</a></li>
                    <li><a href="<?= $baseUrl ?>/category/do-gom-su" class="hover:text-wood transition">Bình Gốm & Chậu Cây</a></li>
                    <li><a href="<?= $baseUrl ?>/category/dong-ho" class="hover:text-wood transition">Đồng Hồ Tối Giản Không Ồn</a></li>
                    <li><a href="<?= $baseUrl ?>/category/ke-gia" class="hover:text-wood transition">Kệ & Giá Treo Gỗ Sồi</a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-white mb-4 border-b border-white/10 pb-2">
                    Dịch vụ & Hỗ trợ
                </h4>
                <ul class="space-y-2.5 text-xs text-cream/70">
                    <li><a href="#" class="hover:text-wood transition">Chính sách giao hàng & lắp đặt</a></li>
                    <li><a href="#" class="hover:text-wood transition">Chính sách đổi trả 30 ngày</a></li>
                    <li><a href="#" class="hover:text-wood transition">Bảo hành sản phẩm 24 tháng</a></li>
                    <li><a href="#" class="hover:text-wood transition">Bí quyết setup phòng ngủ ngủ ngon</a></li>
                    <li><a href="<?= $baseUrl ?>/quote" class="hover:text-wood transition text-amber-warm font-medium">Báo giá B2B Homestay & Khách sạn</a></li>
                </ul>
            </div>

            <!-- Newsletter / Sanctuary Tips -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-white mb-4 border-b border-white/10 pb-2">
                    Bản tin thư giãn
                </h4>
                <p class="text-xs text-cream/60 leading-relaxed mb-3">
                    Nhận cảm hứng bài trí phòng ngủ, mẹo ngủ sâu & ưu đãi đặc quyền hàng tháng.
                </p>
                <form onsubmit="event.preventDefault(); showToast('Cảm ơn bạn đã đăng ký nhận bản tin an yên ✨', 'success'); this.reset();" class="space-y-2">
                    <input type="email" required placeholder="Email của bạn..." 
                           class="w-full bg-white/10 border border-white/20 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder:text-cream/40 focus:outline-none focus:border-wood">
                    <button type="submit" class="w-full py-2.5 bg-wood hover:bg-wooddk text-white rounded-xl text-xs font-semibold tracking-wide transition shadow-sm">
                        Đăng ký nhận tin
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-white/10 bg-black/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-cream/50">
            <p>© 2026 DecorNest. Tất cả quyền được bảo lưu. Chốn về an yên cho mỗi giấc ngủ.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-wood transition">Chính sách bảo mật</a>
                <a href="#" class="hover:text-wood transition">Điều khoản dịch vụ</a>
                <span>🇻🇳 Việt Nam</span>
            </div>
        </div>
    </div>

</footer>
