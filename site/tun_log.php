<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['prenom'])) {
    // If the user is not logged in, return an error
    echo 'User not logged in';
    exit();
}

// Get the action from the GET request
$action = $_GET['action'] ?? '';
$user = $_SESSION['prenom'];
$date = date('Y-m-d H:i:s');

// Specify the log file
$logFile = 'user_actions.log';

// Check if the action is valid (run or stop)
if ($action === 'run' || $action === 'stop') {
    // Log the action in the desired format
    $logEntry = "User: $user, Date: $date, Action: $action executable\n";
    
    // Append the log entry to the log file
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Perform any further actions like running or stopping the executable
    // Uncomment and modify the following lines to execute the commands if needed
    // if ($action === 'run') {
    //     exec('your_run_command_here');
    // } else if ($action === 'stop') {
    //     exec('your_stop_command_here');
    // }

    echo "Action logged successfully!";
} else {
    echo "Invalid action";
}
?>
