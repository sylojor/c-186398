
<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $fileManager = new FileManager($db);
    
    $files = $fileManager->getUserFiles($_SESSION['user_id']);
    
    echo json_encode([
        'success' => true,
        'files' => $files
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to retrieve files'
    ]);
}
?>
