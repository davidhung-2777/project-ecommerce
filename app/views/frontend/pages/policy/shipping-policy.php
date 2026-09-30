<?php
$pageTitle = 'Chính Sách Giao Hàng - DecorNest';
require_once ROOT_PATH . '/app/views/frontend/layouts/header.php';
?>

<div class="min-h-screen bg-cream pt-24 pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-serif font-bold text-charcoaldk mb-4">
                Chính Sách Giao Hàng & Lắp Đặt
            </h1>
            <p class="text-lg text-charcoal/70">
                Miễn phí vận chuyển đơn từ 5.000.000đ
            </p>
        </div>

        <!-- Policy Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-8 md:p-12 space-y-8">
            
            <!-- Commitment -->
            <div class="bg-wood/5 border border-wood/20 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="text-3xl">🚚</div>
                    <div>
                        <h3 class="font-bold text-lg text-charcoaldk mb-2">Cam Kết Giao Hàng</h3>
                        <p class="text-charcoal/70 leading-relaxed">
                            DecorNest hợp tác với <strong>Giao Hàng Nhanh (GHN)</strong> - đơn vị vận chuyển uy tín. 
                            Đội ngũ lắp đặt chuyên nghiệp, tỉ mỉ, đảm bảo sản phẩm đến tay bạn an toàn và hoàn hảo.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 1 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">1.</span> Phí Vận Chuyển
                </h2>
                <div class="space-y-4">
                    <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded-r-lg">
                        <h4 class="font-semibold text-green-900 mb-2">✅ Miễn phí giao hàng:</h4>
                        <ul class="list-disc pl-6 space-y-1 text-green-800 text-sm">
                            <li>Đơn hàng từ <strong>5.000.000đ</strong> trở lên</li>
                            <li>Áp dụng toàn quốc (trừ khu vực hẻo lánh, hải đảo)</li>
                        </ul>
                    </div>

                    <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">💳 Phí vận chuyển áp dụng:</h4>
                        <p class="text-blue-800 text-sm mb-2">Đơn hàng dưới 5.000.000đ:</p>
                        <ul class="list-disc pl-6 space-y-1 text-blue-800 text-sm">
                            <li><strong>Nội thành Hà Nội, HCM:</strong> 30.000đ - 50.000đ</li>
                            <li><strong>Các tỉnh thành khác:</strong> Tính theo khoảng cách và khối lượng (20.000đ - 150.000đ)</li>
                            <li><strong>Khu vực xa trung tâm:</strong> Báo giá riêng</li>
                        </ul>
                        <p class="text-xs text-blue-700 mt-2 italic">
                            * Phí vận chuyển được tính tự động khi bạn nhập địa chỉ tại trang thanh toán
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section 2 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">2.</span> Thời Gian Giao Hàng
                </h2>
                <div class="space-y-3">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="border border-charcoal/10 rounded-lg p-4">
                            <h4 class="font-semibold text-charcoaldk mb-2">📍 Nội thành HN, HCM</h4>
                            <p class="text-sm text-charcoal/70"><strong>1-2 ngày làm việc</strong></p>
                            <p class="text-xs text-charcoal/50 mt-1">Giao hàng trong giờ hành chính (8:00 - 18:00)</p>
                        </div>
                        <div class="border border-charcoal/10 rounded-lg p-4">
                            <h4 class="font-semibold text-charcoaldk mb-2">🏙️ Các tỉnh thành lớn</h4>
                            <p class="text-sm text-charcoal/70"><strong>2-4 ngày làm việc</strong></p>
                            <p class="text-xs text-charcoal/50 mt-1">Đà Nẵng, Cần Thơ, Hải Phòng, Bình Dương...</p>
                        </div>
                        <div class="border border-charcoal/10 rounded-lg p-4">
                            <h4 class="font-semibold text-charcoaldk mb-2">🌄 Khu vực miền núi, xa trung tâm</h4>
                            <p class="text-sm text-charcoal/70"><strong>4-7 ngày làm việc</strong></p>
                            <p class="text-xs text-charcoal/50 mt-1">Tùy điều kiện thời tiết và địa hình</p>
                        </div>
                        <div class="border border-charcoal/10 rounded-lg p-4">
                            <h4 class="font-semibold text-charcoaldk mb-2">🏝️ Hải đảo (Phú Quốc, Cô Tô...)</h4>
                            <p class="text-sm text-charcoal/70"><strong>7-10 ngày làm việc</strong></p>
                            <p class="text-xs text-charcoal/50 mt-1">Có thể phát sinh phí phà, tàu</p>
                        </div>
                    </div>
                    <p class="text-xs text-charcoal/50 italic">
                        * Thời gian được tính từ khi đơn hàng được xác nhận và xử lý. Không áp dụng cho ngày Lễ, Tết.
                    </p>
                </div>
            </section>

            <!-- Section 3 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">3.</span> Quy Trình Giao Nhận
                </h2>
                <div class="space-y-3">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">1</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Xác nhận đơn hàng</h4>
                            <p class="text-sm text-charcoal/70">DecorNest gọi điện xác nhận trong <strong>2 giờ</strong> sau khi đặt</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">2</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Đóng gói cẩn thận</h4>
                            <p class="text-sm text-charcoal/70">Bao bì carton 5 lớp, xốp hơi, băng keo chống va đập</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">3</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Bàn giao cho GHN</h4>
                            <p class="text-sm text-charcoal/70">Bạn nhận mã vận đơn để theo dõi hành trình</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">4</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Liên hệ trước khi giao</h4>
                            <p class="text-sm text-charcoal/70">Shipper gọi điện hẹn giờ giao hàng tiện lợi</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">5</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Kiểm tra hàng khi nhận</h4>
                            <p class="text-sm text-charcoal/70">Mở kiện hàng trước mặt shipper, kiểm tra kỹ trước khi ký nhận</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 4 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">4.</span> Dịch Vụ Lắp Đặt
                </h2>
                <div class="space-y-4">
                    <p class="text-charcoal/80">
                        DecorNest cung cấp dịch vụ lắp đặt chuyên nghiệp tại nhà (áp dụng khu vực nội thành HN, HCM):
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-semibold text-green-900 mb-2">✅ Miễn phí lắp đặt:</h4>
                            <ul class="list-disc pl-6 space-y-1 text-green-800 text-sm">
                                <li>Đèn ngủ, đèn treo tường (điểm nguồn có sẵn)</li>
                                <li>Kệ gỗ, giá treo, tranh khung (không khoan tường)</li>
                                <li>Lắp ráp đồ nội thất nhỏ</li>
                            </ul>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-900 mb-2">💰 Lắp đặt có phí:</h4>
                            <ul class="list-disc pl-6 space-y-1 text-blue-800 text-sm">
                                <li>Khoan tường, trần: <strong>50.000đ/lỗ</strong></li>
                                <li>Đấu nối điện phức tạp: <strong>100.000đ</strong></li>
                                <li>Lắp đặt tổng thể phòng ngủ: <strong>Báo giá riêng</strong></li>
                            </ul>
                        </div>
                    </div>
                    <p class="text-xs text-blue-700 italic">
                        * Vui lòng thông báo nhu cầu lắp đặt khi đặt hàng để được tư vấn và báo giá chính xác
                    </p>
                </div>
            </section>

            <!-- Section 5 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">5.</span> Lưu Ý Khi Nhận Hàng
                </h2>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <ul class="space-y-2 text-amber-900 text-sm">
                        <li><strong>✓ Kiểm tra bao bì:</strong> Nếu thấy dập nát, ướt sũng → từ chối nhận và báo ngay hotline</li>
                        <li><strong>✓ Mở kiện hàng:</strong> Bạn có quyền mở hàng trước khi ký nhận để kiểm tra tình trạng</li>
                        <li><strong>✓ Đối chiếu sản phẩm:</strong> So sánh với đơn hàng (mã, màu, kích thước)</li>
                        <li><strong>✓ Chụp ảnh/video:</strong> Nếu phát hiện lỗi, chụp ảnh/quay video ngay trước mặt shipper</li>
                        <li><strong>✓ Ký biên bản:</strong> Ghi rõ tình trạng hàng lên phiếu giao nhận nếu có vấn đề</li>
                        <li><strong>✓ Liên hệ ngay:</strong> Gọi hotline <strong>1900 1234</strong> trong vòng <strong>24 giờ</strong> để được hỗ trợ</li>
                    </ul>
                </div>
            </section>

            <!-- Section 6 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">6.</span> Theo Dõi Đơn Hàng
                </h2>
                <p class="text-charcoal/80 mb-3">Bạn có thể theo dõi trạng thái đơn hàng qua:</p>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="border border-charcoal/10 rounded-lg p-4 text-center">
                        <div class="text-2xl mb-2">🌐</div>
                        <h4 class="font-semibold text-charcoaldk mb-1">Website DecorNest</h4>
                        <p class="text-sm text-charcoal/70">Đăng nhập → "Đơn hàng của tôi"</p>
                    </div>
                    <div class="border border-charcoal/10 rounded-lg p-4 text-center">
                        <div class="text-2xl mb-2">📦</div>
                        <h4 class="font-semibold text-charcoaldk mb-1">GHN Tracking</h4>
                        <p class="text-sm text-charcoal/70">ghn.vn/pages/tracking</p>
                    </div>
                    <div class="border border-charcoal/10 rounded-lg p-4 text-center">
                        <div class="text-2xl mb-2">📞</div>
                        <h4 class="font-semibold text-charcoaldk mb-1">Gọi hotline</h4>
                        <p class="text-sm text-charcoal/70">1900 1234 (máy lẻ 2)</p>
                    </div>
                </div>
            </section>

            <!-- Contact CTA -->
            <div class="bg-wood/10 border border-wood/30 rounded-xl p-6 text-center">
                <h3 class="text-xl font-bold text-charcoaldk mb-2">Cần hỗ trợ giao hàng?</h3>
                <p class="text-charcoal/70 mb-4">Liên hệ bộ phận vận chuyển DecorNest</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="tel:19001234" class="inline-flex items-center justify-center gap-2 bg-wood hover:bg-wooddk text-white px-6 py-3 rounded-xl font-semibold transition">
                        📞 Hotline: 1900 1234 (máy lẻ 2)
                    </a>
                    <a href="mailto:shipping@decornest.vn" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-charcoal/5 text-charcoaldk px-6 py-3 rounded-xl font-semibold border border-charcoal/20 transition">
                        ✉️ shipping@decornest.vn
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once ROOT_PATH . '/app/views/frontend/layouts/footer.php'; ?>
