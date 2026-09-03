<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Yêu Cầu Báo Giá B2B & Dự Án - DecorNest';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">

    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto mb-12">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cream border border-sand text-xs font-semibold text-wood mb-3">
            <span>🏢</span> DÀNH CHO DOANH NGHIỆP, KHÁCH SẠN & HOMESTAY
        </div>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-charcoal tracking-tight">
            Yêu cầu báo giá số lượng lớn
        </h1>
        <p class="text-xs sm:text-sm text-muted mt-2 leading-relaxed">
            Chọn danh sách sản phẩm decor phòng ngủ và số lượng dự kiến. Đội ngũ chuyên viên DecorNest sẽ liên hệ gửi báo giá chiết khấu đặc quyền trong 24 giờ.
        </p>
    </div>

    <form action="<?= $baseUrl ?>/quote/submit" method="POST" x-data="quoteForm()">
        
        <!-- Product list card -->
        <div class="bg-white border border-beige rounded-3xl overflow-hidden shadow-warm mb-8">
            <div class="bg-cream/60 px-6 py-4 border-b border-beige flex items-center justify-between">
                <h2 class="font-serif font-bold text-sm sm:text-base text-charcoal">Danh sách sản phẩm decor dự án</h2>
                <button type="button" @click="addRow()" class="text-xs text-wood font-bold hover:underline flex items-center gap-1.5">
                    <span class="w-5 h-5 rounded-full bg-wood text-white flex items-center justify-center text-xs">+</span>
                    <span>Thêm sản phẩm</span>
                </button>
            </div>

            <!-- Table header (Desktop) -->
            <div class="hidden sm:grid grid-cols-12 gap-4 px-6 py-3 text-[11px] font-bold text-muted uppercase tracking-wider border-b border-beige/60 bg-cream/20">
                <div class="col-span-6">Sản phẩm</div>
                <div class="col-span-2 text-center">Số lượng</div>
                <div class="col-span-3">Ghi chú yêu cầu</div>
                <div class="col-span-1"></div>
            </div>

            <!-- Dynamic rows -->
            <div class="divide-y divide-beige/60">
                <template x-for="(row, index) in rows" :key="index">
                    <div class="grid sm:grid-cols-12 gap-4 px-6 py-4 items-center">
                        <div class="sm:col-span-6">
                            <label class="sm:hidden text-[10px] text-muted font-bold uppercase mb-1 block">Sản phẩm</label>
                            <select :name="'items[' + index + '][product_id]'" required
                                    class="w-full bg-cream/40 border border-beige rounded-xl px-3 py-2.5 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                                <option value="">-- Chọn sản phẩm decor --</option>
                                <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= number_format($p['price']) ?>đ)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="sm:col-span-2 text-center">
                            <label class="sm:hidden text-[10px] text-muted font-bold uppercase mb-1 block">Số lượng</label>
                            <input type="number" :name="'items[' + index + '][quantity]'" x-model="row.qty" min="1" value="1" required
                                   class="w-full sm:w-24 bg-cream/40 border border-beige rounded-xl px-3 py-2 text-xs sm:text-sm font-bold text-center focus:bg-white focus:outline-none focus:border-wood mx-auto">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="sm:hidden text-[10px] text-muted font-bold uppercase mb-1 block">Ghi chú</label>
                            <input type="text" :name="'items[' + index + '][note]'" placeholder="Kích thước, màu..."
                                   class="w-full bg-cream/40 border border-beige rounded-xl px-3 py-2 text-xs focus:bg-white focus:outline-none focus:border-wood">
                        </div>
                        <div class="sm:col-span-1 flex justify-end">
                            <button type="button" @click="removeRow(index)" x-show="rows.length > 1"
                                    class="text-red-400 hover:text-red-600 transition p-1.5 rounded-lg hover:bg-red-50"
                                    title="Xóa dòng">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Project Notes -->
        <div class="bg-white border border-beige rounded-3xl p-6 sm:p-8 shadow-warm mb-8">
            <h3 class="font-serif font-bold text-sm sm:text-base text-charcoal mb-2">Thông tin dự án & Yêu cầu đặc biệt</h3>
            <p class="text-xs text-muted mb-4">Ví dụ: Dự án 20 phòng homestay tại Hội An, thời gian cần nhận hàng trước tháng sau...</p>
            <textarea name="customer_note" rows="4" placeholder="Mô tả chi tiết dự án, địa điểm giao hàng hoặc các yêu cầu kỹ thuật khác..."
                      class="w-full bg-cream/40 border border-beige rounded-2xl p-4 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood resize-none"></textarea>
        </div>

        <!-- Submit actions -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-cream/50 p-6 rounded-3xl border border-beige">
            <p class="text-xs text-muted max-w-md">
                🌿 Chuyên viên dự án của DecorNest sẽ liên hệ phản hồi qua email hoặc số điện thoại của bạn trong vòng <strong>24 giờ làm việc</strong>.
            </p>
            <button type="submit" class="w-full sm:w-auto bg-charcoal text-white px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition shadow-warm">
                Gửi yêu cầu báo giá →
            </button>
        </div>

    </form>
</div>

<script>
function quoteForm() {
    return {
        rows: [{ qty: 1 }],
        addRow() { this.rows.push({ qty: 1 }); },
        removeRow(index) { if (this.rows.length > 1) this.rows.splice(index, 1); }
    }
}
</script>
