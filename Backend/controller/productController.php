<?php
/**
 * ProductController - Standardized Data Management
 * Handles Product Listing, Retrieval, Creation, and Modification
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/UploadController.php';

class ProductController
{
    private $db;
    private $product;
    private $uploader;

    public function __construct($db)
    {
        $this->db = $db;
        $this->product = new Product($db);
        $this->uploader = new UploadController();
    }

    /**
     * Retrieve all products
     */
    public function listAll()
    {
        $data = $this->product->getAll();
        return [
            "status" => "success",
            "message" => "Products retrieved successfully",
            "data" => $data
        ];
    }

    /**
     * Retrieve featured products
     */
    public function listFeatured()
    {
        $data = $this->product->getFeatured();
        return [
            "status" => "success",
            "message" => "Featured products retrieved successfully",
            "data" => $data
        ];
    }

    /**
     * Retrieve single product by ID
     */
    public function show($id)
    {
        $data = $this->product->getById($id);
        if ($data) {
            return [
                "status" => "success",
                "message" => "Product found",
                "data" => $data
            ];
        }
        return [
            "status" => "error",
            "message" => "Product not found",
            "data" => null
        ];
    }

    /**
     * Add new product (Admin)
     */
    public function add($data, $file)
    {
        $this->product->name = htmlspecialchars(strip_tags($data['name']));
        $this->product->description = htmlspecialchars(strip_tags($data['description']));
        $this->product->ingredients = htmlspecialchars(strip_tags($data['ingredients'] ?? ''));
        $this->product->price = (float) $data['price'];
        $this->product->category = htmlspecialchars(strip_tags($data['category']));
        $this->product->is_featured = isset($data['is_featured']) ? 1 : 0;
        $this->product->stock = (int) ($data['stock'] ?? 0);

        // Handle Image Source using the new UploadController
        $img_type = $data['img_type'] ?? 'upload';
        
        if ($img_type === 'upload' && $file && $file['error'] === 0) {
            $uploadRes = $this->uploader->upload($file, 'prod');
            if ($uploadRes['status'] === 'success') {
                $this->product->image = $uploadRes['data']['url']; // Stores "uploads/filename.jpg"
            } else {
                return $uploadRes;
            }
        } elseif ($img_type === 'url' && !empty($data['image_url'])) {
            // Secure URL download (Phase 2 enhancement)
            $url = $data['image_url'];
            $fileContent = @file_get_contents($url);
            if ($fileContent !== false) {
                $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = "prod_url_" . time() . "." . $ext;
                file_put_contents(__DIR__ . "/../../Frontend/uploads/" . $filename, $fileContent);
                $this->product->image = "uploads/" . $filename;
            }
        } elseif ($img_type === 'existing' && !empty($data['existing_image_select'])) {
            $this->product->image = "images/" . $data['existing_image_select'];
        }

        if ($this->product->create()) {
            return ["status" => "success", "message" => "Product added successfully", "data" => null];
        }
        return ["status" => "error", "message" => "Failed to add product to database", "data" => null];
    }

    /**
     * Update existing product (Admin)
     */
    public function update($id, $data, $file)
    {
        $this->product->id = (int) $id;
        $this->product->name = htmlspecialchars(strip_tags($data['name']));
        $this->product->description = htmlspecialchars(strip_tags($data['description']));
        $this->product->ingredients = htmlspecialchars(strip_tags($data['ingredients'] ?? ''));
        $this->product->price = (float) $data['price'];
        $this->product->category = htmlspecialchars(strip_tags($data['category']));
        $this->product->is_featured = isset($data['is_featured']) ? 1 : 0;
        $this->product->stock = (int) ($data['stock'] ?? 0);

        // Handle Image Update
        $img_type = $data['img_type'] ?? 'upload';
        
        if ($img_type === 'upload' && $file && $file['error'] === 0) {
            $uploadRes = $this->uploader->upload($file, 'prod');
            if ($uploadRes['status'] === 'success') {
                $this->product->image = $uploadRes['data']['url'];
            } else {
                return $uploadRes;
            }
        } elseif ($img_type === 'url' && !empty($data['image_url'])) {
            $url = $data['image_url'];
            $fileContent = @file_get_contents($url);
            if ($fileContent !== false) {
                $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = "prod_url_" . time() . "." . $ext;
                file_put_contents(__DIR__ . "/../../Frontend/uploads/" . $filename, $fileContent);
                $this->product->image = "uploads/" . $filename;
            }
        } elseif ($img_type === 'existing' && !empty($data['existing_image_select'])) {
            $this->product->image = "images/" . $data['existing_image_select'];
        } else {
            $this->product->image = $data['existing_image'] ?? null;
        }

        if ($this->product->update()) {
            return ["status" => "success", "message" => "Product updated successfully", "data" => null];
        }
        return ["status" => "error", "message" => "Failed to update product", "data" => null];
    }

    /**
     * Delete product (Admin)
     */
    public function delete($id)
    {
        // Optional: Clean up images in Phase 3
        if ($this->product->delete($id)) {
            return ["status" => "success", "message" => "Product deleted successfully", "data" => null];
        }
        return ["status" => "error", "message" => "Failed to delete product", "data" => null];
    }

    /**
     * Search products
     */
    public function search($searchTerm = '', $category = '')
    {
        $data = $this->product->search($searchTerm, $category);
        return [
            "status" => "success",
            "message" => "Search results retrieved",
            "data" => $data
        ];
    }

    /**
     * Get distinct categories
     */
    public function getCategories()
    {
        $data = $this->product->getCategories();
        return [
            "status" => "success",
            "message" => "Categories retrieved",
            "data" => $data
        ];
    }

    /**
     * List image assets from the legacy images folder
     */
    public function getExistingImages()
    {
        $dir = __DIR__ . "/../../Frontend/images/";
        if (!is_dir($dir)) return [];
        
        $files = array_diff(scandir($dir), array('.', '..'));
        return array_values($files);
    }
}

// Global initialization
$productController = new ProductController($conn);
?>