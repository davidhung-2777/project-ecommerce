/**
 * Checkout Polling Script
 * Tự động poll trạng thái thanh toán mỗi 2.5 giây
 * Không cần F5 để cập nhật trạng thái
 */

class CheckoutPolling {
    constructor(orderCode) {
        this.orderCode = orderCode;
        this.pollInterval = null;
        this.pollDelay = 2500; // 2.5 giây
        this.maxRetries = 240; // 10 phút (240 * 2.5s)
        this.retryCount = 0;
        this.isPolling = false;
        
        this.init();
    }
    
    init() {
        console.log(`[Polling] Initialized for order: ${this.orderCode}`);
        this.startPolling();
        this.setupCountdown();
    }
    
    /**
     * Bắt đầu polling
     */
    startPolling() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        console.log('[Polling] Started');
        
        // Poll ngay lập tức lần đầu
        this.checkOrderStatus();
        
        // Sau đó poll mỗi 2.5 giây
        this.pollInterval = setInterval(() => {
            this.checkOrderStatus();
        }, this.pollDelay);
    }
    
    /**
     * Dừng polling
     */
    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
            this.isPolling = false;
            console.log('[Polling] Stopped');
        }
    }
    
    /**
     * Kiểm tra trạng thái order
     */
    async checkOrderStatus() {
        this.retryCount++;
        
        // Dừng nếu quá số lần retry
        if (this.retryCount > this.maxRetries) {
            console.log('[Polling] Max retries reached');
            this.stopPolling();
            this.showError('Hết thời gian chờ. Vui lòng kiểm tra lại đơn hàng của bạn.');
            return;
        }
        
        try {
            const response = await fetch(`/project-ecommerce/public/api/order-status.php?order_code=${this.orderCode}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            console.log('[Polling] Status:', data.status);
            
            this.handleStatusChange(data);
            
        } catch (error) {
            console.error('[Polling] Error:', error);
            // Tiếp tục poll dù có lỗi (có thể lỗi mạng tạm thời)
        }
    }
    
    /**
     * Xử lý thay đổi trạng thái
     */
    handleStatusChange(data) {
        const { status, time_remaining_seconds } = data;
        
        switch (status) {
            case 'paid':
                this.onPaymentSuccess(data);
                break;
                
            case 'expired':
                this.onPaymentExpired(data);
                break;
                
            case 'needs_review':
                this.onPaymentNeedsReview(data);
                break;
                
            case 'pending':
                this.onPaymentPending(data);
                break;
        }
    }
    
    /**
     * Thanh toán thành công
     */
    onPaymentSuccess(data) {
        console.log('[Polling] Payment SUCCESS!');
        this.stopPolling();
        
        // Ẩn QR code
        const qrSection = document.getElementById('qr-section');
        if (qrSection) qrSection.style.display = 'none';
        
        // Hiện thông báo thành công
        const successSection = document.getElementById('success-section');
        if (successSection) {
            successSection.style.display = 'block';
            successSection.classList.add('animate-fade-in');
        }
        
        // Update UI
        document.body.classList.add('payment-success');
        
        // Phát âm thanh thông báo (nếu có)
        this.playSuccessSound();
        
        // Redirect sau 3 giây (optional)
        setTimeout(() => {
            // window.location.href = `/project-ecommerce/public/user/orders/${data.order_code}`;
        }, 3000);
    }
    
    /**
     * Đơn hàng hết hạn
     */
    onPaymentExpired(data) {
        console.log('[Polling] Payment EXPIRED');
        this.stopPolling();
        
        // Ẩn QR
        const qrSection = document.getElementById('qr-section');
        if (qrSection) qrSection.style.display = 'none';
        
        // Hiện thông báo hết hạn
        const expiredSection = document.getElementById('expired-section');
        if (expiredSection) {
            expiredSection.style.display = 'block';
        }
        
        // Hiện nút "Tạo lại QR"
        const retryButton = document.getElementById('retry-payment-btn');
        if (retryButton) {
            retryButton.style.display = 'inline-block';
        }
    }
    
    /**
     * Cần review (số tiền không khớp)
     */
    onPaymentNeedsReview(data) {
        console.log('[Polling] Payment NEEDS REVIEW');
        this.stopPolling();
        
        const reviewSection = document.getElementById('review-section');
        if (reviewSection) {
            reviewSection.style.display = 'block';
            reviewSection.innerHTML = `
                <div class="alert alert-warning">
                    <h4>⚠️ Đơn hàng đang được kiểm tra</h4>
                    <p>Chúng tôi đã nhận được thanh toán của bạn nhưng cần xác minh thêm.</p>
                    <p>Đội ngũ hỗ trợ sẽ liên hệ với bạn trong vòng 24h.</p>
                </div>
            `;
        }
    }
    
    /**
     * Đang chờ thanh toán
     */
    onPaymentPending(data) {
        // Update countdown nếu có
        if (data.time_remaining_seconds !== undefined) {
            this.updateCountdown(data.time_remaining_seconds);
        }
    }
    
    /**
     * Setup countdown timer
     */
    setupCountdown() {
        this.countdownElement = document.getElementById('countdown-timer');
    }
    
    /**
     * Update countdown display
     */
    updateCountdown(seconds) {
        if (!this.countdownElement) return;
        
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        
        this.countdownElement.textContent = 
            `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        
        // Thêm class warning khi còn < 3 phút
        if (seconds < 180) {
            this.countdownElement.classList.add('text-warning');
        }
        
        // Thêm class danger khi còn < 1 phút
        if (seconds < 60) {
            this.countdownElement.classList.add('text-danger');
        }
    }
    
    /**
     * Hiện lỗi
     */
    showError(message) {
        const errorDiv = document.getElementById('polling-error');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }
    
    /**
     * Phát âm thanh thông báo thành công
     */
    playSuccessSound() {
        try {
            const audio = new Audio('/project-ecommerce/public/assets/sounds/success.mp3');
            audio.play().catch(e => console.log('Audio play failed:', e));
        } catch (e) {
            // Ignore audio errors
        }
    }
}

// Expose globally
window.CheckoutPolling = CheckoutPolling;
