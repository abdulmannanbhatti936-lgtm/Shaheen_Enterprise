<?php
/**
 * Message Model - Handles Contact Form Submissions
 */
class Message
{
    private $conn;
    private $table = 'messages';

    public $id;
    public $name;
    public $email;
    public $message;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Store contact message
     */
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => htmlspecialchars(strip_tags($this->name)),
            ':email' => htmlspecialchars(strip_tags($this->email)),
            ':message' => htmlspecialchars(strip_tags($this->message))
        ]);
    }

    /**
     * Retrieve all messages
     */
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }
}
?>