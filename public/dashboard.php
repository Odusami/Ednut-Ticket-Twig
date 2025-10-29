<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/TicketManager.php';

// ✅ Redirect if user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: auth.php');
    exit;
}

$user = $_SESSION['user'];
$ticketManager = new TicketManager();

// ✅ Fetch all tickets for this user
$tickets = $ticketManager->getTicketsByUser($user['id']);

// ✅ Fetch only recent tickets (limit 5 recommended)
if (method_exists($ticketManager, 'getRecentTicketsByUser')) {
    $recentTickets = $ticketManager->getRecentTicketsByUser($user['id']);
} else {
    // Fallback: use last 5 from all tickets
    $recentTickets = array_slice($tickets, 0, 5);
}

// ✅ Normalize keys for Twig (camelCase)
$recentTickets = array_map(function ($t) {
    return [
        'id' => $t['id'],
        'title' => $t['title'],
        'status' => $t['status'],
        'description' => $t['description'],
        'priority' => $t['priority'],
        'createdAt' => $t['created_at'] ?? null, // map created_at → createdAt
    ];
}, $recentTickets);

// ✅ Calculate stats
$stats = [
    'total' => count($tickets),
    'open' => count(array_filter($tickets, fn($t) => $t['status'] === 'open')),
    'inProgress' => count(array_filter($tickets, fn($t) => $t['status'] === 'in_progress')),
    'closed' => count(array_filter($tickets, fn($t) => $t['status'] === 'closed')),
];

// ✅ Handle toast messages
$toast = $_SESSION['toast'] ?? null;
unset($_SESSION['toast']);

// ✅ Setup Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, ['debug' => true]);
$twig->addExtension(new \Twig\Extension\DebugExtension());

// ✅ Render dashboard page
echo $twig->render('pages/dashboard.twig', [
    'user' => [
        'name' => $user['name'] ?? 'User',
        'email' => $user['email'] ?? ''
    ],
    'stats' => $stats,
    'recentTickets' => $recentTickets,
    'ticketsCount' => count($tickets),
    'current_page' => 'dashboard',
    'toast' => $toast,
]);
