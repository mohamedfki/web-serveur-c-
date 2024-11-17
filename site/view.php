<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['prenom'])) {
    // If not logged in, redirect to login page
    header('Location: login.html');
    exit();
} else {
    $prenom = $_SESSION['prenom'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View JSON Data</title>
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
        .form-section {
            padding: 50px 0;
            background-color: #fff;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
                    <?php if (!empty($prenom)): ?>
                        <li class="nav-item"><a href="#" class="nav-link"><?php echo $prenom; ?></a></li>
                        <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="login.html" class="nav-link">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <section id="view-json" class="form-section">
        <div class="container">
            <h2>View JSON Data</h2>
            <button id="fetch-button">Fetch Data</button>
            <div id="output"></div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Car Industry. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fetchButton = document.getElementById("fetch-button");

            // Function to make AJAX request
            function fetchData() {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "http://127.0.0.1:1107/file_get", true);

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        var responseData = JSON.parse(xhr.responseText);
                        displayData(responseData);
                    } else {
                        console.error("Request failed with status", xhr.status);
                        displayError("Failed to fetch data. Please try again later.");
                    }
                };

                xhr.onerror = function() {
                    console.error("Request failed");
                    displayError("Failed to connect to the server. Please check your internet connection.");
                };

                xhr.send();
                logAction("view json data"); // Log the action
            }

            // Function to display data on the webpage
            function displayData(data) {
                var output = document.getElementById("output");

                // Create table
                var table = '<table>';
                table += '<tr><th>Key</th><th>Value</th></tr>';
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        table += '<tr><td>' + key + '</td><td>' + data[key] + '</td></tr>';
                    }
                }
                table += '</table>';

                // Set the innerHTML of the output div to the table
                output.innerHTML = table;
            }

            // Function to display error message
            function displayError(message) {
                var output = document.getElementById("output");
                output.innerHTML = "<p style='color: red;'>" + message + "</p>";
            }

            // Function to log user action
            function logAction(action) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "log_action.php", true);
                xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        console.log("Action logged successfully");
                    } else {
                        console.error("Failed to log action with status", xhr.status);
                    }
                };

                xhr.onerror = function() {
                    console.error("Request failed");
                };

                // Prepare JSON data for the POST request
                var jsonData = {
                    action: action
                };

                xhr.send(JSON.stringify(jsonData));
            }

            // Attach event listener to fetch button
            fetchButton.addEventListener('click', fetchData);
        });
    </script>
</body>
</html>
