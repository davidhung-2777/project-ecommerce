<?php
$pageTitle = 'Chính Sách Bảo Mật - DecorNest';
require_once ROOT_PATH . '/app/views/frontend/layouts/header.php';
?>

<div class="min-h-screen bg-cream pt-24 pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-serif font-bold text-charcoaldk mb-4">
                Chính Sách Bảo Mật
            </h1>
            <p class="text-lg text-charcoal/70">
                Cam kết bảo vệ thông tin cá nhân của bạn
            </p>
        </div>

        <!-- Policy Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-8 md:p-12 space-y-8">
            
            <!-- Commitment -->
            <div class="bg-wood/5 border border-wood/20 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="text-3xl">🔒</div>
                    <div>
                        <h3 class="font-bold text-lg text-charcoaldk mb-2">Cam Kết Bảo Mật</h3>
                        <p class="text-charcoal/70 leading-relaxed">
                            DecorNest tôn trọng quyền riêng tư và cam kết bảo vệ thông tin cá nhân của khách hàng. 
                            Chúng tôi chỉ thu thập, sử dụng dữ liệu cho mục đích cung cấp dịch vụ tốt nhất và 
                            <strong>không bao giờ chia sẻ cho bên thứ ba</strong> mà không có sự đồng ý của bạn.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 1 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">1.</span> Thông Tin Chúng Tôi Thu Thập
                </h2>
                <div class="space-y-4">
                    <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg">
                        <h4 class="font-semibold text-blue-900 mb-2">📝 Thông tin bạn cung cấp:</h4>
                        <ul class="list-disc pl-6 space-y-1 text-blue-800 text-sm">
                            <li><strong>Tài khoản:</strong> Họ tên, email, số điện thoại, mật khẩu (mã hóa)</li>
                            <li><strong>Đơn hàng:</strong> Địa chỉ giao hàng, thông tin thanh toán</li>
                            <li><strong>Liên hệ:</strong> Nội dung trao đổi qua email, chat, hotline</li>
                            <li><strong>Báo giá B2B:</strong> Tên công ty, mã số thuế, nhu cầu số lượng</li>
                        </ul>
                    </div>

                    <div class="border-l-4 border-purple-500 bg-purple-50 p-4 rounded-r-lg">
                        <h4 class="font-semibold text-purple-900 mb-2">📊 Thông tin tự động thu thập:</h4>
                        <ul class="list-disc pl-6 space-y-1 text-purple-800 text-sm">
                            <li><strong>Cookies:</strong> Lưu giỏ hàng, tùy chọn ngôn ngữ, phiên đăng nhập</li>
                            <li><strong>Log truy cập:</strong> Địa chỉ IP, trình duyệt, thời gian truy cập</li>
                            <li><strong>Hành vi:</strong> Sản phẩm xem, thêm vào giỏ hàng, từ khóa tìm kiếm</li>
                        </ul>
                        <p class="text-xs text-purple-700 mt-2 italic">
                            * Dữ liệu này giúp cải thiện trải nghiệm mua sắm, gợi ý sản phẩm phù hợp
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section 2 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">2.</span> Mục Đích Sử Dụng Thông Tin
                </h2>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="text-xl">✓</span>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Xử lý đơn hàng & giao hàng</h4>
                            <p class="text-sm text-charcoal/70">Xác nhận đơn, thanh toán, vận chuyển, theo dõi đơn hàng</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-xl">✓</span>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Chăm sóc khách hàng</h4>
                            <p class="text-sm text-charcoal/70">Giải đáp thắc mắc, hỗ trợ bảo hành, xử lý khiếu nại</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-xl">✓</span>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Cải thiện dịch vụ</h4>
                            <p class="text-sm text-charcoal/70">Phân tích hành vi, tối ưu website, phát triển sản phẩm mới</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-xl">✓</span>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Marketing (nếu bạn đồng ý)</h4>
                            <p class="text-sm text-charcoal/70">Gửi email khuyến mãi, thông tin sản phẩm mới, bản tin trang trí</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-xl">✓</span>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Bảo mật & tuân thủ pháp luật</h4>
                            <p class="text-sm text-charcoal/70">Phát hiện gian lận, bảo vệ hệ thống, đáp ứng yêu cầu pháp lý</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 3 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">3.</span> Chia Sẻ Thông Tin
                </h2>
                <div class="space-y-4">
                    <p class="text-charcoal/80">
                        DecorNest <strong>KHÔNG bán hoặc cho thuê</strong> thông tin cá nhân của bạn. 
                        Chúng tôi chỉ chia sẻ dữ liệu với các đối tác sau khi có sự đồng ý:
                    </p>
                    <div class="space-y-3">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">🚚 Đối tác vận chuyển (GHN)</h4>
                            <p class="text-sm text-gray-700">Chia sẻ: Tên, SĐT, địa chỉ giao hàng để giao nhận</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">💳 Cổng thanh toán (VNPay, MoMo, VietQR)</h4>
                            <p class="text-sm text-gray-700">Chia sẻ: Thông tin thanh toán qua kết nối bảo mật SSL</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">📧 Email Marketing (nếu bạn đăng ký)</h4>
                            <p class="text-sm text-gray-700">Chia sẻ: Email để gửi bản tin, khuyến mãi</p>
                        </div>
                    </div>
                    <p class="text-xs text-charcoal/60 italic">
                        * Tất cả đối tác đều ký cam kết bảo mật thông tin khách hàng
                    </p>
                </div>
            </section>

            <!-- Section 4 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">4.</span> Bảo Mật Thông Tin
                </h2>
                <div class="space-y-3">
                    <p class="text-charcoal/80">
                        Chúng tôi áp dụng các biện pháp kỹ thuật và tổ chức để bảo vệ dữ liệu:
                    </p>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="border border-charcoal/10 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xl">🔐</span>
                                <h4 class="font-semibold text-charcoaldk">Mã hóa SSL/TLS</h4>
                            </div>
                            <p class="text-sm text-charcoal/70">Dữ liệu truyền tải được mã hóa (https://)</p>
                        </div>
                        <div class="border border-charcoal/10 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xl">🔑</span>
                                <h4 class="font-semibold text-charcoaldk">Mật khẩu băm</h4>
                            </div>
                            <p class="text-sm text-charcoal/70">Mật khẩu được hash, không lưu dạng plain text</p>
                        </div>
                        <div class="border border-charcoal/10 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xl">🛡️</span>
                                <h4 class="font-semibold text-charcoaldk">Tường lửa & Antivirus</h4>
                            </div>
                            <p class="text-sm text-charcoal/70">Ngăn chặn truy cập trái phép, mã độc</p>
                        </div>
                        <div class="border border-charcoal/10 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xl">👥</span>
                                <h4 class="font-semibold text-charcoaldk">Kiểm soát truy cập</h4>
                            </div>
                            <p class="text-sm text-charcoal/70">Chỉ nhân viên được ủy quyền mới xem dữ liệu</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 5 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">5.</span> Quyền Của Bạn
                </h2>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="font-semibold text-green-900 mb-2">Bạn có quyền:</p>
                    <ul class="space-y-2 text-green-800 text-sm">
                        <li><strong>✓ Truy cập:</strong> Xem thông tin cá nhân DecorNest đang lưu trữ</li>
                        <li><strong>✓ Chỉnh sửa:</strong> Cập nhật thông tin không chính xác trong tài khoản</li>
                        <li><strong>✓ Xóa dữ liệu:</strong> Yêu cầu xóa tài khoản và dữ liệu cá nhân</li>
                        <li><strong>✓ Từ chối marketing:</strong> Hủy đăng ký email khuyến mãi bất cứ lúc nào</li>
                        <li><strong>✓ Rút lại đồng ý:</strong> Ngừng cho phép xử lý dữ liệu (trừ mục đích pháp lý)</li>
                        <li><strong>✓ Khiếu nại:</strong> Liên hệ privacy@decornest.vn nếu có vi phạm</li>
                    </ul>
                </div>
                <p class="text-sm text-charcoal/70 mt-3">
                    Để thực hiện các quyền trên, vui lòng liên hệ: <strong>privacy@decornest.vn</strong> hoặc gọi <strong>1900 1234</strong>
                </p>
            </section>

            <!-- Section 6 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">6.</span> Cookies & Công Nghệ Theo Dõi
                </h2>
                <div class="space-y-3">
                    <p class="text-charcoal/80">
                        Website sử dụng cookies để cải thiện trải nghiệm. Bạn có thể cài đặt trình duyệt để từ chối cookies, 
                        nhưng một số tính năng có thể không hoạt động.
                    </p>
                    <div class="border border-charcoal/10 rounded-lg p-4">
                        <h4 class="font-semibold text-charcoaldk mb-2">Loại cookies chúng tôi sử dụng:</h4>
                        <ul class="list-disc pl-6 space-y-1 text-sm text-charcoal/70">
                            <li><strong>Cookies cần thiết:</strong> Đăng nhập, giỏ hàng, thanh toán</li>
                            <li><strong>Cookies phân tích:</strong> Google Analytics để hiểu hành vi người dùng</li>
                            <li><strong>Cookies marketing:</strong> Hiển thị quảng cáo phù hợp (nếu bạn đồng ý)</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Section 7 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">7.</span> Lưu Trữ & Thời Hạn
                </h2>
                <p class="text-charcoal/80">
                    Chúng tôi lưu trữ thông tin cá nhân trong thời gian cần thiết:
                </p>
                <ul class="list-disc pl-6 space-y-2 text-sm text-charcoal/70 mt-3">
                    <li><strong>Tài khoản hoạt động:</strong> Cho đến khi bạn yêu cầu xóa</li>
                    <li><strong>Lịch sử đơn hàng:</strong> 5 năm (theo quy định kế toán, thuế)</li>
                    <li><strong>Tài khoản không hoạt động:</strong> Xóa sau 3 năm không đăng nhập</li>
                    <li><strong>Cookies:</strong> Tự động hết hạn sau 30 ngày (hoặc khi xóa trình duyệt)</li>
                </ul>
            </section>

            <!-- Section 8 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">8.</span> Cập Nhật Chính Sách
                </h2>
                <p class="text-charcoal/80">
                    DecorNest có thể cập nhật chính sách này theo thời gian. Chúng tôi sẽ thông báo qua email hoặc 
                    thông báo trên website nếu có thay đổi quan trọng. Phiên bản mới nhất luôn được công bố tại trang này.
                </p>
                <p class="text-sm text-charcoal/60 mt-3 italic">
                    Cập nhật lần cuối: 01/01/2026
                </p>
            </section>

            <!-- Contact CTA -->
            <div class="bg-wood/10 border border-wood/30 rounded-xl p-6 text-center">
                <h3 class="text-xl font-bold text-charcoaldk mb-2">Có câu hỏi về bảo mật?</h3>
                <p class="text-charcoal/70 mb-4">Liên hệ bộ phận bảo mật thông tin DecorNest</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="tel:19001234" class="inline-flex items-center justify-center gap-2 bg-wood hover:bg-wooddk text-white px-6 py-3 rounded-xl font-semibold transition">
                        📞 Hotline: 1900 1234
                    </a>
                    <a href="mailto:privacy@decornest.vn" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-charcoal/5 text-charcoaldk px-6 py-3 rounded-xl font-semibold border border-charcoal/20 transition">
                        ✉️ privacy@decornest.vn
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once ROOT_PATH . '/app/views/frontend/layouts/footer.php'; ?>
