
<?php
class FileManager {
    private $conn;
    private $table_name = "uploaded_files";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function uploadFile($file, $user_id) {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            throw new RuntimeException('Exceeded filesize limit.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        
        $allowed_types = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (false === $ext = array_search($mime, $allowed_types, true)) {
            throw new RuntimeException('Invalid file format.');
        }

        $filename = sprintf('%s_%s.%s', uniqid(), date('Y-m-d_H-i-s'), $ext);
        $upload_path = UPLOAD_PATH . $filename;

        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        // Save to database
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, filename=:filename, original_name=:original_name, 
                      file_path=:file_path, file_size=:file_size, mime_type=:mime_type";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':filename', $filename);
        $stmt->bindParam(':original_name', $file['name']);
        $stmt->bindParam(':file_path', $upload_path);
        $stmt->bindParam(':file_size', $file['size']);
        $stmt->bindParam(':mime_type', $mime);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $upload_path
            ];
        }
        
        throw new RuntimeException('Failed to save file information.');
    }

    public function getUserFiles($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteFile($file_id, $user_id) {
        $query = "SELECT file_path FROM " . $this->table_name . " 
                  WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $file_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($file) {
            if (file_exists($file['file_path'])) {
                unlink($file['file_path']);
            }
            
            $delete_query = "DELETE FROM " . $this->table_name . " 
                            WHERE id = :id AND user_id = :user_id";
            $delete_stmt = $this->conn->prepare($delete_query);
            $delete_stmt->bindParam(':id', $file_id);
            $delete_stmt->bindParam(':user_id', $user_id);
            
            return $delete_stmt->execute();
        }
        
        return false;
    }
}
?>
