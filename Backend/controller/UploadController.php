<?php
/**
 * UploadController - Secure File Upload Management
 */
class UploadController
{
    private $targetDir;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    private $maxSize = 2 * 1024 * 1024; // 2MB

    public function __construct()
    {
        $this->targetDir = __DIR__ . '/../../Frontend/uploads/';
        if (!file_exists($this->targetDir)) {
            mkdir($this->targetDir, 0777, true);
        }
    }

    /**
     * Handles single file upload
     * @param array $file - The $_FILES['name'] array
     * @param string $prefix - Optional prefix for the filename
     * @return array - Standardized response {status, message, data}
     */
    public function upload($file, $prefix = 'img')
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ["status" => "error", "message" => "No file uploaded or upload error.", "data" => null];
        }

        // 1. Validate File Type (Direct MIME check)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedTypes)) {
            return ["status" => "error", "message" => "Invalid file type. Only JPG, PNG, and WEBP are allowed.", "data" => null];
        }

        // 2. Validate File Size
        if ($file['size'] > $this->maxSize) {
            return ["status" => "error", "message" => "File too large. Maximum size is 2MB.", "data" => null];
        }

        // 3. Sanitize Filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $cleanName = preg_replace("/[^a-zA-Z0-9]/", "_", pathinfo($file['name'], PATHINFO_FILENAME));
        $newFilename = $prefix . "_" . time() . "_" . $cleanName . "." . $extension;
        $targetPath = $this->targetDir . $newFilename;

        // 4. Move File
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                "status" => "success", 
                "message" => "File uploaded successfully.", 
                "data" => [
                    "filename" => $newFilename,
                    "url" => "uploads/" . $newFilename,
                    "full_path" => $targetPath
                ]
            ];
        }

        return ["status" => "error", "message" => "Failed to save file on server.", "data" => null];
    }

    /**
     * Deletes a file from the uploads folder
     */
    public function delete($filename)
    {
        $path = realpath($this->targetDir . $filename);
        if ($path && strpos($path, realpath($this->targetDir)) === 0 && file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
}

// Global initialization
$uploadController = new UploadController();
?>
