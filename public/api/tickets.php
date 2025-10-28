<?php
// public/api/tickets.php

session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Initialize tickets array if it doesn't exist
if (!isset($_SESSION['tickets'])) {
    $_SESSION['tickets'] = [];
}

// ✅ Handle GET requests (fetch ticket data)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'get') {
        $ticketId = $_GET['ticket_id'] ?? null;
        
        if (!$ticketId) {
            echo json_encode(['success' => false, 'message' => 'Ticket ID is required']);
            exit;
        }
        
        // Find the ticket
        $found = false;
        foreach ($_SESSION['tickets'] as $ticket) {
            if ($ticket['id'] === $ticketId) {
                echo json_encode([
                    'success' => true,
                    'ticket' => $ticket
                ]);
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
        }
        exit;
    }
}
$response = ['success' => false, 'message' => 'Unknown error'];

try {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            if (empty($_POST['title'])) {
                throw new Exception('Title is required');
            }

            $newTicket = [
                'id' => uniqid(),
                'title' => $_POST['title'],
                'description' => $_POST['description'] ?? '',
                'priority' => $_POST['priority'] ?? 'medium',
                'status' => 'open',
                'createdAt' => date('Y-m-d H:i:s'),
                'user_id' => $_SESSION['user']['id']
            ];

            $_SESSION['tickets'][] = $newTicket;

            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Ticket created successfully!'
            ];

            $response = [
                'success' => true,
                'message' => 'Ticket created successfully',
                'ticket' => $newTicket
            ];
            break;

        case 'update':
            if (empty($_POST['ticket_id'])) {
                throw new Exception('Ticket ID is required');
            }

            if (empty($_POST['title'])) {
                throw new Exception('Title is required');
            }

            $ticketId = $_POST['ticket_id'];
            $found = false;

            foreach ($_SESSION['tickets'] as &$ticket) {
                if ($ticket['id'] === $ticketId) {
                    $ticket['title'] = $_POST['title'];
                    $ticket['description'] = $_POST['description'] ?? $ticket['description'];
                    $ticket['status'] = $_POST['status'] ?? $ticket['status'];
                    $ticket['priority'] = $_POST['priority'] ?? $ticket['priority'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new Exception('Ticket not found');
            }

            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Ticket updated successfully!'
            ];

            $response = [
                'success' => true,
                'message' => 'Ticket updated successfully'
            ];
            break;

        case 'delete':
            if (empty($_POST['ticket_id'])) {
                throw new Exception('Ticket ID is required');
            }

            $ticketId = $_POST['ticket_id'];
            $initialCount = count($_SESSION['tickets']);

            $_SESSION['tickets'] = array_filter($_SESSION['tickets'], function ($ticket) use ($ticketId) {
                return $ticket['id'] !== $ticketId;
            });

            if (count($_SESSION['tickets']) >= $initialCount) {
                throw new Exception('Ticket not found');
            }

            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Ticket deleted successfully!'
            ];

            $response = [
                'success' => true,
                'message' => 'Ticket deleted successfully'
            ];
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
exit;
?>
