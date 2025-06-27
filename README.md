
# PHP AI Website

A complete PHP-based AI website with user authentication, AI chat functionality, file management, and admin dashboard.

## Features

- **User Authentication**: Registration, login, logout with secure password hashing
- **AI Chat Interface**: Real-time chat with OpenAI GPT integration
- **File Management**: Upload, download, and delete files with drag & drop support
- **Admin Dashboard**: User management and system statistics
- **Responsive Design**: Modern, mobile-friendly interface
- **Security**: CSRF protection, input validation, secure file uploads

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- OpenAI API key
- cURL extension enabled

## Installation

1. **Clone or download the project files**

2. **Database Setup**
   ```sql
   -- Import the database schema
   mysql -u root -p < database/schema.sql
   ```

3. **Configuration**
   - Edit `config/config.php`
   - Update database credentials
   - Add your OpenAI API key
   - Set proper file paths

4. **File Permissions**
   ```bash
   mkdir uploads
   chmod 755 uploads
   chmod 777 uploads  # For web server write access
   ```

5. **Web Server Setup**
   - Point document root to the project folder
   - Ensure PHP and MySQL are running
   - Test with `http://localhost/index.php`

## Default Admin Account

- **Email**: admin@example.com
- **Password**: admin123

**Important**: Change the default admin password after first login!

## API Configuration

### OpenAI Setup
1. Get your API key from [OpenAI Platform](https://platform.openai.com/)
2. Update `OPENAI_API_KEY` in `config/config.php`
3. Adjust model and parameters in `classes/AIChat.php` if needed

## File Structure

```
php-ai-website/
├── config/
│   ├── config.php          # Main configuration
│   └── database.php        # Database connection
├── classes/
│   ├── User.php           # User management
│   ├── AIChat.php         # AI chat functionality
│   └── FileManager.php    # File operations
├── includes/
│   └── functions.php      # Utility functions
├── assets/
│   ├── css/style.css      # Styles
│   └── js/main.js         # JavaScript
├── ajax/
│   ├── chat.php           # Chat API endpoint
│   ├── upload.php         # File upload handler
│   ├── delete_file.php    # File deletion
│   └── get_files.php      # File listing
├── database/
│   └── schema.sql         # Database structure
├── uploads/               # File storage (create this)
├── index.php              # Homepage
├── login.php              # Login page
├── register.php           # Registration
├── dashboard.php          # User dashboard
├── chat.php               # Chat interface
├── files.php              # File manager
├── admin.php              # Admin panel
└── logout.php             # Logout handler
```

## Security Features

- **Password Hashing**: Using PHP's `password_hash()`
- **CSRF Protection**: Token-based request validation
- **Input Sanitization**: All user inputs are sanitized
- **File Upload Security**: Type and size validation
- **SQL Injection Prevention**: Prepared statements
- **Session Management**: Secure session handling

## Customization

### Styling
- Edit `assets/css/style.css` for visual changes
- Modify color scheme by updating CSS variables
- Add custom animations and effects

### AI Behavior
- Update system prompts in `classes/AIChat.php`
- Adjust model parameters (temperature, max_tokens)
- Add conversation context management

### File Types
- Modify allowed file types in `classes/FileManager.php`
- Update file size limits in `config/config.php`
- Add file preview functionality

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check MySQL credentials in `config/config.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **File Upload Issues**
   - Check `uploads/` directory permissions
   - Verify PHP `upload_max_filesize` setting
   - Ensure web server has write access

3. **AI Chat Not Working**
   - Verify OpenAI API key is correct
   - Check cURL extension is enabled
   - Review API rate limits and credits

4. **Permission Denied**
   - Set proper file permissions: `chmod 755` for directories
   - Ensure web server can read/write files
   - Check PHP error logs

### Debug Mode
Enable PHP error reporting for development:
```php
// Add to config/config.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review PHP error logs
3. Verify configuration settings
4. Test with minimal setup

## Version History

- **v1.0.0** - Initial release with core features
- Complete user authentication system
- AI chat integration
- File management capabilities
- Admin dashboard
- Responsive design
- Security implementations
