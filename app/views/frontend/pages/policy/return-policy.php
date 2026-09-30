<?php
$pageTitle = 'Chính Sách Đổi Trả - DecorNest';
require_once ROOT_PATH . '/app/views/frontend/layouts/header.php';
?>

<div class="min-h-screen bg-cream pt-24 pb-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-serif font-bold text-charcoaldk mb-4">
                Chính Sách Đổi Trả
            </h1>
            <p class="text-lg text-charcoal/70">
                Đổi trả thảnh thơi trong vòng 30 ngày
            </p>
        </div>

        <!-- Policy Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-charcoal/10 p-8 md:p-12 space-y-8">
            
            <!-- Commitment -->
            <div class="bg-wood/5 border border-wood/20 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="text-3xl">🛡️</div>
                    <div>
                        <h3 class="font-bold text-lg text-charcoaldk mb-2">Cam kết của DecorNest</h3>
                        <p class="text-charcoal/70 leading-relaxed">
                            Chúng tôi hiểu rằng việc chọn sản phẩm decor phòng ngủ là quyết định quan trọng. 
                            DecorNest cam kết mang đến trải nghiệm mua sắm an tâm với chính sách đổi trả linh hoạt.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 1 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">1.</span> Điều Kiện Đổi Trả
                </h2>
                <div class="space-y-4 text-charcoal/80">
                    <p><strong>Sản phẩm được đổi trả khi:</strong></p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Trong vòng <strong>30 ngày</strong> kể từ ngày nhận hàng</li>
                        <li>Sản phẩm còn nguyên tem, mác, bao bì đầy đủ</li>
                        <li>Chưa qua sử dụng, không có dấu hiệu bị bẩn, trầy xước</li>
                        <li>Có đầy đủ phụ kiện, hướng dẫn sử dụng kèm theo</li>
                        <li>Có hóa đơn mua hàng hoặc mã đơn hàng hợp lệ</li>
                    </ul>
                </div>
            </section>

            <!-- Section 2 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">2.</span> Trường Hợp Được Đổi Trả
                </h2>
                <div class="space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="font-semibold text-green-900 mb-2">✅ Lỗi từ nhà sản xuất:</p>
                        <ul class="list-disc pl-6 space-y-1 text-green-800 text-sm">
                            <li>Sản phẩm bị lỗi kỹ thuật, hư hỏng từ nhà máy</li>
                            <li>Giao sai màu, sai kích thước so với đơn hàng</li>
                            <li>Sản phẩm bị hư hại trong quá trình vận chuyển</li>
                        </ul>
                        <p class="mt-2 text-sm text-green-700">
                            → DecorNest chịu 100% chi phí vận chuyển đổi trả
                        </p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="font-semibold text-blue-900 mb-2">🔄 Đổi ý sau khi mua:</p>
                        <ul class="list-disc pl-6 space-y-1 text-blue-800 text-sm">
                            <li>Không hợp phong cách phòng ngủ hiện tại</li>
                            <li>Kích thước không phù hợp với không gian</li>
                            <li>Muốn đổi sang màu sắc/mẫu mã khác</li>
                        </ul>
                        <p class="mt-2 text-sm text-blue-700">
                            → Khách hàng chịu phí vận chuyển (nếu có)
                        </p>
                    </div>
                </div>
            </section>

            <!-- Section 3 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">3.</span> Quy Trình Đổi Trả
                </h2>
                <div class="space-y-3">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">1</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Liên hệ bộ phận CSKH</h4>
                            <p class="text-sm text-charcoal/70">Hotline: <strong>1900 1234</strong> hoặc email: <strong>support@decornest.vn</strong></p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">2</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Cung cấp thông tin đơn hàng</h4>
                            <p class="text-sm text-charcoal/70">Mã đơn hàng, lý do đổi trả, hình ảnh sản phẩm (nếu lỗi)</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">3</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">DecorNest xác nhận yêu cầu</h4>
                            <p class="text-sm text-charcoal/70">Thời gian xử lý: <strong>24-48 giờ làm việc</strong></p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">4</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Gửi hàng hoàn trả</h4>
                            <p class="text-sm text-charcoal/70">Đóng gói cẩn thận, gửi về địa chỉ kho DecorNest</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-wood text-white rounded-full flex items-center justify-center font-bold">5</div>
                        <div>
                            <h4 class="font-semibold text-charcoaldk">Nhận hoàn tiền hoặc sản phẩm mới</h4>
                            <p class="text-sm text-charcoal/70">Sau 3-5 ngày làm việc kể từ khi nhận hàng hoàn trả</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 4 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">4.</span> Hình Thức Hoàn Tiền
                </h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="border border-charcoal/10 rounded-lg p-4">
                        <h4 class="font-semibold text-charcoaldk mb-2">💳 Chuyển khoản ngân hàng</h4>
                        <p class="text-sm text-charcoal/70">Hoàn tiền 100% vào tài khoản đã thanh toán</p>
                    </div>
                    <div class="border border-charcoal/10 rounded-lg p-4">
                        <h4 class="font-semibold text-charcoaldk mb-2">🔄 Đổi sản phẩm khác</h4>
                        <p class="text-sm text-charcoal/70">Đổi sang sản phẩm cùng giá trị hoặc bù trừ chênh lệch</p>
                    </div>
                </div>
            </section>

            <!-- Section 5 -->
            <section>
                <h2 class="text-2xl font-bold text-charcoaldk mb-4 flex items-center gap-3">
                    <span class="text-wood">5.</span> Trường Hợp KHÔNG Được Đổi Trả
                </h2>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <ul class="list-disc pl-6 space-y-2 text-red-800 text-sm">
                        <li>Sản phẩm đã qua sử dụng, có dấu hiệu lắp đặt</li>
                        <li>Sản phẩm bị hư hỏng do người dùng (rơi vỡ, trầy xước, ố vàng)</li>
                        <li>Mất tem mác, bao bì, phụ kiện kèm theo</li>
                        <li>Quá thời hạn 30 ngày đổi trả</li>
                        <li>Sản phẩm làm theo yêu cầu riêng (custom order)</li>
                        <li>Sản phẩm giảm giá đặc biệt, thanh lý</li>
                    </ul>
                </div>
            </section>

            <!-- Contact CTA -->
            <div class="bg-wood/10 border border-wood/30 rounded-xl p-6 text-center">
                <h3 class="text-xl font-bold text-charcoaldk mb-2">Cần hỗ trợ thêm?</h3>
                <p class="text-charcoal/70 mb-4">Đội ngũ CSKH DecorNest luôn sẵn sàng tư vấn</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="tel:19001234" class="inline-flex items-center justify-center gap-2 bg-wood hover:bg-wooddk text-white px-6 py-3 rounded-xl font-semibold transition">
                        📞 Hotline: 1900 1234
                    </a>
                    <a href="mailto:support@decornest.vn" class="inline-flex items-center justify-center gap-2 bg-white hover:bg-charcoal/5 text-charcoaldk px-6 py-3 rounded-xl font-semibold border border-charcoal/20 transition">
                        ✉️ support@decornest.vn
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once ROOT_PATH . '/app/views/frontend/layouts/footer.php'; ?>
