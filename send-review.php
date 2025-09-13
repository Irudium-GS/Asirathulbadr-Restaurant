<?php
require __DIR__ . '/vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$inputJson = file_get_contents('php://input');

// Debugging: Save input to debug file
file_put_contents('debug-input.json', $inputJson);

$input = json_decode($inputJson, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'error' => 'Invalid JSON input',
        'inputJson' => $inputJson
    ]);
    exit;
}

// Extract and sanitize values
$name = htmlspecialchars($input['name'] ?? 'Anonymous');
$email = htmlspecialchars($input['email'] ?? 'No email provided');
$phone = htmlspecialchars($input['phone'] ?? 'No phone provided');
$subject = htmlspecialchars($input['subject'] ?? 'Customer Review');
$message = nl2br(htmlspecialchars($input['review'] ?? ''));
$rating = htmlspecialchars($input['rating'] ?? 'N/A');
$visitType = htmlspecialchars($input['visitType'] ?? 'N/A');
$visitDate = htmlspecialchars($input['visitDate'] ?? 'N/A');
$orderedItems = htmlspecialchars($input['orderedItems'] ?? 'N/A');
$recommend = htmlspecialchars($input['recommend'] ?? 'N/A');

$htmlContent = "
    <h2>New Customer Review</h2>
    <p><strong>Name:</strong> {$name}</p>
    <p><strong>Email:</strong> {$email}</p>
    <p><strong>Phone:</strong> {$phone}</p>
    <p><strong>Subject:</strong> {$subject}</p>
    <p><strong>Review:</strong><br>{$message}</p>
    <p><strong>Rating:</strong> {$rating}</p>
    <p><strong>Visit Type:</strong> {$visitType}</p>
    <p><strong>Visit Date:</strong> {$visitDate}</p>
    <p><strong>Ordered Items:</strong> {$orderedItems}</p>
    <p><strong>Recommend?:</strong> {$recommend}</p>
";

try {
    $resend = Resend::client('re_e9wRys8G_JkcriTEBEXZGYb2Z5YAxACvi'); // Add your API key

    $resend->emails->send([
        'from' => 'ACME <onboarding@resend.dev>',
        'to' => ['asirathulbadr@gmail.com'],
        'subject' => "Customer Review from {$name}",
        'html' => $htmlContent,
    ]);

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Review submitted successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'error' => $e->getMessage()]);
}
