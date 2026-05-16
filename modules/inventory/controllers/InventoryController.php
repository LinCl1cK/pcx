<?php
declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'InventoryModel.php';

class InventoryController extends BaseController {
    private InventoryModel $model;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new InventoryModel($pdo);
    }

    public function index(): void {
        $this->redirect(BASE_URL . '/?r=inventory/inventory/list');
    }

    public function list(): void {
        $this->requireEmployee();
        $readOnly = !$this->isAdministrator();
        $stocks = $this->model->getAllInventory();
        View::render(__DIR__ . '/../views/list.php', [
            'stocks' => $stocks,
            'branches' => $this->model->getBranches(),
            'flash' => $this->pullFlash(),
            'readOnly' => $readOnly,
            'employee' => $_SESSION['employee'],
            'navActive' => 'inventory',
            'pageTitle' => 'Inventory',
            'pageHeading' => 'Branch inventory',
        ]);
    }

    public function edit(): void {
        $this->requireAdministrator();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['Inv_Id'] ?? '';
            $qty = (int) ($_POST['Inv_StockQty'] ?? 0);
            $reorder = (int) ($_POST['Inv_ReorderLevel'] ?? 0);
            if ($qty < 0 || $reorder < 0) {
                $this->setFlash('danger', 'Quantities must be zero or positive.');
                $this->redirect(BASE_URL . '/?r=inventory/inventory/list');
            }
            $this->model->updateStock((string) $id, $qty, $reorder);
            $this->redirect(BASE_URL . '/?r=inventory/inventory/list');
        } else {
            $id = $_GET['id'] ?? null;
            $stock = $this->model->getInventoryById((string) $id);
            View::render(__DIR__ . '/../views/edit.php', [
                'stock' => $stock,
                'employee' => $_SESSION['employee'],
                'navActive' => 'inventory',
                'pageTitle' => 'Edit inventory',
                'pageHeading' => 'Edit inventory',
            ]);
        }
    }

    public function transfer(): void {
        $this->requireAdministrator();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/?r=inventory/inventory/list');
        }
        $qty = (int) ($_POST['qty'] ?? 0);
        if ($qty <= 0) {
            $this->setFlash('danger', 'Transfer quantity must be positive.');
            $this->redirect(BASE_URL . '/?r=inventory/inventory/list');
        }
        try {
            $this->model->transferStock(
                (string) $_POST['Inv_ProdId'],
                (string) $_POST['from_branch'],
                (string) $_POST['to_branch'],
                $qty
            );
            $this->setFlash('success', 'Branch transfer completed.');
        } catch (Throwable $e) {
            $this->setFlash('danger', $e->getMessage());
        }
        $this->redirect(BASE_URL . '/?r=inventory/inventory/list');
    }
}
