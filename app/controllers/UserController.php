<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\BusinessProfileModel;
use App\Models\OrderModel;
use App\Models\QuoteModel;
use App\Models\CartModel;

class UserController extends Controller
{
    protected string $viewPath = 'frontend';

    private UserModel $userModel;
    private BusinessProfileModel $bpModel;
    private OrderModel $orderModel;
    private QuoteModel $quoteModel;
    private CartModel $cartModel;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->bpModel    = new BusinessProfileModel();
        $this->orderModel = new OrderModel();
        $this->quoteModel = new QuoteModel();
        $this->cartModel  = new CartModel();
    }

    public function loginForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect($this->baseUrl('user/profile'));
        }
        $this->view('pages/login');
    }

    public function login(): void
    {
        if (!$this->isPost()) {
            $this->redirect($this->baseUrl('user/login'));
        }

        $credential = $this->post('credential'); // email or phone
        $password   = $_POST['password'] ?? '';
        $remember   = isset($_POST['remember']);

        $user = $this->userModel->findByCredential($credential);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Email/SĐT hoặc mật khẩu không đúng.']);
            }
            $_SESSION['login_error'] = 'Email/SĐT hoặc mật khẩu không đúng.';
            $this->redirect($this->baseUrl('user/login'));
        }

        if (!$user['is_active']) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Tài khoản của bạn đã bị khóa.']);
            }
            $_SESSION['login_error'] = 'Tài khoản của bạn đã bị khóa.';
            $this->redirect($this->baseUrl('user/login'));
        }

        // Merge guest cart
        if (!empty($_SESSION['cart_session_id'])) {
            $this->cartModel->mergeGuestCart($_SESSION['cart_session_id'], $user['id']);
            unset($_SESSION['cart_session_id']);
        }

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_type'] = $user['account_type'];

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->update($user['id'], ['remember_token' => $token]);
            setcookie('remember_token', $token, time() + 86400 * 30, '/', '', false, true);
        }

        if ($this->isAjax()) {
            $this->json([
                'success'  => true,
                'message'  => 'Đăng nhập thành công.',
                'redirect' => $this->baseUrl($user['role'] === 'admin' ? 'admin' : 'user/profile'),
            ]);
        }

        $redirect = $_SESSION['login_redirect'] ?? $this->baseUrl('user/profile');
        unset($_SESSION['login_redirect']);
        $this->redirect($redirect);
    }

    public function registerForm(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect($this->baseUrl('user/profile'));
        }
        $this->view('pages/register');
    }

    public function register(): void
    {
        if (!$this->isPost()) {
            $this->redirect($this->baseUrl('user/register'));
        }

        $name        = $this->post('name');
        $email       = $this->post('email');
        $phone       = $this->post('phone');
        $password    = $_POST['password'] ?? '';
        $accountType = $this->post('account_type', 'individual');
        $errors      = [];

        if (empty($name))               $errors[] = 'Vui lòng nhập họ tên.';
        if (empty($email))              $errors[] = 'Vui lòng nhập email.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
        if (strlen($password) < 8)      $errors[] = 'Mật khẩu phải có ít nhất 8 ký tự.';
        if ($this->userModel->findByEmail($email)) $errors[] = 'Email này đã được đăng ký.';

        if ($errors) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors]);
            }
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_input']  = $_POST;
            $this->redirect($this->baseUrl('user/register'));
        }

        $userId = $this->userModel->createUser([
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'password'     => $password,
            'account_type' => $accountType,
        ]);

        // Save business profile
        if ($accountType === 'business') {
            $this->bpModel->create([
                'user_id'             => $userId,
                'company_name'        => $this->post('company_name', ''),
                'tax_code'            => $this->post('tax_code', ''),
                'representative_name' => $this->post('representative_name', ''),
                'invoice_address'     => $this->post('invoice_address', ''),
                'invoice_email'       => $this->post('invoice_email', ''),
            ]);
        }

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Đăng ký thành công! Vui lòng đăng nhập.']);
        }

        $this->setFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        $this->redirect($this->baseUrl('user/login'));
    }

    public function logout(): void
    {
        setcookie('remember_token', '', time() - 3600, '/');
        session_destroy();
        $this->redirect($this->baseUrl());
    }

    public function profile(): void
    {
        $this->requireAuth();
        $user            = $this->userModel->find((int) $_SESSION['user_id']);
        $businessProfile = null;
        if ($user['account_type'] === 'business') {
            $businessProfile = $this->bpModel->findByUserId($user['id']);
        }
        $recentOrders = $this->orderModel->getByUser($user['id'], 1, 5);
        $this->view('pages/profile', compact('user', 'businessProfile', 'recentOrders'));
    }

    public function updateProfile(): void
    {
        $this->requireAuth();
        $userId = (int) $_SESSION['user_id'];

        $this->userModel->update($userId, [
            'name'  => $this->post('name'),
            'phone' => $this->post('phone'),
        ]);

        $user = $this->userModel->find($userId);
        if ($user['account_type'] === 'business') {
            $this->bpModel->upsert($userId, [
                'company_name'        => $this->post('company_name', ''),
                'tax_code'            => $this->post('tax_code', ''),
                'representative_name' => $this->post('representative_name', ''),
                'invoice_address'     => $this->post('invoice_address', ''),
                'invoice_email'       => $this->post('invoice_email', ''),
            ]);
        }

        $_SESSION['user_name'] = $this->post('name');
        $this->setFlash('success', 'Cập nhật thông tin thành công.');
        $this->redirect($this->baseUrl('user/profile'));
    }

    public function orders(): void
    {
        $this->requireAuth();
        $page   = max(1, (int) $this->get('page', 1));
        $result = $this->orderModel->getByUser((int) $_SESSION['user_id'], $page, 10);
        $this->view('pages/account-orders', $result);
    }

    public function orderDetail(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->getFullOrder((int) $id);
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            $this->view('pages/forbidden');
            return;
        }
        $this->view('pages/order-detail', compact('order'));
    }

    public function quotes(): void
    {
        $this->requireAuth();
        $quotes = $this->quoteModel->getByUser((int) $_SESSION['user_id']);
        $this->view('pages/my-quotes', compact('quotes'));
    }

    public function forgotForm(): void
    {
        $this->view('pages/forgot-password');
    }

    public function forgot(): void
    {
        // Simplified: in production, send reset email
        $email = $this->post('email');
        $user  = $this->userModel->findByEmail($email);
        if ($user) {
            // TODO: Generate token, send email
        }
        $this->setFlash('success', 'Nếu email tồn tại, chúng tôi đã gửi hướng dẫn đặt lại mật khẩu.');
        $this->redirect($this->baseUrl('user/forgot-password'));
    }
}
