
<?php
require_once 'config/config.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();
$fileManager = new FileManager($db);

// Get user's files
$user_files = $fileManager->getUserFiles($_SESSION['user_id']);
$csrf_token = generateCSRFToken();
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="nav container">
            <a href="index.php" class="logo"><?php echo SITE_NAME; ?></a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="chat.php">AI Chat</a></li>
                <li><a href="files.php" style="color: #667eea; font-weight: 600;">Files</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="admin.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <h1 style="color: #667eea; margin-bottom: 2rem;">File Manager</h1>

            <!-- File Upload Area -->
            <div class="card mb-4">
                <h2 style="color: #667eea; margin-bottom: 1rem;">Upload Files</h2>
                <div class="file-upload-area" id="file-upload-area">
                    <div class="file-upload-icon" style="font-size: 3rem; margin-bottom: 1rem;">📁</div>
                    <h3>Drop files here or click to upload</h3>
                    <p style="color: #666; margin-top: 0.5rem;">Supports: JPG, PNG, PDF, DOC, TXT (Max 5MB)</p>
                    <input type="file" id="file-input" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.txt,.doc,.docx" style="display: none;">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                </div>
            </div>

            <!-- File List -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="color: #667eea; margin: 0;">Your Files</h2>
                    <span style="color: #666;"><?php echo count($user_files); ?> files</span>
                </div>

                <?php if (!empty($user_files)): ?>
                    <div class="file-list" id="file-list">
                        <?php foreach ($user_files as $file): ?>
                            <div class="file-item">
                                <div class="file-info">
                                    <div class="file-icon">
                                        <?php
                                        if (strpos($file['mime_type'], 'image/') === 0) echo '🖼️';
                                        elseif ($file['mime_type'] === 'application/pdf') echo '📄';
                                        elseif (strpos($file['mime_type'], 'word') !== false) echo '📝';
                                        elseif ($file['mime_type'] === 'text/plain') echo '📃';
                                        else echo '📁';
                                        ?>
                                    </div>
                                    <div>
                                        <div class="file-name" style="font-weight: 600; margin-bottom: 0.25rem;">
                                            <?php echo htmlspecialchars($file['original_name']); ?>
                                        </div>
                                        <div class="file-details" style="color: #666; font-size: 0.9rem;">
                                            <?php echo formatFileSize($file['file_size']); ?> • 
                                            <?php echo date('M j, Y g:i a', strtotime($file['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="file-actions" style="display: flex; gap: 0.5rem;">
                                    <a href="uploads/<?php echo htmlspecialchars($file['filename']); ?>" target="_blank" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Download</a>
                                    <button onclick="deleteFile(<?php echo $file['id']; ?>)" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; background: #dc3545; color: white;">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: #666;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📂</div>
                        <h3>No files uploaded yet</h3>
                        <p>Upload your first file using the area above!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
