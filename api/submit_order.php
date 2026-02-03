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
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $phone === '' || $message === '') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Please fill in all required fields.'
    ]);
    exit;
}

$sanitizedEmail = '';
if ($email !== '') {
    $sanitizedEmail = filter_var($email, FILTER_VALIDATE_EMAIL) ?: '';
}

$order = [
    'id' => uniqid('order_', true),
    'name' => $name,
    'phone' => $phone,
    'email' => $sanitizedEmail,
    'message' => $message,
    'submitted_at' => gmdate('c')
];

$storageDir = __DIR__ . '/../storage';
$ordersFile = $storageDir . '/orders.json';

if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

$orders = [];
if (file_exists($ordersFile)) {
    $existing = file_get_contents($ordersFile);
    if ($existing !== false) {
        $orders = json_decode($existing, true) ?: [];
    }
}

$orders[] = $order;
file_put_contents($ordersFile, json_encode($orders, JSON_PRETTY_PRINT));

$adminEmail = getenv('BOOTH_EMAIL') ?: 'sekharc2a7@gmail.com';
$subject = 'New order request from ' . $name;
$emailBody = "Name: {$name}\nPhone: {$phone}\nEmail: {$sanitizedEmail}\nRequest: {$message}\n";
@mail($adminEmail, $subject, $emailBody);

if ($sanitizedEmail !== '') {
    $customerSubject = 'Visakha Dairy Booth 253 - Request received';
    $customerBody = "Hi {$name},\n\nThanks for reaching out to Booth 253. We received your request and will respond shortly.\n\n- Visakha Dairy Milk Booth 253";
    @mail($sanitizedEmail, $customerSubject, $customerBody);
}

$response = [
    'status' => 'success',
    'message' => 'Thank you, ' . $name . '! Your request has been received.',
    'order_id' => $order['id']
];

echo json_encode($response);
