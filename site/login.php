<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = htmlspecialchars($_POST['prenom']);
    $password = $_POST['password'];

    // Check if the user is the admin
    if ($prenom === 'admin' && $password === 'admin') {
        $_SESSION['prenom'] = $prenom;
        header('Location: index_admin.php');
        exit();
    }

    if (file_exists('users.log')) {
        $users = file('users.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($users as $user) {
            list($storedNom, $storedPrenom, $storedCin, $storedPassword) = explode(', ', $user);
            $storedPrenom = str_replace('Prenom: ', '', $storedPrenom);
            $storedPassword = str_replace('Password: ', '', $storedPassword);

            if ($storedPrenom === $prenom && password_verify($password, $storedPassword)) {
                $_SESSION['prenom'] = $prenom;
                
                // Debugging: Check if session variable is set
                var_dump($_SESSION['prenom']); // Check output in browser console or PHP error log

                header('Location: index.php');
                exit();
            }
        }
    }

    $_SESSION['login_error'] = 'Invalid prenom or password';
    header('Location: login.html');
    exit();
} else {
    http_response_code(405);
    echo 'Method Not Allowed';
}
?>
