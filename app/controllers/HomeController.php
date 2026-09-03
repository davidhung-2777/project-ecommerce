<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class HomeController extends Controller
{
    protected string $viewPath = 'frontend';

    private ProductModel $productModel;
    private CategoryModel $categoryModel;

    public function __construct()
    {
        $this->productModel  = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index(): void
    {
        $featured    = $this->productModel->getFeatured(8);
        $newArrivals = $this->productModel->getNewArrivals(8);
        $categories  = $this->categoryModel->getRootCategories();
        $menuTree    = $this->categoryModel->getMenuTree();

        $this->view('pages/home', compact('featured', 'newArrivals', 'categories', 'menuTree'));
    }
}
