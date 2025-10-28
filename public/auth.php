<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

//  If already logged in, redirect to dashboard
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$isLogin = true;
$errors = [];
$formData = [
    'name' => '',
    'email' => '',
    'password' => '',
    'confirmPassword' => ''
];

//  Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isLogin = ($_POST['mode'] === 'login');
    $formData = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
        'confirmPassword' => $_POST['confirmPassword'] ?? ''
    ];

    // 🔹 Validate form inputs
    if (!$isLogin && empty(trim($formData['name']))) {
        $errors['name'] = 'Name is required';
    }

    if (empty($formData['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email is invalid';
    }

    if (empty($formData['password'])) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($formData['password']) < 6) {
        $errors['password'] = 'Password must be at least 6 characters';
    }

    if (!$isLogin) {
        if (empty($formData['confirmPassword'])) {
            $errors['confirmPassword'] = 'Please confirm your password';
        } elseif ($formData['password'] !== $formData['confirmPassword']) {
            $errors['confirmPassword'] = 'Passwords do not match';
        }
    }

    //  Process authentication if no errors
    if (empty($errors)) {
        require_once __DIR__ . '/../src/Auth.php';
        $auth = new Auth();

        try {
            if ($isLogin) {
                $user = $auth->login($formData['email'], $formData['password']);
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'logged in successfully!.'
                ];
            } else {
                $user = $auth->signup($formData['email'], $formData['password'], $formData['name']);
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Account created successfully!'
                ];
            }

            //  Store full user info in session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ];

            //  Optional: Keep backward compatibility if other files still use these
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            // Redirect to dashboard
            header('Location: dashboard.php');
            exit;

        } catch (Exception $e) {
            $errors['general'] = $e->getMessage();
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
} elseif (isset($_GET['mode'])) {
    $isLogin = ($_GET['mode'] === 'login');
}

//  Get toast from session and clear it
$toast = $_SESSION['toast'] ?? null;
unset($_SESSION['toast']);

//  Load Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('pages/auth.twig', [
    'isLogin' => $isLogin,
    'formData' => $formData,
    'errors' => $errors,
    'toast' => $toast
]);
?>
