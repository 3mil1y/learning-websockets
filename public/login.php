<?php
require_once '../auth.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['anonymous']) && $data['anonymous']) {
    $nickname = isset($data['nickname']) ? trim($data['nickname']) : null;
    $result = createAnonymousUser($nickname);
} else if (isset($data['username']) && isset($data['password'])) {
    $result = authenticate($data['username'], $data['password']);
} else {
    $result = ['success' => false];
}

echo json_encode($result); 