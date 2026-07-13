<?php

declare(strict_types=1);

header('Content-Type: application/json');

echo json_encode([
    'name' => 'TableFlow Backend',
    'status' => 'ok',
    'timestamp' => date(DATE_ATOM),
]);
