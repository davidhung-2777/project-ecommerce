<?php

namespace App\Core;

use App\Models\UserModel;

/**
 * Authentication & Authorization Helper
 * Quản lý đăng nhập, phân quyền admin/user
 */
class Auth
{
    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    public static function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }
    
    /**
     * Kiểm tra user có role admin không
     */
    public static function isAdmin(): bool
    {
        return self::check() && ($_SESSION['user_role'] ?? '') === 'admin';
    }
    
    /**
     * Kiểm tra user có role customer không
     */
    public static function isCustomer(): bool
    {
        return self::check() && ($_SESSION['user_role'] ?? '') === 'customer';
    }
    
    /**
     * Lấy user ID hiện tại
     */
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Lấy user role hiện tại
     */
    public static function role(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * Lấy user name hiện tại
     */
    public static function name(): ?string
    {
        return $_SESSION['user_name'] ?? null;
    }
    
    /**
     * Lấy user email hiện tại
     */
    public static function email(): ?string
    {
        return $_SESSION['user_email'] ?? null;
    }
    
    /**
     * Đăng nhập user
     */
    public static function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_account_type'] = $user['account_type'] ?? 'individual';
        $_SESSION['logged_in_at'] = time();
        
        // Regenerate session ID để bảo mật
        session_regenerate_id(true);
    }
    
    /**
     * Đăng xuất user
     */
    public static function logout(): void
    {
        // Xóa session variables liên quan đến user
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_account_type']);
        unset($_SESSION['logged_in_at']);
        
        // Regenerate session ID
        session_regenerate_id(true);
    }
    
    /**
     * Kiểm tra quyền truy cập admin panel
     * Redirect về home nếu không phải admin
     */
    public static function requireAdmin(): void
    {
        if (!self::check()) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Vui lòng đăng nhập để tiếp tục.'
            ];
            header('Location: ' . ($_ENV['APP_URL'] ?? '') . '/user/login');
            exit;
        }
        
        if (!self::isAdmin()) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Bạn không có quyền truy cập khu vực này.'
            ];
            header('Location: ' . ($_ENV['APP_URL'] ?? '') . '/');
            exit;
        }
    }
    
    /**
     * Kiểm tra đã đăng nhập
     * Redirect về login nếu chưa đăng nhập
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Vui lòng đăng nhập để tiếp tục.'
            ];
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            header('Location: ' . ($_ENV['APP_URL'] ?? '') . '/user/login');
            exit;
        }
    }
    
    /**
     * Kiểm tra quyền sở hữu resource
     * VD: User chỉ được xem đơn hàng của chính mình
     */
    public static function canAccess(string $resource, int $resourceUserId): bool
    {
        // Admin có thể truy cập tất cả
        if (self::isAdmin()) {
            return true;
        }
        
        // User chỉ được truy cập resource của chính mình
        return self::id() === $resourceUserId;
    }
    
    /**
     * Kiểm tra user có permission cụ thể không
     * 
     * @param string $permission VD: 'products.create', 'orders.update', 'users.delete'
     */
    public static function can(string $permission): bool
    {
        // Admin có tất cả quyền
        if (self::isAdmin()) {
            return true;
        }
        
        // Customer permissions
        $customerPermissions = [
            'orders.view_own',      // Xem đơn hàng của mình
            'profile.view',         // Xem profile
            'profile.update',       // Sửa profile
            'cart.manage',          // Quản lý giỏ hàng
            'quotes.create',        // Tạo yêu cầu báo giá
        ];
        
        if (self::isCustomer()) {
            return in_array($permission, $customerPermissions);
        }
        
        return false;
    }
    
    /**
     * Lấy full user data từ database
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        
        static $user = null;
        
        if ($user === null) {
            $userModel = new UserModel();
            $user = $userModel->find(self::id());
        }
        
        return $user;
    }
}
