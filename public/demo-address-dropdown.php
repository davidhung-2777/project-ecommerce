<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Dropdown Địa Chỉ Chuyên Nghiệp - DecorNest</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        /* Custom Select2 styling */
        .select2-container--default .select2-selection--single {
            background-color: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            height: 48px;
            padding: 8px 16px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
            color: #111827;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
        }
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #8B7355;
        }
        .select2-dropdown {
            border: 2px solid #8B7355;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 8px 12px;
        }
        .select2-results__option {
            padding: 10px 15px;
        }
        .select2-results__option--highlighted {
            background-color: #8B7355 !important;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-12">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                🚀 Demo Dropdown Địa Chỉ Chuyên Nghiệp
            </h1>
            <p class="text-gray-600">Giống Shopee, Tiki, Lazada - Powered by GHN API</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
            <h2 class="text-xl font-bold mb-6 text-gray-900">📍 Thông tin giao hàng</h2>
            
            <form class="space-y-5">
                
                <!-- Họ tên -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Họ và tên *</label>
                    <input type="text" placeholder="Nguyễn Văn A" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                </div>

                <!-- Số điện thoại -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Số điện thoại *</label>
                    <input type="tel" placeholder="0901 234 567" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                </div>

                <!-- Tỉnh/Thành phố -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tỉnh / Thành phố *</label>
                    <select id="province-select" class="w-full" required>
                        <option value="">Chọn Tỉnh/Thành phố</option>
                    </select>
                </div>

                <!-- Quận/Huyện -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Quận / Huyện *</label>
                    <select id="district-select" class="w-full" required disabled>
                        <option value="">Chọn Quận/Huyện</option>
                    </select>
                </div>

                <!-- Phường/Xã -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phường / Xã *</label>
                    <select id="ward-select" class="w-full" required disabled>
                        <option value="">Chọn Phường/Xã</option>
                    </select>
                </div>

                <!-- Địa chỉ cụ thể -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Địa chỉ cụ thể (Số nhà, tên đường) *</label>
                    <input type="text" placeholder="Ví dụ: 120 Đường Láng" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                </div>

                <!-- Hiển thị phí ship -->
                <div id="shipping-fee-box" class="hidden bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 font-medium">💰 Phí vận chuyển:</span>
                        <span id="shipping-fee-display" class="text-xl font-bold text-blue-600">--</span>
                    </div>
                    <p id="delivery-time" class="text-xs text-gray-600 mt-2"></p>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition-all">
                    Đặt hàng ngay
                </button>
            </form>
        </div>

        <!-- Info Box -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-6 text-white">
            <h3 class="text-lg font-bold mb-3">✨ Tính năng nổi bật:</h3>
            <ul class="space-y-2 text-sm">
                <li>✅ Tìm kiếm nhanh trong 63 tỉnh/thành Việt Nam</li>
                <li>✅ Tự động load quận/huyện và phường/xã</li>
                <li>✅ Tính phí ship tự động theo địa chỉ</li>
                <li>✅ Không thể nhập sai địa chỉ</li>
                <li>✅ Trải nghiệm giống Shopee, Tiki, Lazada</li>
            </ul>
        </div>

        <!-- Back link -->
        <div class="text-center mt-8">
            <a href="/project-ecommerce/public/" class="text-blue-600 hover:underline">
                ← Về trang chủ DecorNest
            </a>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Sửa đường dẫn API
        const BASE_URL = '/project-ecommerce/public';
        
        // Initialize Select2 with custom styling
        $('.w-full').select2({
            placeholder: function() {
                return $(this).find('option:first').text();
            },
            language: {
                noResults: () => "❌ Không tìm thấy kết quả",
                searching: () => "🔍 Đang tìm kiếm...",
                inputTooShort: () => "⌨️ Nhập để tìm kiếm..."
            },
            minimumResultsForSearch: 0
        });
        
        // Load provinces
        loadProvinces();
        
        function loadProvinces() {
            $.get(BASE_URL + '/api/shipping/provinces.php', function(response) {
                if (response.success) {
                    const select = $('#province-select');
                    select.empty().append('<option value="">🏙️ Chọn Tỉnh/Thành phố</option>');
                    
                    response.data.forEach(province => {
                        select.append(new Option(province.ProvinceName, province.ProvinceID));
                    });
                    
                    console.log('✅ Loaded ' + response.data.length + ' provinces');
                }
            }).fail(function() {
                alert('❌ Lỗi kết nối API. Vui lòng kiểm tra:\n1. XAMPP đã bật chưa?\n2. Token GHN đã đúng chưa?');
            });
        }
        
        // Province change → Load districts
        $('#province-select').on('change', function() {
            const provinceId = $(this).val();
            const provinceName = $(this).find('option:selected').text();
            
            // Reset
            $('#district-select').empty().append('<option value="">📍 Chọn Quận/Huyện</option>').prop('disabled', true).trigger('change');
            $('#ward-select').empty().append('<option value="">🏘️ Chọn Phường/Xã</option>').prop('disabled', true).trigger('change');
            $('#shipping-fee-box').addClass('hidden');
            
            if (!provinceId) return;
            
            console.log('Loading districts for:', provinceName);
            
            $.get(BASE_URL + '/api/shipping/districts.php?province_id=' + provinceId, function(response) {
                if (response.success) {
                    const select = $('#district-select');
                    select.empty().append('<option value="">📍 Chọn Quận/Huyện</option>');
                    
                    response.data.forEach(district => {
                        select.append(new Option(district.DistrictName, district.DistrictID));
                    });
                    
                    select.prop('disabled', false).trigger('change');
                    console.log('✅ Loaded ' + response.data.length + ' districts');
                }
            });
        });
        
        // District change → Load wards
        $('#district-select').on('change', function() {
            const districtId = $(this).val();
            const districtName = $(this).find('option:selected').text();
            
            // Reset
            $('#ward-select').empty().append('<option value="">🏘️ Chọn Phường/Xã</option>').prop('disabled', true).trigger('change');
            $('#shipping-fee-box').addClass('hidden');
            
            if (!districtId) return;
            
            console.log('Loading wards for:', districtName);
            
            $.get(BASE_URL + '/api/shipping/wards.php?district_id=' + districtId, function(response) {
                if (response.success) {
                    const select = $('#ward-select');
                    select.empty().append('<option value="">🏘️ Chọn Phường/Xã</option>');
                    
                    response.data.forEach(ward => {
                        select.append(new Option(ward.WardName, ward.WardCode));
                    });
                    
                    select.prop('disabled', false).trigger('change');
                    console.log('✅ Loaded ' + response.data.length + ' wards');
                }
            });
        });
        
        // Ward change → Calculate shipping fee
        $('#ward-select').on('change', function() {
            const wardCode = $(this).val();
            const districtId = $('#district-select').val();
            
            if (!wardCode || !districtId) {
                $('#shipping-fee-box').addClass('hidden');
                return;
            }
            
            console.log('Calculating shipping fee...');
            $('#shipping-fee-display').html('<span class="animate-pulse">Đang tính...</span>');
            $('#shipping-fee-box').removeClass('hidden');
            
            $.ajax({
                url: BASE_URL + '/api/shipping/calculate-fee.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    district_id: parseInt(districtId),
                    ward_code: wardCode,
                    weight: 5000,
                    order_value: 5000000
                }),
                success: function(response) {
                    if (response.success) {
                        const fee = formatMoney(response.fee) + 'đ';
                        $('#shipping-fee-display').text(fee);
                        
                        if (response.expected_delivery_time) {
                            $('#delivery-time').text('⏰ Dự kiến giao: ' + response.expected_delivery_time);
                        }
                        
                        console.log('✅ Shipping fee:', fee);
                    }
                },
                error: function() {
                    $('#shipping-fee-display').text('50.000đ (mặc định)');
                }
            });
        });
        
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }
    });
    </script>
</body>
</html>
