<?php
session_start();
require_once 'db.php';

// Fetch all existing users to populate the referrer dropdown
$stmt = $pdo->query("SELECT user_id, name FROM Users ORDER BY name ASC");
$existingUsers = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $referrerId = !empty($_POST['referrer_id']) ? intval($_POST['referrer_id']) : null;

    // Insert new user with optional referrer_id
    $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, referrer_id) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $password, $referrerId])) {
        // Automatically log in the user
        $userId = $pdo->lastInsertId();
        $_SESSION['user_id'] = $userId;
        
        // Redirect to the dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Registracija nepavyko. Bandykite iš naujo.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

        .referral-instruction {
            color: white;
            margin-bottom: 20px;
            font-size: 0.9em;
        }

        .login-link {
            color: white;
            font-size: 0.9em;
            display: inline-block;
            margin-top: 20px;
        }

        .login-link:hover {
            text-decoration: underline;
            color: #291a61;
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
    <div class="form-container">
        <h2>Registracija</h2>
        <form action="register.php" method="post">
            <input type="text" name="name" class="form-control" placeholder="Vardas" required>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <input type="password" name="password" class="form-control" placeholder="Slaptažodis" required>
            
            <label for="referrer_id" class="form-label">Žmogus kuris atvedė (nebūtina):</label>
            <select name="referrer_id" id="referrer_id" class="form-control">
                <option value="">-- Niekas --</option>
                <?php foreach ($existingUsers as $user): ?>
                    <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <small class="referral-instruction">Jei jus kažkas čia atvedė, pasirinkite jų vardą iš sąrašo</small>

            <button type="submit" class="register-button">Registruotis</button>
        </form>
        <!-- Link to Login Page -->
        <a href="login.php" class="login-link">Jau turite paskyra? Prisijunkite</a>
    </div>

    <footer style="margin-bottom:-32px">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
  <path fill="#1f1f28" fill-opacity="0.5" d="M0,256L16,245.3C32,235,64,213,96,197.3C128,181,160,171,192,154.7C224,139,256,117,288,112C320,107,352,117,384,154.7C416,192,448,256,480,245.3C512,235,544,149,576,144C608,139,640,213,672,208C704,203,736,117,768,90.7C800,64,832,96,864,133.3C896,171,928,213,960,213.3C992,213,1024,171,1056,154.7C1088,139,1120,149,1152,154.7C1184,160,1216,160,1248,176C1280,192,1312,224,1344,240C1376,256,1408,256,1424,256L1440,256L1440,320L1424,320C1408,320,1376,320,1344,320C1312,320,1280,320,1248,320C1216,320,1184,320,1152,320C1120,320,1088,320,1056,320C1024,320,992,320,960,320C928,320,896,320,864,320C832,320,800,320,768,320C736,320,704,320,672,320C640,320,608,320,576,320C544,320,512,320,480,320C448,320,416,320,384,320C352,320,320,320,288,320C256,320,224,320,192,320C160,320,128,320,96,320C64,320,32,320,16,320L0,320Z"></path>
</svg>
    </footer>
</body>

</html>
