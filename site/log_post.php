<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['prenom'])) {
    // If not logged in, redirect to login page
    header('Location: login.html');
    exit();
}

$prenom = $_SESSION['prenom'];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate and sanitize input (optional, depending on your application)
    $username = $prenom ?? 'unknown'; // Use $prenom as username or fallback to 'unknown'
    $action = isset($data['action']) ? htmlspecialchars(trim($data['action'])) : 'unknown action';

    // Validate if username is empty or not set (though $prenom should always be set if session is valid)
    if (empty($username)) {
        http_response_code(400); // Bad request
        echo 'Username is required';
        exit;
    }

    // Get current date and time
    $date = date('Y-m-d H:i:s');

    // Prepare log entry
    $logEntry = "User: $username, Date: $date, Action: $action\n";

    // Append log entry to the file
    file_put_contents('user_actions.log', $logEntry, FILE_APPEND);

    // Respond with success message
    echo 'Action logged successfully';
} else {
    // Respond with "Method Not Allowed" if not a POST request
    http_response_code(405);
    echo 'Method Not Allowed';
}
?>
