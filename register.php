<?php
session_start();
require_once 'db.php';

// Handle AJAX requests for user search
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $stmt = $pdo->prepare("SELECT user_id, name FROM Users WHERE name LIKE ? ORDER BY name ASC");
    $stmt->execute(["%$query%"]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit();
}

// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $referrerId = !empty($_POST['referrer_id']) ? intval($_POST['referrer_id']) : null;

    // Insert new user with optional referrer_id
    $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, referrer_id) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $password, $referrerId])) {
        $userId = $pdo->lastInsertId();
        $_SESSION['user_id'] = $userId;

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Registracija nepavyko. Bandykite iš naujo.";
    }
}
?>
<!DOCTYPE html><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: black;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .form-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background-color: black;
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            color: white;
            margin-bottom: 30px;
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

        .register-button {
            background-color: #3d2486;
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .register-button:hover {
            background-color: #291a61;
        }

        #user-search-results {
            background-color: #1f1f28;
            color: white;
            border: 1px solid #444;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            z-index: 10;
            width: 100%;
            display: none;
        }

        #user-search-results div {
            padding: 10px;
            cursor: pointer;
        }

        #user-search-results div:hover {
            background-color: #3d2486;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Registracija</h2>
        <form action="register.php" method="POST">
            <input type="text" name="name" class="form-control" placeholder="Vardas" required>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <input type="password" name="password" class="form-control" placeholder="Slaptažodis" required>

            <label for="referrer_search" class="form-label">Žmogus kuris atvedė (nebūtina):</label>
            <div style="position: relative;">
                <input type="text" id="referrer_search" class="form-control" placeholder="Ieškoti žmogaus" autocomplete="off">
                <div id="user-search-results"></div>
            </div>
            <input type="hidden" name="referrer_id" id="referrer_id">

            <button type="submit" class="register-button">Registruotis</button>
        </form>
        <a href="login.php" class="login-link">Jau turite paskyra? Prisijunkite</a>
    </div>

    <script>
        const searchInput = document.getElementById('referrer_search');
        const searchResults = document.getElementById('user-search-results');
        const referrerIdInput = document.getElementById('referrer_id');

        searchInput.addEventListener('input', async () => {
            const query = searchInput.value.trim();
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';

            if (query.length > 0) {
                const response = await fetch(`register.php?query=${encodeURIComponent(query)}`);
                const users = await response.json();

                if (users.length > 0) {
                    users.forEach(user => {
                        const div = document.createElement('div');
                        div.textContent = user.name;
                        div.dataset.userId = user.user_id;

                        div.addEventListener('click', () => {
                            searchInput.value = user.name;
                            referrerIdInput.value = user.user_id;
                            searchResults.innerHTML = '';
                            searchResults.style.display = 'none';
                        });

                        searchResults.appendChild(div);
                    });

                    searchResults.style.display = 'block';
                }
            }
        });

        document.addEventListener('click', (e) => {
            if (!searchResults.contains(e.target) && e.target !== searchInput) {
                searchResults.style.display = 'none';
            }
        });
    </script>
</body>

</html>
