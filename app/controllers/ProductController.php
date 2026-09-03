<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductPriceTierModel;

class ProductController extends Controller
{
    protected string $viewPath = 'frontend';

    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private ProductPriceTierModel $tierModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->tierModel     = new ProductPriceTierModel();
    }

    public function index(): void
    {
        $this->listProducts([]);
    }

    public function category(string $slug): void
    {
        $category = $this->categoryModel->findBySlug($slug);
        if (!$category) {
            $this->redirect($this->baseUrl('products'));
        }
        $this->listProducts(['category_id' => $category['id']], $category);
    }

    public function show(string $slug): void
    {
        $product = $this->productModel->findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            $this->view('pages/404');
            return;
        }

        $this->productModel->incrementViews($product['id']);

        $tiers    = $this->tierModel->getByProduct($product['id']);
        $related  = $this->productModel->getByCategory($product['category_id'], 4, $product['id']);
        $menuTree = $this->categoryModel->getMenuTree();
        $siblings = $this->categoryModel->getSiblings($product['category_id'] ?? 0);

        // Decode JSON fields
        $product['images_arr']       = json_decode($product['images'] ?? '[]', true) ?: [];
        $product['size_options_arr'] = json_decode($product['size_options'] ?? '[]', true) ?: [];
        $product['color_options_arr']= json_decode($product['color_options'] ?? '[]', true) ?: [];

        $this->view('pages/product-detail', compact('product', 'tiers', 'related', 'menuTree', 'siblings'));
    }

    public function search(): void
    {
        $query   = $this->get('q', '');
        $filters = ['search' => $query];
        $this->listProducts($filters);
    }

    /**
     * AJAX filter endpoint.
     */
    public function filter(): void
    {
        $filters = [
            'category_id' => $this->post('category_id'),
            'min_price'   => $this->post('min_price'),
            'max_price'   => $this->post('max_price'),
            'color'       => $this->post('color'),
            'origin'      => $this->post('origin'),
            'is_new'      => $this->post('is_new'),
            'on_sale'     => $this->post('on_sale'),
            'search'      => $this->post('search'),
        ];

        $page    = (int) ($this->post('page', 1, false) ?: 1);
        $perPage = (int) ($this->post('per_page', 16, false) ?: 16);
        $sort    = $this->post('sort', 'newest');

        $result  = $this->productModel->getFiltered($filters, $page, $perPage, $sort);

        if ($this->isAjax()) {
            // Render just the product grid partial
            ob_start();
            $products = $result['data'];
            require ROOT_PATH . '/app/views/frontend/partials/product-grid.php';
            $html = ob_get_clean();

            $this->json([
                'html'       => $html,
                'total'      => $result['total'],
                'last_page'  => $result['last_page'],
                'current_page' => $result['current_page'],
            ]);
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function listProducts(array $filters, ?array $activeCategory = null): void
    {
        $page    = max(1, (int) $this->get('page', 1));
        $perPage = in_array((int) $this->get('per_page', 16), [8, 16, 24]) ? (int) $this->get('per_page', 16) : 16;
        $sort    = $this->get('sort', 'newest');

        // Merge GET filters
        if ($this->get('min_price'))   $filters['min_price']   = $this->get('min_price');
        if ($this->get('max_price'))   $filters['max_price']   = $this->get('max_price');
        if ($this->get('color'))       $filters['color']       = $this->get('color');
        if ($this->get('origin'))      $filters['origin']      = $this->get('origin');
        if ($this->get('is_new'))      $filters['is_new']      = true;
        if ($this->get('on_sale'))     $filters['on_sale']     = true;

        $result     = $this->productModel->getFiltered($filters, $page, $perPage, $sort);
        $categories = $this->categoryModel->getRootCategories();
        $menuTree   = $this->categoryModel->getMenuTree();
        $siblings   = $activeCategory
            ? $this->categoryModel->getSiblings($activeCategory['parent_id'] ?? 0)
            : [];

        $this->view('pages/product-list', array_merge($result, compact(
            'categories', 'menuTree', 'activeCategory', 'filters', 'sort', 'perPage', 'siblings'
        )));
    }
}
