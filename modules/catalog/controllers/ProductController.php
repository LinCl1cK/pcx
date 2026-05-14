<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../../wishlist/models/WishlistModel.php';
require_once __DIR__ . '/../../cart/models/CartModel.php';
require_once __DIR__ . '/../../promotion/models/PromotionModel.php';

class ProductController extends BaseController {
    private ProductModel $productModel;
    private WishlistModel $wishlistModel;
    private CartModel $cartModel;
    private PromotionModel $promotionModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->productModel = new ProductModel($pdo);
        $this->wishlistModel = new WishlistModel($pdo);
        $this->cartModel = new CartModel($pdo);
        $this->promotionModel = new PromotionModel($pdo);
    }

    public function list(): void {
        $search = trim((string) ($_GET['q'] ?? ''));
        $category = trim((string) ($_GET['category'] ?? ''));

        $products = $this->productModel->getAllProducts($search, $category);
        $state = $this->getUserProductState();

        View::render(__DIR__ . '/../views/list.php', [
            'products' => $products,
            'search' => $search,
            'category' => $category,
            'categories' => $this->productModel->getAllCategories(),
            'wishlistIds' => $state['wishlistIds'],
            'cartQuantities' => $state['cartQuantities'],
            'flash' => $this->pullFlash(),
        ]);
    }

    public function home(): void {
        $products = $this->productModel->getFeaturedProducts();
        $newArrivals = $this->productModel->getNewArrivals();
        $categories = $this->productModel->getAllCategories();
        $promotions = $this->promotionModel->getActivePromotions();

        $state = $this->getUserProductState();

        View::render(__DIR__ . '/../views/home.php', [
            'products' => $products,
            'newArrivals' => $newArrivals,
            'categories' => $categories,
            'promotions' => $promotions,
            'wishlistIds' => $state['wishlistIds'],
            'cartQuantities' => $state['cartQuantities'],
            'flash' => $this->pullFlash(),
        ]);
    }

    public function detail(): void {
        $id = $_GET['id'] ?? null;
        if (!$id) { http_response_code(400); echo "Missing product id"; return; }

        $product = $this->productModel->getProductById((string) $id);
        $state = $this->getUserProductState();

        View::render(__DIR__ . '/../views/detail.php', [
            'product' => $product,
            'categories' => $this->productModel->getAllCategories(),
            'wishlistIds' => $state['wishlistIds'],
            'cartQuantities' => $state['cartQuantities'],
            'flash' => $this->pullFlash(),
        ]);
    }

    public function featuredJson(): void {
        $limit = (int) ($_GET['limit'] ?? 8);
        $limit = $limit > 0 ? $limit : 8;
        $this->json($this->productModel->getFeaturedProducts($limit));
    }

    public function newArrivalsJson(): void {
        $limit = (int) ($_GET['limit'] ?? 8);
        $limit = $limit > 0 ? $limit : 8;
        $this->json($this->productModel->getNewArrivals($limit));
    }

    public function categoriesJson(): void {
        $this->json($this->productModel->getAllCategories());
    }

    private function getUserProductState(): array {
        if (empty($_SESSION['user']['id'])) {
            return ['wishlistIds' => [], 'cartQuantities' => []];
        }

        $customerId = (string) $_SESSION['user']['id'];
        return [
            'wishlistIds' => $this->wishlistModel->getWishlistProductIds($customerId),
            'cartQuantities' => $this->cartModel->getCartProductQuantities($customerId),
        ];
    }
}
