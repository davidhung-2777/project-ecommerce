<?php
$baseUrl   = $_ENV['APP_URL'] ?? 'http://localhost/project-ecommerce/public';
$pageTitle = 'Thanh Toán Đơn Hàng - DecorNest';
$items     = $cartData['items'] ?? [];
$subtotal  = $cartData['subtotal'] ?? 0;
$errors    = $_SESSION['checkout_errors'] ?? [];
$oldInput  = $_SESSION['checkout_input'] ?? [];
unset($_SESSION['checkout_errors'], $_SESSION['checkout_input']);
?>

<!-- Include Select2 for searchable dropdowns -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
/* Custom Select2 styling to match DecorNest theme */
.select2-container--default .select2-selection--single {
    background-color: rgba(245, 241, 232, 0.4);
    border: 1px solid #E8DCC4;
    border-radius: 0.75rem;
    height: 46px;
    padding: 8px 12px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #2C2C2C;
    line-height: 28px;
    font-size: 0.875rem;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 44px;
    right: 8px;
}
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #8B7355;
    background-color: white;
}
.select2-dropdown {
    border: 1px solid #8B7355;
    border-radius: 0.75rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.select2-search--dropdown .select2-search__field {
    border: 1px solid #E8DCC4;
    border-radius: 0.5rem;
    padding: 8px 12px;
    font-size: 0.875rem;
}
.select2-results__option {
    padding: 10px 12px;
    font-size: 0.875rem;
}
.select2-results__option--highlighted {
    background-color: #8B7355 !important;
}
</style>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">

    <!-- Steps Indicator -->
    <div class="flex items-center justify-center gap-3 mb-10 max-w-md mx-auto">
        <div class="flex items-center gap-2 text-xs font-semibold text-muted">
            <span class="w-7 h-7 rounded-full bg-sage-light text-sage font-bold flex items-center justify-center text-xs">✓</span>
            <span>Giỏ hàng</span>
        </div>
        <div class="w-10 h-px bg-sand"></div>
        <div class="flex items-center gap-2 text-xs font-bold text-charcoal">
            <span class="w-7 h-7 rounded-full bg-charcoal text-white flex items-center justify-center text-xs shadow-warm">2</span>
            <span>Thanh toán</span>
        </div>
        <div class="w-10 h-px bg-sand"></div>
        <div class="flex items-center gap-2 text-xs text-muted">
            <span class="w-7 h-7 rounded-full bg-cream text-muted flex items-center justify-center text-xs border border-beige">3</span>
            <span>Hoàn tất</span>
        </div>
    </div>

    <?php if ($errors): ?>
    <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-2xl max-w-4xl mx-auto">
        <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>/checkout/process" method="POST" id="checkout-form">

        <div class="grid lg:grid-cols-12 gap-10">
            
            <!-- Left: Checkout Forms -->
            <div class="lg:col-span-8 space-y-8">

                <!-- 1. Shipping Address -->
                <div class="bg-white border border-beige rounded-3xl p-6 sm:p-8 shadow-warm">
                    <h2 class="font-serif font-bold text-lg text-charcoal mb-6 flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-cream text-wood font-sans text-xs font-bold flex items-center justify-center border border-sand">1</span>
                        <span>Thông tin giao hàng & Lắp đặt</span>
                    </h2>
                    
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
                                Họ và tên người nhận *
                            </label>
                            <input type="text" name="shipping_name" required
                                   value="<?= htmlspecialchars($oldInput['shipping_name'] ?? $user['name'] ?? '') ?>"
                                   placeholder="Nguyễn Văn A"
                                   class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
                                Số điện thoại liên hệ *
                            </label>
                            <input type="tel" name="shipping_phone" required
                                   value="<?= htmlspecialchars($oldInput['shipping_phone'] ?? $user['phone'] ?? '') ?>"
                                   placeholder="0901 234 567"
                                   class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                        </div>

                        <!-- DROPDOWN CHUYÊN NGHIỆP -->
                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
                                Tỉnh / Thành phố *
                            </label>
                            <select name="province_id" id="province-select" required class="w-full">
                                <option value="">Chọn Tỉnh/Thành phố</option>
                            </select>
                            <input type="hidden" name="shipping_city" id="shipping_city">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
                                Quận / Huyện *
                            </label>
                            <select name="district_id" id="district-select" required class="w-full" disabled>
                                <option value="">Chọn Quận/Huyện</option>
                            </select>
                            <input type="hidden" name="shipping_district" id="shipping_district">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
                                Phường / Xã *
                            </label>
                            <select name="ward_code" id="ward-select" required class="w-full" disabled>
                                <option value="">Chọn Phường/Xã</option>
                            </select>
                            <input type="hidden" name="shipping_ward" id="shipping_ward">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
                                Địa chỉ cụ thể (Số nhà, tên đường) *
                            </label>
                            <input type="text" name="shipping_address" required
                                   value="<?= htmlspecialchars($oldInput['shipping_address'] ?? '') ?>"
                                   placeholder="Ví dụ: 120 Đường Láng"
                                   class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm focus:bg-white focus:outline-none focus:border-wood">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-[11px] font-semibold text-muted uppercase tracking-wider mb-1.5">
                            Ghi chú giao hàng / Giờ nhận thuận tiện
                        </label>
                        <textarea name="customer_note" rows="2" 
                                  placeholder="Ví dụ: Giao sau 17h, gọi trước 30 phút..."
                                  class="w-full bg-cream/40 border border-beige rounded-xl px-4 py-3 text-xs sm:text-sm resize-none focus:bg-white focus:outline-none focus:border-wood"><?= htmlspecialchars($oldInput['customer_note'] ?? '') ?></textarea>
                    </div>

                    <!-- Hiển thị phí ship -->
                    <div class="mt-6 p-4 bg-sage-light/30 rounded-xl">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="font-bold text-charcoal" id="shipping-fee-display">
                                Chọn địa chỉ để tính phí
                            </span>
                        </div>
                        <input type="hidden" name="shipping_fee" id="shipping-fee-input" value="50000">
                    </div>
                </div>

                <!-- 2. Payment Method (giữ nguyên như cũ) -->
                <div class="bg-white border border-beige rounded-3xl p-6 sm:p-8 shadow-warm">
                    <h2 class="font-serif font-bold text-lg text-charcoal mb-6 flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-cream text-wood font-sans text-xs font-bold flex items-center justify-center border border-sand">2</span>
                        <span>Phương thức thanh toán</span>
                    </h2>
                    
                    <div class="space-y-3">
                        <!-- Payment methods here (keep existing code) -->
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-charcoal hover:bg-wood text-white text-sm font-bold py-4 rounded-xl transition-all duration-300 shadow-warm hover:shadow-warm-lg">
                    Hoàn tất đặt hàng
                </button>
            </div>

            <!-- Right: Order Summary (keep existing code) -->
            <div class="lg:col-span-4">
                <!-- Order summary here -->
            </div>
        </div>
    </form>
</div>

<script>
// Checkout Logic với GHN API
$(document).ready(function() {
    const BASE_URL = '<?= $baseUrl ?>';
    let shippingFee = 50000; // Phí mặc định
    
    // Initialize Select2
    $('#province-select, #district-select, #ward-select').select2({
        placeholder: function() {
            return $(this).data('placeholder');
        },
        allowClear: true,
        language: {
            noResults: function() {
                return "Không tìm thấy kết quả";
            },
            searching: function() {
                return "Đang tìm kiếm...";
            }
        }
    });
    
    // Load provinces on page load
    loadProvinces();
    
    function loadProvinces() {
        $.ajax({
            url: BASE_URL + '/api/shipping/provinces.php',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const select = $('#province-select');
                    select.empty().append('<option value="">Chọn Tỉnh/Thành phố</option>');
                    
                    response.data.forEach(function(province) {
                        select.append(new Option(province.ProvinceName, province.ProvinceID));
                    });
                    
                    select.prop('disabled', false).trigger('change.select2');
                }
            }
        });
    }
    
    // When province changes, load districts
    $('#province-select').on('change', function() {
        const provinceId = $(this).val();
        const provinceName = $(this).find('option:selected').text();
        
        $('#shipping_city').val(provinceName);
        
        // Reset district and ward
        $('#district-select').empty().append('<option value="">Chọn Quận/Huyện</option>').prop('disabled', true).trigger('change.select2');
        $('#ward-select').empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true).trigger('change.select2');
        
        if (!provinceId) return;
        
        // Load districts
        $.ajax({
            url: BASE_URL + '/api/shipping/districts.php?province_id=' + provinceId,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const select = $('#district-select');
                    select.empty().append('<option value="">Chọn Quận/Huyện</option>');
                    
                    response.data.forEach(function(district) {
                        select.append(new Option(district.DistrictName, district.DistrictID));
                    });
                    
                    select.prop('disabled', false).trigger('change.select2');
                }
            }
        });
    });
    
    // When district changes, load wards
    $('#district-select').on('change', function() {
        const districtId = $(this).val();
        const districtName = $(this).find('option:selected').text();
        
        $('#shipping_district').val(districtName);
        
        // Reset ward
        $('#ward-select').empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true).trigger('change.select2');
        
        if (!districtId) return;
        
        // Load wards
        $.ajax({
            url: BASE_URL + '/api/shipping/wards.php?district_id=' + districtId,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const select = $('#ward-select');
                    select.empty().append('<option value="">Chọn Phường/Xã</option>');
                    
                    response.data.forEach(function(ward) {
                        select.append(new Option(ward.WardName, ward.WardCode));
                    });
                    
                    select.prop('disabled', false).trigger('change.select2');
                }
            }
        });
    });
    
    // When ward changes, calculate shipping fee
    $('#ward-select').on('change', function() {
        const wardCode = $(this).val();
        const wardName = $(this).find('option:selected').text();
        const districtId = $('#district-select').val();
        
        $('#shipping_ward').val(wardName);
        
        if (!wardCode || !districtId) return;
        
        // Calculate shipping fee
        $.ajax({
            url: BASE_URL + '/api/shipping/calculate-fee.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                district_id: parseInt(districtId),
                ward_code: wardCode,
                weight: 5000, // 5kg default
                order_value: <?= (int)$subtotal ?>
            }),
            success: function(response) {
                if (response.success) {
                    shippingFee = response.fee;
                    $('#shipping-fee-input').val(shippingFee);
                    $('#shipping-fee-display').html(
                        '<strong>' + formatMoney(shippingFee) + 'đ</strong>'
                    );
                    
                    // Update total
                    updateTotal();
                }
            }
        });
    });
    
    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount);
    }
    
    function updateTotal() {
        // Update total amount in order summary
        const subtotal = <?= (int)$subtotal ?>;
        const total = subtotal + shippingFee;
        // Update UI here
    }
});
</script>
