<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed.'
    ]);
    exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $phone === '' || $message === '') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Please fill in all required fields.'
    ]);
    exit;
}

// In a real deployment, save to database or send an email.
$response = [
    'status' => 'success',
    'message' => 'Thank you, ' . $name . '! Your request has been received.'
];

echo json_encode($response);
