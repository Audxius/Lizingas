<?php
session_start();
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
</head>
<body>
    <h2>Update Number of Services</h2>
    <form action="profile.php" method="post">
        <label for="num_services">Number of Services:</label>
        <input type="number" name="num_services" id="num_services" value="<?= htmlspecialchars($user['num_services']) ?>" required><br><br>
        <button type="submit">Update Services</button>
    </form>
</body>
</html>

