
<?php
require_once 'config/config.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();

// Get user statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM chat_history WHERE user_id = :user_id) as total_messages,
    (SELECT COUNT(*) FROM uploaded_files WHERE user_id = :user_id) as total_files,
    (SELECT SUM(file_size) FROM uploaded_files WHERE user_id = :user_id) as total_storage";

$stats_stmt = $db->prepare($stats_query);
$stats_stmt->bindParam(':user_id', $_SESSION['user_id']);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get recent activity
$recent_query = "SELECT 'message' as type, message as content, created_at FROM chat_history WHERE user_id = :user_id
    UNION ALL
    SELECT 'file' as type, original_name as content, created_at FROM uploaded_files WHERE user_id = :user_id
    ORDER BY created_at DESC LIMIT 10";

$recent_stmt = $db->prepare($recent_query);
$recent_stmt->bindParam(':user_id', $_SESSION['user_id']);
$recent_stmt->execute();
$recent_activity = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
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
                <li><a href="files.php">Files</a></li>
                <li><a href="dashboard.php" style="color: #667eea; font-weight: 600;">Dashboard</a></li>
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

            <h1 style="color: #667eea; margin-bottom: 2rem;">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

            <!-- Statistics Cards -->
            <div class="dashboard-grid mb-4">
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_messages'] ?? 0); ?></div>
                    <div class="stat-label">Messages Sent</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_files'] ?? 0); ?></div>
                    <div class="stat-label">Files Uploaded</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo formatFileSize($stats['total_storage'] ?? 0); ?></div>
                    <div class="stat-label">Storage Used</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <h2 style="color: #667eea; margin-bottom: 1rem;">Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="chat.php" class="btn btn-primary">Start New Chat</a>
                    <a href="files.php" class="btn btn-secondary">Upload Files</a>
                    <a href="chat.php?history=1" class="btn btn-secondary">View Chat History</a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <h2 style="color: #667eea; margin-bottom: 1rem;">Recent Activity</h2>
                <?php if (!empty($recent_activity)): ?>
                    <div class="activity-list">
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="activity-item" style="display: flex; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #f0f0f0;">
                                <div class="activity-icon" style="margin-right: 1rem;">
                                    <?php if ($activity['type'] === 'message'): ?>
                                        <span style="font-size: 1.5rem;">💬</span>
                                    <?php else: ?>
                                        <span style="font-size: 1.5rem;">📁</span>
                                    <?php endif; ?>
                                </div>
                                <div class="activity-content" style="flex: 1;">
                                    <?php if ($activity['type'] === 'message'): ?>
                                        <div style="font-weight: 500;">Sent a message</div>
                                        <div style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars(substr($activity['content'], 0, 50)) . (strlen($activity['content']) > 50 ? '...' : ''); ?></div>
                                    <?php else: ?>
                                        <div style="font-weight: 500;">Uploaded file</div>
                                        <div style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars($activity['content']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="activity-time" style="color: #999; font-size: 0.8rem;">
                                    <?php echo date('M j, g:i a', strtotime($activity['created_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #666; text-align: center; padding: 2rem;">No recent activity. Start by sending a message or uploading a file!</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
