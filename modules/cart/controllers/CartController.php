<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../../catalog/models/ProductModel.php';

class CartController extends BaseController {
    private CartModel $model;

    private ProductModel $productModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new CartModel($pdo);
        $this->productModel = new ProductModel($pdo);
    }

    public function view(string $path = '', array $data = []): void {
        if ($path !== '') {
            parent::view($path, $data);
            return;
        }

        $this->requireCustomer('cart/cart/view');
        $customerId = (string) $_SESSION['user']['id'];

        $items = $this->model->getCartItems($customerId);
        $customerStmt = $this->db->prepare("SELECT Cus_ContactNo, Cus_Address FROM Customer WHERE Cus_Id = :id LIMIT 1");
        $customerStmt->execute([':id' => $customerId]);
        $customer = $customerStmt->fetch() ?: [];
        $subtotal = array_reduce($items, static function (float $sum, array $item): float {
            return $sum + (float) $item['line_total'];
        }, 0.0);

        View::render(__DIR__ . '/../views/cart.php', [
            'pageTitle' => 'Shopping Cart - PCX Store',
            'user' => $_SESSION['user'],
            'items' => $items,
            'customer' => $customer,
            'subtotal' => $subtotal,
            'categories' => $this->productModel->getAllCategories(),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function add(): void {
        $this->requireCustomer('cart/cart/view');
        $productId = trim((string) ($_POST['product_id'] ?? ''));
        $quantity = (int) ($_POST['quantity'] ?? 1);
        $redirect = trim((string) ($_POST['redirect'] ?? 'cart/cart/view'));

        if ($productId === '') {
            $this->setFlash('danger', 'Invalid product selection.');
            $this->redirectToRoute($redirect);
        }

        try {
            $ok = $this->model->addToCart((string) $_SESSION['user']['id'], $productId, $quantity);
            $this->setFlash($ok ? 'success' : 'danger', $ok ? 'Added to cart.' : 'Unable to add cart item.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        if ($redirect === 'catalog/product/detail') {
            $this->redirect(BASE_URL . '/?r=catalog/product/detail&id=' . urlencode($productId));
        }
        $this->redirectToRoute($redirect);
    }

    public function update(): void {
        $this->requireCustomer('cart/cart/view');
        $productId = trim((string) ($_POST['product_id'] ?? ''));
        $quantity = (int) ($_POST['quantity'] ?? 1);
        if ($productId === '') {
            $this->setFlash('danger', 'Invalid product selection.');
            $this->redirectToRoute('cart/cart/view');
        }

        try {
            $ok = $this->model->updateCartQuantity((string) $_SESSION['user']['id'], $productId, $quantity);
            $this->setFlash($ok ? 'success' : 'danger', $ok ? 'Cart updated.' : 'Unable to update cart.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=cart/cart/view');
    }

    public function remove(): void {
        $this->requireCustomer('cart/cart/view');
        $productId = trim((string) ($_POST['product_id'] ?? ''));
        if ($productId === '') {
            $this->setFlash('danger', 'Invalid product selection.');
            $this->redirectToRoute('cart/cart/view');
        }

        $ok = $this->model->removeFromCart((string) $_SESSION['user']['id'], $productId);
        $this->setFlash($ok ? 'success' : 'danger', $ok ? 'Removed from cart.' : 'Unable to remove cart item.');
        $this->redirect(BASE_URL . '/?r=cart/cart/view');
    }
}

