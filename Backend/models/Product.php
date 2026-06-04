<?php
class Product
{
    private $conn;
    private $table = 'products';

    public $id;
    public $name;
    public $description;
    public $ingredients;
    public $price;
    public $image;
    public $category;
    public $is_featured;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT id, name, description, ingredients, price, image, category, is_featured, stock, created_at 
                  FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    public function getFeatured()
    {
        $query = "SELECT id, name, description, price, image, category 
                  FROM " . $this->table . " WHERE is_featured = 1 LIMIT 4";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $query = "SELECT id, name, description, ingredients, price, image, category, is_featured, stock 
                  FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (name, description, ingredients, price, image, category, is_featured) 
                  VALUES (:name, :description, :ingredients, :price, :image, :category, :is_featured)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':ingredients', $this->ingredients);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':is_featured', $this->is_featured, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function update()
    {
        $query = "UPDATE " . $this->table . " 
                  SET name=:name, description=:description, ingredients=:ingredients, price=:price, image=:image, category=:category, is_featured=:is_featured 
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':ingredients', $this->ingredients);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':is_featured', $this->is_featured, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function search($searchTerm = '', $category = '')
    {
        $query = "SELECT id, name, description, price, image, category, is_featured, created_at 
                  FROM " . $this->table . " WHERE 1=1";
        $params = [];

        if (!empty($searchTerm)) {
            $query .= " AND (name LIKE :search OR description LIKE :search)";
            $params[':search'] = "%$searchTerm%";
        }

        if (!empty($category) && $category !== 'All') {
            $query .= " AND category = :category";
            $params[':category'] = $category;
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCategories()
    {
        $query = "SELECT DISTINCT category FROM " . $this->table . " WHERE category IS NOT NULL AND category != ''";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }
}
?>