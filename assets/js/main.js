
// Main JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initChat();
    initFileUpload();
    initForms();
    initAlerts();
});

// Chat functionality
function initChat() {
    const chatForm = document.getElementById('chat-form');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');

    if (chatForm && chatMessages && chatInput) {
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });

        // Auto-scroll to bottom of chat
        scrollToBottom(chatMessages);
    }
}

function sendMessage() {
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const sendButton = document.getElementById('send-button');
    
    const message = chatInput.value.trim();
    if (!message) return;

    // Add user message to chat
    addMessageToChat(message, 'user');
    
    // Clear input and disable button
    chatInput.value = '';
    sendButton.disabled = true;
    sendButton.innerHTML = '<div class="spinner"></div>';

    // Send AJAX request
    fetch('ajax/chat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            message: message,
            csrf_token: document.querySelector('input[name="csrf_token"]').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessageToChat(data.response, 'assistant');
        } else {
            addMessageToChat('Sorry, there was an error processing your request.', 'assistant');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        addMessageToChat('Sorry, there was a connection error.', 'assistant');
    })
    .finally(() => {
        sendButton.disabled = false;
        sendButton.innerHTML = 'Send';
        chatInput.focus();
    });
}

function addMessageToChat(message, role) {
    const chatMessages = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${role}`;
    messageDiv.textContent = message;
    
    chatMessages.appendChild(messageDiv);
    scrollToBottom(chatMessages);
}

function scrollToBottom(element) {
    element.scrollTop = element.scrollHeight;
}

// File upload functionality
function initFileUpload() {
    const uploadArea = document.getElementById('file-upload-area');
    const fileInput = document.getElementById('file-input');

    if (uploadArea && fileInput) {
        // Click to upload
        uploadArea.addEventListener('click', () => fileInput.click());

        // Drag and drop
        uploadArea.addEventListener('dragover', handleDragOver);
        uploadArea.addEventListener('dragleave', handleDragLeave);
        uploadArea.addEventListener('drop', handleDrop);

        // File input change
        fileInput.addEventListener('change', handleFileSelect);
    }
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        uploadFiles(files);
    }
}

function handleFileSelect(e) {
    const files = e.target.files;
    if (files.length > 0) {
        uploadFiles(files);
    }
}

function uploadFiles(files) {
    const formData = new FormData();
    
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    
    formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

    // Show loading
    showUploadProgress();

    fetch('ajax/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Files uploaded successfully!', 'success');
            refreshFileList();
        } else {
            showAlert(data.error || 'Upload failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Upload failed', 'error');
    })
    .finally(() => {
        hideUploadProgress();
    });
}

function showUploadProgress() {
    const uploadArea = document.getElementById('file-upload-area');
    uploadArea.innerHTML = '<div class="spinner"></div><p>Uploading...</p>';
}

function hideUploadProgress() {
    const uploadArea = document.getElementById('file-upload-area');
    uploadArea.innerHTML = `
        <div class="file-upload-icon">📁</div>
        <h3>Drop files here or click to upload</h3>
        <p>Supports: JPG, PNG, PDF, DOC, TXT (Max 5MB)</p>
    `;
}

function refreshFileList() {
    fetch('ajax/get_files.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateFileList(data.files);
        }
    })
    .catch(error => console.error('Error refreshing file list:', error));
}

function updateFileList(files) {
    const fileList = document.getElementById('file-list');
    if (!fileList) return;

    fileList.innerHTML = '';
    
    files.forEach(file => {
        const fileItem = createFileItem(file);
        fileList.appendChild(fileItem);
    });
}

function createFileItem(file) {
    const div = document.createElement('div');
    div.className = 'file-item';
    div.innerHTML = `
        <div class="file-info">
            <div class="file-icon">${getFileIcon(file.mime_type)}</div>
            <div>
                <div class="file-name">${file.original_name}</div>
                <div class="file-details">${formatFileSize(file.file_size)} • ${formatDate(file.created_at)}</div>
            </div>
        </div>
        <div class="file-actions">
            <button class="btn btn-sm btn-secondary" onclick="downloadFile('${file.filename}')">Download</button>
            <button class="btn btn-sm btn-danger" onclick="deleteFile(${file.id})">Delete</button>
        </div>
    `;
    return div;
}

function getFileIcon(mimeType) {
    if (mimeType.startsWith('image/')) return '🖼️';
    if (mimeType === 'application/pdf') return '📄';
    if (mimeType.includes('word')) return '📝';
    if (mimeType === 'text/plain') return '📃';
    return '📁';
}

function formatFileSize(bytes) {
    const sizes = ['B', 'KB', 'MB', 'GB'];
    if (bytes === 0) return '0 B';
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString();
}

function downloadFile(filename) {
    window.open(`uploads/${filename}`, '_blank');
}

function deleteFile(fileId) {
    if (!confirm('Are you sure you want to delete this file?')) return;

    fetch('ajax/delete_file.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            file_id: fileId,
            csrf_token: document.querySelector('input[name="csrf_token"]').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('File deleted successfully!', 'success');
            refreshFileList();
        } else {
            showAlert('Failed to delete file', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Failed to delete file', 'error');
    });
}

// Form enhancements
function initForms() {
    // Add loading states to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<div class="spinner"></div> Processing...';
            }
        });
    });

    // Real-time validation
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('blur', validateInput);
        input.addEventListener('input', clearValidation);
    });
}

function validateInput(e) {
    const input = e.target;
    const value = input.value.trim();
    
    // Email validation
    if (input.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showInputError(input, 'Please enter a valid email address');
            return;
        }
    }
    
    // Password validation
    if (input.name === 'password' && value) {
        if (value.length < 6) {
            showInputError(input, 'Password must be at least 6 characters');
            return;
        }
    }
    
    // Required field validation
    if (input.required && !value) {
        showInputError(input, 'This field is required');
        return;
    }
    
    clearInputError(input);
}

function showInputError(input, message) {
    clearInputError(input);
    
    input.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'input-error';
    errorDiv.textContent = message;
    input.parentNode.appendChild(errorDiv);
}

function clearInputError(input) {
    input.classList.remove('error');
    const errorDiv = input.parentNode.querySelector('.input-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function clearValidation(e) {
    clearInputError(e.target);
}

// Alert system
function initAlerts() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    // Add close button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '×';
    closeBtn.className = 'alert-close';
    closeBtn.onclick = () => alertDiv.remove();
    alertDiv.appendChild(closeBtn);
    
    // Insert at top of main content
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
    }
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.style.opacity = '0';
            setTimeout(() => alertDiv.remove(), 300);
        }
    }, 5000);
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}
