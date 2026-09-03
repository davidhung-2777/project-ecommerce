<?php
$pageTitle = isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm';
$base      = $_ENV['APP_URL'] ?? '';
$isEdit    = isset($product);
$action    = $isEdit ? $base . '/admin/products/' . $product['id'] . '/edit' : $base . '/admin/products/create';
?>
<div class="max-w-4xl space-y-5">
    <div class="flex items-center gap-3">
        <a href="<?= $base ?>/admin/products" class="text-muted hover:text-charcoal text-sm">← Sản phẩm</a>
        <span class="text-muted">/</span>
        <h1 class="text-xl font-bold text-charcoal"><?= $pageTitle ?></h1>
    </div>

    <form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="grid lg:grid-cols-3 gap-6">

        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-5">

            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">Thông tin cơ bản</h2>
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Tên sản phẩm *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($product['name'] ?? '') ?>"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">SKU</label>
                        <input type="text" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Danh mục</label>
                        <select name="category_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Mô tả ngắn</label>
                    <textarea name="short_desc" rows="2"
                              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood resize-none"><?= htmlspecialchars($product['short_desc'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted mb-1.5">Mô tả chi tiết</label>
                    <textarea name="description" rows="6"
                              class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood resize-none"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">Giá bán</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Giá niêm yết (đ) *</label>
                        <input type="number" name="price" required step="1000" min="0"
                               value="<?= htmlspecialchars($product['price'] ?? '') ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Giá khuyến mãi (đ)</label>
                        <input type="number" name="sale_price" step="1000" min="0"
                               value="<?= htmlspecialchars($product['sale_price'] ?? '') ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Phí lắp đặt (đ)</label>
                        <input type="number" name="install_fee" step="1000" min="0"
                               value="<?= htmlspecialchars($product['install_fee'] ?? 0) ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                </div>

                <!-- Bulk Pricing Tiers -->
                <div x-data="tierForm()" class="border-t border-gray-100 pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-semibold text-charcoal uppercase tracking-wide">Giá sỉ theo số lượng</h3>
                        <button type="button" @click="addTier()" class="text-xs text-wood hover:underline">+ Thêm bậc giá</button>
                    </div>
                    <template x-for="(tier, i) in tiers" :key="i">
                        <div class="grid grid-cols-5 gap-3 mb-2 items-center">
                            <div>
                                <input type="number" :name="'tiers[' + i + '][min_qty]'" x-model="tier.min_qty" placeholder="SL tối thiểu" min="1" required
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-wood">
                            </div>
                            <div>
                                <input type="number" :name="'tiers[' + i + '][max_qty]'" x-model="tier.max_qty" placeholder="SL tối đa (để trống = không giới hạn)"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-wood">
                            </div>
                            <div>
                                <input type="number" :name="'tiers[' + i + '][price]'" x-model="tier.price" placeholder="Đơn giá (đ)" required step="1000" min="0"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-wood">
                            </div>
                            <div>
                                <input type="text" :name="'tiers[' + i + '][label]'" x-model="tier.label" placeholder="Nhãn (VD: Giá sỉ)"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-wood">
                            </div>
                            <button type="button" @click="tiers.splice(i, 1)" class="text-red-400 hover:text-red-600">✕</button>
                        </div>
                    </template>
                    <p class="text-xs text-muted mt-1">Tip: SL tối đa để trống = "không giới hạn"</p>
                </div>
            </div>

            <!-- Inventory -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">Tồn kho & Chi tiết</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Số lượng tồn kho *</label>
                        <input type="number" name="stock" required min="0"
                               value="<?= htmlspecialchars($product['stock'] ?? 0) ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Xuất xứ</label>
                        <input type="text" name="origin" value="<?= htmlspecialchars($product['origin'] ?? '') ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Chất liệu</label>
                        <input type="text" name="material" value="<?= htmlspecialchars($product['material'] ?? '') ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Kích thước</label>
                        <input type="text" name="dimensions" placeholder="VD: 160x200x30cm" value="<?= htmlspecialchars($product['dimensions'] ?? '') ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Kích thước biến thể (phân tách bằng dấu phẩy)</label>
                        <input type="text" name="size_options" placeholder="VD: 1.2m, 1.4m, 1.6m, 1.8m"
                               value="<?= htmlspecialchars(implode(', ', json_decode($product['size_options'] ?? '[]', true) ?: [])) ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-muted mb-1.5">Màu sắc biến thể (phân tách bằng dấu phẩy)</label>
                        <input type="text" name="color_options" placeholder="VD: Trắng, Be, Nâu gỗ"
                               value="<?= htmlspecialchars(implode(', ', json_decode($product['color_options'] ?? '[]', true) ?: [])) ?>"
                               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-wood">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="space-y-5">

            <!-- Status -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">Trạng thái</h2>
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" name="is_active" value="1" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="rounded text-charcoal">
                    Hiển thị trên website
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" name="is_featured" value="1" <?= ($product['is_featured'] ?? 0) ? 'checked' : '' ?>
                           class="rounded text-charcoal">
                    Sản phẩm nổi bật
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-sm">
                    <input type="checkbox" name="is_new" value="1" <?= ($product['is_new'] ?? 0) ? 'checked' : '' ?>
                           class="rounded text-charcoal">
                    Hàng mới về
                </label>
            </div>

            <!-- Thumbnail -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">Ảnh đại diện</h2>
                <?php if (!empty($product['thumbnail'])): ?>
                <img src="<?= htmlspecialchars($product['thumbnail']) ?>" class="w-full rounded-lg">
                <?php endif; ?>
                <input type="file" name="thumbnail" accept="image/*"
                       class="w-full text-sm text-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-cream file:text-charcoal file:text-xs hover:file:bg-beige">
                <p class="text-xs text-muted">JPG, PNG, WebP. Tối đa 5MB.</p>
            </div>

            <!-- Gallery -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">Bộ sưu tập ảnh</h2>
                <input type="file" name="images[]" accept="image/*" multiple
                       class="w-full text-sm text-muted file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-cream file:text-charcoal file:text-xs hover:file:bg-beige">
            </div>

            <!-- SEO -->
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-3">
                <h2 class="font-semibold text-sm text-charcoal border-b pb-3">SEO</h2>
                <div>
                    <label class="block text-xs font-medium text-muted mb-1">Meta Title</label>
                    <input type="text" name="seo_title" value="<?= htmlspecialchars($product['seo_title'] ?? '') ?>"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-wood">
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted mb-1">Meta Description</label>
                    <textarea name="seo_desc" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:border-wood resize-none"><?= htmlspecialchars($product['seo_desc'] ?? '') ?></textarea>
                </div>
            </div>

            <button type="submit" class="w-full bg-charcoal text-white py-3 rounded-xl text-sm font-semibold hover:bg-wooddk transition">
                <?= $isEdit ? 'Cập nhật sản phẩm' : 'Tạo sản phẩm' ?>
            </button>
        </div>

    </form>
</div>

<script>
function tierForm() {
    return {
        tiers: <?= json_encode(array_map(fn($t) => ['min_qty' => $t['min_qty'], 'max_qty' => $t['max_qty'], 'price' => $t['price'], 'label' => $t['label']], $tiers ?? [])) ?>,
        addTier() { this.tiers.push({ min_qty: '', max_qty: '', price: '', label: '' }); }
    }
}
</script>
