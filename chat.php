
<?php
require_once 'config/config.php';
requireLogin();

$database = new Database();
$db = $database->getConnection();
$chat = new AIChat($db);

// Get chat history
$chat_history = $chat->getChatHistory($_SESSION['user_id']);
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="nav container">
            <a href="index.php" class="logo"><?php echo SITE_NAME; ?></a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="chat.php" style="color: #667eea; font-weight: 600;">AI Chat</a></li>
                <li><a href="files.php">Files</a></li>
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
            <h1 style="color: #667eea; margin-bottom: 2rem; text-align: center;">AI Chat Assistant</h1>

            <div class="chat-container">
                <div class="chat-header">
                    🤖 AI Assistant - Ready to help!
                </div>
                
                <div class="chat-messages" id="chat-messages">
                    <?php if (!empty($chat_history)): ?>
                        <?php foreach (array_reverse($chat_history) as $msg): ?>
                            <div class="message <?php echo $msg['role']; ?>">
                                <?php echo htmlspecialchars($msg['message']); ?>
                                <div style="font-size: 0.8rem; opacity: 0.7; margin-top: 0.5rem;">
                                    <?php echo date('M j, g:i a', strtotime($msg['created_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="message assistant">
                            Hello! I'm your AI assistant. How can I help you today?
                        </div>
                    <?php endif; ?>
                </div>
                
                <form class="chat-input" id="chat-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="text" id="chat-input" placeholder="Type your message here..." class="form-control" required>
                    <button type="submit" id="send-button" class="btn btn-primary">Send</button>
                </form>
            </div>

            <div class="card mt-4">
                <h3 style="color: #667eea; margin-bottom: 1rem;">Tips for better conversations:</h3>
                <ul style="color: #666; line-height: 1.8;">
                    <li>Be specific and clear in your questions</li>
                    <li>Ask follow-up questions for more details</li>
                    <li>Use the AI to brainstorm, analyze, or get creative ideas</li>
                    <li>Your conversation history is saved for reference</li>
                </ul>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
