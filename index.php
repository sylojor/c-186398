
<?php
require_once 'config/config.php';

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - AI-Powered Website</title>
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
                <?php if (isLoggedIn()): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn btn-secondary">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary">Register</a></li>
                <?php endif; ?>
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

            <div class="hero-section text-center mb-4">
                <h1 style="font-size: 3rem; margin-bottom: 1rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Welcome to <?php echo SITE_NAME; ?>
                </h1>
                <p style="font-size: 1.25rem; margin-bottom: 2rem; color: #666;">
                    Your AI-powered digital assistant for intelligent conversations and file management
                </p>
                
                <?php if (!isLoggedIn()): ?>
                    <div class="hero-actions" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="register.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">Get Started Free</a>
                        <a href="login.php" class="btn btn-secondary" style="font-size: 1.1rem; padding: 1rem 2rem;">Sign In</a>
                    </div>
                <?php else: ?>
                    <div class="hero-actions" style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <a href="chat.php" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">Start Chatting</a>
                        <a href="dashboard.php" class="btn btn-secondary" style="font-size: 1.1rem; padding: 1rem 2rem;">Dashboard</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="features-section">
                <div class="dashboard-grid">
                    <div class="card">
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🤖</div>
                            <h3 style="color: #667eea; margin-bottom: 1rem;">AI Chat</h3>
                        </div>
                        <p style="color: #666; line-height: 1.6;">
                            Engage in intelligent conversations with our advanced AI assistant. Get answers, brainstorm ideas, and solve problems efficiently.
                        </p>
                        <?php if (isLoggedIn()): ?>
                            <a href="chat.php" class="btn btn-primary mt-2">Start Chatting</a>
                        <?php endif; ?>
                    </div>

                    <div class="card">
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">📁</div>
                            <h3 style="color: #667eea; margin-bottom: 1rem;">File Management</h3>
                        </div>
                        <p style="color: #666; line-height: 1.6;">
                            Upload, organize, and manage your files securely. Support for documents, images, and various file formats.
                        </p>
                        <?php if (isLoggedIn()): ?>
                            <a href="files.php" class="btn btn-primary mt-2">Manage Files</a>
                        <?php endif; ?>
                    </div>

                    <div class="card">
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🔒</div>
                            <h3 style="color: #667eea; margin-bottom: 1rem;">Secure & Private</h3>
                        </div>
                        <p style="color: #666; line-height: 1.6;">
                            Your data is protected with enterprise-grade security. All conversations and files are encrypted and private.
                        </p>
                    </div>
                </div>

                <?php if (isLoggedIn()): ?>
                    <div class="card">
                        <h2 style="color: #667eea; margin-bottom: 1rem; text-align: center;">Quick Actions</h2>
                        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                            <a href="chat.php" class="btn btn-primary">New Chat</a>
                            <a href="files.php" class="btn btn-secondary">Upload Files</a>
                            <a href="dashboard.php" class="btn btn-secondary">View Dashboard</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer style="text-align: center; padding: 2rem; color: #666; background: rgba(255, 255, 255, 0.8); margin-top: 4rem;">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
