<?php

namespace App\Core;

abstract class Controller
{
    protected string $viewPath = '';

    /**
     * Render a view file with optional layout.
     */
    protected function view(string $view, array $viewData = [], string $layout = 'main'): void
    {
        // Extract data to local variables for views
        extract($viewData, EXTR_OVERWRITE);

        $viewFile = ROOT_PATH . '/app/views/' . $this->viewPath . '/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }

        // Buffer the view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // If no layout, output content directly
        if (!$layout) {
            echo $content;
            return;
        }

        $layoutFile = ROOT_PATH . '/app/views/' . $this->viewPath . '/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            // Fallback: just output content
            echo $content;
            return;
        }

        require $layoutFile;
    }

    /**
     * Return JSON response.
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Redirect to a URL.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Get base URL.
     */
    protected function baseUrl(string $path = ''): string
    {
        return ($_ENV['APP_URL'] ?? '') . '/' . ltrim($path, '/');
    }

    /**
     * Set flash message.
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Get and clear flash message.
     */
    protected function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Require authentication - redirect if not logged in.
     */
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->setFlash('error', 'Vui lòng đăng nhập để tiếp tục.');
            $this->redirect($this->baseUrl('user/login'));
        }
    }

    /**
     * Require admin role.
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            $this->redirect($this->baseUrl());
        }
    }

    /**
     * Check if request is POST.
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request is AJAX.
     */
    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Sanitize input.
     */
    protected function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get POST data with optional sanitization.
     */
    protected function post(string $key, mixed $default = null, bool $sanitize = true): mixed
    {
        $value = $_POST[$key] ?? $default;
        if ($sanitize && is_string($value)) {
            return $this->sanitize($value);
        }
        return $value;
    }

    /**
     * Get GET data.
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }
}
