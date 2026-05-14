<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/PromotionModel.php';

class PromotionController extends BaseController {
    private PromotionModel $model;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->model = new PromotionModel($pdo);
    }

    public function list(): void {
        $promotions = $this->model->getActivePromotions();
        View::render(__DIR__ . '/../views/list.php', [
            'promotions' => $promotions,
        ]);
    }
}

