<?php

namespace App\Core;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $r = $this->router;

        // ── Frontend ──────────────────────────────────────────
        $r->get('/',                          'HomeController',     'index');
        $r->get('/home',                      'HomeController',     'index');

        // Products
        $r->get('/products',                  'ProductController',  'index');
        $r->get('/products/:slug',            'ProductController',  'show');
        $r->get('/category/:slug',            'ProductController',  'category');
        $r->get('/search',                    'ProductController',  'search');
        $r->post('/products/filter',          'ProductController',  'filter');

        // Cart
        $r->get('/cart',                      'CartController',     'index');
        $r->post('/cart/add',                 'CartController',     'add');
        $r->post('/cart/update',              'CartController',     'update');
        $r->post('/cart/remove',              'CartController',     'remove');
        $r->get('/cart/count',                'CartController',     'count');
        $r->get('/cart/mini',                 'CartController',     'mini');

        // Checkout
        $r->get('/checkout',                  'CheckoutController', 'index');
        $r->post('/checkout/process',         'CheckoutController', 'process');
        $r->get('/checkout/success/:orderNum','CheckoutController', 'success');

        // Payment
        $r->post('/payment/initiate',         'PaymentController',  'initiate');
        $r->get('/payment/momo-return',       'PaymentController',  'momoReturn');
        $r->post('/payment/momo-ipn',         'PaymentController',  'momoIpn');
        $r->get('/payment/vnpay-return',      'PaymentController',  'vnpayReturn');
        $r->post('/payment/vnpay-ipn',        'PaymentController',  'vnpayIpn');
        $r->get('/payment/bank-info/:orderNum','PaymentController', 'bankInfo');
        
        // Webhook (Public endpoint - NO AUTH)
        $r->post('/api/webhook/payment',      'WebhookController',  'handlePayment');

        // Quote (B2B)
        $r->get('/quote',                     'QuoteController',    'index');
        $r->post('/quote/submit',             'QuoteController',    'submit');
        $r->get('/quote/:id',                 'QuoteController',    'show');
        $r->post('/quote/:id/accept',         'QuoteController',    'accept');

        // User
        $r->get('/user/login',                'UserController',     'loginForm');
        $r->post('/user/login',               'UserController',     'login');
        $r->get('/user/register',             'UserController',     'registerForm');
        $r->post('/user/register',            'UserController',     'register');
        $r->get('/user/logout',               'UserController',     'logout');
        $r->get('/user/profile',              'UserController',     'profile');
        $r->post('/user/profile/update',      'UserController',     'updateProfile');
        $r->get('/user/orders',               'UserController',     'orders');
        $r->get('/user/orders/:id',           'UserController',     'orderDetail');
        $r->get('/user/quotes',               'UserController',     'quotes');
        $r->get('/user/forgot-password',      'UserController',     'forgotForm');
        $r->post('/user/forgot-password',     'UserController',     'forgot');
        
        // Customer Dashboard (Riêng cho user đã đăng nhập)
        $r->get('/dashboard',                 'DashboardController', 'index');
        $r->get('/dashboard/orders',          'DashboardController', 'orders');
        $r->get('/dashboard/orders/:id',      'DashboardController', 'orderDetail');
        $r->get('/dashboard/profile',         'DashboardController', 'profile');
        $r->post('/dashboard/profile/update', 'DashboardController', 'updateProfile');
        $r->get('/dashboard/quotes',          'DashboardController', 'quotes');

        // Orders
        $r->get('/orders/:id',                'OrderController',    'show');

        // ── Admin ─────────────────────────────────────────────
        $r->get('/admin',                     'AdminController',    'dashboard');
        $r->get('/admin/dashboard',           'AdminController',    'dashboard');

        $r->get('/admin/products',            'AdminController',    'products');
        $r->get('/admin/products/create',     'AdminController',    'createProduct');
        $r->post('/admin/products/create',    'AdminController',    'storeProduct');
        $r->get('/admin/products/:id/edit',   'AdminController',    'editProduct');
        $r->post('/admin/products/:id/edit',  'AdminController',    'updateProduct');
        $r->post('/admin/products/:id/delete','AdminController',    'deleteProduct');

        $r->get('/admin/categories',          'AdminController',    'categories');
        $r->post('/admin/categories/create',  'AdminController',    'storeCategory');
        $r->post('/admin/categories/:id/edit','AdminController',    'updateCategory');
        $r->post('/admin/categories/:id/delete','AdminController',  'deleteCategory');

        $r->get('/admin/orders',              'AdminController',    'orders');
        $r->get('/admin/orders/:id',          'AdminController',    'orderDetail');
        $r->post('/admin/orders/:id/status',  'AdminController',    'updateOrderStatus');

        $r->get('/admin/quotes',              'AdminController',    'quotes');
        $r->get('/admin/quotes/:id',          'AdminController',    'quoteDetail');
        $r->post('/admin/quotes/:id/respond', 'AdminController',    'respondQuote');
        $r->post('/admin/quotes/:id/convert', 'AdminController',    'convertQuote');
        $r->post('/admin/quotes/:id/reject',  'AdminController',    'rejectQuote');

        $r->get('/admin/users',               'AdminController',    'users');
        $r->get('/admin/users/:id',           'AdminController',    'userDetail');
        $r->post('/admin/users/:id/block',    'AdminController',    'blockUser');
        $r->post('/admin/users/:id/unblock',  'AdminController',    'unblockUser');

        $r->get('/admin/payments',            'AdminController',    'payments');
        $r->post('/admin/payments/:id/confirm','AdminController',   'confirmPayment');

        $r->get('/admin/reports',             'AdminController',    'reports');
    }

    public function run(): void
    {
        $url    = $_GET['url'] ?? '/';
        $url    = '/' . ltrim($url, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        // Allow method override via hidden _method field
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $this->router->dispatch($url, $method);
    }
}
