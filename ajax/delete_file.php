
<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!verifyCSRFToken($input['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit;
}

$file_id = intval($input['file_id'] ?? 0);

if (!$file_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid file ID']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $fileManager = new FileManager($db);
    
    if ($fileManager->deleteFile($file_id, $_SESSION['user_id'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'File not found or access denied']);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete file'
    ]);
}
?>
