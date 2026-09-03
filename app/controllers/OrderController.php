<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\OrderModel;

class OrderController extends Controller
{
    protected string $viewPath = 'frontend';

    private OrderModel $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $order = $this->orderModel->getFullOrder((int) $id);

        if (!$order) {
            http_response_code(404);
            $this->view('pages/404');
            return;
        }

        // Only the order owner or admin can view
        if ($order['user_id'] && $order['user_id'] != ($_SESSION['user_id'] ?? 0)
            && ($_SESSION['user_role'] ?? '') !== 'admin') {
            $this->redirect($this->baseUrl());
        }

        $this->view('pages/order-detail', compact('order'));
    }
}
