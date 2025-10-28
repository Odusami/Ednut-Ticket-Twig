<?php
// public/tickets.php

require_once __DIR__ . '/../vendor/autoload.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: auth.php');
    exit;
}
$user = $_SESSION['user'];

// Initialize tickets array if not exists
if (!isset($_SESSION['tickets'])) {
    $_SESSION['tickets'] = [];
}

$tickets = $_SESSION['tickets'];

// Check if we should auto-open the create modal
$autoOpenModal = isset($_GET['create']) && $_GET['create'] === 'new';

// Calculate ticket statistics
$stats = [
    'total' => count($tickets),
    'open' => count(array_filter($tickets, fn($ticket) => $ticket['status'] === 'open')),
    'inProgress' => count(array_filter($tickets, fn($ticket) => $ticket['status'] === 'in_progress')),
    'closed' => count(array_filter($tickets, fn($ticket) => $ticket['status'] === 'closed')),
];

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('pages/tickets.twig', [
    'tickets' => $tickets,
    'stats' => $stats,
    'autoOpenModal' => $autoOpenModal,
    'user' => [
        'isAuthenticated' => true,
        'name' => $_SESSION['user']['name'] ?? 'User'
    ],
    'current_page' => 'tickets',
    'session' => $_SESSION
]);