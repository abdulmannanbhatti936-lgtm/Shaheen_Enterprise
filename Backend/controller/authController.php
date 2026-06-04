<?php
/**
 * AuthController - Bulletproof Session & Auth Management
 * Handles Login, Logout, Registration, and Session Verification
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $db;
    private $user;

    public function __construct($db)
    {
        $this->db = $db;
        $this->user = new User($db);
    }

    /**
     * Standardized Registration Logic
     */
    public function register($username, $email, $password, $role = 'customer')
    {
        // 1. Basic Sanitization
        $username = trim(htmlspecialchars(strip_tags($username)));
        $email = trim(filter_var($email, FILTER_SANITIZE_EMAIL));

        // 2. Comprehensive Validation
        if (empty($username) || empty($email) || empty($password)) {
            return ["status" => "error", "message" => "All fields are required"];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["status" => "error", "message" => "Invalid email format"];
        }

        if (strlen($password) < 8) {
            return ["status" => "error", "message" => "Password must be at least 8 characters long"];
        }

        // 3. Check for existence
        if ($this->user->emailExists($email)) {
            return ["status" => "error", "message" => "An account with this email already exists"];
        }

        // 4. Data Preparation
        $this->user->username = $username;
        $this->user->email = $email;
        $this->user->password = $password; // Hashed inside Model::create()

        // 5. Execution
        if ($this->user->create($role)) {
            return ["status" => "success", "message" => "Account registered successfully. You can now login."];
        }
        return ["status" => "error", "message" => "Registration system error. Please try again later."];
    }

    /**
     * Bulletproof Login Logic
     */
    public function login($email, $password)
    {
        $email = trim(filter_var($email, FILTER_SANITIZE_EMAIL));

        if (empty($email) || empty($password)) {
            return ["status" => "error", "message" => "Email and password are required"];
        }

        if ($this->user->login($email, $password)) {
            // Set session variables securely
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['username'] = $this->user->username;
            $_SESSION['email'] = $this->user->email;
            $_SESSION['role'] = $this->user->role;
            $_SESSION['last_activity'] = time(); // For session timeout check

            return [
                "status" => "success", 
                "message" => "Login successful",
                "data" => [
                    "id" => $this->user->id,
                    "username" => $this->user->username,
                    "role" => $this->user->role
                ]
            ];
        }

        return ["status" => "error", "message" => "Invalid email or password credentials"];
    }

    /**
     * Secure Logout
     */
    public function logout()
    {
        $_SESSION = array(); // Clear array
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        return ["status" => "success", "message" => "Logged out successfully"];
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        // Add optional session timeout logic here (e.g. 30 mins)
        if (isset($_SESSION['user_id'])) {
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
                $this->logout();
                return false;
            }
            $_SESSION['last_activity'] = time();
            return true;
        }
        return false;
    }

    /**
     * Retrieve serialized user data
     */
    public function getCurrentUser()
    {
        if ($this->isLoggedIn()) {
            return [
                "id" => $_SESSION['user_id'],
                "username" => $_SESSION['username'],
                "email" => $_SESSION['email'],
                "role" => $_SESSION['role'] ?? 'customer'
            ];
        }
        return null;
    }
}

// Global initialized instance
$authController = new AuthController($conn);
?>