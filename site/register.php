<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $nom = htmlspecialchars($data['nom']);
    $prenom = htmlspecialchars($data['prenom']);
    $cin = htmlspecialchars($data['cin']);
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $userExists = false;

    if (file_exists('users.log')) {
        $users = file('users.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($users as $user) {
            list($storedNom, $storedPrenom, $storedCin, $storedPassword) = explode(', ', $user);
            $storedPrenom = str_replace('Prenom: ', '', $storedPrenom);
            if ($storedPrenom === $prenom) {
                $userExists = true;
                break;
            }
        }
    }

    if ($userExists) {
        $_SESSION['error_message'] = 'Le prenom existe déjà. Veuillez en choisir un autre.';
        header('Location: regist_er.php');
    } else {
        $logEntry = "Nom: $nom, Prenom: $prenom, CIN: $cin, Password: $password\n";
        file_put_contents('users.log', $logEntry, FILE_APPEND);
        header('Location: listusers.php');
    }
    exit();
} else {
    http_response_code(405);
    echo 'Method Not Allowed';
}
?>
