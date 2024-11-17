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
    <title>Post JSON Data</title>
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

    <section id="post-json" class="form-section">
        <div class="container">
            <h1>Post JSON Data</h1>
            <form id="postDataForm">
                <label for="a">Value of a (integer):</label><br>
                <input type="number" id="a" name="a" required><br><br>
                
                <label for="b">Value of b (integer):</label><br>
                <input type="number" id="b" name="b" required><br><br>
                
                <label for="c">Value of c (string):</label><br>
                <input type="text" id="c" name="c" required><br><br>
                
                <button type="submit">Submit</button>
            </form>
            <div id="responseMessage"></div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Car Industry. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('postDataForm').addEventListener('submit', function(event) {
            event.preventDefault();

            // Prepare JSON object from form data
            let formData = {
                "a": parseInt(document.getElementById('a').value),
                "b": parseInt(document.getElementById('b').value),
                "c": document.getElementById('c').value
            };

            // Create a new XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Configure it: POST request to the specified URL
            xhr.open('POST', 'http://127.0.0.1:1107/file_post', true);

            // Set the Content-Type header for JSON
            xhr.setRequestHeader('Content-Type', 'application/json');

            // Define what happens on successful data submission
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    // Display response from server
                    document.getElementById('responseMessage').innerHTML = xhr.responseText;
                } else {
                    // Handle request error
                    document.getElementById('responseMessage').innerHTML = 'Request failed, status ' + xhr.status;
                }
            };

            // Define what happens in case of an error
            xhr.onerror = function() {
                console.error('Request failed');
                document.getElementById('responseMessage').innerHTML = 'An error occurred, please try again later.';
            };

            // Send the request with the JSON data
            xhr.send(JSON.stringify(formData));
            logAction();
        });

        // Function to log user action
        function logAction() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "log_post.php", true);
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
                action: "post json data"
            };

            xhr.send(JSON.stringify(jsonData));
        }
    </script>
    <script>
        const mobileMenu = document.getElementById('mobile-menu');
        const navList = document.querySelector('.nav-list');

        mobileMenu.addEventListener('click', () => {
            navList.classList.toggle('active');
        });
    </script>
</body>
</html>
