<?php

class TicketManager {
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
        CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            title TEXT NOT NULL,
            description TEXT,
            status TEXT DEFAULT "open",
            priority TEXT DEFAULT "medium",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users (id)
        )
    ');
    }
    
    public function getTicketsByUser($userId) {
        $stmt = $this->db->prepare('
            SELECT * FROM tickets 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTicket($ticketId, $userId) {
        $stmt = $this->db->prepare('
            SELECT * FROM tickets 
            WHERE id = ? AND user_id = ?
        ');
        $stmt->execute([$ticketId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createTicket($userId, $title, $description, $priority = 'medium') {
        $stmt = $this->db->prepare('
            INSERT INTO tickets (user_id, title, description, priority) 
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$userId, $title, $description, $priority]);
        
        return $this->db->lastInsertId();
    }
    
   // Update the updateTicket method to ensure updated_at changes:
public function updateTicket($ticketId, $userId, $data) {
    $allowedFields = ['title', 'description', 'status', 'priority'];
    $updates = [];
    $params = [];
    
    foreach ($data as $field => $value) {
        if (in_array($field, $allowedFields)) {
            $updates[] = "$field = ?";
            $params[] = $value;
        }
    }
    
    // Always update the updated_at timestamp
    $updates[] = "updated_at = CURRENT_TIMESTAMP";
    $params[] = $ticketId;
    $params[] = $userId;
    
    $stmt = $this->db->prepare("
        UPDATE tickets 
        SET " . implode(', ', $updates) . " 
        WHERE id = ? AND user_id = ?
    ");
    
    return $stmt->execute($params);
}
    
    public function deleteTicket($ticketId, $userId) {
        $stmt = $this->db->prepare('
            DELETE FROM tickets 
            WHERE id = ? AND user_id = ?
        ');
        return $stmt->execute([$ticketId, $userId]);
    }
}
?>