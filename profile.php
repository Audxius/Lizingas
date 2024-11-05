<?php
include 'header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch the current number of services
$stmt = $pdo->prepare("SELECT num_services FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update the number of services
    $numServices = intval($_POST['num_services']);
    $stmt = $pdo->prepare("UPDATE Users SET num_services = ? WHERE user_id = ?");
    $stmt->execute([$numServices, $userId]);
    echo "Services updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Services</title>
    <style>
        /* Style the form labels */
        label {
            display: block;
            font-size: 20px;
            margin-bottom: 8px;
        }

        /* Style the input fields */
        input[type="number"] {
            width: 100%; /* Make all input fields the same width */
            padding: 8px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        /* Style the form container */
        form {
            max-width: 400px; /* Set a max-width for the form */
            margin: 0 auto; /* Center the form on the page */
        }
    </style>
</head>

<body>
    <h2>Kiek paslaugų užsiprenumeravęs</h2>
    <form action="profile.php" method="post">
        <label for="num_services">Paslaugų skaičius:</label>
        <input type="number" name="num_services" id="num_services" value="<?= htmlspecialchars($user['num_services']) ?>" required><br><br>
        <button type="submit">Atnaujinti skaičių</button>
    </form>
</body>
</html>
<?php include 'footer.php'; ?>
