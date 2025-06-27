
<?php
require_once 'config/config.php';
requireLogin();

if (!isAdmin()) {
    flashMessage('Access denied. Admin privileges required.', 'error');
    redirect('dashboard.php');
}

$database = new Database();
$db = $database->getConnection();

// Get system statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM users WHERE role = 'admin') as total_admins,
    (SELECT COUNT(*) FROM chat_history) as total_messages,
    (SELECT COUNT(*) FROM uploaded_files) as total_files,
    (SELECT SUM(file_size) FROM uploaded_files) as total_storage";

$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Get recent users
$users_query = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 10";
$users_stmt = $db->prepare($users_query);
$users_stmt->execute();
$recent_users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="admin.php" style="color: #667eea; font-weight: 600;">Admin</a></li>
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

            <h1 style="color: #667eea; margin-bottom: 2rem;">Admin Dashboard</h1>

            <!-- System Statistics -->
            <div class="dashboard-grid mb-4">
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_messages'] ?? 0); ?></div>
                    <div class="stat-label">Total Messages</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_files'] ?? 0); ?></div>
                    <div class="stat-label">Total Files</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo formatFileSize($stats['total_storage'] ?? 0); ?></div>
                    <div class="stat-label">Storage Used</div>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="color: #667eea; margin: 0;">Recent Users</h2>
                    <span style="color: #666;"><?php echo count($recent_users); ?> users shown</span>
                </div>

                <?php if (!empty($recent_users)): ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e1e5e9;">ID</th>
                                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e1e5e9;">Username</th>
                                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e1e5e9;">Email</th>
                                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e1e5e9;">Role</th>
                                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e1e5e9;">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_users as $user): ?>
                                    <tr style="border-bottom: 1px solid #f0f0f0;">
                                        <td style="padding: 1rem;"><?php echo $user['id']; ?></td>
                                        <td style="padding: 1rem; font-weight: 600;"><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td style="padding: 1rem;"><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td style="padding: 1rem;">
                                            <span style="padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.8rem; font-weight: 600; 
                                                  background: <?php echo $user['role'] === 'admin' ? '#667eea' : '#28a745'; ?>; 
                                                  color: white;">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; color: #666;">
                                            <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: #666; text-align: center; padding: 2rem;">No users found.</p>
                <?php endif; ?>
            </div>

            <!-- Admin Actions -->
            <div class="card mt-4">
                <h2 style="color: #667eea; margin-bottom: 1rem;">Admin Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button class="btn btn-secondary" onclick="alert('Feature coming soon!')">Export User Data</button>
                    <button class="btn btn-secondary" onclick="alert('Feature coming soon!')">View System Logs</button>
                    <button class="btn btn-secondary" onclick="alert('Feature coming soon!')">Manage Settings</button>
                    <button class="btn btn-secondary" onclick="alert('Feature coming soon!')">Database Backup</button>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
