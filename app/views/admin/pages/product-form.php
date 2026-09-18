<?php
$isEdit    = !empty($product['id']);
$pageTitle = $isEdit ? 'Sửa sản phẩm: ' . htmlspecialchars($product['name'] ?? '') : 'Thêm sản phẩm mới';
$base      = $_ENV['APP_URL'] ?? '';
$action    = $isEdit ? $base . '/admin/products/' . (int)$product['id'] . '/edit' : $base . '/admin/products/create';

// Ensure arrays exist
$categories = $categories ?? [];
$tiers      = $tiers ?? [];
$product    = $product ?? [];

// Decode gallery images
$galleryImages = [];
if (!empty($product['images'])) {
    $galleryImages = is_array($product['images']) ? $product['images'] : (json_decode($product['images'], true) ?: []);
}

if (!function_exists('adminImgUrl')) {
    function adminImgUrl($url, $base) {
        if (empty($url)) return $base . '/assets/images/product-placeholder.jpg';
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) return $url;
        return rtrim($base, '/') . '/' . ltrim($url, '/');
    }
}
?>

<div class="max-w-5xl space-y-6">
    <!-- Breadcrumb & Title -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="<?= $base ?>/admin/products" class="text-xs font-semibold text-wood hover:underline flex items-center gap-1">
                <span>←</span>
                <span>Danh sách sản phẩm</span>
            </a>
            <span class="text-gray-300">/</span>
            <h1 class="text-xl sm:text-2xl font-bold text-charcoal"><?= $isEdit ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' ?></h1>
        </div>
        <a href="<?= $base ?>/admin/products" class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-charcoal hover:bg-gray-100 transition">
            Hủy bỏ
        </a>
    </div>

    <form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="grid lg:grid-cols-12 gap-6">

        <!-- Left Column: Main Info (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Basic Info Card -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4 shadow-sm">
                <h2 class="font-bold text-sm text-charcoal pb-3 border-b border-gray-100 flex items-center gap-2">
                    <span>📝</span>
                    <span>Thông tin cơ bản</span>
                </h2>
                
                <div>
                    <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Tên sản phẩm decor *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($product['name'] ?? '') ?>"
                           placeholder="Ví dụ: Đèn ngủ gỗ sồi phong cách Japandi..."
                           class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Mã SKU</label>
                        <input type="text" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>"
                               placeholder="VD: DN-LAMP-01"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Danh mục</label>
                        <select name="category_id" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ((int)($product['category_id'] ?? 0) === (int)$cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Mô tả ngắn (Hiển thị đầu trang)</label>
                    <textarea name="short_desc" rows="2" placeholder="Tóm tắt những điểm nổi bật của sản phẩm..."
                              class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20 resize-none"><?= htmlspecialchars($product['short_desc'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Mô tả chi tiết & Câu chuyện chữa lành</label>
                    <textarea name="description" rows="6" placeholder="Mô tả kỹ lưỡng về chất liệu, nguồn cảm hứng, hướng dẫn bài trí trong phòng ngủ..."
                              class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Pricing & Bulk Tiers Card -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4 shadow-sm">
                <h2 class="font-bold text-sm text-charcoal pb-3 border-b border-gray-100 flex items-center gap-2">
                    <span>🏷️</span>
                    <span>Giá bán & Chiết khấu</span>
                </h2>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Giá niêm yết (đ) *</label>
                        <input type="number" name="price" required step="1000" min="0"
                               value="<?= htmlspecialchars($product['price'] ?? '') ?>"
                               placeholder="590000"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Giá khuyến mãi (đ)</label>
                        <input type="number" name="sale_price" step="1000" min="0"
                               value="<?= htmlspecialchars($product['sale_price'] ?? '') ?>"
                               placeholder="Để trống nếu không giảm"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-wood outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Phí lắp đặt (đ)</label>
                        <input type="number" name="install_fee" step="1000" min="0"
                               value="<?= htmlspecialchars($product['install_fee'] ?? 0) ?>"
                               placeholder="0"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                </div>

                <!-- Bulk Price Tiers Form (Alpine) -->
                <div x-data="tierForm()" class="border-t border-gray-100 pt-4 mt-2">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-xs font-bold text-charcoal uppercase tracking-wider">Bảng giá sỉ dự án (B2B Tiers)</h3>
                            <p class="text-[11px] text-muted">Ưu đãi giảm giá tự động theo số lượng khi mua sỉ</p>
                        </div>
                        <button type="button" @click="addTier()" class="px-3 py-1.5 rounded-lg bg-cream text-wood hover:bg-beige text-xs font-bold transition flex items-center gap-1">
                            <span>+ Thêm bậc giá</span>
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <template x-for="(tier, i) in tiers" :key="i">
                            <div class="grid grid-cols-12 gap-2.5 items-center p-3 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="col-span-3">
                                    <label class="block text-[10px] text-muted font-bold uppercase mb-1">SL Tối thiểu</label>
                                    <input type="number" :name="'tiers[' + i + '][min_qty]'" x-model="tier.min_qty" placeholder="VD: 5" min="1" required
                                           class="w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium">
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-[10px] text-muted font-bold uppercase mb-1">SL Tối đa</label>
                                    <input type="number" :name="'tiers[' + i + '][max_qty]'" x-model="tier.max_qty" placeholder="Trống = ∞"
                                           class="w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium">
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-[10px] text-muted font-bold uppercase mb-1">Đơn giá sỉ (đ)</label>
                                    <input type="number" :name="'tiers[' + i + '][price]'" x-model="tier.price" placeholder="450000" required step="1000" min="0"
                                           class="w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-wood">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] text-muted font-bold uppercase mb-1">Nhãn</label>
                                    <input type="text" :name="'tiers[' + i + '][label]'" x-model="tier.label" placeholder="Giá sỉ"
                                           class="w-full rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-xs">
                                </div>
                                <div class="col-span-1 flex justify-end pt-4">
                                    <button type="button" @click="tiers.splice(i, 1)" class="p-1 text-red-400 hover:text-red-600 transition" title="Xóa bậc giá">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Specifications Card -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4 shadow-sm">
                <h2 class="font-bold text-sm text-charcoal pb-3 border-b border-gray-100 flex items-center gap-2">
                    <span>📐</span>
                    <span>Quy cách & Tồn kho</span>
                </h2>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Số lượng tồn kho *</label>
                        <input type="number" name="stock" required min="0"
                               value="<?= htmlspecialchars($product['stock'] ?? 10) ?>"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Xuất xứ</label>
                        <input type="text" name="origin" value="<?= htmlspecialchars($product['origin'] ?? '') ?>"
                               placeholder="VD: Việt Nam, Nhật Bản..."
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Chất liệu</label>
                        <input type="text" name="material" value="<?= htmlspecialchars($product['material'] ?? '') ?>"
                               placeholder="VD: Gỗ sồi tự nhiên, vải lanh..."
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Kích thước</label>
                        <input type="text" name="dimensions" placeholder="VD: 35 x 45 x 60 cm" value="<?= htmlspecialchars($product['dimensions'] ?? '') ?>"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Kích thước biến thể (phân cách bằng dấu phẩy)</label>
                        <?php 
                        $sizeVal = '';
                        if (!empty($product['size_options'])) {
                            $sizeArr = is_array($product['size_options']) ? $product['size_options'] : json_decode($product['size_options'], true);
                            $sizeVal = is_array($sizeArr) ? implode(', ', $sizeArr) : $product['size_options'];
                        }
                        ?>
                        <input type="text" name="size_options" placeholder="VD: Tiêu chuẩn, Lớn, Đôi..."
                               value="<?= htmlspecialchars($sizeVal) ?>"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-charcoal uppercase tracking-wider mb-1.5">Màu sắc biến thể (phân cách bằng dấu phẩy)</label>
                        <?php 
                        $colorVal = '';
                        if (!empty($product['color_options'])) {
                            $colorArr = is_array($product['color_options']) ? $product['color_options'] : json_decode($product['color_options'], true);
                            $colorVal = is_array($colorArr) ? implode(', ', $colorArr) : $product['color_options'];
                        }
                        ?>
                        <input type="text" name="color_options" placeholder="VD: Be mộc, Nâu sồi, Trắng ngà..."
                               value="<?= htmlspecialchars($colorVal) ?>"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-2.5 text-xs sm:text-sm text-charcoal outline-none transition focus:bg-white focus:border-wood focus:ring-2 focus:ring-wood/20">
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Thumbnail, Status, Gallery & SEO (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Publish Status Card -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-3.5 shadow-sm">
                <h2 class="font-bold text-sm text-charcoal pb-3 border-b border-gray-100 flex items-center gap-2">
                    <span>⚙️</span>
                    <span>Trạng thái hiển thị</span>
                </h2>

                <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-cream/40 cursor-pointer transition border border-transparent hover:border-beige">
                    <input type="checkbox" name="is_active" value="1" <?= (!isset($product['is_active']) || $product['is_active']) ? 'checked' : '' ?>
                           class="w-4 h-4 rounded text-charcoal accent-wood">
                    <div>
                        <span class="text-xs font-bold text-charcoal block">Hiển thị trên website</span>
                        <span class="text-[11px] text-muted">Khách hàng có thể tìm và mua</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-cream/40 cursor-pointer transition border border-transparent hover:border-beige">
                    <input type="checkbox" name="is_featured" value="1" <?= (!empty($product['is_featured'])) ? 'checked' : '' ?>
                           class="w-4 h-4 rounded text-charcoal accent-wood">
                    <div>
                        <span class="text-xs font-bold text-charcoal block">Sản phẩm nổi bật</span>
                        <span class="text-[11px] text-muted">Ưu tiên hiển thị tại trang chủ</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-cream/40 cursor-pointer transition border border-transparent hover:border-beige">
                    <input type="checkbox" name="is_new" value="1" <?= (!empty($product['is_new'])) ? 'checked' : '' ?>
                           class="w-4 h-4 rounded text-charcoal accent-wood">
                    <div>
                        <span class="text-xs font-bold text-charcoal block">Hàng mới về (New)</span>
                        <span class="text-[11px] text-muted">Gắn nhãn New trong danh mục</span>
                    </div>
                </label>
            </div>

            <!-- Thumbnail Upload Card -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4 shadow-sm">
                <h2 class="font-bold text-sm text-charcoal pb-3 border-b border-gray-100 flex items-center gap-2">
                    <span>🖼️</span>
                    <span>Ảnh đại diện</span>
                </h2>

                <div id="thumb-preview-box" class="w-full h-44 rounded-xl overflow-hidden bg-cream border border-beige flex items-center justify-center relative">
                    <img id="thumb-preview-img" 
                         src="<?= adminImgUrl($product['thumbnail'] ?? '', $base) ?>" 
                         alt="Xem trước ảnh" 
                         class="w-full h-full object-cover"
                         onerror="this.src='<?= $base ?>/assets/images/product-placeholder.jpg'; this.onerror=null;">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Tải ảnh mới (JPG, PNG, WebP &lt; 5MB)</label>
                    <input type="file" name="thumbnail" accept="image/*" id="thumb-input"
                           class="w-full text-xs text-muted file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:bg-cream file:text-charcoal file:text-xs file:font-semibold hover:file:bg-beige file:cursor-pointer">
                </div>
            </div>

            <!-- Gallery Upload Card -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4 shadow-sm">
                <h2 class="font-bold text-sm text-charcoal pb-3 border-b border-gray-100 flex items-center gap-2">
                    <span>📸</span>
                    <span>Bộ sưu tập ảnh chi tiết</span>
                </h2>

                <?php if (!empty($galleryImages)): ?>
                <div>
                    <p class="text-[11px] font-bold text-muted uppercase mb-2">Ảnh hiện có trong bộ sưu tập:</p>
                    <div class="grid grid-cols-3 gap-2">
                        <?php foreach ($galleryImages as $gImg): ?>
                        <div class="w-full h-16 rounded-lg overflow-hidden bg-cream border border-beige">
                            <img src="<?= adminImgUrl($gImg, $base) ?>" class="w-full h-full object-cover" onerror="this.src='<?= $base ?>/assets/images/product-placeholder.jpg'">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1.5">Thêm ảnh mới vào bộ sưu tập</label>
                    <input type="file" name="images[]" accept="image/*" multiple
                           class="w-full text-xs text-muted file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:bg-cream file:text-charcoal file:text-xs file:font-semibold hover:file:bg-beige file:cursor-pointer">
                    <p class="text-[10px] text-muted mt-1.5">Có thể chọn nhiều file ảnh cùng lúc.</p>
                </div>
            </div>

            <!-- SEO Settings Card -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-3.5 shadow-sm">
                <h2 class="font-bold text-sm text-charcoal pb-3 border-b border-gray-100 flex items-center gap-2">
                    <span>🌐</span>
                    <span>Tối ưu hóa SEO</span>
                </h2>
                <div>
                    <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1">Meta Title</label>
                    <input type="text" name="seo_title" value="<?= htmlspecialchars($product['seo_title'] ?? '') ?>"
                           placeholder="Tiêu đề hiển thị trên Google..."
                           class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-xs text-charcoal outline-none focus:bg-white focus:border-wood">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-muted uppercase tracking-wider mb-1">Meta Description</label>
                    <textarea name="seo_desc" rows="2" placeholder="Đoạn trích tóm tắt trên công cụ tìm kiếm..."
                              class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-3 py-2 text-xs text-charcoal outline-none focus:bg-white focus:border-wood resize-none"><?= htmlspecialchars($product['seo_desc'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Form Submit CTA -->
            <div class="space-y-3">
                <button type="submit" 
                        class="w-full rounded-2xl bg-charcoal text-white py-4 text-xs font-bold uppercase tracking-wider hover:bg-wooddk transition-all duration-200 shadow-warm hover:shadow-warm-lg flex items-center justify-center gap-2">
                    <span><?= $isEdit ? '✓ Cập nhật sản phẩm' : '✓ Lưu sản phẩm mới' ?></span>
                </button>
                <a href="<?= $base ?>/admin/products" 
                   class="w-full inline-block text-center py-3 rounded-2xl border border-gray-200 bg-white text-xs font-semibold text-charcoal hover:bg-gray-50 transition">
                    Hủy và quay lại
                </a>
            </div>

        </div>

    </form>
</div>

<script>
function tierForm() {
    return {
        tiers: <?= !empty($tiers) && is_array($tiers) ? json_encode(array_map(fn($t) => [
            'min_qty' => $t['min_qty'] ?? '',
            'max_qty' => $t['max_qty'] ?? '',
            'price'   => $t['price'] ?? '',
            'label'   => $t['label'] ?? ''
        ], $tiers)) : '[]' ?>,
        
        addTier() { 
            this.tiers.push({ 
                min_qty: '', 
                max_qty: '', 
                price: '', 
                label: '' 
            }); 
        }
    }
}

// Live thumbnail image preview
document.addEventListener('DOMContentLoaded', function() {
    const thumbInput = document.getElementById('thumb-input');
    const previewImg = document.getElementById('thumb-preview-img');
    if (thumbInput && previewImg) {
        thumbInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
