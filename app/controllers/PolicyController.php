<?php

namespace App\Controllers;

use App\Core\Controller;

class PolicyController extends Controller
{
    protected string $viewPath = 'frontend';

    public function returnPolicy(): void
    {
        $this->view('pages/policy/return-policy');
    }

    public function warrantyPolicy(): void
    {
        $this->view('pages/policy/warranty-policy');
    }

    public function shippingPolicy(): void
    {
        $this->view('pages/policy/shipping-policy');
    }

    public function privacyPolicy(): void
    {
        $this->view('pages/policy/privacy-policy');
    }

    public function termsOfService(): void
    {
        $this->view('pages/policy/terms-of-service');
    }
}
