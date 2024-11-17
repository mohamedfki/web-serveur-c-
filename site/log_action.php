<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['prenom'])) {
    // If not logged in, redirect to login page
    header('Location: login.html');
    exit();
}

$prenom = $_SESSION['prenom'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Sanitize and validate the action (optional, depending on application needs)
    $action = isset($data['action']) ? htmlspecialchars($data['action']) : 'unknown action';

    // Check if user exists in the system (assuming user data is stored in a file)
    $userExists = false;
    if (file_exists('users.log')) {
        $users = file('users.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($users as $user) {
            list($storedNom, $storedPrenom, $storedCin, $storedPassword) = explode(', ', $user);
            $storedPrenom = str_replace('Prenom: ', '', $storedPrenom);

            // Compare logged-in user's prenom with stored prenom
            if ($storedPrenom === $prenom) {
                $userExists = true;
                break;
            }
        }
    }

    // If user exists, log the action; otherwise, redirect to login page
    if ($userExists) {
        // Log username, date, and action to a file
        $date = date('Y-m-d H:i:s');
        $logEntry = "User: $prenom, Date: $date, Action: $action\n";
        
        // Append log entry to the file
        file_put_contents('user_actions.log', $logEntry, FILE_APPEND);

        // Response to indicate success
        echo 'Action logged successfully';
    } else {
        // Redirect to login page for invalid user
        header('Location: login.html');
        exit();
    }
} else {
    // Handle invalid HTTP method (not POST)
    http_response_code(405);
    echo 'Method Not Allowed';
}
?>
