<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\OrderModel;
use App\Models\QuoteModel;

/**
 * Dashboard Controller
 * Dashboard riêng cho Customer (không phải Admin)
 */
class DashboardController extends Controller
{
    protected string $viewPath = 'frontend';
    
    private OrderModel $orderModel;
    private QuoteModel $quoteModel;
    
    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->quoteModel = new QuoteModel();
    }
    
    /**
     * Customer Dashboard - Trang chủ sau khi đăng nhập
     */
    public function index(): void
    {
        Auth::requireAuth();
        
        // Nếu là admin, redirect về admin dashboard
        if (Auth::isAdmin()) {
            $this->redirect($this->baseUrl('admin'));
        }
        
        $userId = Auth::id();
        
        // Thống kê của customer
        $stats = [
            'total_orders' => $this->orderModel->count("user_id = ?", [$userId]),
            'pending_orders' => $this->orderModel->count("user_id = ? AND status = 'pending'", [$userId]),
            'completed_orders' => $this->orderModel->count("user_id = ? AND status = 'delivered'", [$userId]),
            'total_spent' => $this->orderModel->getTotalSpent($userId),
        ];
        
        // Đơn hàng gần nhất
        $recentOrders = $this->orderModel->findAllWhere(
            'user_id = ? ORDER BY created_at DESC LIMIT 5',
            [$userId]
        );
        
        // Yêu cầu báo giá
        $recentQuotes = $this->quoteModel->findAllWhere(
            'user_id = ? ORDER BY created_at DESC LIMIT 3',
            [$userId]
        );
        
        $user = Auth::user();
        
        $this->view('pages/dashboard', compact('stats', 'recentOrders', 'recentQuotes', 'user'));
    }
    
    /**
     * Danh sách đơn hàng của user
     */
    public function orders(): void
    {
        Auth::requireAuth();
        
        $userId = Auth::id();
        $page = max(1, (int) $this->get('page', 1));
        $status = $this->get('status', '');
        
        $filters = ['user_id' => $userId];
        if ($status) {
            $filters['status'] = $status;
        }
        
        $result = $this->orderModel->getFiltered($filters, $page, 10);
        
        $this->view('pages/my-orders', $result + compact('status'));
    }
    
    /**
     * Chi tiết đơn hàng
     */
    public function orderDetail(string $id): void
    {
        Auth::requireAuth();
        
        $orderId = (int) $id;
        $order = $this->orderModel->getFullOrder($orderId);
        
        if (!$order) {
            $this->setFlash('error', 'Đơn hàng không tồn tại.');
            $this->redirect($this->baseUrl('dashboard/orders'));
        }
        
        // Kiểm tra quyền sở hữu
        if (!Auth::canAccess('order', $order['user_id'])) {
            $this->setFlash('error', 'Bạn không có quyền xem đơn hàng này.');
            $this->redirect($this->baseUrl('dashboard'));
        }
        
        $this->view('pages/order-detail', compact('order'));
    }
    
    /**
     * Profile / Thông tin tài khoản
     */
    public function profile(): void
    {
        Auth::requireAuth();
        
        $user = Auth::user();
        
        $this->view('pages/profile', compact('user'));
    }
    
    /**
     * Cập nhật profile
     */
    public function updateProfile(): void
    {
        Auth::requireAuth();
        
        if (!$this->isPost()) {
            $this->redirect($this->baseUrl('dashboard/profile'));
        }
        
        $userId = Auth::id();
        
        $data = [
            'name' => $this->post('name'),
            'phone' => $this->post('phone'),
        ];
        
        // Nếu có thay đổi password
        if ($this->post('new_password')) {
            $currentPassword = $this->post('current_password');
            $newPassword = $this->post('new_password');
            $confirmPassword = $this->post('confirm_password');
            
            // Verify current password
            $user = Auth::user();
            if (!password_verify($currentPassword, $user['password'])) {
                $this->setFlash('error', 'Mật khẩu hiện tại không đúng.');
                $this->redirect($this->baseUrl('dashboard/profile'));
            }
            
            if ($newPassword !== $confirmPassword) {
                $this->setFlash('error', 'Mật khẩu mới không khớp.');
                $this->redirect($this->baseUrl('dashboard/profile'));
            }
            
            if (strlen($newPassword) < 6) {
                $this->setFlash('error', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
                $this->redirect($this->baseUrl('dashboard/profile'));
            }
            
            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }
        
        // Update user
        $userModel = new \App\Models\UserModel();
        $userModel->update($userId, $data);
        
        // Update session
        $_SESSION['user_name'] = $data['name'];
        
        $this->setFlash('success', 'Cập nhật thông tin thành công.');
        $this->redirect($this->baseUrl('dashboard/profile'));
    }
    
    /**
     * Danh sách yêu cầu báo giá
     */
    public function quotes(): void
    {
        Auth::requireAuth();
        
        $userId = Auth::id();
        $page = max(1, (int) $this->get('page', 1));
        
        $quotes = $this->quoteModel->findAllWhere(
            'user_id = ? ORDER BY created_at DESC LIMIT 10 OFFSET ?',
            [$userId, ($page - 1) * 10]
        );
        
        $total = $this->quoteModel->count('user_id = ?', [$userId]);
        
        $this->view('pages/my-quotes', compact('quotes', 'total', 'page'));
    }
}
