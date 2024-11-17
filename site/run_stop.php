<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['prenom'])) {
    // If not logged in, redirect to login page
    header('Location: login.html');
    exit();
}

$prenom = $_SESSION['prenom'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executable Control</title>
    <script>
       function sendRequest(action) {
    fetch(`http://127.0.0.1:1107/${action}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            document.getElementById("result").innerHTML = data;
            console.log('Response:', data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById("result").innerHTML = `Error: ${error.message}`;
        });

 fetch(`tun_log.php?action=${action}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            document.getElementById("result").innerHTML = data;
            console.log('Response:', data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById("result").innerHTML = `Error: ${error.message}`;
        });
}

    </script>
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
        .navbar .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }
        .navbar .menu-toggle .bar {
            width: 25px;
            height: 3px;
            background-color: #fff;
            margin: 4px 0;
        }
        .form-section {
            padding: 50px 0;
            background-color: #fff;
            text-align: center;
        }
        form {
            display: inline-block;
            text-align: left;
        }
        form label {
            font-weight: bold;
        }
        form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-transform: uppercase;
            font-weight: bold;
        }
        form button:hover {
            background-color: #555;
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
                    <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="postt.php" class="nav-link">POST</a></li>
                    <li class="nav-item"><a href="view.php" class="nav-link">VIEW</a></li>
 <li class="nav-item"><a href="run_stop.php" class="nav-link">run</a></li>
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

    <h1>Control Executable</h1>
    <button onclick="sendRequest('run')">Run Executable</button>
    <button onclick="sendRequest('stop')">Stop Executable</button>
    <div id="result"></div>
</body>
</html>
