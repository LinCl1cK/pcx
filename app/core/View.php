<?php
// app/core/View.php
declare(strict_types=1);

class View {
    public static function render(string $file, array $data = []): void {
        extract($data, EXTR_SKIP);
        require $file;
    }
}
