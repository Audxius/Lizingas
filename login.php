<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify password and start a session if successful
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: dashboard.php");
    } else {
        echo "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: black;
            color: white;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            margin-top: auto;
            margin-bottom: auto;
        }

        .form-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background-color: black;
            border-radius: 8px;
        }

        .form-control {
            background-color: #1f1f28;
            color: white;
            border: 1px solid #444;
            margin-bottom: 15px;
        }

        .form-control::placeholder {
            color: #ccc;
        }   

        h1 {
            color: white;
            margin-bottom: 30px;
        }

        .login-button {
            background-color: #3d2486; /* Custom color */
            color: white;
            width: 100%; /* Make the button full width */
            padding: 12px; /* Increase padding for a larger appearance */
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-button:hover {
            background-color: #291a61; /* Darker shade on hover */
        }

        footer {
            width: 100%;
            background: black;
            text-align: center;
            padding-top: 10px;
        }

        svg {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h1>Prisijungti</h1>
                <form action="login.php" method="post">
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Slaptažodis"
                        required>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <a href="register.php" class="text-white">Registruotis</a>
                    </div>
                    <button type="submit" class="login-button">Prisijungti</button>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#1f1f28" fill-opacity="0.5"
                d="M0,96L80,90.7C160,85,320,75,480,85.3C640,96,800,128,960,133.3C1120,139,1280,117,1360,106.7L1440,96L1440,320L0,320Z">
            </path>
        </svg>
    </footer>
</body>

</html>
