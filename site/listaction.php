<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['prenom'])) {
    $prenom = $_SESSION['prenom'];
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Creation - Welcome</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
        }
        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .logo {
            font-size: 24px;
            text-transform: uppercase;
            color: #fff;
            text-decoration: none;
        }
        .navbar .nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .navbar .nav-item {
            margin-left: 20px;
        }
        .navbar .nav-link {
            text-decoration: none;
            color: #fff;
            text-transform: uppercase;
            font-weight: bold;
        }
        .hero {
            background: url('car-image.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
        }
        .hero h1 {
            font-size: 50px;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 24px;
        }
        .section {
            padding: 50px 0;
            background-color: #fff;
            text-align: center;
        }
        .section h2 {
            margin-bottom: 20px;
        }
        .section p {
            font-size: 18px;
        }
        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        @media (max-width: 768px) {
            .navbar .menu-toggle {
                display: flex;
                flex-direction: column;
                cursor: pointer;
            }
            .navbar .menu-toggle .bar {
                width: 25px;
                height: 3px;
                background-color: #fff;
                margin: 4px 0;
            }
            .navbar .nav-list {
                display: none;
                flex-direction: column;
                width: 100%;
                position: absolute;
                top: 60px;
                left: 0;
                background-color: #333;
            }
            .navbar .nav-list.active {
                display: flex;
            }
            .navbar .nav-item {
                margin: 10px 0;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a href="home.php" class="logo">CarCreation</a>
                <div class="menu-toggle" id="mobile-menu">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
                <ul class="nav-list">
                    <li class="nav-item"><a href="index_admin.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="listusers.php" class="nav-link">list users</a></li>
                    <li class="nav-item"><a href="listaction.php" class="nav-link">list action</a></li>
                    <li class="nav-item"><a href="regist_er.php" class="nav-link">register user</a></li>

                    <?php if(! empty($prenom)): ?>
                        <li class="nav-item"><a href="#" class="nav-link"><?php echo $prenom; ?></a></li>
                        <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="login.html" class="nav-link">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
<br></br>
<center>
<?php
// File path to the users.log
$file = 'user_actions.log';

// Check if the file exists
if (file_exists($file)) {
    // Start the HTML table
    echo "<table border='1' cellpadding='10' cellspacing='0'>";
    echo "<tr><th>User</th><th>Date</th><th>Action</th></tr>";

    // Read the file into an array, each line as an element
    $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Iterate over each line and parse the user details
    foreach ($users as $user) {
        // Split the user string by ', ' to separate the details
        list($user, $date, $action) = explode(', ', $user);

        // Clean the extracted data
        $user = str_replace('User: ', '', $user);
        $date = str_replace('Date: ', '', $date);
        $action = str_replace('Action: ', '', $action);

        // Output each user as a table row
        echo "<tr>";
        echo "<td>$user</td>";
        echo "<td>$date</td>";
        echo "<td>$action</td>";
        echo "</tr>";
    }

    // End the table
    echo "</table>";
} else {
    echo "File not found.";
}
?></center>


</body>
</html>
