<?php

class Auth {
    private $db;
    
    public function __construct() {
        $this->initDatabase();
    }
    
    private function initDatabase() {
        $dbUrl = getenv('DATABASE_URL');
        
        if ($dbUrl) {
            $dbParts = parse_url($dbUrl);
            $this->db = new PDO(
                "pgsql:host=" . $dbParts['host'] . ";port=" . $dbParts['port'] . ";dbname=" . ltrim($dbParts['path'], '/'),
                $dbParts['user'],
                $dbParts['pass']
            );
        } else {
            $this->db = new PDO('sqlite:' . __DIR__ . '/../database.sqlite');
        }
        
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createTables();
    }
    
    private function createTables() {
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }
    
    public function login($email, $password) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception('Invalid email or password');
        }
        
        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
    }
    
    public function signup($email, $password, $name) {
        // Check if user already exists
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            throw new Exception('User with this email already exists');
        }
        
        // Create new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $hashedPassword]);
        
        return [
            'id' => $this->db->lastInsertId(),
            'name' => $name,
            'email' => $email
        ];
    }
}
?>