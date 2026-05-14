<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/WishlistModel.php';
require_once __DIR__ . '/../../catalog/models/ProductModel.php';

class WishlistController extends BaseController {
    private WishlistModel $model;
    private ProductModel $productModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new WishlistModel($pdo);
        $this->productModel = new ProductModel($pdo);
    }

    public function view(string $path = '', array $data = []): void {
        $this->requireCustomer('wishlist/wishlist/view');
        $customerId = (string) $_SESSION['user']['id'];

        $items = $this->model->getWishlistItems($customerId);
        View::render(__DIR__ . '/../views/wishlist.php', [
            'pageTitle' => 'Wishlist - PCX Store',
            'user' => $_SESSION['user'],
            'items' => $items,
            'categories' => $this->productModel->getAllCategories(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function add(): void {
        $this->requireCustomer('wishlist/wishlist/view');
        $productId = trim((string) ($_POST['product_id'] ?? ''));
        $redirect = trim((string) ($_POST['redirect'] ?? 'wishlist/wishlist/view'));

        if ($productId === '') {
            $this->setFlash('danger', 'Invalid product selection.');
            $this->redirectToRoute($redirect);
        }

        $ok = $this->model->addToWishlist((string) $_SESSION['user']['id'], $productId);
        $this->setFlash($ok ? 'success' : 'danger', $ok ? 'Added to wishlist.' : 'Unable to add wishlist item.');
        if ($redirect === 'catalog/product/detail') {
            $this->redirect(BASE_URL . '/?r=catalog/product/detail&id=' . urlencode($productId));
        }
        $this->redirectToRoute($redirect);
    }

    public function remove(): void {
        $this->requireCustomer('wishlist/wishlist/view');
        $productId = trim((string) ($_POST['product_id'] ?? ''));
        $redirect = trim((string) ($_POST['redirect'] ?? 'wishlist/wishlist/view'));

        $ok = $productId !== '' && $this->model->removeFromWishlist((string) $_SESSION['user']['id'], $productId);
        $this->setFlash($ok ? 'success' : 'danger', $ok ? 'Removed from wishlist.' : 'Unable to remove wishlist item.');
        if ($redirect === 'catalog/product/detail') {
            $this->redirect(BASE_URL . '/?r=catalog/product/detail&id=' . urlencode($productId));
        }
        $this->redirectToRoute($redirect);
    }
}

