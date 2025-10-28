<?php
session_start();
$tickets = $_SESSION['tickets'] ?? [];
require_once __DIR__ . '/../vendor/autoload.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: auth.php');
    exit;
}

require_once __DIR__ . '/../src/TicketManager.php';

$ticketManager = new TicketManager();

$user = $_SESSION['user'];
$tickets = $ticketManager->getTicketsByUser($user['id']);


// Calculate stats
$stats = [
    'total' => count($tickets),
    'open' => count(array_filter($tickets, fn($t) => $t['status'] === 'open')),
    'inProgress' => count(array_filter($tickets, fn($t) => $t['status'] === 'in_progress')),
    'closed' => count(array_filter($tickets, fn($t) => $t['status'] === 'closed')),
];



// Get toast from session and clear it
$toast = $_SESSION['toast'] ?? null;
unset($_SESSION['toast']);

// Load Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'debug' => true, // ✅ enables debug mode
]);

$twig->addExtension(new \Twig\Extension\DebugExtension()); // ✅ adds dump() support


echo $twig->render('pages/dashboard.twig', [
    'user' => [
        'name' => $_SESSION['user']['name'] ?? $user['name'] ?? 'User',
        'email' => $_SESSION['user']['email'] ?? $user['email'] ?? '',
        'toto' => $_SESSION['tickets']['email'] ?? $user['email'] ?? '',

    ],
    'stats' => $stats,
    'recentTickets' => $recentTickets,
    'ticketsCount' => count($tickets),
    'current_page' => 'dashboard',
    'toast' => $toast ?? null
]);


?>
